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

$action = $_POST['action'] ?? '';
$court_id = $_POST['id'] ?? null;

if (!$court_id) {
    echo json_encode(['status' => 'error', 'message' => 'Court ID required']);
    exit;
}

if ($action === 'toggle_status') {
    $stmt = $conn->prepare("UPDATE courts SET is_active = NOT is_active WHERE id = ?");
    $stmt->bind_param("i", $court_id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Court status updated']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
    }
    exit;
}

if ($action === 'update_info') {
    $name = trim($_POST['name'] ?? '');
    $price = $_POST['price'] ?? 0;
    $sport_type = $_POST['sport_type'] ?? 'football';
    $description = trim($_POST['description'] ?? '');

    if (empty($name) || $price <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Valid name and price are required']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE courts SET name = ?, price_per_hour = ?, sport_type = ?, description = ? WHERE id = ?");
    $stmt->bind_param("sdssi", $name, $price, $sport_type, $description, $court_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Court updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update court']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
?>
