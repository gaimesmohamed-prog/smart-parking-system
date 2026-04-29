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
