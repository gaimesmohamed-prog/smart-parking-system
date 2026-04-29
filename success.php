<?php
require_once 'header.php';
require_once __DIR__ . '/config.php';

// Fetch the real booking ID from URL
$booking_id = isset($_GET['id']) ? "#" . str_pad($_GET['id'], 5, "0", STR_PAD_LEFT) : "#00000";
?>
<?php include 'navbar.php'; ?>

<div class="wrapper animate-fade-in" style="min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    <div class="glass-card" style="padding: 50px; text-align: center; max-width: 500px; width: 90%;">
        <div style="width: 100px; height: 100px; background-color: #00b894; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 50px; margin: 0 auto 30px; box-shadow: 0 10px 20px rgba(0, 184, 148, 0.2);">
            <i class="fa-solid fa-check"></i>
        </div>

        <h1 style="font-size: 28px; font-weight: bold; margin-bottom: 15px; color: var(--text-main);">Booking Successful!</h1>
        
        <p style="color: var(--text-muted); font-size: 16px; margin-bottom: 40px; line-height: 1.6;">Your parking spot has been reserved successfully.</p>

        <div style="background: rgba(0,0,0,0.05); border: 1px solid var(--glass-border); padding: 15px 25px; border-radius: 12px; font-weight: bold; font-size: 18px; margin-bottom: 40px; color: var(--text-main);">
            Booking ID: <span style="color: var(--accent-color);"><?php echo $booking_id; ?></span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 15px; width: 100%;">
            <a href="dashboard.php" class="mbtn mbtn-enter" style="text-decoration: none;">Go to Dashboard</a>
            <a href="history.php" class="mbtn mbtn-close" style="text-decoration: none;">View History</a>
        </div>
    </div>
</div>

</body>
</html>
