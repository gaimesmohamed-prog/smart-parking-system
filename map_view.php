<?php
require_once 'header.php'; 

// جلب الأماكن المحجوزة والمتاحة
$occupied_slots  = [];
$reserved_slots  = [];
$result = $conn->query("SELECT DISTINCT slot_id, status FROM cars WHERE status IN ('occupied','reserved')");
if ($result) {
    while($row = $result->fetch_assoc()) {
        $id = strtoupper($row['slot_id']);
        if ($row['status'] === 'occupied') $occupied_slots[] = $id;
        else                              $reserved_slots[]  = $id;
    }
}

$map_slots = [
    ['id' => 'A1', 'lat' => 30.0450, 'lng' => 31.2355],
    ['id' => 'A2', 'lat' => 30.0450, 'lng' => 31.2360],
    ['id' => 'A3', 'lat' => 30.0450, 'lng' => 31.2365],
    ['id' => 'B1', 'lat' => 30.0445, 'lng' => 31.2355],
    ['id' => 'B2', 'lat' => 30.0445, 'lng' => 31.2360],
    ['id' => 'B3', 'lat' => 30.0445, 'lng' => 31.2365],
    ['id' => 'B4', 'lat' => 30.0440, 'lng' => 31.2355]
];

foreach ($map_slots as &$slot) {
    if (in_array($slot['id'], $occupied_slots)) {
        $slot['status'] = 'occupied';
    } elseif (in_array($slot['id'], $reserved_slots)) {
        $slot['status'] = 'reserved';
    } else {
        $slot['status'] = 'free';
    }
}
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<?php include 'navbar.php'; ?>

<div class="wrapper animate-fade-in">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="margin:0; color: var(--text-main);">
            <i class="fas fa-map-marked-alt"></i> خريطة المواقف الحية
        </h2>
        <?php $back_link = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'app_dashboard.php' : 'dashboard.php'; ?>
        <a href="<?php echo $back_link; ?>" class="icon-btn" title="رجوع">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>
    
    <div class="glass-card" style="height: 70vh; overflow: hidden; position: relative; border-radius: 24px;">
        <div id="map" style="height: 100%; width: 100%;"></div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([30.0447, 31.2360], 18);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    var slotsData = <?php echo json_encode($map_slots); ?>;
    slotsData.forEach(function(slot) {
        var color = slot.status === 'occupied' ? '#ff4d4d' : (slot.status === 'reserved' ? '#ff9f43' : '#00d2d3');
        var popupText = slot.status === 'occupied' ? 'مشغول' : (slot.status === 'reserved' ? 'محجوز' : 'متاح – اضغط للحجز');

        var customIcon = L.divIcon({
            className: 'custom-icon',
            html: `<div style="background:${color}; border: 3px solid white; color: white; border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-weight: bold; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">${slot.id}</div>`,
            iconSize: [35, 35]
        });

        var marker = L.marker([slot.lat, slot.lng], {icon: customIcon}).addTo(map);
        marker.bindPopup('<b>' + slot.id + '</b><br>' + popupText);

        if (slot.status === 'free') {
            marker.on('click', function() {
                window.location.href = "confirm_booking.php?slot=" + slot.id;
            });
        }
    });
</script>
</body>
</html>

</body>
</html>