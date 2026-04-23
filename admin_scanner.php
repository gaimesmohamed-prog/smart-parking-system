<?php require_once 'header.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Smart Scanner | قارئ البوابة الذكي</title>
    
    <!-- html5-qrcode library -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Noto+Kufi+Arabic:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #5D5FEF;
            --secondary: #6C63FF;
            --dark: #2D3436;
            --success: #00B894;
            --danger: #D63031;
            --warning: #FDCB6E;
        }

        body {
            font-family: 'Outfit', 'Noto Kufi Arabic', sans-serif;
            background-color: #f1f2f6;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            color: var(--dark);
        }

        .header {
            background: var(--primary);
            color: white;
            width: 100%;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(93, 95, 239, 0.3);
            margin-bottom: 30px;
        }

        .header h1 { margin: 0; font-size: 24px; }

        .main-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 90%;
            max-width: 600px;
            gap: 20px;
        }

        /* Camera Box */
        .scanner-card {
            background: white;
            padding: 20px;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            box-sizing: border-box;
            text-align: center;
        }

        #reader {
            width: 100%;
            border-radius: 15px;
            overflow: hidden;
            border: 4px solid #eee;
        }

        /* Message Overlay */
        .feedback-overlay {
            width: 100%;
            padding: 20px;
            border-radius: 20px;
            margin-top: 20px;
            display: none;
            animation: slideIn 0.3s ease-out;
            text-align: center;
            font-size: 20px;
            font-weight: 700;
            box-sizing: border-box;
        }

        .alert-success { background: #dff9fb; color: var(--success); border: 2px solid var(--success); }
        .alert-error { background: #ffeaa7; color: var(--danger); border: 2px solid var(--danger); }
        .alert-info { background: #e1f5fe; color: #0277bd; border: 2px solid #0277bd; }

        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 50px;
            background: #eee;
            font-size: 14px;
            margin-top: 10px;
            font-weight: 600;
        }

        .controls {
            margin-top: 20px;
            width: 100%;
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-outline { background: #f1f2f6; border: 2px solid #ddd; }

        /* Audio Beep Simulation (No file needed, we can use Web Audio API) */
    </style>

    <!-- Global Nav Styles -->
    
</head>
<body>
<?php include 'navbar.php'; ?>


    <div class="header">
        <h1>📊 لوحة متابعة البوابة (المدير)</h1>
    </div>

    <div class="main-container">
        
        <div class="scanner-card">
            <h3>📷 المسح التلقائي للكاميرا</h3>
            <p style="color: #666; font-size: 14px;">وجه تذكرة الـ QR نحو الكاميرا لتسجيل الدخول أو الخروج تلقائياً</p>
            
            <div id="reader"></div>
            
            <div id="status-display" class="status-badge">الكاميرا جاهزة...</div>
        </div>

        <!-- Feedback Message Area -->
        <div id="feedback" class="feedback-overlay"></div>

        <div class="controls">
            <a href="app_dashboard.php" class="btn btn-outline" style="text-align:center; text-decoration: none;">رجوع للوحة التحكم</a>
            <button class="btn btn-primary" onclick="location.reload()">إعادة تشغيل الكاميرا</button>
        </div>

    </div>

    <script>
        let lastScannedData = "";
        let isProcessing = false;

        // Beep Sound using Web Audio API
        function playBeep(type = 'success') {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);

            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(type === 'success' ? 880 : 220, audioCtx.currentTime); // A5 for success, A3 for error
            
            gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);

            oscillator.start();
            oscillator.stop(audioCtx.currentTime + 0.3);
        }

        function onScanSuccess(decodedText, decodedResult) {
            // Prevent duplicate scans within 3 seconds
            if (isProcessing || decodedText === lastScannedData) return;

            isProcessing = true;
            lastScannedData = decodedText;
            
            document.getElementById('status-display').innerText = "تم الالتقاط! جارِ المعالجة...";
            playBeep('success');

            // AJAX call to process_scan.php
            const formData = new FormData();
            formData.append('qr_data', decodedText);

            fetch('process_scan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const feedbackDiv = document.getElementById('feedback');
                feedbackDiv.style.display = 'block';
                
                if (data.status === 'success') {
                    feedbackDiv.className = 'feedback-overlay alert-success';
                    feedbackDiv.innerHTML = `<h3>${data.type === 'entry' ? '🟢 ترحيب' : '🔴 وداعاً'}</h3><p>${data.message}</p>`;
                } else {
                    feedbackDiv.className = 'feedback-overlay alert-error';
                    feedbackDiv.innerHTML = `<h3>❌ خطأ</h3><p>${data.message}</p>`;
                    playBeep('error');
                }

                document.getElementById('status-display').innerText = "في انتظار عملية جديدة...";
                
                // Allow scanning again after 4 seconds
                setTimeout(() => {
                    isProcessing = false;
                    lastScannedData = "";
                    feedbackDiv.style.display = 'none';
                }, 4000);
            })
            .catch(error => {
                console.error('Error:', error);
                isProcessing = false;
                lastScannedData = "";
            });
        }

        function onScanFailure(error) {
            // Ignore failure during active scan
        }

        const html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", 
            { fps: 10, qrbox: {width: 250, height: 250} },
            /* verbose= */ false
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);

    </script>
</body>
</html>
