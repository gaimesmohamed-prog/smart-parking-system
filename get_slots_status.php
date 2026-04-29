<?php
/**
 * api/get_slots_status.php
 * Ultra-stable status provider to prevent UI flickering.
 */
header('Content-Type: application/json');

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$slots = [];
$rows = ['A', 'B', 'C', 'D', 'E'];

// 1. Get Physical Status from parking_slots
$physicalStatus = [];
$res1 = $conn->query("SELECT slot_label, status FROM parking_slots");
while ($res1 && $row = $res1->fetch_assoc()) {
    $physicalStatus[strtoupper($row['slot_label'])] = $row['status'];
}

// 2. Get Active Bookings from cars (Reserved/Occupied)
$activeBookings = [];
$res2 = $conn->query("SELECT slot_id, status FROM cars WHERE status IN ('reserved', 'occupied')");
while ($res2 && $row = $res2->fetch_assoc()) {
    $activeBookings[strtoupper($row['slot_id'])] = $row['status'];
}

// 3. Merge Logic: Priority goes to 'occupied' > 'reserved' > 'physical' > 'available'
foreach ($rows as $r) {
    for ($i = 1; $i <= 10; $i++) {
        $id = $r . $i;
        
        // Determine final stable status
        $status = 'available';
        
        if (isset($activeBookings[$id])) {
            $status = $activeBookings[$id];
        } elseif (isset($physicalStatus[$id])) {
            $status = $physicalStatus[$id];
        }

        $slots[] = [
            'id'       => $id,
            'status'   => $status,
            'entry_ts' => 0
        ];
    }
}

echo json_encode([
    'status' => 'success',
    'slots'  => $slots,
]);
?>
