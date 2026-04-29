<?php
require_once 'header.php';

// ── معالجة إلغاء الحجز ────────────────────────────────────────────────────────
if (isset($_POST['cancel_booking'])) {
    $car_id = intval($_POST['car_id']);
    $user_id = intval($_SESSION['user_id'] ?? 0);
    
    $result = parking_cancel_reservation($conn, $car_id, $user_id);
    
    if ($result['success']) {
        set_success_message("✅ " . $result['message']);
    } else {
        set_error_message("❌ " . $result['message']);
    }
    header('Location: dashboard.php');
    exit;
}

$slots_status = [];
$user_condition = "user_id = " . intval($_SESSION['user_id'] ?? 0);

$res = $conn->query("SELECT slot_id, status FROM cars WHERE status IN ('occupied', 'reserved')");
while($row = $res->fetch_assoc()){ $slots_status[strtoupper($row['slot_id'])] = $row['status']; }

// جلب آخر حجز نشط للمستخدم لعرض الـ QR
$last_res = $conn->query("SELECT * FROM cars WHERE user_id = " . intval($_SESSION['user_id'] ?? 0) . " AND status IN ('reserved', 'occupied') ORDER BY car_id DESC LIMIT 1");
$last = ($last_res) ? $last_res->fetch_assoc() : null;
?>
<?php include 'navbar.php'; ?>

<div class="wrapper animate-fade-in">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin:0; color: var(--text-main);">
            <i class="fas fa-th-large"></i> <?php echo __('dashboard'); ?>
        </h2>
    </div>

    <div class="nav-buttons">
        <a href="map_view.php" class="nav-btn">
            <i class="fas fa-plus-circle"></i>
            <span>حجز مكان جديد</span>
        </a>
        <a href="history.php" class="nav-btn" style="background: linear-gradient(135deg, #00b894 0%, #55efc4 100%);">
            <i class="fas fa-history"></i>
            <span>سجل الحجوزات</span>
        </a>
        <a href="wallet.php" class="nav-btn" style="background: linear-gradient(135deg, #fdcb6e 0%, #ffeaa7 100%);">
            <i class="fas fa-wallet"></i>
            <span>شحن المحفظة</span>
        </a>
        <a href="reports.php" class="nav-btn" style="background: linear-gradient(135deg, #fab1a0 0%, #ff7675 100%);">
            <i class="fas fa-file-invoice"></i>
            <span>التقارير</span>
        </a>
    </div>

    <?php if($last): ?>
    <div class="ticket-box animate-fade-in" style="animation-delay: 0.2s;">
        <div style="flex: 1;">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                <div style="width: 50px; height: 50px; background: var(--accent-color); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div>
                    <h4 style="margin:0; font-size: 18px; color: var(--text-main);"><?php echo __('ticket_details'); ?>: <?php echo $last['slot_id']; ?></h4>
                    <span class="badge" style="background: #e1f5fe; color: #01579b; font-size: 12px; padding: 4px 10px; border-radius: 20px;"><?php echo $last['status'] === 'reserved' ? 'محجوز' : 'مشغول'; ?></span>
                </div>
            </div>
            <div style="font-size: 14px; color: #636e72;">
                <p style="margin:5px 0;"><i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['user'] ?? 'User'); ?></p>
                <p style="margin:5px 0;"><i class="fas fa-id-card"></i> <?php echo $last['plate_number']; ?></p>
            </div>
        </div>
        <div style="text-align: center; border-left: 1px dashed #ddd; padding-right: 25px;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php echo $last['parking_guid']; ?>" width="100" style="border-radius: 10px;">
            <p style="margin-top: 10px; font-size: 11px; color: #b2bec3;"><?php echo __('qr_entrance'); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <div class="maps-grid">
        <div class="figma-map animate-fade-in" style="animation-delay: 0.3s;">
            <div style="position: absolute; top: 20px; right: 20px; z-index: 10; display: flex; gap: 10px;">
                <a href="map_view.php" class="glass-card" style="background: white; padding: 8px 15px; text-decoration: none; font-size: 13px; font-weight: 600; color: var(--accent-color);">
                    <i class="fas fa-expand"></i> خريطة تفاعلية
                </a>
            </div>
            <iframe src="https://maps.google.com/maps?q=30.0444,31.2357&z=15&output=embed" width="100%" height="100%" style="border:0;"></iframe>
            <div class="map-pin" style="border-radius: 15px; padding: 12px; border: none; box-shadow: 0 10px 20px rgba(0,0,0,0.2);">
                <div style="font-size: 14px; font-weight: 700; color: #2d3436; margin-bottom: 5px;">M1 - متاح</div>
                <a href="confirm_booking.php?slot=M1" style="display:inline-block; background: var(--accent-color); color:white; padding: 6px 15px; border-radius: 8px; text-decoration:none; font-size: 12px; font-weight: 600;">احجز الآن</a>
            </div>
        </div>

        <div class="grid-map animate-fade-in" style="animation-delay: 0.4s;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h5 style="margin:0;"><i class="fas fa-th"></i> حالة المواقف الحالية</h5>
                <span style="font-size: 11px; opacity: 0.8;">تحديث تلقائي كل 15 ثانية</span>
            </div>
            <div class="slot-grid" id="main-grid-map">
                <?php 
                $rows = ['A', 'B', 'C', 'D'];
                foreach($rows as $r) {
                    for($i=1; $i<=10; $i++) {
                        $s = $r . $i;
                        $class = 'free';
                        if (isset($slots_status[$s])) {
                            $class = $slots_status[$s] == 'occupied' ? 'busy' : 'reserved';
                        }
                        if ($class === 'free') {
                            echo "<a href='confirm_booking.php?slot=$s' class='slot $class' id='slot-$s' style='text-decoration:none;'>$s</a>";
                        } else {
                            echo "<div class='slot $class' id='slot-$s'>$s</div>";
                        }
                    }
                }
                ?>
            </div>
            <div style="display:flex; gap:15px; margin-top:20px; font-size:12px; justify-content:center;">
                <span><i class="fas fa-circle" style="color: #00d2d3;"></i> متاح</span>
                <span><i class="fas fa-circle" style="color: #ff9f43;"></i> محجوز</span>
                <span><i class="fas fa-circle" style="color: #ff4d4d;"></i> مشغول</span>
            </div>
        </div>
    </div>

    <div class="history-container animate-fade-in" style="animation-delay: 0.5s;">
        <h4 style="margin-bottom: 20px;"><i class="fas fa-list-ul"></i> الحجوزات الأخيرة</h4>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>اللوحة</th>
                        <th>المكان</th>
                        <th>الحالة</th>
                        <th>الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $table_res = $conn->query("SELECT * FROM cars WHERE user_id = " . intval($_SESSION['user_id'] ?? 0) . " ORDER BY car_id DESC LIMIT 4");
                    while($row = $table_res->fetch_assoc()): ?>
                    <tr>
                        <td style="font-weight: 700; color: #2d3436;"><?php echo $row['plate_number']; ?></td>
                        <td><span class="badge" style="background: #e1f5fe; color: #01579b; font-weight: 700;"><?php echo $row['slot_id']; ?></span></td>
                        <td>
                            <?php if ($row['status'] === 'occupied'): ?>
                                <span style="color:#ff4d4d; font-weight: 600;"><i class="fas fa-car"></i> داخل الموقف</span>
                            <?php elseif ($row['status'] === 'reserved'): ?>
                                <span style="color:#ff9f43; font-weight: 600;"><i class="fas fa-clock"></i> بانتظار الدخول</span>
                            <?php else: ?>
                                <span style="color:#00d2d3; font-weight: 600;"><i class="fas fa-check-circle"></i> مكتمل</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['status'] === 'reserved'): ?>
                                <div style="display:flex; gap:8px; justify-content: center;">
                                    <a href="ticket.php?guid=<?php echo $row['parking_guid']; ?>" class="badge" style="background: var(--accent-color); color: white; text-decoration: none;">دخول</a>
                                    <form method="POST" style="margin:0">
                                        <input type="hidden" name="car_id" value="<?php echo $row['car_id']; ?>">
                                        <button type="submit" name="cancel_booking" style="background: #dfe6e9; color: #636e72; padding: 6px 12px; border-radius: 8px; border:none; cursor:pointer; font-size: 11px; font-weight:700;" onclick="return confirm('إلغاء الحجز؟')">إلغاء</button>
                                    </form>
                                </div>
                            <?php elseif ($row['status'] === 'occupied'): ?>
                                <a href="ticket.php?guid=<?php echo $row['parking_guid']; ?>" class="badge" style="background: #ff4d4d; color: white; text-decoration: none;">خروج</a>
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
</div>

<script src="assets/js/dashboard.js"></script>
<script>
// تحديث ألوان المواقف كل 5 ثوانٍ
function refreshSlots() {
    fetch('get_slots_status.php')
        .then(r => r.json())
        .then(data => {
            if (!data || !data.slots) return;
            data.slots.forEach(slot => {
                const el = document.getElementById('slot-' + slot.id);
                if (el) {
                    const statusClass = slot.status === 'occupied' ? 'busy' : slot.status === 'reserved' ? 'reserved' : 'free';
                    el.className = 'slot ' + statusClass;
                }
            });
        })
        .catch(() => {});
}
setInterval(refreshSlots, 5000);
</script>

</body>
</html>
