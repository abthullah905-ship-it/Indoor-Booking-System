<?php
session_start();
require_once '../includes/db.php';

if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, full_name FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['full_name'];
                
                // Update last login
                $update = $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                $update->bind_param("i", $admin['id']);
                $update->execute();
                
                header("Location: index.php");
                exit;
            } else {
                $error = 'Invalid credentials.';
            }
        } else {
            $error = 'Invalid credentials.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ALS Indoor</title>
    <link rel="icon" href="uploads/nav_logo1.png">
    <meta name="base-url" content="/futsalbs">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: url('../uploads/ij_cric.png') center/cover no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.85);
            z-index: 0;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-card);
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
            box-shadow: var(--shadow-lg);
            text-align: center;
        }
        .login-card h2 {
            color: white;
            margin-bottom: 2rem;
            font-size: 2rem;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            padding-left: 2.5rem;
        }
        .input-icon-wrapper {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .input-icon-wrapper svg {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            width: 18px;
            height: 18px;
            z-index: 2;
        }
        .btn-primary {
            width: 100%;
            padding: 0.875rem;
            font-size: 1.1rem;
            margin-top: 1rem;
        }
        .error-message {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            padding: 0.75rem;
            border-radius: var(--radius-btn);
            margin-bottom: 1.5rem;
            border: 1px solid rgba(239, 68, 68, 0.3);
            text-align: left;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>ALS Admin Control</h2>
    
    <?php if ($error): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="input-icon-wrapper">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            <input type="text" name="username" class="form-control" placeholder="Admin Username" required autofocus>
        </div>
        
        <div class="input-icon-wrapper">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Secure Login</button>
    </form>
    
    <div class="mt-4" style="margin-top: 2rem;">
        <a href="../index.php" style="color: rgba(255,255,255,0.7); font-size: 0.875rem;">&larr; Back to Website</a>
    </div>
</div>

</body>
</html>
