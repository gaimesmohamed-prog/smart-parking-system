<?php
// 1. حل مشكلة الـ Session: نفتح السيشين فقط لو لم يكن مفتوحاً بالفعل
if (session_status() === PHP_SESSION_NONE) {
    // إعدادات الكوكيز والسيشين توضع هنا قبل أي كود آخر
    session_start();
}

// 2. الاتصال بقاعدة البيانات (يفضل يكون هنا عشان يخدم كل الصفحات)
$conn = new mysqli("localhost", "root", "112004ٌة#", "parkingapp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 3. تعريف دالة الترجمة __() لو مش موجودة عشان نمنع الـ Undefined Function Error
if (!function_exists('__')) {
    function __($text) {
        // حالياً هترجع النص زي ما هو، ولو عندك ملف لغات ممكن تربطه هنا
        return $text;
    }
}

// 4. منع الـ Header Injection (حل مشكلة سطر 8 اللي كانت بتظهر)
ob_start(); 
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        /* ستايل بسيط للهيدر عشان يكون شكله احترافي */
        .main-header {
            background: #5D5FEF;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .user-info { font-size: 14px; }
    </style>
</head>
<body>

<?php 
// لو اليوزر مسجل دخول، اظهر اسمه فوق لشياكة العرض قدام الدكتور
if(isset($_SESSION['user'])): ?>
    <div class="main-header">
        <div><i class="fas fa-parking"></i> نظام الركن الذكي</div>
        <div class="user-info">
            أهلاً، <?php echo htmlspecialchars($_SESSION['user']); ?> | 
            <a href="logout.php" style="color: white; text-decoration: underline;">خروج</a>
        </div>
    </div>
<?php endif; ?>