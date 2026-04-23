<?php
// صفحة الدفع الوهمي - pay_now.php
$plate = $_GET['plate'] ?? '---';
$lot   = $_GET['lot'] ?? '0';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>بوابة الدفع الذكية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .payment-card { max-width: 400px; margin: 30px auto; border-radius: 20px; border: none; }
        .method-btn { padding: 15px; border-radius: 12px; font-weight: bold; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; gap: 10px; }
    </style>
</head>
<body>
<div class="container text-center">
    <div class="card payment-card shadow-lg p-4">
        <h4 class="mb-3">دفع رسوم الموقف 🚗</h4>
        <div class="alert alert-info">
            رقم السيارة: <strong><?php echo $plate; ?></strong><br>
            مكان الركن: <strong>P<?php echo $lot; ?></strong>
        </div>
        <h2 class="text-primary mb-4">20.00 ج.م</h2>
        
        <p class="text-muted small">اختر وسيلة الدفع للإتمام:</p>
        
        <button onclick="pay('Vodafone Cash')" class="btn btn-danger w-100 method-btn">
             Vodafone Cash
        </button>
        
        <button onclick="pay('InstaPay')" class="btn btn-outline-dark w-100 method-btn">
             InstaPay
        </button>
    </div>
</div>

<script>
function pay(method) {
    if(confirm('هل تريد تأكيد الدفع بمبلغ 20 ج.م عبر ' + method + '؟')) {
        // التحويل للمرحلة الرابعة (النجاح وفتح المكان)
        window.location.href = 'payment_success.php?lot=<?php echo $lot; ?>';
    }
}
</script>
</body>
</html>