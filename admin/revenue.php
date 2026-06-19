<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: login.php");
    exit;
}

$year = $_GET['year'] ?? date('Y');

// Fetch revenue data
$sql = "
    SELECT 
        MONTHNAME(b.booking_date) as month_name,
        MONTH(b.booking_date) as month_num,
        COUNT(b.id) as total_bookings,
        SUM(c.price_per_hour) as total_revenue
    FROM bookings b
    JOIN courts c ON b.court_id = c.id
    WHERE b.payment_status = 'Paid' 
        AND b.status = 'Confirmed'
        AND YEAR(b.booking_date) = ?
    GROUP BY MONTH(b.booking_date)
    ORDER BY month_num ASC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $year);
$stmt->execute();
$result = $stmt->get_result();

$months = [];
$total_bookings = 0;
$total_revenue = 0;

while ($row = $result->fetch_assoc()) {
    $months[] = $row;
    $total_bookings += $row['total_bookings'];
    $total_revenue += $row['total_revenue'];
}

$base_path = '../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Reports - Admin</title>
    <link rel="icon" href="uploads/nav_logo1.png">
    <meta name="base-url" content="/futsalbs">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .revenue-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .summary-card {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-sm);
            text-align: center;
        }
        .summary-card h3 {
            font-size: 2.5rem;
            color: var(--primary-green);
            margin-bottom: 0.5rem;
        }
        .summary-card p {
            color: var(--text-secondary);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

<div class="admin-layout">
    <?php include '../includes/admin_sidebar.php'; ?>
    
    <div class="admin-main">
        <div class="admin-header">
            <h2>Revenue Reports</h2>
            <div class="d-flex align-items-center flex-wrap" style="gap: 1rem;">
                <form method="GET" class="d-flex align-items-center flex-wrap" style="gap: 0.5rem;">
                    <label for="year" class="form-label mb-0">Select Year:</label>
                    <select name="year" id="year" class="form-control" onchange="this.form.submit()" style="width: auto;">
                        <?php 
                        $currentYear = date('Y');
                        for($i = $currentYear; $i >= $currentYear - 5; $i--): 
                        ?>
                            <option value="<?php echo $i; ?>" <?php if($year == $i) echo 'selected'; ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </form>
                <button onclick="exportToPDF()" class="btn btn-outline" style="color: var(--danger); border-color: var(--danger);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.5rem;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Export PDF
                </button>
                <a href="../api/export_revenue.php?year=<?php echo $year; ?>" class="btn btn-outline">Export CSV</a>
            </div>
        </div>
        
        <div class="revenue-summary">
            <div class="summary-card">
                <h3>Rs. <?php echo number_format($total_revenue, 2); ?></h3>
                <p>Total Revenue (<?php echo htmlspecialchars($year); ?>)</p>
            </div>
            <div class="summary-card">
                <h3 style="color: var(--heading-color);"><?php echo number_format($total_bookings); ?></h3>
                <p>Total Bookings (<?php echo htmlspecialchars($year); ?>)</p>
            </div>
        </div>
        
        <div class="card">
            <h3 class="mb-4">Monthly Breakdown</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Total Bookings</th>
                            <th>Total Revenue (Rs.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($months) > 0): ?>
                            <?php foreach($months as $m): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($m['month_name']); ?></strong></td>
                                <td><?php echo $m['total_bookings']; ?></td>
                                <td style="color: var(--primary-green); font-weight: 600;">Rs. <?php echo number_format($m['total_revenue'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center">No revenue data for this year.</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if(count($months) > 0): ?>
                    <tfoot>
                        <tr style="background: var(--table-header-bg); color: var(--heading-color);">
                            <td><strong>GRAND TOTAL</strong></td>
                            <td><strong><?php echo $total_bookings; ?></strong></td>
                            <td style="color: var(--primary-green); font-weight: bold; font-size: 1.1rem;">Rs. <?php echo number_format($total_revenue, 2); ?></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function exportToPDF() {
    const tableHTML = document.querySelector('.table-responsive').innerHTML;
    const content = `
        <div style="font-family: sans-serif; padding: 20px;">
            <h2 style="text-align:center; color: #0f172a; margin-bottom: 5px;">ALS Indoor Futsal</h2>
            <h3 style="text-align:center; color: #5cb85c; margin-bottom: 30px; margin-top: 0;">Revenue Report (${<?php echo $year; ?>})</h3>
            
            <table style="width: 100%; margin-bottom: 30px; background: #f1f5f9; border-radius: 8px;">
                <tr>
                    <td style="padding: 15px; text-align: center;"><strong>Total Bookings:</strong><br><span style="font-size: 1.5rem; color: #0f172a;"><?php echo $total_bookings; ?></span></td>
                    <td style="padding: 15px; text-align: center;"><strong>Total Revenue:</strong><br><span style="font-size: 1.5rem; color: #5cb85c;">Rs. <?php echo number_format($total_revenue, 2); ?></span></td>
                </tr>
            </table>
            
            <style>
                table.table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                table.table th, table.table td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
                table.table th { background-color: #f8fafc; color: #0f172a; font-weight: bold; }
                table.table tfoot tr td { font-weight: bold; background-color: #f8fafc; }
            </style>
            ${tableHTML}
        </div>
    `;

    const opt = {
        margin:       10,
        filename:     'Revenue_Report_<?php echo $year; ?>.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    
    html2pdf().set(opt).from(content).save();
}
</script>
<script src="../assets/js/main.js"></script>
</body>
</html>
