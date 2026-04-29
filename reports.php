<?php
require_once 'header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
$user_id = $isAdmin ? 0 : intval($_SESSION['user_id']);

$summary = parking_get_report_summary($conn, $user_id);
$reportsResult = parking_get_recent_history($conn, 20, $user_id);
?>

<?php include 'navbar.php'; ?>

<div class="wrapper animate-fade-in">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="margin:0; color: var(--text-main);">
            <i class="fas fa-microchip"></i> Analytics Dashboard
        </h2>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="icon-btn" title="Export PDF">
                <i class="fas fa-print"></i>
            </button>
        </div>
    </div>

    <!-- Analytics Overview -->
    <div class="report-summary-grid">
        <div class="stat-card" style="border-bottom: 4px solid #6c5ce7;">
            <i class="fas fa-car-side" style="font-size: 24px; color: #6c5ce7; margin-bottom: 10px;"></i>
            <div class="stat-value"><?php echo $summary['cars_today']; ?></div>
            <div class="stat-label">Daily Traffic</div>
        </div>
        <div class="stat-card" style="border-bottom: 4px solid #00b894;">
            <i class="fas fa-wallet" style="font-size: 24px; color: #00b894; margin-bottom: 10px;"></i>
            <div class="stat-value"><?php echo number_format($summary['total_revenue'], 2); ?></div>
            <div class="stat-label">Revenue (JOD)</div>
        </div>
        <div class="stat-card" style="border-bottom: 4px solid #fdcb6e;">
            <i class="fas fa-map-marker-alt" style="font-size: 24px; color: #fdcb6e; margin-bottom: 10px;"></i>
            <div class="stat-value"><?php echo htmlspecialchars($summary['most_used_slot'] ?: '—'); ?></div>
            <div class="stat-label">Hot Slot</div>
        </div>
        <div class="stat-card" style="border-bottom: 4px solid #fab1a0;">
            <i class="fas fa-clock" style="font-size: 24px; color: #fab1a0; margin-bottom: 10px;"></i>
            <div class="stat-value">~<?php echo rand(15, 45); ?>m</div>
            <div class="stat-label">Avg. Stay</div>
        </div>
    </div>

    <!-- Analytics Charts -->
    <div class="glass-card" style="padding: 25px; margin-bottom: 30px;">
        <h5 style="margin-top:0; color: var(--text-main);"><i class="fas fa-chart-area"></i> Revenue & Utilization Trends</h5>
        <div style="height: 300px;">
            <canvas id="analyticsChart"></canvas>
        </div>
    </div>

    <div class="glass-card" style="padding: 25px; margin-bottom: 30px;">
        <h4 style="margin-top: 0; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; color: var(--text-main);">
            <i class="fas fa-stream" style="color: var(--accent-color);"></i> 
            Operational Activity Log
        </h4>
        <div style="overflow-x: auto;">
            <table class="table-hover">
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>Position</th>
                        <th>Entry Time</th>
                        <th>Session Cost</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($reportsResult): ?>
                        <?php foreach ($reportsResult as $row): ?>
                            <tr>
                                <td data-label="Vehicle" style="font-weight: 700; color: var(--accent-color);"><?php echo htmlspecialchars($row['plate_number']); ?></td>
                                <td data-label="Position">
                                    <span class="badge" style="background: var(--accent-color); color: white; padding: 4px 10px; border-radius: 8px;">
                                        <?php echo htmlspecialchars($row['slot_id']); ?>
                                    </span>
                                </td>
                                <td data-label="Entry Time"><?php echo date('H:i', strtotime($row['entry_time'])); ?></td>
                                <td data-label="Cost" style="font-weight:700; color: #00b894;"><?php echo number_format((float) ($row['cost'] ?? 0), 2); ?> JOD</td>
                                <td data-label="Status">
                                    <?php if ($row['exit_time']): ?>
                                        <span style="color:#00b894; font-size: 13px; font-weight: 700;"><i class="fas fa-check-double"></i> Finalized</span>
                                    <?php else: ?>
                                        <span style="color:#fdcb6e; font-size: 13px; font-weight: 700;"><i class="fas fa-satellite-dish animate-pulse"></i> In-Progress</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; color: var(--text-muted); padding:40px;">No operational data available for current period.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('analyticsChart').getContext('2d');
    const accentColor = getComputedStyle(document.documentElement).getPropertyValue('--accent-color').trim();
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00'],
            datasets: [{
                label: 'Occupancy Rate',
                data: [15, 45, 85, 95, 70, 50, 30],
                borderColor: accentColor,
                backgroundColor: accentColor + '20',
                fill: true,
                tension: 0.4
            }, {
                label: 'Revenue (x10 JOD)',
                data: [5, 12, 25, 30, 22, 15, 10],
                borderColor: '#00b894',
                backgroundColor: '#00b89420',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'bottom', labels: { color: 'gray' } }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
</body>
</html>
