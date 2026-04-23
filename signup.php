<?php
require_once 'header.php';

$success = false;
$error = "";

function table_exists(mysqli $conn, $tableName) {
    $safeTable = $conn->real_escape_string($tableName);
    $result = $conn->query("SHOW TABLES LIKE '{$safeTable}'");
    return $result && $result->num_rows > 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $mobile = trim($_POST['mobile_phone'] ?? '');
    $car_plate = trim($_POST['car_plate'] ?? '');

    if ($full_name === '' || $email === '' || $password === '') {
        $error = "جميع الحقول الأساسية مطلوبة.";
    } else {
        $passwordHash = hash_password($password);
        $userStmt = $conn->prepare(
            "INSERT INTO users (full_name, email, password, mobile_phone) VALUES (?, ?, ?, ?)"
        );

        if (!$userStmt) {
            $error = "User Error: " . $conn->error;
        } else {
            $userStmt->bind_param('ssss', $full_name, $email, $passwordHash, $mobile);

            if ($userStmt->execute()) {
                $last_user_id = $conn->insert_id;
                $success = true;

                if (table_exists($conn, 'user_profiles')) {
                    $profileStmt = $conn->prepare(
                        "INSERT INTO user_profiles (user_id, phone_number, email, car_plate_number) VALUES (?, ?, ?, ?)"
                    );

                    if ($profileStmt) {
                        $profileStmt->bind_param('isss', $last_user_id, $mobile, $email, $car_plate);
                        $profileStmt->execute();
                        $profileStmt->close();
                    }
                }
            } else {
                $error = "User Error: " . $userStmt->error;
            }

            $userStmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Parking App</title>
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
            margin-top: 60px; /* Space for navbar */
            margin-bottom: 30px;
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
            margin-bottom: 15px;
            text-align: left;
        }
        html[dir="rtl"] .glass-input-group { text-align: right; }
        .glass-input-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 13px;
            text-shadow: 0 1px 3px rgba(0,0,0,0.6);
        }
        .glass-input {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.3);
            background: rgba(0,0,0,0.2);
            color: white;
            font-size: 15px;
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
            margin-top: 15px;
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
        .success-box { background: rgba(46, 125, 50, 0.3); border: 1px solid rgba(46, 125, 50, 0.5); padding: 20px; border-radius: 16px; text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="glass-container">
    <h1>Sign Up</h1>

    <?php if ($success): ?>
        <div class="success-box">
            <i class="fa-solid fa-circle-check" style="font-size: 40px; margin-bottom: 10px; color: #4caf50;"></i>
            <p style="font-weight: bold; text-shadow: 0 1px 2px rgba(0,0,0,0.5);">Registration successful!</p>
            <a href="login.php" class="glass-btn" style="text-decoration: none; display: inline-block; width: auto; padding: 10px 20px; margin-top: 10px;">Log In Now</a>
        </div>
    <?php else: ?>
        <form method="POST">
            <?php if ($error) echo "<div class='error-msg'>$error</div>"; ?>

            <div class="glass-input-group">
                <label>Name</label>
                <input type="text" name="full_name" class="glass-input" placeholder="Your full name" required>
            </div>

            <div class="glass-input-group">
                <label><?php echo __('email'); ?></label>
                <input type="email" name="email" class="glass-input" placeholder="example@email.com" required>
            </div>

            <div class="glass-input-group">
                <label>Password</label>
                <input type="password" name="password" class="glass-input" placeholder="********" required>
            </div>

            <button type="submit" class="glass-btn">Sign Up</button>

            <div style="text-align: center; margin-top: 25px; font-size: 14px; text-shadow: 0 1px 2px rgba(0,0,0,0.5);">
                Already have an account? <a href="login.php" class="glass-link">Log in</a>
            </div>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
