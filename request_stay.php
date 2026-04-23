<?php require_once 'header.php'; ?>
<?php
$slot_id = $_GET['slot'] ?? 'B3'; // استلام رقم الركنة
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Long Stay</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #5D5FEF; margin: 0; display: flex; align-items: flex-end; height: 100vh; }
        .request-container { 
            background: white; width: 100%; height: 85%; border-radius: 50px 50px 0 0; 
            padding: 40px 20px; box-sizing: border-box; text-align: center; 
        }
        h1 { color: white; position: absolute; top: 50px; left: 0; right: 0; font-size: 32px; }
        
        .input-group {
            border: 2px solid #000; margin-bottom: 30px; padding: 15px;
            text-align: left; border-radius: 5px; font-size: 24px;
        }
        .label { color: #888; }
        .value { font-weight: bold; }

        .send-btn {
            background-color: #5D5FEF; color: white; width: 100%; padding: 20px;
            border: none; border-radius: 10px; font-size: 24px; font-weight: bold;
            cursor: pointer; margin-top: 50px;
        }
    </style>

    <!-- Global Nav Styles -->
    
</head>
<body>
<?php include 'navbar.php'; ?>


<h1>Request Long Stay</h1>

<div class="request-container">
    <div class="input-group">
        <span class="label">Slot number:</span>
        <span class="value"><?php echo $slot_id; ?></span>
    </div>

    <div class="input-group">
        <span class="label">Enter Duration:</span>
        <span class="value">2 Days</span> </div>

    <button class="send-btn" onclick="alert('Request Sent to Admin!')">Send Request to Admin</button>
</div>

</body>
</html>