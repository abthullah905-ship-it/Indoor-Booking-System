<?php
require_once __DIR__ . '/config.php';

try {
    // We use mysqli to keep compatibility with existing queries
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Database connection failed. Please try again later.");
}
?>
