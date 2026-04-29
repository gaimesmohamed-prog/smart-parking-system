<?php
require_once 'header.php';

$success = false;
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $confirm_email = trim($_POST['confirm_email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $mobile = trim($_POST['mobile_phone'] ?? '');
    
    $car_plate = trim($_POST['car_plate'] ?? '');
    $car_model = trim($_POST['car_model'] ?? '');
    $car_color = trim($_POST['car_color'] ?? '');

    if ($email !== $confirm_email) {
        $error = "Emails do not match.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (empty($full_name) || empty($email) || empty($password)) {
        $error = "Basic fields (Name, Email, Password) are required.";
    } else {
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if ($checkStmt) {
            $checkStmt->bind_param('s', $email);
            $checkStmt->execute();
            $checkStmt->store_result();
            
            if ($checkStmt->num_rows > 0) {
                $error = "This email is already registered.";
            } else {
                $conn->begin_transaction();
                try {
                    $passwordHash = hash_password($password);
                    $userStmt = $conn->prepare("INSERT INTO users (full_name, email, password, mobile_phone) VALUES (?, ?, ?, ?)");
                    $userStmt->bind_param('ssss', $full_name, $email, $passwordHash, $mobile);
                    
                    if ($userStmt->execute()) {
                        $last_user_id = $conn->insert_id;
                        $carStmt = $conn->prepare("INSERT INTO cars (plate_number, car_model, car_color, user_id, status, slot_id, parking_guid) VALUES (?, ?, ?, ?, 'available', '', '')");
                        $carStmt->bind_param('sssi', $car_plate, $car_model, $car_color, $last_user_id);
                        $carStmt->execute();
                        $carStmt->close();

                        $profileStmt = $conn->prepare("INSERT INTO user_profiles (user_id, phone_number, email, car_plate_number) VALUES (?, ?, ?, ?)");
                        $profileStmt->bind_param('isss', $last_user_id, $mobile, $email, $car_plate);
                        $profileStmt->execute();
                        $profileStmt->close();

                        $conn->commit();
                        $success = true;
                    } else {
                        throw new Exception("Error creating user.");
                    }
                    $userStmt->close();
                } catch (Exception $e) {
                    $conn->rollback();
                    $error = "Registration failed: " . $e->getMessage();
                }
            }
            $checkStmt->close();
        } else {
            $error = "Database preparation error.";
        }
    }
}
?>
<?php include 'navbar.php'; ?>
<div class="glass-container" style="max-width: 500px;">
    <h1 style="font-family: 'Righteous', cursive; margin-bottom: 30px;"><?php echo __('signup'); ?></h1>
    
    <?php if ($success): ?>
        <div style="background: rgba(0, 184, 148, 0.1); border: 1px solid #00b894; color: #00b894; padding: 20px; border-radius: 16px; margin-bottom: 20px; font-weight: 600;">
            <p>Registration successful! Welcome to the Parking App.</p>
            <a href="login.php" class="mbtn mbtn-enter" style="text-decoration: none; margin-top: 15px;">Go to Login</a>
        </div>
    <?php else: ?>
        <form method="POST">
            <?php if ($error): ?>
                <div style="background: rgba(255, 77, 77, 0.1); border: 1px solid #ff4d4d; color: #ff4d4d; padding: 12px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; font-size: 14px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; text-align: left;">
                <div style="grid-column: span 2; margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 5px; margin-left: 5px;"><?php echo __('full_name'); ?></label>
                    <input type="text" name="full_name" style="width: 100%; padding: 12px 15px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.05); color: var(--text-main);" placeholder="John Doe" required>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 5px; margin-left: 5px;"><?php echo __('email'); ?></label>
                    <input type="email" name="email" style="width: 100%; padding: 12px 15px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.05); color: var(--text-main);" placeholder="mail@example.com" required>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 5px; margin-left: 5px;"><?php echo __('confirm_email'); ?></label>
                    <input type="email" name="confirm_email" style="width: 100%; padding: 12px 15px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.05); color: var(--text-main);" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 5px; margin-left: 5px;"><?php echo __('password'); ?></label>
                    <input type="password" name="password" style="width: 100%; padding: 12px 15px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.05); color: var(--text-main);" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 5px; margin-left: 5px;"><?php echo __('confirm_password'); ?></label>
                    <input type="password" name="confirm_password" style="width: 100%; padding: 12px 15px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.05); color: var(--text-main);" required>
                </div>

                <div style="grid-column: span 2; margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 5px; margin-left: 5px;"><?php echo __('phone'); ?></label>
                    <input type="text" name="mobile_phone" style="width: 100%; padding: 12px 15px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.05); color: var(--text-main);" placeholder="07XXXXXXXX" required>
                </div>

                <div style="grid-column: span 2; border-top: 1px solid var(--glass-border); padding-top: 15px; margin-top: 10px; margin-bottom: 15px;">
                    <h4 style="margin-bottom: 15px; color: var(--accent-color);"><i class="fas fa-car"></i> Car Details</h4>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 5px; margin-left: 5px;"><?php echo __('car_plate'); ?></label>
                    <input type="text" name="car_plate" style="width: 100%; padding: 12px 15px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.05); color: var(--text-main);" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 5px; margin-left: 5px;"><?php echo __('car_model'); ?></label>
                    <input type="text" name="car_model" style="width: 100%; padding: 12px 15px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.05); color: var(--text-main);" placeholder="Toyota Camry" required>
                </div>
            </div>

            <button type="submit" class="mbtn mbtn-enter" style="border: none; margin-top: 20px;"><?php echo __('signup'); ?></button>
        </form>
        <div style="text-align: center; margin-top: 25px; font-size: 14px; color: var(--text-muted);">
            Already have an account? <a href="login.php" style="color: var(--accent-color); font-weight: 700; text-decoration: none;">Login</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>

