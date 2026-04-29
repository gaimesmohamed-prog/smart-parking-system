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
    echo json_encode([
        'status' => 'error', 
        'message' => '⚠️ تذكرة غير صالحة أو منتهية الصلاحية.'
    ]);
    exit;
}

// === حالة الدخول (إذا كان محجوزاً فقط) ===
if (($car['status'] ?? '') === 'reserved') {
    $entry = parking_mark_entry($conn, $qrData);
    
    $userName = "زائر";
    $uStmt = $conn->prepare("SELECT full_name FROM users WHERE user_id = ?");
    if ($uStmt) {
        $uStmt->bind_param("i", $car['user_id']);
        $uStmt->execute();
        $uRes = $uStmt->get_result()->fetch_assoc();
        $userName = $uRes['full_name'] ?? "زائر";
        $uStmt->close();
    }

    echo json_encode([
        'status' => $entry['success'] ? 'success' : 'error',
        'type' => 'entry',
        'message' => '✅ تم تسجيل دخول المركبة بنجاح!',
        'plate' => $car['plate_number'],
        'slot' => $car['slot_id'],
        'full_name' => $userName
    ]);
    exit;
}

// === حالة الخروج (إذا كان مشغولاً) ===
if (($car['status'] ?? '') === 'occupied') {
    $exit = parking_complete_exit($conn, $qrData);
    echo json_encode([
        'status' => $exit['success'] ? 'success' : 'error',
        'type' => 'exit',
        'message' => $exit['success']
            ? "🚪 تم تسجيل الخروج للمركبة {$exit['plate']}. التكلفة: {$exit['cost']} ج.م"
            : $exit['message'],
        'plate' => $exit['plate'] ?? null,
        'cost' => $exit['cost'] ?? null,
        'slot' => $car['slot_id']
    ]);
    exit;
}

echo json_encode([
    'status' => 'error',
    'message' => '⚠️ السيارة في حالة غير معروفة أو تم معالجتها مسبقاً.'
]);
?>
