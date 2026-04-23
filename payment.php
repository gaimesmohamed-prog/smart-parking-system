<?php
require_once 'header.php';

$guid = $_GET['guid'] ?? '';
$ticket = $guid !== '' ? parking_get_ticket_data($conn, $guid) : null;
$amount = isset($_GET['amount']) ? (float) $_GET['amount'] : 0.0;
$success = false;
$message = '';

if (!$ticket) {
    $message = 'Ticket not found.';
} else {
    $car = $ticket['car'];
    $history = $ticket['history'];
    if ($amount <= 0 && $history && !empty($history['cost'])) {
        $amount = (float) $history['cost'];
    }
}

if (isset($_POST['confirm_payment']) && $ticket) {
    $exit = parking_complete_exit($conn, $guid);
    if ($exit['success']) {
        $success = true;
        $amount = (float) $exit['cost'];
        $message = "Payment completed and slot {$exit['slot_id']} is available again.";
    } else {
        $message = $exit['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f0f0f0; margin: 0; }
        .success-card { background: white; padding: 40px; border-radius: 30px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-top: 10px solid #28a745; max-width: 420px; }
        .btn-pay { background: #28a745; color: white; border: none; padding: 15px 30px; border-radius: 10px; font-size: 18px; cursor: pointer; width: 100%; }
        .message { margin-top: 15px; color: #444; }
    </style>
</head>
<body>
    <div class="success-card">
        <?php if (!$success): ?>
            <h2 style="margin-bottom:20px;">Confirm Payment <?php echo number_format($amount, 2); ?> EGP</h2>
            <?php if ($ticket): ?>
                <p>Vehicle: <strong><?php echo htmlspecialchars($ticket['car']['plate_number']); ?></strong></p>
                <p>Slot: <strong><?php echo htmlspecialchars($ticket['car']['slot_id']); ?></strong></p>
                <form method="POST">
                    <button type="submit" name="confirm_payment" class="btn-pay">Pay Now</button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <h1 style="color:#28a745; font-size: 60px;">&#10003;</h1>
            <h2>Payment Completed</h2>
        <?php endif; ?>

        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    </div>
</body>
</html>
