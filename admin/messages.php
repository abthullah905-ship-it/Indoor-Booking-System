<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: login.php");
    exit;
}

// Get messages
$messages = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

$base_path = '../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Admin</title>
    <link rel="icon" href="https://res.cloudinary.com/dyqnvpyhs/image/upload/v1781865006/ivty3iruftjnsk3ymq1d.png">
    <meta name="base-url" content="/futsalbs">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .msg-cell {
            max-width: 300px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>

<div class="admin-layout">
    <?php include '../includes/admin_sidebar.php'; ?>
    
    <div class="admin-main">
        <div class="admin-header">
            <h2>Contact Messages</h2>
        </div>
        
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Sender Details</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date Received</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($messages) > 0): ?>
                            <?php foreach($messages as $m): ?>
                            <tr>
                                <td><?php echo $m['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($m['name']); ?></strong><br>
                                    <a href="mailto:<?php echo htmlspecialchars($m['email']); ?>" style="color: var(--primary-green); text-decoration: none;">
                                        <?php echo htmlspecialchars($m['email']); ?>
                                    </a>
                                </td>
                                <td><strong><?php echo htmlspecialchars($m['subject']); ?></strong></td>
                                <td class="msg-cell text-secondary"><?php echo nl2br(htmlspecialchars($m['message'])); ?></td>
                                <td style="white-space: nowrap;"><?php echo date('M j, Y g:i A', strtotime($m['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">No messages found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/main.js"></script>
</body>
</html>