<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/email_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid method']);
    exit;
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email address']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $subject, $message);

if ($stmt->execute()) {
    $messageData = [
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message
    ];
    // Send email to admin
    sendAdminContactMessage(SMTP_FROM_EMAIL, $messageData);
    
    echo json_encode(['status' => 'success', 'message' => 'Message sent successfully. We will get back to you soon.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send message. Please try again later.']);
}
?>
