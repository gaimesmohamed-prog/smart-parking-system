<?php require_once 'header.php'; ?>
<?php
session_start();
// الاتصال بقاعدة البيانات
require_once __DIR__ . '/config.php';

$message = "";

if (isset($_POST['verify_btn'])) {
    $staff_id = $_POST['staff_id'];
    $password = $_POST['password'];
    $role     = $_POST['role']; // Staff or Admin

    // فحص البيانات (هنا بنفترض إن فيه جدول اسمه staff)
    // تقدري تستخدمي Admin/123 للتجربة حالياً
    if ($staff_id == "admin" && $password == "123") {
        $_SESSION['admin_role'] = $role;
        header("Location: app_dashboard.php"); // يوديه للوحة التحكم
    } else {
        $message = "❌ Invalid Credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff / Admin Verification</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #5D5FEF; margin: 0; display: flex; flex-direction: column; align-items: center; height: 100vh; }
        
        h1 { color: white; font-size: 32px; margin-top: 50px; text-align: center; width: 80%; }

        .container { 
            background: white; width: 100%; flex-grow: 1; border-radius: 60px 60px 0 0; 
            padding: 50px 30px; box-sizing: border-box; margin-top: 30px;
        }

        label { font-size: 28px; font-weight: bold; display: block; margin-bottom: 10px; }
        
        input { 
            width: 100%; padding: 20px; font-size: 20px; border: 2px solid #000; 
            border-radius: 10px; margin-bottom: 30px; box-sizing: border-box;
        }

        .role-selection { display: flex; justify-content: space-between; margin-bottom: 50px; }
        
        /* تصميم أزرار الـ Role */
        .role-btn {
            width: 45%; padding: 15px; border: 2px solid #000; background: white;
            font-size: 24px; font-weight: bold; cursor: pointer; border-radius: 5px;
        }

        .role-btn.active { background: #5D5FEF; color: white; border-color: #5D5FEF; }

        .verify-btn { 
            background-color: #5D5FEF; color: white; width: 100%; padding: 20px; 
            border: none; border-radius: 10px; font-size: 26px; font-weight: bold; cursor: pointer; 
        }
    </style>

    <!-- Global Nav Styles -->
    
</head>
<body>
<?php include 'navbar.php'; ?>


<h1>Staff / Admin Verification</h1>

<div class="container">
    <form method="POST">
        <label>Staff ID:</label>
        <input type="text" name="staff_id" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>Role</label>
        <div class="role-selection">
            <input type="hidden" name="role" id="role_value" value="Staff">
            <button type="button" class="role-btn active" onclick="setRole('Staff', this)">Staff</button>
            <button type="button" class="role-btn" onclick="setRole('Admin', this)">Admin</button>
        </div>

        <button type="submit" name="verify_btn" class="verify-btn">Verify Access</button>
    </form>
</div>

<script>
    function setRole(role, btn) {
        document.getElementById('role_value').value = role;
        // تغيير شكل الأزرار
        document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }
</script>

</body>
</html>