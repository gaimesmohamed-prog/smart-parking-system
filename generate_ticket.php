<?php
// استقبال البيانات من الرابط (URL)
$name  = $_GET['name'] ?? 'عميل';
$plate = $_GET['plate'] ?? '---';
$lot   = $_GET['lot'] ?? '0';

// شيل localhost وحط الـ IP بتاعك (مثلاً 192.168.1.5)
$actual_link = "http://192.168.1.8:8080/parking%20app%20test/pay_now.php?plate=" . urlencode($plate) . "&lot=" . $lot;;

$qr_image_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($actual_link);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تذكرة ركن السيارة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .ticket-card {
            max-width: 400px;
            margin: 50px auto;
            border: 2px dashed #333;
            border-radius: 15px;
            background: #fff;
            padding: 20px;
        }
        .qr-frame {
            border: 1px solid #ddd;
            padding: 10px;
            display: inline-block;
            background: #f9f9f9;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="container text-center">
    <div class="ticket-card shadow-lg">
        <h3 class="text-primary">🎫 تذكرة دخول الموقف</h3>
        <p class="text-muted">مشروع إدارة المواقف الذكي (BIS)</p>
        <hr>
        
        <div class="text-end px-3">
            <p><strong>الاسم:</strong> <?php echo htmlspecialchars($name); ?></p>
            <p><strong>رقم اللوحة:</strong> <?php echo htmlspecialchars($plate); ?></p>
            <p><strong>المكان المخصص:</strong> <span class="badge bg-success" style="font-size: 1.1rem;">P<?php echo htmlspecialchars($lot); ?></span></p>
        </div>

        <div class="my-4 qr-frame">
            <img src="<?php echo $qr_image_url; ?>" alt="QR Code" class="img-fluid">
        </div>

        <p class="small text-danger">⚠️ برجاء الاحتفاظ بالتذكرة للمسح عند الخروج</p>
        
        <div class="mt-4 no-print">
            <button onclick="window.print()" class="btn btn-dark w-100 mb-2">طباعة التذكرة 🖨️</button>
            <a href="dashboard.php" class="btn btn-outline-primary w-100">العودة للوحة التحكم</a>
        </div>
    </div>
</div>

</body>
</html>