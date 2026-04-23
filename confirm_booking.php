<?php
require_once 'header.php';

$slotId = parking_normalize_slot($_GET['slot'] ?? ($_SESSION['pending_booking']['slot_id'] ?? parking_find_available_slot($conn) ?? 'A1'));
$hourlyRate = parking_get_slot_price($conn, $slotId);
$durationHours = 2;
$totalPrice = $hourlyRate * $durationHours;

parking_ensure_slot($conn, $slotId, 'available');
$slotMap = parking_get_slot_status_map($conn);
$currentStatus = $slotMap[$slotId] ?? 'available';

if ($currentStatus === 'occupied' || $currentStatus === 'reserved') {
    set_error_message("Slot {$slotId} is not available right now.");
    redirect('parking_details.php');
}

$_SESSION['pending_booking'] = [
    'slot_id' => $slotId,
    'parking_name' => 'Cairo Mall Parking',
    'location' => '6th October, Cairo',
    'duration_hours' => $durationHours,
    'hourly_rate' => $hourlyRate,
    'total_price' => $totalPrice,
    'booking_time' => date('h:i A') . ' - ' . date('h:i A', strtotime("+{$durationHours} hours")),
];
$booking = $_SESSION['pending_booking'];
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking - Smart Parking</title>
    <style>
        .confirm-header { display: flex; align-items: center; gap: 10px; margin-top: 20px; margin-bottom: 20px; }
        .confirm-header h2 { font-size: 24px; margin: 0; font-family: 'Inter', sans-serif; font-weight: bold; }
        .summary-card { background: white; border-radius: var(--radius-lg); padding: 5px 0; margin-bottom: 30px; }
        .summary-item { padding: 15px 0; border-bottom: 1px solid #f0f0f0; }
        .summary-item:last-child { border-bottom: none; }
        .summary-label { font-size: 14px; font-weight: bold; color: var(--text-dark); margin-bottom: 8px; display: block; }
        .summary-value { background-color: var(--input-bg); padding: 12px 20px; border-radius: var(--radius-lg); color: #333; font-weight: 500; font-size: 14px; }
        .button-group { display: flex; flex-direction: column; gap: 15px; padding-bottom: 40px; }
        .figma-btn-secondary { background-color: #3f51b5; color: white; border: none; padding: 15px; border-radius: 8px; font-weight: bold; text-decoration: none; text-align: center; font-size: 16px; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="figma-container">
    <div class="confirm-header">
        <h2 class="serif-font">Confirm Booking</h2>
    </div>

    <div class="summary-card">
        <div class="summary-item"><span class="summary-label">Parking Name:</span><div class="summary-value"><?php echo htmlspecialchars($booking['parking_name']); ?></div></div>
        <div class="summary-item"><span class="summary-label">Location:</span><div class="summary-value"><?php echo htmlspecialchars($booking['location']); ?></div></div>
        <div class="summary-item"><span class="summary-label">Slot:</span><div class="summary-value"><?php echo htmlspecialchars($booking['slot_id']); ?></div></div>
        <div class="summary-item"><span class="summary-label">Booking Time:</span><div class="summary-value"><?php echo htmlspecialchars($booking['booking_time']); ?></div></div>
        <div class="summary-item"><span class="summary-label">Duration:</span><div class="summary-value"><?php echo (int) $booking['duration_hours']; ?> hours</div></div>
        <div class="summary-item"><span class="summary-label">Total Price:</span><div class="summary-value"><?php echo number_format($booking['total_price'], 2); ?> EGP</div></div>
    </div>

    <div class="button-group">
        <a href="payment_method.php" class="figma-btn">Continue to Payment</a>
        <a href="parking_details.php" class="figma-btn-secondary">Back</a>
    </div>
</div>

</body>
</html>
