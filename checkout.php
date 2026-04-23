<?php
require_once 'header.php';
require_once __DIR__ . '/config.php';

$car_id = intval($_GET['car_id'] ?? 0);
if (!$car_id) {
    die("Invalid Car ID");
}

$stmt = $conn->prepare("SELECT * FROM cars WHERE car_id = ? AND status IN ('occupied', 'reserved')");
$stmt->bind_param('i', $car_id);
$stmt->execute();
$res = $stmt->get_result();
$car = $res->fetch_assoc();
$stmt->close();

if (!$car) {
    die("السيارة غير موجودة أو تم الدفع لها مسبقاً.");
}

$fee = calculate_parking_fee(10); // Fixed demo fee
$slot_id = $car['slot_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_confirm'])) {
    // 1. Update car status
    $conn->query("UPDATE cars SET status = 'completed' WHERE car_id = $car_id");
    
    // 2. Update parking history
    $conn->query("UPDATE parking_history SET exit_time = NOW() WHERE plate_number = '{$car['plate_number']}' AND exit_time IS NULL");
    
    // 3. Free the slot
    parking_update_slot_status($conn, $slot_id, 'available');
    
    set_success_message("تم الدفع بنجاح! تم تسجيل خروج السيارة وإخلاء المكان.");
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دفع الفاتورة - Smart Parking</title>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="figma-container">
    <br><br>
    <a href="dashboard.php" style="color: #333; text-decoration: none;"><i class="fa-solid fa-arrow-right"></i> رجوع للوحة التحكم</a>
    
    <div style="text-align: center; margin-top: 40px;">
        <i class="fa-solid fa-file-invoice-dollar" style="font-size: 60px; color: #5D5FEF;"></i>
        <h2 style="margin-top: 20px;">فاتورة الركن</h2>
        <p style="color: #666; margin-bottom: 30px;">برجاء مراجعة التفاصيل والدفع لإخلاء المكان.</p>
        
        <div style="background: #f8f9fa; padding: 20px; border-radius: 15px; text-align: right; max-width: 400px; margin: 0 auto;">
            <p><strong>رقم السيارة:</strong> <?php echo htmlspecialchars($car['plate_number']); ?></p>
            <p><strong>مكان الركن:</strong> <span style="color: #5D5FEF; font-weight: bold;"><?php echo htmlspecialchars($slot_id); ?></span></p>
            <hr>
            <h3 style="text-align: center; color: #2ecc71; margin: 15px 0;">تم الدفع مسبقاً من المحفظة</h3>
        </div>

        <form method="POST" style="margin-top: 30px; max-width: 400px; margin-left: auto; margin-right: auto;">
            <button type="submit" name="pay_confirm" class="figma-btn" style="background: #e74c3c; font-size: 18px;">
                <i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج وإخلاء المكان
            </button>
        </form>
    </div>
</div>

</body>
</html>
