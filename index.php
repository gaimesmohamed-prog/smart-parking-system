<?php require_once 'header.php'; ?>
<div style='padding:40px; text-align:center;'>
    <h1>🚗 تطبيق ركن السيارات الذكي</h1>
    <p><a href='setup.php'>1. إعداد قاعدة البيانات</a></p>
    <p><a href='login.php'>2. تسجيل دخول (admin/admin123)</a></p>
    <p><a href='add_car.php'>3. حجز مكان</a></p>
    <p><a href='migrate_passwords.php'>4. تحديث كلمات السر (إن وُجد)</a></p>
    <p>DB: <?php echo DB_NAME; ?> | Status: <span style='color:green;'>جاهز ✓</span></p>
</div>

