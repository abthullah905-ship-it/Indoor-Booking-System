<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid method']);
    exit;
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
    exit;
}

$court_id = $_POST['court_id'] ?? null;

if (!$court_id) {
    echo json_encode(['status' => 'error', 'message' => 'Court ID required']);
    exit;
}

$conn->begin_transaction();
try {
    $stmt1 = $conn->prepare("DELETE FROM bookings WHERE court_id = ?");
    $stmt1->bind_param("i", $court_id);
    $stmt1->execute();

    $stmt2 = $conn->prepare("DELETE FROM courts WHERE id = ?");
    $stmt2->bind_param("i", $court_id);
    $stmt2->execute();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Court deleted successfully']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete court']);
}
?>
