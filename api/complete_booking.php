<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/email_helper.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
    exit;
}

$booking_id = $_POST['booking_id'] ?? null;

if (!$booking_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing booking ID']);
    exit;
}

// Get user and booking info for email
$stmtInfo = $conn->prepare("
    SELECT b.id, c.name as court_name, u.email, u.full_name, b.status
    FROM bookings b
    JOIN courts c ON b.court_id = c.id
    JOIN users u ON b.user_id = u.id
    WHERE b.id = ?
");
$stmtInfo->bind_param("i", $booking_id);
$stmtInfo->execute();
$info = $stmtInfo->get_result()->fetch_assoc();

if (!$info) {
    echo json_encode(['status' => 'error', 'message' => 'Booking not found']);
    exit;
}

if ($info['status'] === 'Completed') {
    echo json_encode(['status' => 'error', 'message' => 'Booking is already completed']);
    exit;
}

$stmt = $conn->prepare(
    "UPDATE bookings SET status = 'Completed' WHERE id = ?"
);
$stmt->bind_param("i", $booking_id);

if ($stmt->execute()) {
    sendBookingCompleted($info['email'], $info['full_name'], $info);
    echo json_encode(['status' => 'success', 'message' => 'Booking marked as completed']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error updating booking']);
}
?>
