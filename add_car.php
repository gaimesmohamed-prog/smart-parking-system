<?php
require_once 'header.php';

$dashboard_link = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'app_dashboard.php' : 'dashboard.php';

$simulated_plate = 'ABC-' . rand(100, 999);
$message = '';
$msgClass = '';

if (isset($_POST['confirm_booking'])) {
    $reservation = parking_create_reservation($conn, [
        'plate_number' => $_POST['plate_number'] ?? '',
        'slot_id' => $_POST['slot_id'] ?? '',
        'car_type' => $_POST['car_type'] ?? 'sedan',
        'user_id' => $_SESSION['user_id'] ?? 0,
    ]);

    if ($reservation['success']) {
        header('Location: ticket.php?guid=' . urlencode($reservation['guid']));
        exit;
    }

    $message = $reservation['message'];
    $msgClass = 'error';
}
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <title>Vehicle Entry - Smart Parking</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 420px; text-align: center; }
        .header-icon { font-size: 50px; color: #1a73e8; margin-bottom: 15px; }
        h2 { margin-bottom: 25px; color: #333; }
        label { display: block; text-align: left; margin-bottom: 5px; font-weight: bold; color: #555; }
        input, select { width: 100%; padding: 12px; margin-bottom: 20px; border: 2px solid #eee; border-radius: 10px; font-size: 16px; box-sizing: border-box; }
        button { width: 100%; padding: 15px; background: #1a73e8; color: white; border: none; border-radius: 10px; font-size: 18px; font-weight: bold; cursor: pointer; }
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: bold; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .back-link { margin-top: 20px; display: block; color: #666; text-decoration: none; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="card">
    <div class="header-icon"><i class="fas fa-camera-retro"></i></div>
    <h2>Register Vehicle Entry</h2>

    <?php if ($message !== ''): ?>
        <div class="alert <?php echo $msgClass; ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Plate Number:</label>
        <input type="text" name="plate_number" value="<?php echo htmlspecialchars($_POST['plate_number'] ?? $simulated_plate); ?>" required>

        <label>Vehicle Type:</label>
        <select name="car_type">
            <option value="sedan">Sedan</option>
            <option value="truck">Truck</option>
        </select>

        <label>Slot ID:</label>
        <input type="text" name="slot_id" placeholder="A1" value="<?php echo htmlspecialchars($_POST['slot_id'] ?? ''); ?>" required>

        <button type="submit" name="confirm_booking">Issue Ticket</button>
    </form>

    <a href="<?php echo $dashboard_link; ?>" class="back-link">Back to Dashboard</a>
</div>

</body>
</html>
