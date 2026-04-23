<?php
header('Content-Type: application/json');

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/parking_backend.php';

if (!isset($_POST['qr_data'])) {
    echo json_encode(['status' => 'error', 'message' => 'No QR data received']);
    exit;
}

$qrData = trim($_POST['qr_data']);
$car = parking_find_car_by_guid($conn, $qrData);

if (!$car) {
    $slot = parking_find_available_slot($conn);
    if (!$slot) {
        echo json_encode(['status' => 'error', 'message' => 'No available parking slots right now.']);
        exit;
    }

    $plate = 'NEW-' . rand(100, 999);
    $reservation = parking_create_reservation($conn, [
        'plate_number' => $plate,
        'slot_id' => $slot,
        'car_type' => 'sedan',
        'user_id' => 0,
    ]);

    if (!$reservation['success']) {
        echo json_encode(['status' => 'error', 'message' => $reservation['message']]);
        exit;
    }

    $entry = parking_mark_entry($conn, $reservation['guid']);
    echo json_encode([
        'status' => $entry['success'] ? 'success' : 'error',
        'type' => 'entry',
        'message' => $entry['message'],
        'plate' => $plate,
        'slot' => $slot,
    ]);
    exit;
}

if (($car['status'] ?? '') === 'reserved') {
    $entry = parking_mark_entry($conn, $qrData);
    echo json_encode([
        'status' => $entry['success'] ? 'success' : 'error',
        'type' => 'entry',
        'message' => $entry['message'],
        'plate' => $car['plate_number'],
        'slot' => $car['slot_id'],
    ]);
    exit;
}

$exit = parking_complete_exit($conn, $qrData);
echo json_encode([
    'status' => $exit['success'] ? 'success' : 'error',
    'type' => $exit['success'] ? 'exit' : 'error',
    'message' => $exit['success']
        ? "Vehicle {$exit['plate']} exited. Duration: {$exit['hours']} hour(s). Cost: {$exit['cost']} EGP."
        : $exit['message'],
    'plate' => $exit['plate'] ?? null,
    'cost' => $exit['cost'] ?? null,
    'history_id' => $exit['history_id'] ?? null,
]);
?>
