<?php
require_once 'header.php';
require_once __DIR__ . '/config.php';

// Fetch the real booking ID from URL
$booking_id = isset($_GET['id']) ? "#" . str_pad($_GET['id'], 5, "0", STR_PAD_LEFT) : "#00000";
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Successful - Smart Parking</title>
    <style>
        .success-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 80vh;
            padding-bottom: 50px;
        }

        .check-icon {
            width: 100px;
            height: 100px;
            background-color: #4CAF50;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(76, 175, 80, 0.2);
        }

        .success-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 15px;
            font-family: 'Playfair Display', serif;
        }

        .success-text {
            color: var(--text-gray);
            font-size: 14px;
            margin-bottom: 40px;
            max-width: 250px;
            line-height: 1.6;
        }

        .booking-id-box {
            background-color: #f8f9fa;
            border: 1px solid #eee;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 40px;
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
            width: 100%;
            max-width: 300px;
        }
        .figma-btn-outline {
            background-color: transparent;
            color: var(--primary-blue);
            border: 2px solid var(--primary-blue);
            padding: 15px;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
            font-size: 16px;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="figma-container">
    <div class="success-container">
        <div class="check-icon">
            <i class="fa-solid fa-check"></i>
        </div>

        <div class="success-title">Booking Successful!</div>
        
        <p class="success-text">Your parking spot has been reserved successfully.</p>

        <div class="booking-id-box">
            Booking ID: <?php echo $booking_id; ?>
        </div>

        <div class="button-group">
            <a href="app_dashboard.php" class="figma-btn">Go Home</a>
            <a href="reports.php" class="figma-btn-outline">View Booking</a>
        </div>
    </div>
</div>

</body>
</html>
