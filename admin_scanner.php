<?php require_once 'header.php'; ?>
<!-- html5-qrcode library -->
<script src="https://unpkg.com/html5-qrcode"></script>

<style>
    .scanner-card { background: white; padding: 20px; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 500px; margin: 20px auto; text-align: center; }
    #reader { width: 100%; border-radius: 15px; overflow: hidden; border: 4px solid #eee; }
    .feedback-overlay { width: 100%; padding: 20px; border-radius: 20px; margin-top: 20px; display: none; text-align: center; font-size: 20px; font-weight: 700; }
    .alert-success { background: #dff9fb; color: #00B894; border: 2px solid #00B894; }
    .alert-error { background: #ffeaa7; color: #D63031; border: 2px solid #D63031; }
    .status-badge { display: inline-block; padding: 5px 15px; border-radius: 50px; background: #eee; font-size: 14px; margin-top: 10px; font-weight: 600; }
    .btn-scanner { padding: 12px 25px; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; background: var(--primary); color: white; text-decoration: none; display: inline-block; }
</style>

<?php include 'navbar.php'; ?>

<div class="wrapper animate-fade-in" style="max-width: 600px;">
    <div style="text-align: center; margin-bottom: 30px;">
        <h2 style="color: var(--text-main); font-weight: 800; font-family: 'Righteous', cursive;">
            <i class="fas fa-qrcode" style="color: var(--accent-color);"></i> <?php echo __('qr_entrance'); ?>
        </h2>
        <p style="color: var(--text-muted);">وجه تذكرة الـ QR نحو الكاميرا للمسح التلقائي</p>
    </div>

    <div class="glass-card" style="padding: 25px; text-align: center; overflow: hidden;">
        <div id="reader" style="width: 100%; border-radius: 16px; overflow: hidden; border: 2px solid var(--glass-border); background: #000;"></div>
        
        <div id="status-display" style="display: inline-block; padding: 8px 20px; border-radius: 50px; background: rgba(99, 102, 241, 0.1); color: var(--accent-color); font-size: 13px; margin-top: 20px; font-weight: 700;">
            <i class="fas fa-camera"></i> الكاميرا جاهزة...
        </div>
    </div>

    <div id="feedback" style="margin-top: 25px; display: none;"></div>

    <div style="display: flex; gap: 15px; margin-top: 30px;">
        <a href="app_dashboard.php" class="mbtn mbtn-close" style="text-decoration: none;">رجوع</a>
        <button class="mbtn mbtn-enter" onclick="location.reload()" style="background: var(--accent-color);">إعادة تشغيل</button>
    </div>
</div>

<script>
    let lastScannedData = "";
    let isProcessing = false;

    function playBeep(type = 'success') {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();
        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);
        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(type === 'success' ? 880 : 220, audioCtx.currentTime);
        gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
        oscillator.start();
        oscillator.stop(audioCtx.currentTime + 0.3);
    }

    function onScanSuccess(decodedText) {
        if (isProcessing || decodedText === lastScannedData) return;
        isProcessing = true;
        lastScannedData = decodedText;
        
        document.getElementById('status-display').innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المعالجة...';
        playBeep('success');

        const formData = new FormData();
        formData.append('qr_data', decodedText);

        fetch('process_scan.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            const feedbackDiv = document.getElementById('feedback');
            feedbackDiv.style.display = 'block';
            
            if (data.status === 'success') {
                const isEntry = data.type === 'entry';
                feedbackDiv.innerHTML = `
                    <div class="glass-card" style="padding: 20px; border-left: 8px solid ${isEntry ? '#00b894' : '#ff7675'}; animation: fadeInUp 0.4s forwards;">
                        <h3 style="color: ${isEntry ? '#00b894' : '#ff7675'}; margin-bottom: 10px;">
                            ${isEntry ? '🟢 دخول معتمد' : '🔴 خروج معتمد'}
                        </h3>
                        <p style="margin:0; font-weight: 600; color: var(--text-main);">${data.message}</p>
                        <div style="margin-top: 10px; font-size: 13px; color: var(--text-muted);">
                            <strong>اللوحة:</strong> ${data.plate} | <strong>المكان:</strong> ${data.slot}
                        </div>
                    </div>
                `;
            } else {
                feedbackDiv.innerHTML = `
                    <div class="glass-card" style="padding: 20px; border-left: 8px solid #ff4d4d; animation: fadeInUp 0.4s forwards;">
                        <h3 style="color: #ff4d4d; margin-bottom: 10px;">❌ خطأ في المسح</h3>
                        <p style="margin:0; font-weight: 600; color: var(--text-main);">${data.message}</p>
                    </div>
                `;
                playBeep('error');
            }

            document.getElementById('status-display').innerHTML = '<i class="fas fa-check"></i> جاهز لمسح جديد';
            
            setTimeout(() => {
                isProcessing = false;
                lastScannedData = "";
                feedbackDiv.style.opacity = '0';
                setTimeout(() => {
                    feedbackDiv.style.display = 'none';
                    feedbackDiv.style.opacity = '1';
                }, 400);
            }, 5000);
        })
        .catch(() => {
            isProcessing = false;
            lastScannedData = "";
            document.getElementById('status-display').innerHTML = '<i class="fas fa-exclamation-triangle"></i> خطأ اتصال';
        });
    }

    const html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 15, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess, () => {});
</script>
</body>
</html>
