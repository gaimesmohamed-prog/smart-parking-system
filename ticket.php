<?php
require_once 'header.php';

$guid = trim($_GET['guid'] ?? '');
if ($guid === '') {
    set_error_message('رقم التكيت غير موجود.');
    redirect('dashboard.php');
}

// تسجيل دخول السيارة → تحويل reserved → occupied + بدء التايمر
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_entry'])) {
    parking_mark_entry($conn, $guid);
    // الأدمن يرجع للداشبورد، المستخدم العادي يفضل على التكيت
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header('Location: app_dashboard.php');
    } else {
        header('Location: ticket.php?guid=' . urlencode($guid));
    }
    exit;
}

$ticket = parking_get_ticket_data($conn, $guid);
if (!$ticket) {
    http_response_code(404);
    die('التكيت غير موجود.');
}

$car        = $ticket['car'];
$history    = $ticket['history'];
$slotId     = $car['slot_id'];
$plate      = $car['plate_number'];
$status     = $car['status'];          // reserved | occupied | completed
$entryTime  = $history['entry_time'] ?? null;
$dashboard  = 'dashboard.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تكيت الركن - <?php echo htmlspecialchars($slotId); ?></title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(160deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
            min-height: 100vh;
        }

        .ticket-page { display: flex; flex-direction: column; align-items: center;
                       padding: 80px 16px 40px; min-height: 100vh; }

        /* ── بطاقة التكيت ── */
        .ticket-card {
            background: #fff; border-radius: 24px;
            width: 100%; max-width: 400px;
            box-shadow: 0 20px 60px rgba(0,0,0,.4);
            overflow: hidden;
        }

        .ticket-header {
            padding: 28px 24px 20px;
            text-align: center;
            color: #fff;
        }
        .ticket-header.reserved  { background: linear-gradient(135deg,#f39c12,#e67e22); }
        .ticket-header.occupied  { background: linear-gradient(135deg,#e74c3c,#c0392b); }
        .ticket-header.completed { background: linear-gradient(135deg,#27ae60,#1e8449); }

        .ticket-header .status-emoji { font-size: 40px; }
        .ticket-header h2 { margin: 8px 0 4px; font-size: 22px; }
        .ticket-header .slot-num { font-size: 56px; font-weight: 900; letter-spacing: 2px; }

        /* ── timer ── */
        .timer-wrap {
            background: #1a1a2e; color: #fff;
            text-align: center; padding: 20px;
        }
        .timer-label { font-size: 12px; color: #aaa; margin-bottom: 6px; letter-spacing: 1px; }
        .timer-display {
            font-size: 44px; font-weight: 900; letter-spacing: 4px;
            font-variant-numeric: tabular-nums;
        }
        .timer-display.running { color: #e74c3c; }
        .timer-display.waiting { color: #f39c12; }
        .cost-display { font-size: 20px; font-weight: 700; margin-top: 8px; color: #f1c40f; }

        /* ── تفاصيل ── */
        .ticket-body { padding: 20px 24px; }
        .detail-row { display: flex; justify-content: space-between;
                      padding: 10px 0; border-bottom: 1px solid #f0f0f0;
                      font-size: 14px; }
        .detail-row:last-child { border-bottom: none; }
        .dl { color: #777; }
        .dv { font-weight: 700; color: #222; }

        /* ── QR ── */
        .qr-wrap { text-align: center; padding: 16px 24px 0; }
        .qr-wrap img { border: 3px solid #eee; border-radius: 12px; padding: 8px; }
        .qr-hint { font-size: 12px; color: #aaa; margin-top: 8px; }

        /* ── أزرار ── */
        .ticket-footer { padding: 20px 24px 28px; display: flex; flex-direction: column; gap: 10px; }
        .btn { display: block; padding: 14px; border-radius: 12px; font-size: 16px;
               font-weight: 700; text-align: center; text-decoration: none; cursor: pointer; border: none; }
        .btn-dash   { background: #5D5FEF; color: #fff; }
        .btn-out    { background: #e74c3c; color: #fff; }
        .btn-enter  { background: linear-gradient(135deg,#27ae60,#1e8449); color: #fff;
                      width: 100%; font-size: 17px; animation: pulse 1.8s infinite; }
        @keyframes pulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(39,174,96,.5); }
            50%      { box-shadow: 0 0 0 10px rgba(39,174,96,0); }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="ticket-page">
    <div class="ticket-card">

        <!-- Header يتغير لون حسب الحالة -->
        <div class="ticket-header <?php echo $status; ?>" id="ticketHeader">
            <div class="status-emoji" id="statusEmoji">
                <?php
                if ($status === 'occupied')  echo '🔴';
                elseif ($status === 'reserved') echo '🟡';
                else echo '✅';
                ?>
            </div>
            <h2 id="statusText">
                <?php
                if ($status === 'occupied')  echo 'مكان مشغول';
                elseif ($status === 'reserved') echo 'محجوز — في انتظار الدخول';
                else echo 'مكتمل';
                ?>
            </h2>
            <div class="slot-num"><?php echo htmlspecialchars($slotId); ?></div>
        </div>

        <!-- Timer -->
        <div class="timer-wrap">
            <div class="timer-label">⏱ وقت الركن المنقضي</div>
            <div class="timer-display <?php echo $status === 'occupied' ? 'running' : 'waiting'; ?>"
                 id="timerDisplay">
                <?php echo $status === 'occupied' ? '00:00:00' : '⏳ انتظار'; ?>
            </div>
            <div class="cost-display" id="costDisplay">
                <?php echo $status === 'occupied' ? '0.00 ج.م' : 'سيبدأ الحساب عند الدخول'; ?>
            </div>
        </div>

        <!-- تفاصيل -->
        <div class="ticket-body">
            <div class="detail-row">
                <span class="dl">رقم اللوحة</span>
                <span class="dv"><?php echo htmlspecialchars($plate); ?></span>
            </div>
            <div class="detail-row">
                <span class="dl">المكان</span>
                <span class="dv"><?php echo htmlspecialchars($slotId); ?></span>
            </div>
            <div class="detail-row">
                <span class="dl">وقت الحجز</span>
                <span class="dv"><?php echo $entryTime ? date('h:i A', strtotime($entryTime)) : '—'; ?></span>
            </div>
            <div class="detail-row">
                <span class="dl">سعر الساعة</span>
                <span class="dv">10 ج.م</span>
            </div>
        </div>

        <!-- QR -->
        <div class="qr-wrap">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=<?php echo urlencode($guid); ?>"
                 width="160" height="160" alt="QR Code">
            <p class="qr-hint">امسح الباركود عند البوابة</p>
        </div>

        <!-- أزرار -->
        <div class="ticket-footer">

            <?php if ($status === 'reserved'): ?>
                <!-- زرار دخول السيارة — يظهر بس لما المكان لسه أصفر -->
                <form method="POST" style="margin:0">
                    <button type="submit" name="mark_entry" class="btn btn-enter"
                            onclick="return confirm('هتدخل السيارة دلوقتي؟ التايمر هيبدأ من دلوقتي!')">
                        🚗 دخول السيارة &mdash; بدء التايمر
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($status === 'occupied'): ?>
                <a href="checkout.php?car_id=<?php echo (int)($car['car_id'] ?? 0); ?>&guid=<?php echo urlencode($guid); ?>"
                   class="btn btn-out">
                    🚪 تسجيل الخروج وحساب الفاتورة
                </a>
            <?php endif; ?>

            <a href="<?php echo $dashboard; ?>" class="btn btn-dash">🏠 لوحة التحكم</a>
        </div>
    </div>
</div>

<script>
// ── بيانات أولية ─────────────────────────────────────────────────────────────
const GUID          = <?php echo json_encode($guid); ?>;
const RATE_PER_SEC  = 10 / 3600;   // 10 ج.م / ساعة → لكل ثانية
const ENTRY_TS      = <?php echo $entryTime ? strtotime($entryTime) : 'null'; ?>;

let currentStatus   = <?php echo json_encode($status); ?>;
let elapsedBase     = 0;
let timerInterval   = null;

// ── مساعد: تحويل ثانية → HH:MM:SS ───────────────────────────────────────────
function fmtTime(sec) {
    sec = Math.max(0, Math.floor(sec));
    const h = String(Math.floor(sec / 3600)).padStart(2, '0');
    const m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
    const s = String(sec % 60).padStart(2, '0');
    return `${h}:${m}:${s}`;
}

// ── بدء عداد المكان المشغول ──────────────────────────────────────────────────
function startTimer(baseSec) {
    elapsedBase = baseSec;
    const start = Date.now();

    if (timerInterval) clearInterval(timerInterval);

    timerInterval = setInterval(() => {
        const elapsed = elapsedBase + (Date.now() - start) / 1000;
        document.getElementById('timerDisplay').textContent = fmtTime(elapsed);
        document.getElementById('costDisplay').textContent  =
            (elapsed * RATE_PER_SEC).toFixed(2) + ' ج.م';
    }, 1000);
}

// ── polling كل 10 ث للتحقق من تغيير الحالة (reserved → occupied) ─────────────
function pollStatus() {
    fetch(`api/slot_timer.php?guid=${encodeURIComponent(GUID)}`)
        .then(r => r.json())
        .then(data => {
            if (data.error) return;

            if (data.status !== currentStatus) {
                currentStatus = data.status;
                updateUI(data);
            } else if (data.status === 'occupied') {
                // حدّث الـ base في حالة انقطع الاتصال
                if (!timerInterval) startTimer(data.elapsed_sec);
            }
        })
        .catch(() => {});
}

function updateUI(data) {
    const header  = document.getElementById('ticketHeader');
    const emoji   = document.getElementById('statusEmoji');
    const txt     = document.getElementById('statusText');
    const display = document.getElementById('timerDisplay');

    header.className = 'ticket-header ' + data.status;

    if (data.status === 'occupied') {
        emoji.textContent   = '🔴';
        txt.textContent     = 'مكان مشغول';
        display.className   = 'timer-display running';
        startTimer(data.elapsed_sec);
    } else if (data.status === 'reserved') {
        emoji.textContent   = '🟡';
        txt.textContent     = 'محجوز — في انتظار الدخول';
        display.className   = 'timer-display waiting';
        display.textContent = '⏳ انتظار';
        document.getElementById('costDisplay').textContent = 'سيبدأ الحساب عند الدخول';
        if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }
    } else {
        if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }
    }
}

// ── تشغيل ────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    if (currentStatus === 'occupied' && ENTRY_TS) {
        const serverNow = <?php echo time(); ?>;
        startTimer(serverNow - ENTRY_TS);
    }
    setInterval(pollStatus, 10000);   // فحص كل 10 ثواني
});
</script>
</body>
</html>
