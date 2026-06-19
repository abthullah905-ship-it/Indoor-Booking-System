<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
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

$user_id = $_SESSION['user_id'];
$full_name = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($full_name) || empty($phone)) {
    echo json_encode(['status' => 'error', 'message' => 'Name and phone are required.']);
    exit;
}

// Check if phone is already used by another user
$stmt = $conn->prepare("SELECT id FROM users WHERE phone = ? AND id != ?");
$stmt->bind_param("si", $phone, $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Phone number is already in use by another account.']);
    exit;
}

if (!empty($password)) {
    if (strlen($password) < 6) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters.']);
        exit;
    }
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, password = ? WHERE id = ?");
    $stmt->bind_param("sssi", $full_name, $phone, $hashed, $user_id);
} else {
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
    $stmt->bind_param("ssi", $full_name, $phone, $user_id);
}

if ($stmt->execute()) {
    $_SESSION['user_name'] = $full_name;
    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update profile']);
}
?>
