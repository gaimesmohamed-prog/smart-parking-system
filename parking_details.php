<?php
require_once 'header.php';

$counts = parking_get_counts($conn);
$selectedSlot = $_GET['slot'] ?? parking_find_available_slot($conn) ?? 'A1';
$pricePerHour = parking_default_hourly_rate();
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Details - Smart Parking</title>
    <style>
        .details-header { display: flex; align-items: center; gap: 10px; margin-top: 20px; margin-bottom: 20px; }
        .details-header h2 { font-size: 24px; margin: 0; font-family: 'Inter', sans-serif; font-weight: bold; }
        .details-hero { width: 100%; height: 250px; background: url('https://i.ibb.co/L8N7p7v/map-placeholder.png') center/cover; border-radius: var(--radius-lg); margin-bottom: 20px; border: 1px solid #eee; }
        .parking-title { font-size: 20px; font-weight: bold; margin-bottom: 20px; text-align: center; font-family: 'Playfair Display', serif; }
        .info-segment { margin-bottom: 20px; }
        .info-label { font-size: 14px; font-weight: bold; color: var(--text-dark); margin-bottom: 8px; display: block; }
        .info-value-box { background-color: var(--input-bg); padding: 12px 20px; border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; gap: 10px; color: #333; font-weight: 500; min-height: 45px; }
        .booking-footer { margin-top: 30px; padding-bottom: 40px; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="figma-container">
    <div class="details-header">
        <h2 class="serif-font">Parking Details</h2>
    </div>

    <div class="details-hero"></div>
    <div class="parking-title">Cairo Mall Parking</div>

    <div class="info-segment">
        <span class="info-label">Parking Name:</span>
        <div class="info-value-box">Cairo Mall Parking</div>
    </div>

    <div class="info-segment">
        <span class="info-label">Location:</span>
        <div class="info-value-box">6th October, Cairo</div>
    </div>

    <div class="info-segment">
        <span class="info-label">Available Spots:</span>
        <div class="info-value-box"><?php echo $counts['available_slots']; ?></div>
    </div>

    <div class="info-segment">
        <span class="info-label">Recommended Slot:</span>
        <div class="info-value-box"><?php echo htmlspecialchars($selectedSlot); ?></div>
    </div>

    <div class="info-segment">
        <span class="info-label">Price per Hour:</span>
        <div class="info-value-box"><?php echo number_format($pricePerHour, 2); ?> EGP/hour</div>
    </div>

    <div class="booking-footer">
        <a href="confirm_booking.php?slot=<?php echo urlencode($selectedSlot); ?>" class="figma-btn" style="text-decoration: none; display: block; text-align: center;">Book Now</a>
    </div>
</div>

</body>
</html>
