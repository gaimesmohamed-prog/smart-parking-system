<?php 
require_once 'header.php'; 
require_once __DIR__ . '/config.php';

$slots_status = [];
$user_condition = "user_id = " . intval($_SESSION['user_id'] ?? 0);

$res = $conn->query("SELECT slot_id, status FROM cars WHERE status IN ('occupied', 'reserved')");
while($row = $res->fetch_assoc()){ $slots_status[strtoupper($row['slot_id'])] = $row['status']; }

$last_res = $conn->query("SELECT * FROM cars WHERE $user_condition ORDER BY car_id DESC LIMIT 1");
$last = $last_res->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo __('dashboard'); ?> - Smart Parking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="wrapper">
    <h3 style="text-align: center; color: #1a73e8;">🅿️ <?php echo __('dashboard'); ?></h3>

    <div class="nav-buttons">
        <a href="map_view.php" class="nav-btn" style="background: #2c3e50;"><i class="fas fa-map-marked-alt"></i> <?php echo __('book_now'); ?></a>
        <a href="history.php" class="nav-btn" style="background: #00acc1;"><?php echo __('history'); ?></a>
        <a href="reports.php" class="nav-btn" style="background: #ff9800;"><?php echo __('reports'); ?></a>
        <a href="#" onclick="window.print(); return false;" class="nav-btn" style="background: #28a745;"><?php echo __('print'); ?></a>
    </div>

    <?php if($last): ?>
    <div class="ticket-box">
        <div style="text-align: right;">
            <h4 style="margin:0; color:#1a73e8;"><?php echo __('ticket_details'); ?>: <?php echo $last['slot_id']; ?></h4>
            <p style="margin:5px 0;"><?php echo __('full_name'); ?>: <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?> | <?php echo __('license_plate'); ?>: <?php echo $last['plate_number']; ?></p>
        </div>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=<?php echo $last['parking_guid']; ?>" width="80">
    </div>
    <?php endif; ?>

    <div class="maps-grid">
        <div class="figma-map">
            <div style="padding:10px; font-weight:bold; background:white; display:flex; justify-content:space-between; align-items:center;">
                <span><?php echo __('view_map'); ?></span>
                <a href="map_view.php" style="background:#1a73e8; color:white; padding:5px 10px; border-radius:5px; text-decoration:none; font-size:12px;">🗺️ <?php echo __('interactive_map'); ?></a>
            </div>
            <iframe src="https://maps.google.com/maps?q=30.0444,31.2357&z=15&output=embed" width="100%" height="100%" style="border:0;"></iframe>
            <div class="map-pin">
                <span style="font-size:11px;">A1 <?php echo __('available'); ?></span>
                <a href="confirm_booking.php?slot=A1" style="display:block; background:#1a73e8; color:white; padding:3px; border-radius:4px; text-decoration:none; font-size:10px; margin-top:3px;"><?php echo __('book_now'); ?></a>
            </div>
        </div>

        <div class="grid-map">
            <div style="text-align:center; border:1px solid gold; padding:2px;"><?php echo __('qr_entrance'); ?></div>
            <div class="slot-grid" id="main-grid-map">
                <?php 
                $rows = ['A', 'B', 'C', 'D', 'E'];
                foreach($rows as $r) {
                    for($i=1; $i<=10; $i++) {
                        $s = $r . $i;
                        $class = 'free';
                        if (isset($slots_status[$s])) {
                            $class = $slots_status[$s] == 'occupied' ? 'busy' : 'reserved';
                        }
                        if ($class === 'free') {
                            echo "<a href='confirm_booking.php?slot=$s' class='slot $class' id='slot-$s' style='text-decoration:none; color:inherit; display:flex; align-items:center; justify-content:center;'>$s</a>";
                        } else {
                            echo "<div class='slot $class' id='slot-$s'>$s</div>";
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <div style="background:white; padding:15px; border-radius:15px; border:1px solid #eee;">
        <h4><?php echo __('history'); ?></h4>
        <table>
            <thead><tr><th><?php echo __('license_plate'); ?></th><th><?php echo __('slot_id'); ?></th><th><?php echo __('status'); ?></th><th>إجراء</th></tr></thead>
            <tbody>
                <?php 
                $table_res = $conn->query("SELECT * FROM cars WHERE $user_condition ORDER BY car_id DESC LIMIT 4");
                while($row = $table_res->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo $row['plate_number']; ?></strong></td>
                    <td style="color:#1a73e8; font-weight:bold;"><?php echo $row['slot_id']; ?></td>
                    <td>
                        <?php if ($row['status'] === 'occupied' || $row['status'] === 'reserved'): ?>
                            <span style="color:#e74c3c;">محجوز</span>
                        <?php else: ?>
                            <span style="color:#2ecc71;">مكتمل</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['status'] === 'occupied' || $row['status'] === 'reserved'): ?>
                            <a href="checkout.php?car_id=<?php echo $row['car_id']; ?>" style="background: #e74c3c; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight:bold;">دفع وخروج</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="assets/js/dashboard.js"></script>

</body>
</html>
