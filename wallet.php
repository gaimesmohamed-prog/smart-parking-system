<?php
require_once 'header.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$user_id = intval($_SESSION['user_id']);

// Handle recharge request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recharge'])) {
    $amount = floatval($_POST['amount'] ?? 100);
    $method = $_POST['method'] ?? 'visa';
    
    $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE user_id = ?");
    $stmt->bind_param('di', $amount, $user_id);
    if ($stmt->execute()) {
        set_success_message("✅ تم شحن محفظتك بنجاح بمبلغ " . number_format($amount, 2) . " ج.م عبر $method");
    } else {
        set_error_message("❌ حدث خطأ أثناء عملية الشحن.");
    }
    redirect('wallet.php');
}

// Fetch current balance
$stmt = $conn->prepare("SELECT balance FROM users WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$balance = $user['balance'] ?? 0.00;
?>
<?php include 'navbar.php'; ?>

<div class="wrapper animate-fade-in wallet-wrapper">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin:0; color: white; text-shadow: 0 2px 10px rgba(0,0,0,0.2);">
            <i class="fas fa-wallet"></i> المحفظة الذكية
        </h2>
    </div>

    <div class="balance-card">
        <div class="balance-label">الرصيد المتاح</div>
        <div class="balance-amount">
            <?php echo number_format($balance, 2); ?>
            <span class="balance-currency">JOD</span>
        </div>
        <div style="margin-top: 15px; font-size: 14px; opacity: 0.8;">
            <i class="fas fa-shield-alt"></i> عمليات دفع آمنة ومشفرة
        </div>
    </div>

    <form method="POST" id="rechargeForm" class="glass-card" style="padding: 30px;">
        <h3 class="section-title"><i class="fas fa-credit-card"></i> اختر وسيلة الشحن</h3>
        <div class="payment-methods">
            <div class="method-card active" onclick="setMethod('visa', this)">
                <i class="fab fa-cc-visa"></i>
                <span>بطاقة بنكية</span>
            </div>
            <div class="method-card" onclick="setMethod('v-cash', this)">
                <i class="fas fa-mobile-alt"></i>
                <span>محفظة كاش</span>
            </div>
            <div class="method-card" onclick="setMethod('instapay', this)">
                <i class="fas fa-bolt"></i>
                <span>انستا باي</span>
            </div>
        </div>

        <h3 class="section-title"><i class="fas fa-coins"></i> حدد المبلغ</h3>
        <div class="amount-selector">
            <div class="amount-btn" onclick="setAmount(5, this)">5 JOD</div>
            <div class="amount-btn active" onclick="setAmount(10, this)">10 JOD</div>
            <div class="amount-btn" onclick="setAmount(20, this)">20 JOD</div>
        </div>

        <input type="hidden" name="amount" id="amountInput" value="10">
        <input type="hidden" name="method" id="methodInput" value="visa">
        
        <button type="submit" name="recharge" class="recharge-btn">
            <i class="fas fa-plus-circle"></i> شحن المحفظة الآن
        </button>
    </form>
</div>

<script>
function setMethod(method, el) {
    document.querySelectorAll('.method-card').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('methodInput').value = method;
}

function setAmount(val, el) {
    document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('amountInput').value = val;
}
</script>

</body>
</html>
