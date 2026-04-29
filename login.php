<?php
require_once 'header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // التحقق من صحة المدخلات
    if (empty($username) || empty($password)) {
        $error = "يرجى إدخال البريد الإلكتروني وكلمة المرور";
    } else {
        $primaryKey = parking_users_primary_key($conn);
        // البحث عن المستخدم بالاسم الكامل أو البريد الإلكتروني
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR full_name = ? ORDER BY {$primaryKey} DESC LIMIT 1");

        if ($stmt) {
            $stmt->bind_param('ss', $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $user = $result->fetch_assoc()) {
                $storedPassword = $user['password'] ?? '';
                
                // التحقق من كلمة المرور
                $isValid = verify_password($password, $storedPassword);

                // دعم كلمات السر غير المشفرة (Plaintext) كخيار احتياطي
                if (!$isValid && $storedPassword !== '' && $storedPassword === $password) {
                    $isValid = true;
                    // تحديث كلمة المرور لتكون مشفرة مستقبلاً
                    $newHash = hash_password($password);
                    $userId = get_user_primary_key($user);
                    $pkCol = isset($user['user_id']) ? 'user_id' : 'id';
                    $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE {$pkCol} = ?");
                    if ($updateStmt) {
                        $updateStmt->bind_param('si', $newHash, $userId);
                        $updateStmt->execute();
                        $updateStmt->close();
                    }
                }

                if ($isValid) {
                    $_SESSION['user'] = $user['full_name'];
                    $_SESSION['user_id'] = get_user_primary_key($user);
                    $_SESSION['role'] = $user['role'] ?? 'user';
                    
                    set_success_message('مرحبا ' . $user['full_name']);
                    
                    if ($_SESSION['role'] === 'admin') {
                        redirect('app_dashboard.php');
                    } else {
                        redirect('dashboard.php');
                    }
                } else {
                    $error = "كلمة المرور غير صحيحة";
                }
            } else {
                $error = "هذا الحساب غير موجود (تحقق من البريد الإلكتروني)";
            }
            $stmt->close();
        } else {
            $error = "خطأ في قاعدة البيانات: " . $conn->error;
        }
    }
}
?>

<?php include 'navbar.php'; ?>
<div class="glass-container">
    <h1 style="font-family: 'Righteous', cursive; margin-bottom: 30px;"><?php echo __('login'); ?></h1>

    <form method="POST">
        <?php if ($error): ?>
            <div style="background: rgba(255, 77, 77, 0.1); border: 1px solid #ff4d4d; color: #ff4d4d; padding: 12px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; font-size: 14px;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div style="text-align: left; margin-bottom: 20px;">
            <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-muted); margin-bottom: 8px; margin-left: 5px;"><?php echo __('email'); ?></label>
            <input type="text" name="username" style="width: 100%; padding: 14px 20px; border-radius: 14px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.05); color: var(--text-main); font-weight: 600;" placeholder="example@email.com" required>
        </div>

        <div style="text-align: left; margin-bottom: 30px;">
            <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-muted); margin-bottom: 8px; margin-left: 5px;"><?php echo __('password'); ?></label>
            <input type="password" name="password" style="width: 100%; padding: 14px 20px; border-radius: 14px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.05); color: var(--text-main); font-weight: 600;" placeholder="********" required>
        </div>

        <button type="submit" name="login_btn" class="mbtn mbtn-enter" style="border: none;"><?php echo __('login'); ?></button>

        <div style="text-align: center; margin-top: 25px; font-size: 14px; color: var(--text-muted);">
            Don't have an account? <a href="signup.php" style="color: var(--accent-color); font-weight: 700; text-decoration: none;">Sign Up</a>
        </div>
    </form>
</div>

</body>
</html>
