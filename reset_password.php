<?php
require_once 'includes/db.php';

$email = 'abthullah905@gmail.com';
$password = '123456';
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Check if user exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    // Update password
    $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $update->bind_param("ss", $hashed, $email);
    if ($update->execute()) {
        echo "<h3>Success!</h3>Password for <strong>$email</strong> has been reset to <strong>$password</strong>.<br>You can now log in with these credentials.";
    } else {
        echo "Error updating password: " . $conn->error;
    }
} else {
    // Create user
    $insert = $conn->prepare("INSERT INTO users (full_name, email, phone, password) VALUES ('ijas Ahamed', ?, '0759522555', ?)");
    $insert->bind_param("ss", $email, $hashed);
    if ($insert->execute()) {
        echo "<h3>Success!</h3>User <strong>$email</strong> has been created with password <strong>$password</strong>.<br>You can now log in.";
    } else {
        echo "Error creating user: " . $conn->error;
    }
}
?>
