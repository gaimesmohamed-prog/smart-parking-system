<?php
require_once 'header.php';

$summary = parking_get_report_summary($conn);
$historyRows = parking_get_recent_history($conn, 15);
$counts = parking_get_counts($conn);
$totalSlots = max(1, $counts['total_slots']);
$occupiedCount = $counts['occupied_slots'] + $counts['reserved_slots'];
$occupancyRate = round(($occupiedCount / $totalSlots) * 100);

$peakData = array_fill(0, 24, 0);
$peakRes = $conn->query("SELECT HOUR(entry_time) AS hr, COUNT(*) AS count FROM parking_history WHERE entry_time >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY hr ORDER BY hr ASC");
while ($peakRes && $row = $peakRes->fetch_assoc()) {
    $peakData[(int) $row['hr']] = (int) $row['count'];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; background: #F8F9FD; color: #2D3436; margin: 0; padding: 0; }
        .header-bg { background: linear-gradient(135deg, #5D5FEF 0%, #a29bfe 100%); height: 250px; width: 100%; position: absolute; top: 0; left: 0; z-index: -1; border-radius: 0 0 50px 50px; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .nav-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; color: white; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-bottom: 40px; }
        .stat-card, .chart-container, .table-card { background: rgba(255, 255, 255, 0.9); padding: 25px; border-radius: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); }
        .stat-value { font-size: 32px; font-weight: 700; margin: 5px 0; }
        .charts-row { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; margin-bottom: 40px; }
        table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        th { text-align: right; padding: 15px; color: #7f8c8d; font-weight: 600; font-size: 14px; }
        td { padding: 20px 15px; background: #fff; vertical-align: middle; }
        .plate-box { background: #f1f2f6; padding: 6px 12px; border-radius: 8px; font-weight: 700; border: 1px solid #dfe6e9; font-family: monospace; }
        .cost-tag { color: #00D2B1; font-weight: 700; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="header-bg"></div>

<div class="container">
    <nav class="nav-bar">
        <h1>لوحة التحليلات والتقارير</h1>
        <div>
            <button onclick="window.print()" style="padding:10px 20px; border:none; border-radius:12px; cursor:pointer;">تصدير PDF</button>
            <a href="app_dashboard.php" style="padding:10px 20px; border-radius:12px; text-decoration:none; background:#fff; color:#5D5FEF; margin-right:10px;">رجوع</a>
        </div>
    </nav>

    <div class="stats-grid">
        <div class="stat-card"><div class="stat-value"><?php echo number_format($summary['total_revenue'], 2); ?> EGP</div><div>إجمالي الإيرادات</div></div>
        <div class="stat-card"><div class="stat-value"><?php echo $occupancyRate; ?>%</div><div>نسبة الإشغال الحالية</div></div>
        <div class="stat-card"><div class="stat-value"><?php echo $summary['total_sessions']; ?></div><div>إجمالي العمليات</div></div>
    </div>

    <div class="charts-row">
        <div class="chart-container"><canvas id="peakHoursChart" height="150"></canvas></div>
        <div class="chart-container"><canvas id="occupancyChart"></canvas></div>
    </div>

    <div class="table-card">
        <h3>سجل النشاط الأخير</h3>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr><th>رقم اللوحة</th><th>رقم الركنة</th><th>وقت الدخول</th><th>وقت الخروج</th><th>التكلفة</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($historyRows as $row): ?>
                        <tr>
                            <td><span class="plate-box"><?php echo htmlspecialchars($row['plate_number']); ?></span></td>
                            <td><b><?php echo htmlspecialchars($row['slot_id']); ?></b></td>
                            <td><?php echo date('Y/m/d h:i A', strtotime($row['entry_time'])); ?></td>
                            <td><?php echo $row['exit_time'] ? date('h:i A', strtotime($row['exit_time'])) : 'قيد الانتظار'; ?></td>
                            <td><span class="cost-tag"><?php echo number_format((float) ($row['cost'] ?? 0), 2); ?> EGP</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const peakCtx = document.getElementById('peakHoursChart').getContext('2d');
new Chart(peakCtx, {
    type: 'bar',
    data: {
        labels: Array.from({length: 24}, (_, i) => i + ':00'),
        datasets: [{ data: <?php echo json_encode(array_values($peakData)); ?>, backgroundColor: 'rgba(93, 95, 239, 0.7)', borderColor: 'rgba(93, 95, 239, 1)', borderWidth: 1, borderRadius: 5 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: {} } }
});

const occCtx = document.getElementById('occupancyChart').getContext('2d');
new Chart(occCtx, {
    type: 'doughnut',
    data: {
        labels: ['مشغول', 'متاح'],
        datasets: [{ data: [<?php echo $occupiedCount; ?>, <?php echo max(0, $totalSlots - $occupiedCount); ?>], backgroundColor: ['#FF7675', '#00D2B1'], borderWidth: 0 }]
    },
    options: { cutout: '75%', plugins: { legend: { position: 'bottom' } } }
});
</script>

</body>
</html>
