<?php
require_once 'header.php';

$user_id = (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') ? (int)($_SESSION['user_id'] ?? 0) : 0;

$summary = parking_get_report_summary($conn, $user_id);
$reportsResult = parking_get_recent_history($conn, 20, $user_id);
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Smart Parking</title>
    <style>
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; margin-bottom: 25px; }
        .page-title { font-size: 24px; font-weight: bold; margin: 0; font-family: 'Inter', sans-serif; }
        .report-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .report-card { background: white; padding: 20px 10px; border-radius: var(--radius-lg); box-shadow: 0 4px 12px rgba(0,0,0,0.03); text-align: center; border: 1px solid #f0f0f0; }
        .report-card .val { font-size: 20px; font-weight: 800; display: block; color: var(--text-dark); margin: 5px 0; }
        .report-card .lbl { font-size: 11px; color: var(--text-gray); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .section-title { font-size: 18px; font-weight: bold; margin-bottom: 15px; font-family: 'Playfair Display', serif; }
        .table-card { background: white; border-radius: var(--radius-lg); box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 40px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; border-bottom: 1px solid #f5f5f5; font-size: 13px; }
        th { background-color: #fafafa; color: var(--text-gray); font-weight: 700; text-transform: uppercase; font-size: 11px; }
        .plate-cell { font-weight: 800; color: var(--text-dark); }
        .status-badge { font-size: 11px; padding: 4px 10px; border-radius: 12px; font-weight: bold; }
        .status-active { background: #e3f2fd; color: #1976d2; }
        .status-done { background: #f5f5f5; color: #757575; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="figma-container">
    <div class="page-header">
        <h1 class="page-title">Analytics</h1>
        <i class="fa-solid fa-chart-pie" style="font-size: 22px; color: var(--primary-blue);"></i>
    </div>

    <div class="report-grid">
        <div class="report-card"><span class="val"><?php echo $summary['cars_today']; ?></span><span class="lbl">Today's Traffic</span></div>
        <div class="report-card"><span class="val"><?php echo number_format($summary['total_revenue'], 0); ?></span><span class="lbl">Total EGP</span></div>
        <div class="report-card"><span class="val"><?php echo htmlspecialchars($summary['most_used_slot']); ?></span><span class="lbl">Hot Spot</span></div>
    </div>

    <div class="section-title">Parking Activity Log</div>
    <div class="table-card">
        <table>
            <thead>
                <tr><th>Vehicle</th><th>Slot</th><th>Time In</th><th>Cost</th></tr>
            </thead>
            <tbody>
                <?php if ($reportsResult): ?>
                    <?php foreach ($reportsResult as $row): ?>
                        <tr>
                            <td class="plate-cell"><?php echo htmlspecialchars($row['plate_number']); ?></td>
                            <td><span class="status-badge <?php echo $row['exit_time'] ? 'status-done' : 'status-active'; ?>"><?php echo htmlspecialchars($row['slot_id']); ?></span></td>
                            <td><?php echo date('H:i', strtotime($row['entry_time'])); ?></td>
                            <td style="font-weight:700;"><?php echo number_format((float) ($row['cost'] ?? 0), 0); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center; color:#999; padding:20px;">No records yet</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="text-align: center; padding-bottom: 40px;">
        <button onclick="window.print()" class="figma-btn-secondary" style="width: auto; padding: 12px 30px; border-radius: 20px; font-size: 14px;">Export Report</button>
    </div>
</div>

</body>
</html>
