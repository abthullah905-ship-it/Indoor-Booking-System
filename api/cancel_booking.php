<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/email_helper.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
    exit;
}

$booking_id = $_POST['booking_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$booking_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing booking ID']);
    exit;
}

// Check booking ownership and current status
$stmtInfo = $conn->prepare("
    SELECT b.id, b.status, c.name as court_name, b.booking_date, b.start_time, u.email, u.full_name
    FROM bookings b
    JOIN courts c ON b.court_id = c.id
    JOIN users u ON b.user_id = u.id
    WHERE b.id = ? AND b.user_id = ?
");
$stmtInfo->bind_param("ii", $booking_id, $user_id);
$stmtInfo->execute();
$info = $stmtInfo->get_result()->fetch_assoc();

if (!$info) {
    echo json_encode(['status' => 'error', 'message' => 'Booking not found or access denied']);
    exit;
}

if (in_array($info['status'], ['Cancelled', 'Completed'])) {
    echo json_encode(['status' => 'error', 'message' => 'This booking cannot be cancelled (already ' . strtolower($info['status']) . ')']);
    exit;
}

$stmt = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE id = ?");
$stmt->bind_param("i", $booking_id);

if ($stmt->execute()) {
    $timeLabel = date('g:i A', strtotime($info['start_time'])) . ' - ' . date('g:i A', strtotime('+1 hour', strtotime($info['start_time'])));
    $bookingData = [
        'id' => $info['id'],
        'court_name' => $info['court_name'],
        'booking_date' => $info['booking_date'],
        'time_label' => $timeLabel,
        'start_time' => $info['start_time']
    ];
    
    sendBookingCancellation($info['email'], $info['full_name'], $bookingData);
    
    echo json_encode(['status' => 'success', 'message' => 'Booking cancelled successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error cancelling booking']);
}
?>
