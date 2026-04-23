<?php
require_once 'header.php';

$dashboardSlots = [];
$rows = ['A', 'B', 'C', 'D', 'E'];
foreach ($rows as $r) {
    for ($i = 1; $i <= 10; $i++) {
        $dashboardSlots[] = $r . $i;
    }
}
$dashboard = parking_get_dashboard_slots($conn, $dashboardSlots);
$slots = $dashboard['slots'];
$freeCount = $dashboard['free_count'];
$occupiedCount = $dashboard['occupied_count'];
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Parking - Live Dashboard</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#5D5FEF">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root { --primary: #5D5FEF; --success: #28a745; --danger: #dc3545; --warning: #ffc107; --info: #007bff; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f8f9fd; margin: 0; padding: 20px; }
        .header-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; margin-top: 60px; }
        .stat-box { font-size: 22px; font-weight: 700; color: #333; }
        .admin-tools { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 30px; }
        .admin-btn { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 18px; border-radius: 20px; text-decoration: none; color: white; font-weight: bold; transition: 0.3s; box-shadow: 0 8px 15px rgba(0,0,0,0.1); }
        .btn-camera { background: linear-gradient(135deg, var(--primary) 0%, #8E90FF 100%); }
        .btn-reports { background: linear-gradient(135deg, #00D2B1 0%, #00E6C3 100%); }
        .closest-btn { background-color: var(--primary); color: white; width: 100%; padding: 15px; border-radius: 12px; text-align: center; font-size: 20px; font-weight: bold; margin-bottom: 30px; display: block; text-decoration: none; }
        .grid-container { display: grid; grid-template-columns: repeat(10, 1fr); gap: 5px; }
        @media (max-width: 768px) {
            .grid-container { grid-template-columns: repeat(10, 1fr); gap: 3px; }
            .slot-card { padding: 5px; height: 50px; }
            .slot-id { font-size: 14px; flex-direction: column; gap: 2px; }
            .slot-status { font-size: 8px; display: none; }
        }
        .slot-card { background: white; border: 2px solid; border-radius: 8px; padding: 10px; color: #333; height: 70px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; }
        .slot-id { font-size: 16px; font-weight: 800; display: flex; align-items: center; gap: 5px; }
        .dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
        .slot-status { font-size: 10px; opacity: 0.8; margin-top: 4px; }
        .not-occupied { border-color: var(--success); background-color: #e8f5e9; } .not-occupied .dot { background: var(--success); }
        .occupied { border-color: var(--danger); background-color: #ffebee; color: #b71c1c; } .occupied .dot { background: var(--danger); }
        .warning { border-color: var(--warning); background-color: #fff8e1; color: #f57f17; } .warning .dot { background: var(--warning); }
        .closest { border-color: var(--info); } .closest .dot { background: var(--info); }
        .view-map { background: var(--primary); color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="header-nav">
    <div class="stat-box" id="free-count">Available: <?php echo $freeCount; ?></div>
    <div class="stat-box" id="used-count">Occupied: <?php echo $occupiedCount; ?></div>
</div>

<div class="admin-tools">
    <a href="add_car.php" class="admin-btn btn-create" style="background: linear-gradient(135deg, #FF9A9E 0%, #FECFEF 100%); color: #333;"><i class="fas fa-plus-circle"></i><span>Create Ticket</span></a>
    <a href="admin_scanner.php" class="admin-btn btn-camera"><i class="fas fa-video"></i><span>QR Entrance</span></a>
    <a href="reports.php" class="admin-btn btn-reports"><i class="fas fa-chart-pie"></i><span>Reports</span></a>
    <a href="parking_details.php" class="admin-btn btn-flow" style="background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);"><i class="fas fa-route"></i><span>Booking Flow</span></a>
</div>

<a href="map_view.php" class="closest-btn">Closest Slot: B3</a>

<h3 style="margin-bottom: 20px;">Dashboard (Real-time)</h3>

<div class="grid-container" id="slots-container">
    <?php foreach ($slots as $slot): ?>
        <div class="slot-card <?php echo $slot['status']; ?>">
            <div class="slot-id"><?php echo $slot['id']; ?><span class="dot"></span></div>
            <div class="slot-status"><?php echo $slot['text']; ?></div>
        </div>
    <?php endforeach; ?>
</div>

<div style="text-align: center; margin-top: 30px;">
    <a href="map_view.php" class="view-map">View Map</a>
</div>

<script>
    function updateDashboard() {
        fetch('get_slots_status.php')
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'success') return;

                document.getElementById('free-count').innerText = 'Available: ' + data.free_count;
                document.getElementById('used-count').innerText = 'Occupied: ' + data.occupied_count;

                let html = '';
                data.dashboard_slots.forEach(slot => {
                    html += `
                        <div class="slot-card ${slot.status}">
                            <div class="slot-id">${slot.id}<span class="dot"></span></div>
                            <div class="slot-status">${slot.text}</div>
                        </div>
                    `;
                });
                document.getElementById('slots-container').innerHTML = html;
            });
    }

    setInterval(updateDashboard, 3000);
</script>

</body>
</html>
