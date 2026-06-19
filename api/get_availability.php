<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

$court_id = isset($_GET['court_id']) ? (int)$_GET['court_id'] : 0;
$date     = $_GET['date'] ?? date('Y-m-d');

if (!$court_id) {
    echo json_encode(['status' => 'error', 'message' => 'Court ID required']);
    exit;
}

$time_slots = [
    '07:00:00','08:00:00','09:00:00','10:00:00','11:00:00',
    '12:00:00','13:00:00','14:00:00','15:00:00','16:00:00',
    '17:00:00','18:00:00','19:00:00','20:00:00','21:00:00'
];

$stmt = $conn->prepare(
    "SELECT start_time FROM bookings
     WHERE court_id = ? AND booking_date = ?
     AND status NOT IN ('Cancelled','Completed')"
);
$stmt->bind_param("is", $court_id, $date);
$stmt->execute();
$result = $stmt->get_result();

$booked = [];
while ($row = $result->fetch_assoc()) {
    $booked[$row['start_time']] = true;
}

$today       = date('Y-m-d');
$currentHour = (int)date('H');
$slots       = [];

foreach ($time_slots as $time) {
    $slotHour = (int)substr($time, 0, 2);
    $label    = date('g:i A', strtotime($time)) . ' - ' 
              . date('g:i A', strtotime('+1 hour', strtotime($time)));

    if ($date < $today) {
        $status = 'Past';
    } elseif ($date === $today && $slotHour <= $currentHour) {
        $status = 'Past';
    } elseif (isset($booked[$time])) {
        $status = 'Booked';
    } else {
        $status = 'Available';
    }

    $slots[] = ['time_label' => $label, 'raw_time' => $time, 'status' => $status];
}

echo json_encode(['status' => 'success', 'data' => $slots]);