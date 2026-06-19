<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Filters
$status_filter = $_GET['status'] ?? '';
$court_filter = $_GET['court_id'] ?? '';
$date_filter = $_GET['date'] ?? '';

$where = "1=1";
$params = [];
$types = "";

if ($status_filter) {
    $where .= " AND b.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}
if ($court_filter) {
    $where .= " AND b.court_id = ?";
    $params[] = $court_filter;
    $types .= "i";
}
if ($date_filter) {
    $where .= " AND b.booking_date = ?";
    $params[] = $date_filter;
    $types .= "s";
}

$sql = "SELECT b.*, c.name as court_name, c.price_per_hour, u.full_name as user_name, u.phone 
        FROM bookings b 
        JOIN courts c ON b.court_id = c.id 
        JOIN users u ON b.user_id = u.id 
        WHERE $where 
        ORDER BY b.booking_date DESC, b.start_time DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$bookings = $stmt->get_result();

$courts = $conn->query("SELECT id, name FROM courts")->fetch_all(MYSQLI_ASSOC);
$base_path = '../';

function getBadgeClass($status) {
    return match($status) {
        'Paid', 'Confirmed', 'Completed' => 'badge-success',
        'Pending' => 'badge-warning',
        'Unpaid' => 'badge-danger',
        'Cancelled' => 'badge-secondary',
        default => 'badge-primary'
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Admin</title>
    <link rel="icon" href="uploads/nav_logo1.png">
    <meta name="base-url" content="/futsalbs">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .filter-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-sm);
        }
        .filter-bar form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            width: 100%;
            align-items: flex-end;
        }
        .filter-item { flex: 1; min-width: 150px; }
    </style>
</head>
<body>

<div class="admin-layout">
    <?php include '../includes/admin_sidebar.php'; ?>
    
    <div class="admin-main">
        <div class="admin-header">
            <h2>Manage Bookings</h2>
        </div>
        
        <div class="filter-bar">
            <form method="GET">
                <div class="filter-item">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        <option value="Pending" <?php if($status_filter==='Pending') echo 'selected'; ?>>Pending</option>
                        <option value="Confirmed" <?php if($status_filter==='Confirmed') echo 'selected'; ?>>Confirmed</option>
                        <option value="Completed" <?php if($status_filter==='Completed') echo 'selected'; ?>>Completed</option>
                        <option value="Cancelled" <?php if($status_filter==='Cancelled') echo 'selected'; ?>>Cancelled</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label class="form-label">Court</label>
                    <select name="court_id" class="form-control">
                        <option value="">All</option>
                        <?php foreach($courts as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php if($court_filter==$c['id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-item">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($date_filter); ?>">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="bookings.php" class="btn btn-outline" style="margin-left: 0.5rem;">Reset</a>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Court & Date</th>
                            <th>Status & Payment</th>
                            <th>Receipt</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($bookings->num_rows > 0): ?>
                            <?php while($b = $bookings->fetch_assoc()): ?>
                            <tr id="booking-row-<?php echo $b['id']; ?>">
                                <td>#<?php echo $b['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($b['user_name']); ?></strong><br>
                                    <small class="text-secondary"><?php echo htmlspecialchars($b['phone']); ?></small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($b['court_name']); ?><br>
                                    <small class="text-secondary">
                                        <?php echo $b['booking_date']; ?> | <?php echo date('g:i A', strtotime($b['start_time'])); ?>
                                    </small>
                                </td>
                                <td>
                                    <div>Status: <span class="badge <?php echo getBadgeClass($b['status']); ?>" id="status-<?php echo $b['id']; ?>"><?php echo $b['status']; ?></span></div>
                                    <div class="mt-1">Pay: <span class="badge <?php echo getBadgeClass($b['payment_status']); ?>" id="payment-<?php echo $b['id']; ?>"><?php echo $b['payment_status']; ?></span></div>
                                </td>
                                <td>
                                    <?php if($b['payment_screenshot']): ?>
                                        <button onclick="viewReceipt('<?php echo htmlspecialchars($b['payment_screenshot']); ?>')" class="btn btn-outline btn-sm">View</button>
                                    <?php else: ?>
                                        <span class="text-secondary">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($b['payment_status'] !== 'Paid'): ?>
                                        <button onclick="verifyPayment(<?php echo $b['id']; ?>)" class="btn btn-success btn-sm mb-1 w-100">Verify Pay</button>
                                    <?php endif; ?>
                                    
                                    <?php if($b['status'] === 'Confirmed'): ?>
                                        <button onclick="completeBooking(<?php echo $b['id']; ?>)" class="btn btn-primary btn-sm mb-1 w-100">Complete</button>
                                    <?php endif; ?>
                                    
                                    <button onclick="deleteBooking(<?php echo $b['id']; ?>)" class="btn btn-danger btn-sm w-100">Delete</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">No bookings found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div class="modal-overlay" id="receipt-modal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 class="modal-title">Payment Screenshot</h3>
            <button class="modal-close" onclick="closeModal('receipt-modal')">&times;</button>
        </div>
        <div class="modal-body text-center">
            <img id="receipt-img" src="" alt="Receipt" style="max-width: 100%; max-height: 70vh; border-radius: var(--radius-card);">
        </div>
    </div>
</div>

<script>
    const CSRF_TOKEN = '<?php echo $_SESSION['csrf_token']; ?>';
    function viewReceipt(filename) {
        document.getElementById('receipt-img').src = '../uploads/payments/' + filename;
        openModal('receipt-modal');
    }
</script>
<script src="../assets/js/main.js"></script>
<script src="../assets/js/admin.js"></script>
</body>
</html>
