<?php
header('Content-Type: application/json');

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/parking_backend.php';

$dashboardSlots = [];
$rows = ['A', 'B', 'C', 'D', 'E'];
foreach ($rows as $r) {
    for ($i = 1; $i <= 10; $i++) {
        $dashboardSlots[] = $r . $i;
    }
}
$dashboard = parking_get_dashboard_slots($conn, $dashboardSlots);

$occupiedGlobal = [];
$globalRes = $conn->query("SELECT slot_id FROM cars WHERE status = 'occupied'");
while ($globalRes && $row = $globalRes->fetch_assoc()) {
    $occupiedGlobal[] = strtoupper($row['slot_id']);
}

echo json_encode([
    'status' => 'success',
    'free_count' => $dashboard['free_count'],
    'occupied_count' => $dashboard['occupied_count'],
    'dashboard_slots' => $dashboard['slots'],
    'occupied_global' => $occupiedGlobal,
]);
?>
