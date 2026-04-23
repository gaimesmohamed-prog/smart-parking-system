<?php require_once 'header.php'; ?>
<?php
// الاتصال بقاعدة البيانات
require_once __DIR__ . '/config.php';

$status = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $msg = $_POST['message'];

    $sql = "INSERT INTO complaints (name, email, message) VALUES ('$name', '$email', '$msg')";
    
    if ($conn->query($sql) === TRUE) {
        $status = "success";
    } else {
        $status = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مركز المساعدة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: url('My_design.jpg') no-repeat center center fixed; background-size: cover; color: white; }
        .help-container { margin-top: 50px; background: rgba(0,0,0,0.6); padding: 30px; border-radius: 20px; backdrop-filter: blur(5px); }
    </style>

    <!-- Global Nav Styles -->
    
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container help-container text-center">
    <h1>مركز المساعدة والشكاوي</h1>
    <p>نحن هنا لخدمتك، أرسل استفسارك وسنرد عليك في أقرب وقت.</p>
    
    <?php if($status == "success"): ?>
        <div class="alert alert-success">تم إرسال شكواك بنجاح!</div>
    <?php endif; ?>

    <button class="btn btn-lg btn-info mt-3" data-bs-toggle="modal" data-bs-target="#contactModal">أرسل شكوى الآن</button>
    <br><br>
    <a href="dashboard.php" class="text-white">⬅ العودة للوحة التحكم</a>
</div>

<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:#1E293B; color:white;">
            <div class="modal-header border-0">
                <h5 class="modal-title">تقديم شكوى جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="text" name="name" class="form-control mb-3" placeholder="الاسم الكامل" required>
                    <input type="email" name="email" class="form-control mb-3" placeholder="البريد الإلكتروني" required>
                    <textarea name="message" class="form-control mb-3" rows="4" placeholder="اشرح مشكلتك هنا..." required></textarea>
                    <button type="submit" class="btn btn-primary w-100">إرسال الآن</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>