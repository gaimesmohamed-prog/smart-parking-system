<?php require_once 'header.php'; ?>
<?php
// 1. الاتصال بقاعدة البيانات
require_once __DIR__ . '/config.php';

// 2. كود المعالجة عند الضغط على Approve
if (isset($_POST['approve'])) {
    $request_id = $_POST['request_id'];
    $slot_id = $_POST['slot_id'];
    $plate = $_POST['plate_number'];

    // تحديث حالة الطلب في قاعدة البيانات (نفترض وجود جدول طلبات)
    $conn->query("UPDATE requests SET status='approved' WHERE id='$request_id'");
    
    // إضافة السيارة لجدول الـ cars عشان تظهر في الخريطة والجدول
    $guid = uniqid();
    $conn->query("INSERT INTO cars (plate_number, slot_id, status, parking_guid) VALUES ('$plate', '$slot_id', 'occupied', '$guid')");
    
    echo "<script>alert('تم الموافقة وتفعيل الحجز للمكان $slot_id');</script>";
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Alerts & Notification - Admin</title>
    <style>
        body { font-family: sans-serif; background-color: #f0f2f5; margin: 0; }
        .header { background: #1a73e8; color: white; padding: 20px; text-align: center; font-size: 20px; font-weight: bold; }
        .container { max-width: 900px; margin: 30px auto; padding: 0 20px; }
        
        /* كارت التنبيه بستايل فيجما */
        .alert-card { 
            background: white; border-radius: 20px; padding: 25px; 
            border: 1px solid #ddd; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
        }
        .alert-type { display: flex; align-items: center; gap: 10px; font-weight: bold; margin-bottom: 15px; }
        .details { color: #555; font-size: 15px; margin-bottom: 20px; line-height: 1.6; }
        
        /* الأزرار بستايل فيجما */
        .btn-approve { 
            background: #1a73e8; color: white; border: none; padding: 12px 0; 
            width: 100%; border-radius: 10px; font-weight: bold; cursor: pointer; font-size: 16px; 
        }
        .btn-reject { 
            background: #f1f3f4; color: #d93025; border: none; padding: 12px 20px; 
            border-radius: 10px; font-weight: bold; cursor: pointer; margin-top: 10px; width: 100%;
        }
    </style>

    <!-- Global Nav Styles -->
    
</head>
<body>
<?php include 'navbar.php'; ?>


<div class="header">Alerts & Notification</div>

<div class="container">
    <div class="alert-card">
        <div class="alert-type">
            <span style="color: #f9ab00; font-size: 24px;">●</span> Long Stay Request
        </div>
        <div class="details">
            slot ID: <strong>B3</strong><br>
            requested duration: <strong>2 days</strong><br>
            plate number: <strong>ABC-278</strong>
        </div>
        
        <form method="POST">
            <input type="hidden" name="request_id" value="1">
            <input type="hidden" name="slot_id" value="B3">
            <input type="hidden" name="plate_number" value="ABC-278">
            <button type="submit" name="approve" class="btn-approve">Approve</button>
            <button type="button" class="btn-reject">Reject</button>
        </form>
    </div>

    <div class="alert-card" style="border-left: 5px solid #d93025;">
        <div class="alert-type" style="color: #d93025;">
            <i class="fas fa-exclamation-triangle"></i> Overstay Alert
        </div>
        <div class="details">
            slot ID: <strong>B1</strong><br>
            parked duration: <strong>26 hours</strong><br>
            status: <span style="color: #d93025;">action required</span>
        </div>
        <button class="btn-approve" style="background: #1a73e8;">View Slot Details</button>
    </div>
</div>

</body>
</html>