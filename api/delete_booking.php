<?php
session_start();
require_once '../includes/db.php';

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

$stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
$stmt->bind_param("i", $booking_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Booking deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error deleting booking']);
}
?>
