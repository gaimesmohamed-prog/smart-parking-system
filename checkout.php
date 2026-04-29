<?php
require_once 'header.php';

$car_id = intval($_GET['car_id'] ?? 0);
$guid   = trim($_GET['guid'] ?? '');
$userId = intval($_SESSION['user_id'] ?? 0);

// ── جلب السيارة ──────────────────────────────────────────────────────────────
if ($car_id) {
    $stmt = $conn->prepare("SELECT * FROM cars WHERE car_id = ? AND status IN ('occupied','reserved')");
    $stmt->bind_param('i', $car_id);
} elseif ($guid !== '') {
    $stmt = $conn->prepare("SELECT * FROM cars WHERE parking_guid = ? AND status IN ('occupied','reserved')");
    $stmt->bind_param('s', $guid);
} else {
    die('بيانات غير صحيحة.');
}
$stmt->execute();
$car = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$car) {
    set_error_message('السيارة غير موجودة أو تم تسجيل خروجها مسبقاً.');
    $back = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'app_dashboard.php' : 'dashboard.php';
    redirect($back);
}

$slot_id   = $car['slot_id'];
$car_id    = $car['car_id'];
$guidVal   = $car['parking_guid'];

// ── جلب سجل الدخول ───────────────────────────────────────────────────────────
$hStmt = $conn->prepare("SELECT * FROM parking_history WHERE plate_number = ? AND slot_id = ? AND exit_time IS NULL ORDER BY id DESC LIMIT 1");
$hStmt->bind_param('ss', $car['plate_number'], $slot_id);
$hStmt->execute();
$history = $hStmt->get_result()->fetch_assoc();
$hStmt->close();

$ratePerHour  = parking_get_slot_price($conn, $slot_id);
$entryTime    = $history['entry_time'] ?? $car['created_at'] ?? null;
$elapsedSec   = $entryTime ? max(0, time() - strtotime($entryTime)) : 0;
$elapsedHours = max(1, ceil($elapsedSec / 3600)); // Charge at least 1 hour
$totalFee     = round($elapsedHours * $ratePerHour, 2);
$minFee       = max($totalFee, 0); 

// ── جلب رصيد المستخدم ────────────────────────────────────────────────────────
$bStmt = $conn->prepare("SELECT balance FROM users WHERE user_id = ?");
$bStmt->bind_param('i', $userId);
$bStmt->execute();
$bRow = $bStmt->get_result()->fetch_assoc();
$bStmt->close();
$balance = (float)($bRow['balance'] ?? 0);

$shortage = max(0, $totalFee - $balance);

// ── معالجة تسجيل الخروج ──────────────────────────────────────────────────────
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout_confirm'])) {
    $result = parking_complete_exit($conn, $guidVal, $userId);

    if ($result['success']) {
        $msg = "✅ " . $result['message'] . "\n" .
               "المدة: " . $result['hours'] . " ساعة/ساعات\n" .
               "المبلغ الإجمالي: " . number_format($result['cost'], 2) . " ج.م";
        
        set_success_message($msg);
        $final_redirect = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'app_dashboard.php' : 'dashboard.php';
        redirect($final_redirect);
    } else {
        $error = 'حدث خطأ: ' . $result['message'];
    }
}

// ── دالة مساعدة: تنسيق الوقت ─────────────────────────────────────────────────
function fmtDuration($sec) {
    $h = floor($sec / 3600);
    $m = floor(($sec % 3600) / 60);
    $s = $sec % 60;
    return sprintf('%02d:%02d:%02d', $h, $m, $s);
}
?>
<?php include 'navbar.php'; ?>

<div class="page">
    <div class="card">
        <div class="card-header">
            <div class="slot"><?php echo htmlspecialchars($slot_id); ?></div>
            <h2>🧾 فاتورة الخروج</h2>
        </div>

        <div class="summary">
            <div class="row">
                <span class="rl">رقم السيارة</span>
                <span class="rv"><?php echo htmlspecialchars($car['plate_number']); ?></span>
            </div>
            <div class="row">
                <span class="rl">وقت الدخول</span>
                <span class="rv"><?php echo $entryTime ? date('h:i A', strtotime($entryTime)) : '—'; ?></span>
            </div>
            <div class="row">
                <span class="rl">وقت الخروج</span>
                <span class="rv"><?php echo date('h:i A'); ?></span>
            </div>
            <div class="row">
                <span class="rl">المدة الإجمالية</span>
                <span class="rv"><?php echo fmtDuration($elapsedSec); ?></span>
            </div>
            <div class="row">
                <span class="rl">سعر الساعة</span>
                <span class="rv"><?php echo number_format($ratePerHour, 0); ?> ج.م</span>
            </div>
            <div class="row">
                <span class="rl">المبلغ الإجمالي</span>
                <span class="rv big"><?php echo number_format($totalFee, 2); ?> ج.م</span>
            </div>
        </div>

        <!-- رصيد المحفظة -->
        <div class="balance-box <?php echo $balance >= $totalFee ? 'bal-ok' : 'bal-err'; ?>">
            <div>
                <div class="bal-label">رصيد محفظتك</div>
                <div class="bal-val <?php echo $balance >= $totalFee ? 'c-green' : 'c-red'; ?>">
                    <?php echo number_format($balance, 2); ?> ج.م
                </div>
            </div>
            <i class="fa-solid fa-wallet" style="font-size:26px; color:<?php echo $balance >= $totalFee ? '#2e7d32' : '#c62828'; ?>"></i>
        </div>

        <?php if ($shortage > 0): ?>
            <div class="shortage-warn">
                ⚠️ رصيدك لا يكفي. سيُخصم ما هو متاح (<?php echo number_format($balance, 2); ?> ج.م)
                والمبلغ المتبقي (<?php echo number_format($shortage, 2); ?> ج.م) عليك.
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="footer">
            <form method="POST" style="margin:0">
                <button type="submit" name="checkout_confirm" class="btn btn-confirm">
                    🚪 تأكيد الخروج وخصم <?php echo number_format(min($balance, $totalFee), 2); ?> ج.م
                </button>
            </form>
            <a href="ticket.php?guid=<?php echo urlencode($guidVal); ?>" class="btn btn-back">
                ← رجوع للتكيت
            </a>
        </div>
    </div>
</div>

</body>
</html>
