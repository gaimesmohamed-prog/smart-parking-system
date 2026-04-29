<?php
/**
 * api/mark_entry.php
 * AJAX — يسجّل دخول السيارة (reserved → occupied) ويرجع JSON
 */
require_once '../header.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$guid = trim($_POST['guid'] ?? '');
if ($guid === '') {
    echo json_encode(['success' => false, 'message' => 'GUID missing']);
    exit;
}

$result = parking_mark_entry($conn, $guid);

// أرجع entry_time الجديد عشان التايمر يبدأ من الصفر بالظبط
$entryTime = null;
if ($result['success']) {
    $stmt = $conn->prepare(
        "SELECT h.entry_time FROM parking_history h
         JOIN cars c ON c.plate_number = h.plate_number AND c.slot_id = h.slot_id
         WHERE c.parking_guid = ? AND h.exit_time IS NULL
         ORDER BY h.id DESC LIMIT 1"
    );
    $stmt->bind_param('s', $guid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $entryTime = $row['entry_time'] ?? date('Y-m-d H:i:s');
}

echo json_encode([
    'success'    => $result['success'],
    'message'    => $result['message'] ?? '',
    'entry_time' => $entryTime,
    'entry_ts'   => $entryTime ? strtotime($entryTime) : time(),
]);
