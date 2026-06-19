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

$name = trim($_POST['name'] ?? '');
$price = (float)($_POST['price'] ?? 0);
$sport_type = $_POST['sport_type'] ?? 'football';
$description = trim($_POST['description'] ?? '');

if (empty($name) || $price <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Valid name and price are required']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO courts (name, price_per_hour, sport_type, description) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sdss", $name, $price, $sport_type, $description);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Court added successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add court']);
}
?>
