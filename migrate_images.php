<?php
require_once __DIR__ . '/includes/db.php';
// db.php includes config.php which has Cloudinary config.

header('Content-Type: application/json');

function uploadToCloudinary($filePath) {
    if (!file_exists($filePath)) return false;
    
    $mime_type = mime_content_type($filePath);
    if (!$mime_type) $mime_type = 'image/jpeg';
    
    $timestamp = time();
    $signature = sha1('timestamp=' . $timestamp . CLOUDINARY_API_SECRET);

    $ch = curl_init('https://api.cloudinary.com/v1_1/' . CLOUDINARY_CLOUD_NAME . '/image/upload');
    $cfile = new CURLFile($filePath, $mime_type, basename($filePath));
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
        return $result['secure_url'] ?? false;
    }
    return false;
}

$mapping = [];
$errors = [];

// 1. Migrate Static Images in uploads/
$uploadsDir = __DIR__ . '/uploads/';
$files = scandir($uploadsDir);
foreach ($files as $file) {
    if ($file === '.' || $file === '..' || is_dir($uploadsDir . $file)) continue;
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        $localPath = 'uploads/' . $file;
        $url = uploadToCloudinary($uploadsDir . $file);
        if ($url) {
            $mapping[$localPath] = $url;
        } else {
            $errors[] = "Failed to upload $file";
        }
    }
}

// 2. Migrate Payment Screenshots
$res = $conn->query("SELECT id, payment_screenshot FROM bookings WHERE payment_screenshot IS NOT NULL AND payment_screenshot != '' AND payment_screenshot NOT LIKE 'http%'");
$paymentsMigrated = 0;
while ($row = $res->fetch_assoc()) {
    $id = $row['id'];
    $filename = $row['payment_screenshot'];
    $filePath = __DIR__ . '/uploads/payments/' . $filename;
    
    $url = uploadToCloudinary($filePath);
    if ($url) {
        $stmt = $conn->prepare("UPDATE bookings SET payment_screenshot = ? WHERE id = ?");
        $stmt->bind_param("si", $url, $id);
        $stmt->execute();
        $paymentsMigrated++;
    } else {
        $errors[] = "Failed to upload payment screenshot $filename for booking $id";
    }
}

echo json_encode([
    'status' => 'success',
    'static_mapping' => $mapping,
    'payments_migrated' => $paymentsMigrated,
    'errors' => $errors
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
