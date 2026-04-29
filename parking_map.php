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
<?php include 'navbar.php'; ?>

<div class="wrapper animate-fade-in" style="margin-top: 40px;">
    <div style="text-align: center; margin-bottom: 40px;">
        <h2 style="color: var(--text-main); font-weight: 800; font-family: 'Righteous', cursive;">
            <i class="fas fa-map-marked-alt" style="color: var(--accent-color);"></i> <?php echo __('view_map'); ?>
        </h2>
        <p style="color: var(--text-muted);"><?php echo __('qr_entrance'); ?> Real-time Status</p>
    </div>

    <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
        <?php foreach($all_slots as $slot_guid): ?>
            <div style="flex: 0 0 auto;">
                <?php if(isset($occupied_slots[$slot_guid])): ?>
                    <div class="glass-card" style="width: 150px; padding: 20px; text-align: center; border-bottom: 5px solid #ff4d4d; opacity: 0.8;">
                        <i class="fas fa-car" style="font-size: 30px; color: #ff4d4d; margin-bottom: 10px;"></i>
                        <div style="font-weight: 800; font-size: 18px; color: var(--text-main);"><?php echo $slot_guid; ?></div>
                        <div style="font-size: 11px; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo htmlspecialchars($occupied_slots[$slot_guid]); ?></div>
                    </div>
                <?php else: ?>
                    <a href="confirm_booking.php?slot=<?php echo urlencode($slot_guid); ?>" class="glass-card" style="display: block; width: 150px; padding: 20px; text-align: center; border-bottom: 5px solid #00b894; text-decoration: none; transition: transform 0.3s;">
                        <i class="fas fa-check-circle" style="font-size: 30px; color: #00b894; margin-bottom: 10px;"></i>
                        <div style="font-weight: 800; font-size: 18px; color: var(--text-main);"><?php echo $slot_guid; ?></div>
                        <div style="font-size: 11px; color: #00b894; font-weight: 700;"><?php echo __('available'); ?></div>
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="text-align: center; margin-top: 50px;">
        <a href="dashboard.php" class="mbtn mbtn-close" style="display: inline-flex; width: auto; padding: 12px 30px; text-decoration: none;">
            <i class="fas fa-arrow-left"></i> <?php echo __('back'); ?>
        </a>
    </div>
</div>

</body>
</html>