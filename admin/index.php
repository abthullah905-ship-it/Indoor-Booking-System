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

// KPI Queries
$kpis = [
    'revenue' => 0,
    'users' => 0,
    'pending_bookings' => 0,
    'courts' => 0
];

$res = $conn->query("SELECT SUM(c.price_per_hour) as rev FROM bookings b JOIN courts c ON b.court_id = c.id WHERE b.payment_status = 'Paid'");
if ($row = $res->fetch_assoc()) $kpis['revenue'] = $row['rev'] ?? 0;

$res = $conn->query("SELECT COUNT(*) as c FROM users");
if ($row = $res->fetch_assoc()) $kpis['users'] = $row['c'];

$res = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status = 'Pending'");
if ($row = $res->fetch_assoc()) $kpis['pending_bookings'] = $row['c'];

$res = $conn->query("SELECT COUNT(*) as c FROM courts WHERE is_active = 1");
if ($row = $res->fetch_assoc()) $kpis['courts'] = $row['c'];

// Recent bookings
$recentStmt = $conn->query("
    SELECT b.id, b.booking_date, b.start_time, b.status, c.name as court_name, u.full_name as user_name
    FROM bookings b
    JOIN courts c ON b.court_id = c.id
    JOIN users u ON b.user_id = u.id
    ORDER BY b.created_at DESC LIMIT 5
");
$recent_bookings = $recentStmt->fetch_all(MYSQLI_ASSOC);

$base_path = '../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ALS Indoor</title>
    <link rel="icon" href="uploads/nav_logo1.png">
    <meta name="base-url" content="/futsalbs">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }
        .kpi-card {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .kpi-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .kpi-revenue .kpi-icon { background: #dcfce7; color: #16a34a; }
        .kpi-users .kpi-icon { background: #e0e7ff; color: #4f46e5; }
        .kpi-pending .kpi-icon { background: #fef3c7; color: #d97706; }
        .kpi-courts .kpi-icon { background: #f3e8ff; color: #9333ea; }
        
        .kpi-value {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--dark-navy);
            line-height: 1.2;
        }
        .kpi-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>

<div class="admin-layout">
    <?php include '../includes/admin_sidebar.php'; ?>
    
    <div class="admin-main">
        <div class="admin-header">
            <h2>Dashboard</h2>
            <div class="d-flex align-items-center">
                <span class="mr-3 text-secondary">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                <div style="width: 40px; height: 40px; background: var(--primary-green); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                    <?php echo substr($_SESSION['admin_name'], 0, 1); ?>
                </div>
            </div>
        </div>
        
        <div class="kpi-grid">
            <div class="kpi-card kpi-revenue">
                <div class="kpi-icon">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </div>
                <div>
                    <div class="kpi-value">Rs. <?php echo number_format($kpis['revenue']); ?></div>
                    <div class="kpi-label">Total Revenue</div>
                </div>
            </div>
            
            <div class="kpi-card kpi-users">
                <div class="kpi-icon">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div>
                    <div class="kpi-value"><?php echo $kpis['users']; ?></div>
                    <div class="kpi-label">Registered Users</div>
                </div>
            </div>
            
            <div class="kpi-card kpi-pending">
                <div class="kpi-icon">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div>
                    <div class="kpi-value"><?php echo $kpis['pending_bookings']; ?></div>
                    <div class="kpi-label">Pending Bookings</div>
                </div>
            </div>
            
            <div class="kpi-card kpi-courts">
                <div class="kpi-icon">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                </div>
                <div>
                    <div class="kpi-value"><?php echo $kpis['courts']; ?></div>
                    <div class="kpi-label">Active Courts</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 style="margin: 0;">Recent Bookings</h3>
                <a href="bookings.php" class="btn btn-outline btn-sm">View All</a>
            </div>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Court</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($recent_bookings) > 0): ?>
                            <?php foreach($recent_bookings as $b): ?>
                            <tr>
                                <td>#<?php echo $b['id']; ?></td>
                                <td><?php echo htmlspecialchars($b['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($b['court_name']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($b['booking_date'])) . ' at ' . date('g:i A', strtotime($b['start_time'])); ?></td>
                                <td>
                                    <?php 
                                        $badgeClass = match($b['status']) {
                                            'Confirmed', 'Completed' => 'badge-success',
                                            'Pending' => 'badge-warning',
                                            'Cancelled' => 'badge-secondary',
                                            default => 'badge-primary'
                                        };
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo $b['status']; ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">No recent bookings found.</td></tr>
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
