<?php
require_once 'includes/db.php';

echo "Test 1: Verify '123456' against id 3's hash:\n";
$res = $conn->query("SELECT password FROM users WHERE id = 3");
$row = $res->fetch_assoc();
$hash = $row['password'];
echo "Hash: " . $hash . "\n";
echo "Verify result: " . (password_verify('123456', $hash) ? 'TRUE' : 'FALSE') . "\n\n";

echo "Test 2: Hash '123456' manually:\n";
$new_hash = password_hash('123456', PASSWORD_DEFAULT);
echo "New hash: " . $new_hash . "\n";
echo "Verify new hash: " . (password_verify('123456', $new_hash) ? 'TRUE' : 'FALSE') . "\n";
?>
0