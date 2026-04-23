<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/parking_backend.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$entryTime = $input['entry_time'] ?? null;
$exitTime = $input['exit_time'] ?? null;
$pricePerHour = isset($input['price_per_hour']) ? (float) $input['price_per_hour'] : parking_default_hourly_rate();

if (!$entryTime) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'entry_time is required']);
    exit;
}

$entryTimestamp = strtotime($entryTime);
$exitTimestamp = $exitTime ? strtotime($exitTime) : time();

if ($entryTimestamp === false || $exitTimestamp === false || $exitTimestamp < $entryTimestamp) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid time range']);
    exit;
}

$hours = max(1, ceil(($exitTimestamp - $entryTimestamp) / 3600));
$totalCost = $hours * $pricePerHour;

echo json_encode([
    'success' => true,
    'data' => [
        'hours' => $hours,
        'formatted_duration' => format_duration($hours),
        'price_per_hour' => $pricePerHour,
        'total_cost' => $totalCost,
        'currency' => 'EGP',
    ],
]);
?>
