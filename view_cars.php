<?php require_once 'header.php'; ?>
<?php
// 1. الاتصال بقاعدة البيانات
require_once __DIR__ . '/config.php';

// 2. كود الحذف (لو دوستي على زرار مسح)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM cars WHERE car_id = $id");
    header("Location: view_cars.php"); // تحديث الصفحة بعد الحذف
}

// 3. جلب البيانات من الجدول
$result = $conn->query("SELECT * FROM cars ORDER BY car_id DESC");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سجل السيارات المحجوزة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; padding: 30px; }
        .table-container { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .table thead { background-color: #1a73e8; color: white; }
        .btn-back { background-color: #0EA5A4; color: white; border-radius: 10px; margin-bottom: 20px; text-decoration: none; display: inline-block; padding: 10px 20px; }
    </style>

    <!-- Global Nav Styles -->
    
</head>
<body>
<?php include 'navbar.php'; ?>


<div class="container">
    <a href="dashboard.php" class="btn-back">⬅ العودة للوحة التحكم</a>
    
    <div class="table-container">
        <h2 class="text-center mb-4">🚗 سجل السيارات الموجودة في الموقف</h2>
        
        <table class="table table-hover text-center">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>رقم اللوحة</th>
                    <th>الموديل</th>
                    <th>اللون</th>
                    <th>رقم الركنة (Slot)</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['car_id']; ?></td>
                    <td><?php echo $row['car_plate']; ?></td>
                    <td><?php echo $row['car_model']; ?></td>
                    <td><?php echo $row['car_color']; ?></td>
                    <td><span class="badge bg-danger"><?php echo $row['slot_id']; ?></span></td>
                    <td>
                        <a href="view_cars.php?delete=<?php echo $row['car_id']; ?>" 
                           class="btn btn-sm btn-outline-danger" 
                           onclick="return confirm('هل أنت متأكد من خروج هذه السيارة؟')">إتمام الخروج (مسح)</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <?php if ($result->num_rows == 0): ?>
            <p class="text-center text-muted">لا توجد سيارات مسجلة حالياً.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>