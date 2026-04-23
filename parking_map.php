<?php require_once 'header.php'; ?>
<?php
require_once __DIR__ . '/config.php';

// جلب كل الأماكن المحجوزة حالياً من قاعدة البيانات
$sql = "SELECT cars.slot_id, COALESCE(users.full_name, cars.plate_number) AS user_name FROM cars LEFT JOIN users ON cars.user_id = users.user_id";
$result = $conn->query($sql);
$occupied_slots = [];
while($row = $result->fetch_assoc()) {
    $occupied_slots[$row['slot_id']] = $row['user_name'];
}

// تعريف أماكن الجراج (GUIDs)
$all_slots = ['M1', 'M2', 'M3', 'M4', 'M5', 'M6', 'M7', 'M8']; 
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>خريطة المواقف التفاعلية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/parking_map.css">

    <!-- Global Nav Styles -->
    <style>
        .slot.available {
            cursor: pointer;
            text-decoration: none !important;
            color: inherit !important;
            display: block;
        }
        .slot.available:hover {
            opacity: 0.8;
            transform: scale(1.05);
            transition: 0.2s;
        }
    </style>
</head>
<body class="bg-light p-4">
<?php include 'navbar.php'; ?>

    <div class="container text-center">
        <h2 class="mb-4">🗺️ خريطة توزيع السيارات (GUID Real-time)</h2>
        <div class="row g-3 justify-content-center">
            <?php foreach($all_slots as $slot_guid): ?>
                <div class="col-auto">
                    <?php if(isset($occupied_slots[$slot_guid])): ?>
                        <div class="slot occupied">
                            <span>🚗 <?php echo $slot_guid; ?></span>
                            <small><?php echo htmlspecialchars($occupied_slots[$slot_guid]); ?></small>
                        </div>
                    <?php else: ?>
                        <a href="confirm_booking.php?slot=<?php echo urlencode($slot_guid); ?>" class="slot available">
                            <span>✅ <?php echo $slot_guid; ?></span>
                            <small>متاح (اضغط للحجز)</small>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-5">
            <a href="dashboard.php" class="btn btn-primary">العودة للوحة التحكم</a>
        </div>
    </div>
</body>
</html>