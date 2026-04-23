<?php
require_once 'header.php';

$dashboard_link = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'app_dashboard.php' : 'dashboard.php';

$guid = $_GET['guid'] ?? '';
$ticket = $guid !== '' ? parking_get_ticket_data($conn, $guid) : null;

if (!$ticket) {
    http_response_code(404);
    die('Ticket not found.');
}

$car = $ticket['car'];
$history = $ticket['history'];
$entryTime = $history['entry_time'] ?? date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Ticket</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #5D5FEF; margin: 0; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .ticket-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); width: 350px; text-align: center; }
        h1 { margin: 0; color: #333; font-size: 28px; }
        .slot-display { font-size: 48px; font-weight: bold; color: #5D5FEF; margin: 10px 0; }
        .details { font-size: 18px; color: #555; margin-bottom: 25px; line-height: 1.6; }
        .qr-code { border: 4px solid #f4f4f4; border-radius: 10px; padding: 10px; background: white; margin-bottom: 20px; }
        .btn { display: inline-block; padding: 12px 25px; background: #5D5FEF; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; width: 100%; box-sizing: border-box; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="ticket-card">
    <h1>Parking Ticket</h1>
    <div class="slot-display"><?php echo htmlspecialchars($car['slot_id']); ?></div>

    <div class="details">
        Plate: <b><?php echo htmlspecialchars($car['plate_number']); ?></b><br>
        Status: <b><?php echo htmlspecialchars($car['status']); ?></b><br>
        Price: <b><?php echo number_format((float)($history['cost'] ?? 0), 2); ?> EGP</b><br>
        Date: <b><?php echo date('Y-m-d h:i A', strtotime($entryTime)); ?></b>
    </div>

    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode($car['parking_guid']); ?>" class="qr-code" alt="QR Code">

    <p style="color: #777; font-size: 14px; margin-bottom: 20px;">Scan this QR code at the entry/exit gate.</p>

    <a href="<?php echo $dashboard_link; ?>" class="btn">Back to Dashboard</a>
</div>

</body>
</html>
