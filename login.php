<?php
require_once 'header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $primaryKey = parking_users_primary_key($conn);
    $stmt = $conn->prepare("SELECT * FROM users WHERE full_name = ? OR email = ? ORDER BY {$primaryKey} DESC LIMIT 1");

    if ($stmt) {
        $stmt->bind_param('ss', $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $storedPassword = $user['password'] ?? '';
            $isValid = verify_password($password, $storedPassword);

            if (!$isValid && $storedPassword !== '' && hash_equals($storedPassword, $password)) {
                $isValid = true;

                $newHash = hash_password($password);
                $userId = get_user_primary_key($user);
                $primaryKey = isset($user['user_id']) ? 'user_id' : 'id';
                $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE {$primaryKey} = ?");

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
            }
        }

        $stmt->close();
    }

    $error = "بيانات الدخول غير صحيحة";
}
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('login'); ?> - Parking App</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: url('WhatsApp Image 2026-04-23 at 7.03.59 PM.jpeg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* Fallback dark overlay to ensure text is readable */
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 0;
        }
        .navbar-wrapper { position: absolute; top: 0; width: 100%; z-index: 10; }
        .glass-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 40px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            color: white;
            z-index: 1;
        }
        .glass-container h1 {
            font-size: 32px;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 800;
            letter-spacing: 1px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }
        .glass-input-group {
            margin-bottom: 20px;
            text-align: left;
        }
        html[dir="rtl"] .glass-input-group { text-align: right; }
        .glass-input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
            text-shadow: 0 1px 3px rgba(0,0,0,0.6);
        }
        .glass-input {
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.3);
            background: rgba(0,0,0,0.2);
            color: white;
            font-size: 16px;
            box-sizing: border-box;
            transition: all 0.3s;
        }
        .glass-input::placeholder { color: rgba(255,255,255,0.5); }
        .glass-input:focus {
            outline: none;
            background: rgba(0,0,0,0.4);
            border-color: #5D5FEF;
            box-shadow: 0 0 10px rgba(93, 95, 239, 0.5);
        }
        .glass-btn {
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            background: linear-gradient(135deg, #1a73e8 0%, #5D5FEF 100%);
            color: white;
            border: none;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .glass-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(93, 95, 239, 0.4);
        }
        .glass-link {
            color: #8ce1ff;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
        }
        .glass-link:hover { color: #fff; text-shadow: 0 0 5px rgba(255,255,255,0.5); }
        .error-msg { background: rgba(255, 50, 50, 0.2); border: 1px solid rgba(255, 50, 50, 0.5); padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
    </style>
</head>
<body>

<div class="glass-container">
    <h1><?php echo __('login'); ?></h1>

    <form method="POST">
        <?php if ($error) echo "<div class='error-msg'>$error</div>"; ?>

        <div class="glass-input-group">
            <label><?php echo __('email'); ?></label>
            <input type="text" name="username" class="glass-input" placeholder="example@email.com" required>
        </div>

        <div class="glass-input-group">
            <label>Password</label>
            <input type="password" name="password" class="glass-input" placeholder="********" required>
        </div>

        <button type="submit" name="login_btn" class="glass-btn"><?php echo __('login'); ?></button>

        <div style="text-align: center; margin-top: 25px; font-size: 14px; text-shadow: 0 1px 2px rgba(0,0,0,0.5);">
            Don't have an account? <a href="signup.php" class="glass-link">Sign Up</a>
        </div>
    </form>
</div>

</body>
</html>
