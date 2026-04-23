<?php
require_once 'header.php';
require_once __DIR__ . '/config.php';

if (isset($_POST['qr_data'])) {
    $qr_data = $conn->real_escape_string($_POST['qr_data']);

    $car_query = $conn->query("SELECT * FROM cars WHERE parking_guid = '$qr_data'");
    
    if ($car_query->num_rows > 0) {
        $car = $car_query->fetch_assoc();
        $slot = $car['slot_id'];
        $plate = $car['plate_number'];

        $conn->query("UPDATE cars SET status = 'occupied' WHERE parking_guid = '$qr_data'");
        $conn->query("UPDATE parking_slots SET status = 'occupied' WHERE slot_label = '$slot'");
        $conn->query("UPDATE parking_history SET entry_time = NOW() WHERE plate_number = '$plate' AND slot_id = '$slot' ORDER BY id DESC LIMIT 1");

        echo json_encode(['status' => 'success', 'message' => "Success! Car ($plate) checked into slot ($slot)."]);
    } else {
        echo json_encode(['status' => 'error', 'message' => "Invalid or expired ticket!"]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Scanner - Smart Parking</title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        .scanner-card {
            background: white;
            padding: 30px;
            border-radius: var(--radius-lg);
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        #reader { 
            width: 100%; 
            border-radius: var(--radius-lg); 
            overflow: hidden; 
            margin-bottom: 20px; 
            border: 2px solid var(--primary-blue); 
        }
        .result-box { 
            margin-top: 20px; 
            padding: 15px; 
            border-radius: var(--radius-lg); 
            display: none; 
            font-weight: bold; 
        }
        .success { background: #e8f5e9; color: #2e7d32; }
        .error { background: #ffebee; color: #c62828; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="figma-container">
    <br><br>
    <div class="scanner-card">
        <h1 class="serif-font" style="margin-bottom: 10px;">Check In</h1>
        <p style="color: var(--text-gray); margin-bottom: 30px;">Scan the QR Code on your ticket to open the gate.</p>
        
        <div id="reader"></div>
        <div id="result" class="result-box"></div>
        
        <br>
        <a href="app_dashboard.php" class="figma-link">Back to Dashboard</a>
    </div>
</div>

<script>
    function onScanSuccess(decodedText, decodedResult) {
        html5QrcodeScanner.clear();
        fetch('scanner.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'qr_data=' + encodeURIComponent(decodedText)
        })
        .then(response => response.json())
        .then(data => {
            let resultBox = document.getElementById('result');
            resultBox.style.display = 'block';
            resultBox.className = 'result-box ' + (data.status === 'success' ? 'success' : 'error');
            resultBox.innerText = data.message;
            if (data.status === 'success') {
                setTimeout(() => { window.location.href = 'app_dashboard.php'; }, 2000);
            }
        });
    }

    const html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess);
</script>

</body>
</html>
