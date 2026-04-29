<?php
/**
 * api/slot_timer.php
 * AJAX endpoint — يرجع حالة المكان + الوقت المنقضي + التكلفة
 */
require_once '../header.php';

header('Content-Type: application/json');

$guid = trim($_GET['guid'] ?? '');
if ($guid === '') {
    echo json_encode(['error' => 'No GUID']);
    exit;
}

$stmt = $conn->prepare("SELECT c.status, c.slot_id, c.plate_number, h.entry_time
                        FROM cars c
                        LEFT JOIN parking_history h
                          ON h.plate_number = c.plate_number
                         AND h.slot_id = c.slot_id
                         AND h.exit_time IS NULL
                        WHERE c.parking_guid = ?
                        LIMIT 1");
$stmt->bind_param('s', $guid);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['error' => 'Not found']);
    exit;
}

$status     = $row['status'];          // reserved | occupied | completed
$entryTime  = $row['entry_time'];
$ratePerHour = 10.0;

$elapsedSec  = 0;
$cost        = 0.0;

if ($status === 'occupied' && $entryTime) {
    $elapsedSec = max(0, time() - strtotime($entryTime));
    $hours      = $elapsedSec / 3600;
    $cost       = $hours * $ratePerHour;
}

echo json_encode([
    'status'      => $status,
    'slot_id'     => $row['slot_id'],
    'elapsed_sec' => $elapsedSec,
    'cost'        => round($cost, 2),
    'entry_time'  => $entryTime,
]);
