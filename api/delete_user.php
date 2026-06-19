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

$user_id = $_POST['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing user ID']);
    exit;
}

// Start transaction to delete bookings then user
$conn->begin_transaction();

try {
    // Delete user's bookings first to satisfy foreign key constraint
    $stmt1 = $conn->prepare("DELETE FROM bookings WHERE user_id = ?");
    $stmt1->bind_param("i", $user_id);
    $stmt1->execute();

    // Delete the user
    $stmt2 = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'User and their bookings deleted successfully']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete user: ' . $e->getMessage()]);
}
?>
