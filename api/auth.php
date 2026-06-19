<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid method']);
    exit;
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'register') {
    $name  = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email']     ?? '');
    $phone = trim($_POST['phone']     ?? '');
    $pass  =      $_POST['password']  ?? '';

    if (empty($name) || empty($email) || empty($phone) || empty($pass)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    $hashed = password_hash($pass, PASSWORD_DEFAULT);
    $stmt   = $conn->prepare("INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $hashed);

    if ($stmt->execute()) {
        session_regenerate_id(true);
        $_SESSION['user_id']   = $stmt->insert_id;
        $_SESSION['user_name'] = $name;
        echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Registration failed. Phone or email may already be in use.']);
    }
    exit;
}

if ($action === 'login') {
    // New UI uses email for login
    $email = trim($_POST['email']    ?? '');
    $pass  =      $_POST['password'] ?? '';

    // Anti-brute force: simple implementation
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_login_attempt'] = time();
    }
    
    if ($_SESSION['login_attempts'] >= 5 && (time() - $_SESSION['last_login_attempt']) < 900) {
        echo json_encode(['status' => 'error', 'message' => 'Too many failed attempts. Please wait 15 minutes.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($pass, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['login_attempts'] = 0;
        echo json_encode(['status' => 'success', 'message' => 'Login successful']);
    } else {
        $_SESSION['login_attempts']++;
        $_SESSION['last_login_attempt'] = time();
        echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
?>
