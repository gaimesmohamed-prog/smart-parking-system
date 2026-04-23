<?php
require_once 'header.php';
require_once __DIR__ . '/config.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$user_id = intval($_SESSION['user_id']);

// Handle recharge request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recharge'])) {
    $amount = 100.00; // Fixed recharge amount for demo
    $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE user_id = ?");
    $stmt->bind_param('di', $amount, $user_id);
    if ($stmt->execute()) {
        set_success_message("تم شحن رصيدك بنجاح بمبلغ $amount ج.م");
    } else {
        set_error_message("حدث خطأ أثناء الشحن.");
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
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>محفظتي - Smart Parking</title>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="figma-container" style="text-align: center; padding-top: 40px;">
    <i class="fa-solid fa-wallet" style="font-size: 60px; color: #5D5FEF; margin-bottom: 20px;"></i>
    <h2 style="margin-bottom: 10px;">المحفظة الإلكترونية</h2>
    <p style="color: #666; margin-bottom: 30px;">رصيدك الحالي المتاح للحجز</p>
    
    <div style="background: linear-gradient(135deg, #1a73e8, #5D5FEF); color: white; padding: 30px; border-radius: 15px; margin: 0 auto 40px; max-width: 350px; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">
        <h3 style="margin: 0; font-size: 16px; opacity: 0.9;">الرصيد المتاح</h3>
        <div style="font-size: 40px; font-weight: bold; margin-top: 10px;"><?php echo number_format($balance, 2); ?> <span style="font-size: 20px;">ج.م</span></div>
    </div>

    <form method="POST">
        <button type="submit" name="recharge" class="figma-btn" style="background: #2ecc71; font-size: 18px; max-width: 350px; margin: 0 auto;">
            <i class="fa-solid fa-plus-circle"></i> شحن المحفظة (100 ج.م)
        </button>
    </form>
</div>

</body>
</html>
