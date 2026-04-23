<?php
require_once 'header.php';

$counts = parking_get_counts($conn);
$totalSlots = $counts['total_slots'];
$occupiedSlots = $counts['occupied_slots'] + $counts['reserved_slots'];
$availableSlots = $counts['available_slots'];
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        .home-header { display: flex; align-items: center; gap: 10px; margin-top: 20px; margin-bottom: 20px; }
        .home-header h2 { font-size: 24px; margin: 0; font-family: 'Inter', sans-serif; font-weight: bold; }
        .parking-app-header { background-color: var(--input-bg); padding: 12px; border-radius: var(--radius-lg); text-align: center; font-weight: bold; font-size: 14px; color: #333; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 1px; }
        .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px; }
        .stat-card { background: white; padding: 16px; border-radius: 14px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .stat-card strong { display: block; font-size: 26px; margin-bottom: 6px; }
        .map-box-figma { width: 100%; height: 300px; background: url('https://i.ibb.co/L8N7p7v/map-placeholder.png') center/cover; border-radius: var(--radius-lg); margin-bottom: 30px; border: 1px solid #eee; }
        .book-now-container { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; padding-bottom: 40px; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="figma-container">
    <div class="home-header">
        <h2 class="serif-font">Admin Dashboard</h2>
        <i class="fa-solid fa-house"></i>
    </div>

    <div class="parking-app-header">SMART PARKING CONTROL</div>

    <div class="stats-row">
        <div class="stat-card"><strong><?php echo $totalSlots; ?></strong><span>Total Slots</span></div>
        <div class="stat-card"><strong><?php echo $occupiedSlots; ?></strong><span>Occupied / Reserved</span></div>
        <div class="stat-card"><strong><?php echo $availableSlots; ?></strong><span>Available</span></div>
    </div>

    <div class="map-box-figma"></div>

    <div class="book-now-container">
        <a href="add_car.php" class="figma-btn" style="text-decoration: none; display: block; text-align: center;">Create Ticket</a>
        <a href="admin_scanner.php" class="figma-btn" style="text-decoration: none; display: block; text-align: center;">Scan Entry / Exit</a>
        <a href="reports.php" class="figma-btn" style="text-decoration: none; display: block; text-align: center;">Reports</a>
        <a href="parking_details.php" class="figma-btn" style="text-decoration: none; display: block; text-align: center;">Booking Flow</a>
    </div>
</div>

</body>
</html>
