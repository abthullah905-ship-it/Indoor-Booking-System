<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/email_helper.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to book']);
    exit;
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
    exit;
}

$user_id      = $_SESSION['user_id'];
$court_id     = $_POST['court_id']     ?? null;
$booking_date = $_POST['booking_date'] ?? null;
$start_time   = $_POST['start_time']   ?? null;

if (!$court_id || !$booking_date || !$start_time) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// Validate date is not in the past
$today = date('Y-m-d');
if ($booking_date < $today) {
    echo json_encode(['status' => 'error', 'message' => 'Cannot book a slot in the past']);
    exit;
}

// Validate time if booking is for today
if ($booking_date === $today) {
    $currentHour = (int)date('H');
    $bookingHour = (int)date('H', strtotime($start_time));
    if ($bookingHour <= $currentHour) {
        echo json_encode(['status' => 'error', 'message' => 'Cannot book past time slots for today']);
        exit;
    }
}

// Check for existing active booking on this slot
$check = $conn->prepare(
    "SELECT id FROM bookings
     WHERE court_id = ? AND booking_date = ? AND start_time = ?
       AND status NOT IN ('Cancelled', 'Completed')"
);
$check->bind_param("iss", $court_id, $booking_date, $start_time);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'This slot is already booked. Please choose another time.']);
    exit;
}

// Get court info for email
$courtStmt = $conn->prepare("SELECT name, price_per_hour FROM courts WHERE id = ?");
$courtStmt->bind_param("i", $court_id);
$courtStmt->execute();
$court = $courtStmt->get_result()->fetch_assoc();

// Get user email for notification
$userStmt = $conn->prepare("SELECT full_name, email, phone FROM users WHERE id = ?");
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();

$stmt = $conn->prepare(
    "INSERT INTO bookings (court_id, user_id, booking_date, start_time, payment_status, status)
     VALUES (?, ?, ?, ?, 'Unpaid', 'Pending')"
);
$stmt->bind_param("iiss", $court_id, $user_id, $booking_date, $start_time);

if ($stmt->execute()) {
    $booking_id = $stmt->insert_id;
    $timeLabel = date('g:i A', strtotime($start_time)) . ' - ' . date('g:i A', strtotime('+1 hour', strtotime($start_time)));
    
    $bookingData = [
        'id'            => $booking_id,
        'court_name'    => $court['name'],
        'booking_date'  => $booking_date,
        'time_label'    => $timeLabel,
        'price_per_hour'=> $court['price_per_hour'],
    ];

    // Send confirmation email to user
    sendBookingConfirmation($user['email'], $user['full_name'], $bookingData);
    
    // Send notification to admin
    // In a real application, you might query the admin emails. Here we use SMTP_FROM_EMAIL or a fixed admin email.
    sendAdminNewBooking(SMTP_FROM_EMAIL, $bookingData, $user);

    echo json_encode(['status' => 'success', 'booking_id' => $booking_id, 'message' => 'Booking successful']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Booking failed. Please try again.']);
}
?>
