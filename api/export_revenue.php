<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    die('Unauthorized');
}

$year = $_GET['year'] ?? date('Y');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=revenue_report_' . $year . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Month', 'Total Bookings', 'Total Revenue']);

$stmt = $conn->prepare("CALL GetMonthlyRevenue(?)");
$stmt->bind_param("i", $year);
$stmt->execute();
$result = $stmt->get_result();

$total_bookings = 0;
$total_revenue = 0;

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['month_name'], 
        $row['total_bookings'], 
        'Rs. ' . number_format($row['total_revenue'], 2)
    ]);
    $total_bookings += $row['total_bookings'];
    $total_revenue += $row['total_revenue'];
}

fputcsv($output, []); // Empty line
fputcsv($output, ['TOTAL', $total_bookings, 'Rs. ' . number_format($total_revenue, 2)]);

fclose($output);
?>
