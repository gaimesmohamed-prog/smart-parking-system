<?php
require_once 'header.php';

$booking = $_SESSION['pending_booking'] ?? null;
if (!$booking) {
    redirect('parking_details.php');
}

$user_id = $_SESSION['user_id'] ?? 0;
$stmt = $conn->prepare("SELECT balance FROM users WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user_row = $res->fetch_assoc();
$balance = $user_row['balance'] ?? 0.00;
$stmt->close();

$error = '';

if (isset($_POST['confirm_payment'])) {
    if ($balance < $booking['total_price']) {
        $error = 'رصيدك غير كافٍ. يرجى شحن المحفظة أولاً.';
    } else {
        $plateNumber = trim($_POST['plate_number'] ?? '');
        $carType = $_POST['car_type'] ?? 'sedan';

        $reservation = parking_create_reservation($conn, [
            'plate_number' => $plateNumber,
            'slot_id' => $booking['slot_id'],
            'car_type' => $carType,
            'user_id' => $user_id,
            'deduct_balance' => $booking['total_price'],
        ]);

        if ($reservation['success']) {
            $_SESSION['last_booking'] = [
                'guid' => $reservation['guid'],
                'slot_id' => $reservation['slot_id'],
                'plate_number' => $reservation['plate_number'],
            ];
            unset($_SESSION['pending_booking']);
            header('Location: ticket.php?guid=' . urlencode($reservation['guid']));
            exit;
        }

        $error = $reservation['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Method - Smart Parking</title>
    <style>
        .payment-header { display: flex; align-items: center; justify-content: space-between; margin-top: 20px; margin-bottom: 20px; }
        .payment-header h2 { font-size: 24px; margin: 0; font-family: 'Inter', sans-serif; font-weight: bold; }
        .details-list { border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0; margin-bottom: 20px; }
        .detail-item { padding: 15px 0; border-bottom: 1px solid #f0f0f0; }
        .detail-item:last-child { border-bottom: none; }
        .detail-label { font-size: 14px; font-weight: bold; color: var(--text-dark); margin-bottom: 8px; display: block; }
        .detail-value { background-color: var(--input-bg); padding: 12px 20px; border-radius: var(--radius-lg); color: #333; font-weight: 500; font-size: 14px; text-align: center; }
        .payment-selection { margin-bottom: 30px; }
        .payment-option { display: flex; align-items: center; justify-content: space-between; background: #f9f9f9; padding: 12px 20px; border-radius: 12px; margin-bottom: 10px; font-weight: bold; color: #555; }
        .button-group { display: flex; flex-direction: column; gap: 15px; padding-bottom: 40px; }
        .figma-btn-secondary { background-color: #3f51b5; color: white; border: none; padding: 15px; border-radius: 8px; font-weight: bold; text-decoration: none; text-align: center; font-size: 16px; }
        .error-box { color: #c62828; margin-bottom: 15px; font-weight: 600; }
        input, select { width: 100%; padding: 12px; margin-top: 8px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="figma-container">
    <div class="payment-header">
        <h2 class="serif-font">Payment Method</h2>
        <i class="fa-solid fa-credit-card"></i>
    </div>

    <?php if ($error !== ''): ?>
        <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="details-list">
        <div class="detail-item"><span class="detail-label">Parking Name:</span><div class="detail-value"><?php echo htmlspecialchars($booking['parking_name']); ?></div></div>
        <div class="detail-item"><span class="detail-label">Date:</span><div class="detail-value"><?php echo date('d/m/Y'); ?></div></div>
        <div class="detail-item"><span class="detail-label">Time:</span><div class="detail-value"><?php echo htmlspecialchars($booking['booking_time']); ?></div></div>
        <div class="detail-item"><span class="detail-label">Slot:</span><div class="detail-value"><?php echo htmlspecialchars($booking['slot_id']); ?></div></div>
        <div class="detail-item"><span class="detail-label">Total Price:</span><div class="detail-value"><?php echo number_format($booking['total_price'], 2); ?> EGP</div></div>
    </div>

    <form method="POST" class="button-group">
        <div class="payment-selection">
            <div style="font-size: 14px; font-weight: bold; margin-bottom: 15px;">Booking Details</div>
            <label class="detail-label">Plate Number
                <input type="text" name="plate_number" value="<?php echo htmlspecialchars($_POST['plate_number'] ?? 'ABC-' . rand(100, 999)); ?>" required>
            </label>
            <label class="detail-label">Vehicle Type
                <select name="car_type">
                    <option value="sedan">Sedan</option>
                    <option value="truck">Truck</option>
                </select>
            </label>
            <div class="payment-option">
                <span>Wallet Balance</span>
                <span style="color: <?php echo ($balance >= $booking['total_price']) ? '#27ae60' : '#e74c3c'; ?>;">
                    <?php echo number_format($balance, 2); ?> EGP <i class="fa-solid fa-wallet"></i>
                </span>
            </div>
        </div>

        <?php if ($balance < $booking['total_price']): ?>
            <a href="wallet.php" class="figma-btn" style="background-color: #e74c3c; text-decoration: none; text-align: center;"><i class="fa-solid fa-plus-circle"></i> Recharge Wallet</a>
        <?php else: ?>
            <button type="submit" name="confirm_payment" class="figma-btn">Confirm Payment</button>
        <?php endif; ?>
        <a href="confirm_booking.php" class="figma-btn-secondary">Back</a>
    </form>
</div>

</body>
</html>
