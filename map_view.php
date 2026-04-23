<?php
require_once 'header.php'; 

// جلب الأماكن المحجوزة
$occupied_slots = [];
$result = $conn->query("SELECT DISTINCT slot_id FROM cars WHERE status = 'occupied'");
if ($result) {
    while($row = $result->fetch_assoc()) {
        $occupied_slots[] = strtoupper($row['slot_id']);
    }
}

// تصميم إحداثيات افتراضية لـ 8 مواقف
$map_slots = [
    ['id' => 'A1', 'lat' => 30.0450, 'lng' => 31.2355],
    ['id' => 'A2', 'lat' => 30.0450, 'lng' => 31.2360],
    ['id' => 'A3', 'lat' => 30.0450, 'lng' => 31.2365],
    ['id' => 'B1', 'lat' => 30.0445, 'lng' => 31.2355],
    ['id' => 'B2', 'lat' => 30.0445, 'lng' => 31.2360],
    ['id' => 'B3', 'lat' => 30.0445, 'lng' => 31.2365],
    ['id' => 'B4', 'lat' => 30.0440, 'lng' => 31.2355]
];

// دمج الحالة (Status)
foreach ($map_slots as &$slot) {
    if (in_array($slot['id'], $occupied_slots)) {
        $slot['status'] = 'occupied';
    } else {
        $slot['status'] = 'free';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>خريطة المواقف - Parking Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { margin: 0; padding: 0; }
        #map { height: 100vh; width: 100vw; } 
        
        .back-btn {
            position: absolute; top: 15px; right: 15px; z-index: 1000;
            background: #5D5FEF; color: white; padding: 10px 20px;
            border-radius: 25px; text-decoration: none; font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        .marker-free { background-color: #28a745; border: 3px solid white; color: white; border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.5); }
        .marker-occupied { background-color: #dc3545; border: 3px solid white; color: white; border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.5); }
    </style>

    <!-- Global Nav Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'navbar.php'; ?>


<a href="dashboard.php" class="back-btn">العودة للرئيسية</a>
<div id="map"></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    var map = L.map('map').setView([30.0447, 31.2360], 18); // تقريب زووم 18

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    var slotsData = <?php echo json_encode($map_slots); ?>;

    slotsData.forEach(function(slot) {
        var markerClass = slot.status === 'occupied' ? 'marker-occupied' : 'marker-free';
        var popupText = slot.status === 'occupied' ? 'محجوز (اضغط للتفاصيل)' : 'متاح (اضغط للحجز)';
        
        var customIcon = L.divIcon({
            className: 'custom-icon',
            html: `<div class="${markerClass}">${slot.id}</div>`,
            iconSize: [35, 35]
        });

        var marker = L.marker([slot.lat, slot.lng], {icon: customIcon}).addTo(map);
        
        marker.on('click', function() {
            window.location.href = "confirm_booking.php?slot=" + slot.id;
        });
    });
</script>

</body>
</html>