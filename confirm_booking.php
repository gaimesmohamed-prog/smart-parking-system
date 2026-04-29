<?php
require_once 'header.php';

// ── جلب بيانات المكان ────────────────────────────────────────────────────────
$slotId      = parking_normalize_slot($_GET['slot'] ?? ($_SESSION['pending_booking']['slot_id'] ?? parking_find_available_slot($conn) ?? 'A1'));
$hourlyRate  = parking_get_slot_price($conn, $slotId);
$userId      = intval($_SESSION['user_id'] ?? 0);

// ── جلب بيانات السيارة المسجلة للمستخدم ──────────────────────────────────────────
$carStmt = $conn->prepare("SELECT plate_number, car_model FROM cars WHERE user_id = ? LIMIT 1");
$carStmt->bind_param('i', $userId);
$carStmt->execute();
$carRow = $carStmt->get_result()->fetch_assoc();
$carStmt->close();

$registeredPlate = $carRow['plate_number'] ?? '';
$carModel = $carRow['car_model'] ?? 'سيدان';

// ── التحقق من حالة المكان ────────────────────────────────────────────────────
$slotMap       = parking_get_slot_status_map($conn);
$currentStatus = $slotMap[$slotId] ?? 'available';

if ($currentStatus === 'occupied' || $currentStatus === 'reserved') {
    set_error_message("المكان {$slotId} محجوز حالياً. اختر مكاناً آخر.");
    $back = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'app_dashboard.php' : 'dashboard.php';
    redirect($back);
}

// ── جلب رصيد المحفظة ─────────────────────────────────────────────────────────
$stmt = $conn->prepare("SELECT balance FROM users WHERE user_id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$balRow  = $stmt->get_result()->fetch_assoc();
$stmt->close();
$balance = (float)($balRow['balance'] ?? 0);

// ── حفظ بيانات الحجز في الـ Session ──────────────────────────────────────────
$_SESSION['pending_booking'] = [
    'slot_id'      => $slotId,
    'parking_name' => 'Cairo Mall Parking',
    'location'     => '6th October, Cairo',
    'hourly_rate'  => $hourlyRate,
];
$booking = $_SESSION['pending_booking'];
$error = '';

// ── معالجة نموذج الحجز ───────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking'])) {
    if ($registeredPlate === '') {
        $error = 'لا توجد سيارة مسجلة بحسابك. يرجى إضافة سيارة أولاً.';
    } elseif ($balance <= 0) {
        $error = 'رصيدك غير كافٍ للحجز. يرجى شحن المحفظة أولاً.';
    } else {
        $reservation = parking_create_reservation($conn, [
            'plate_number'   => $registeredPlate,
            'slot_id'        => $slotId,
            'car_type'       => $_POST['car_type'] ?? 'sedan',
            'reserved_hours' => intval($_POST['reserved_hours'] ?? 1),
            'user_id'        => $userId
        ]);

        if ($reservation['success']) {
            redirect("success.php?id=" . $reservation['car_id']);
        } else {
            $error = $reservation['message'];
        }
    }
}
?>

<?php include 'navbar.php'; ?>

<div class="wrapper animate-fade-in">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="margin:0; color: var(--text-main);">
            <i class="fas fa-calendar-check" style="color: var(--accent-color);"></i> تأكيد الحجز
        </h2>
    </div>

    <div class="glass-card" style="padding: 30px; margin-bottom: 30px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="display: inline-block; background: var(--accent-color); color: white; padding: 15px 30px; border-radius: 20px; font-weight: 800; font-size: 24px; box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);">
                <?php echo htmlspecialchars($slotId); ?>
            </div>
            <p style="color: var(--text-muted); margin-top:15px; font-weight: 600;"><?php echo htmlspecialchars($booking['parking_name']); ?></p>
        </div>

        <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 16px; padding: 20px;">
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--glass-border);">
                <span style="color: var(--text-muted);">سعر الساعة</span>
                <strong style="color: var(--text-main);"><?php echo number_format($hourlyRate, 0); ?> ج.م</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 10px 0;">
                <span style="color: var(--text-muted);">الموقع</span>
                <strong style="color: var(--text-main);"><?php echo htmlspecialchars($booking['location']); ?></strong>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
        <div class="stat-card" style="margin-bottom: 25px; border-left: 5px solid <?php echo $balance > 0 ? '#10b981' : '#ef4444'; ?>;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div class="stat-label">رصيد المحفظة الحالي</div>
                    <div class="stat-value" style="color: <?php echo $balance > 0 ? '#10b981' : '#ef4444'; ?>;"><?php echo number_format($balance, 2); ?> ج.م</div>
                </div>
                <i class="fas fa-wallet" style="font-size: 30px; opacity: 0.2;"></i>
            </div>
        </div>

        <?php if ($error !== ''): ?>
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
                <i class="fas fa-exclamation-circle"></i> <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($balance <= 0): ?>
            <div style="text-align: center; padding: 20px;">
                <p style="color: var(--text-muted);">عذراً، يجب شحن رصيدك للمتابعة</p>
                <a href="wallet.php" class="mbtn mbtn-enter" style="text-decoration: none;">شحن المحفظة</a>
            </div>
        <?php elseif ($registeredPlate === ''): ?>
            <div style="text-align: center; padding: 20px;">
                <p style="color: var(--text-muted);">يرجى تسجيل بيانات سيارتك أولاً</p>
                <a href="profile.php" class="mbtn mbtn-enter" style="text-decoration: none;">إضافة سيارة</a>
            </div>
        <?php else: ?>
            <form method="POST" class="glass-card" style="padding: 25px;">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; color: var(--text-muted); margin-bottom: 10px; font-size: 13px; font-weight: 700;">نوع المركبة</label>
                    <select name="car_type" class="search-input" style="width: 100%;">
                        <option value="sedan" <?php echo $carModel == 'سيدان' ? 'selected' : ''; ?>>سيارة صغيرة (سيدان)</option>
                        <option value="suv">سيارة كبيرة (SUV)</option>
                        <option value="truck">شاحنة</option>
                    </select>
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; color: var(--text-muted); margin-bottom: 10px; font-size: 13px; font-weight: 700;">مدة الحجز المتوقعة</label>
                    <select name="reserved_hours" class="search-input" style="width: 100%;">
                        <option value="1">1 ساعة</option>
                        <option value="2" selected>2 ساعة</option>
                        <option value="3">3 ساعات</option>
                        <option value="5">5 ساعات</option>
                    </select>
                </div>

                <button type="submit" name="confirm_booking" class="mbtn mbtn-enter" style="border: none;">
                    ✅ تأكيد الحجز للمركبة (<?php echo htmlspecialchars($registeredPlate); ?>)
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
