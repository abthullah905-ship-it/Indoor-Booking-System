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

$timestamp = time();
$signature = sha1('timestamp=' . $timestamp . CLOUDINARY_API_SECRET);

$ch = curl_init('https://api.cloudinary.com/v1_1/' . CLOUDINARY_CLOUD_NAME . '/image/upload');
$cfile = new CURLFile($tmpPath, $mime_type, $_FILES['screenshot']['name']);
$data = [
    'file' => $cfile,
    'api_key' => CLOUDINARY_API_KEY,
    'timestamp' => $timestamp,
    'signature' => $signature
];

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    $result = json_decode($response, true);
    if (isset($result['secure_url'])) {
        $cloudinary_url = $result['secure_url'];
        
        $stmt = $conn->prepare("UPDATE bookings SET payment_screenshot = ?, payment_status = 'Pending' WHERE id = ?");
        $stmt->bind_param("si", $cloudinary_url, $booking_id);
        $stmt->execute();
        
        echo json_encode(['status' => 'success', 'message' => 'Payment screenshot uploaded successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Cloudinary upload failed: Invalid response']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Cloudinary upload failed']);
}
?>
