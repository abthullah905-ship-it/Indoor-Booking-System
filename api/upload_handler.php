<?php
session_start();
require_once '../includes/db.php';

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

if (!$booking_id || !isset($_FILES['screenshot'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing data']);
    exit;
}

$tmpPath = $_FILES['screenshot']['tmp_name'];
if (!is_uploaded_file($tmpPath)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid file upload']);
    exit;
}

// Server-side MIME type check using finfo
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $tmpPath);
finfo_close($finfo);

$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

if (!in_array($mime_type, $allowed_types)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed.']);
    exit;
}

// Verify booking belongs to user
$stmt = $conn->prepare("SELECT id FROM bookings WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Booking not found or access denied']);
    exit;
}

$ext = match($mime_type) {
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp',
    default => 'jpg'
};

$filename = 'payment_' . $booking_id . '_' . time() . '.' . $ext;
$dest     = '../uploads/payments/' . $filename;

if (move_uploaded_file($tmpPath, $dest)) {
    $stmt = $conn->prepare("UPDATE bookings SET payment_screenshot = ?, payment_status = 'Pending' WHERE id = ?");
    $stmt->bind_param("si", $filename, $booking_id);
    $stmt->execute();
    echo json_encode(['status' => 'success', 'message' => 'Payment screenshot uploaded successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'File upload failed']);
}
?>
