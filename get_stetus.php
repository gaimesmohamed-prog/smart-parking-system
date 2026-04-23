<?php
// ملف get_status.php
$conn = new mysqli("localhost", "root", "112004ٌة#", "parkingapp");

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed"]));
}

// جلب حالة الـ 20 مكان
$sql = "SELECT lot_id, is_available FROM parking_lot ORDER BY lot_id ASC LIMIT 20";
$result = $conn->query($sql);

$status_data = [];
while($row = $result->fetch_assoc()) {
    $status_data[] = [
        'id' => $row['lot_id'],
        'status' => $row['is_available']
    ];
}

// إرسال البيانات بتنسيق JSON
header('Content-Type: application/json');
echo json_encode($status_data);
$conn->close();
?>