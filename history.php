<?php
require_once 'header.php';

// Search logic
$search = $_GET['search'] ?? '';
$where = "WHERE 1=1 ";
if ($search) {
    $where .= " AND plate_number LIKE '%" . $conn->real_escape_string($search) . "%'";
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $where .= " AND user_id = " . intval($_SESSION['user_id'] ?? 0);
}

$history_slot_column = parking_history_slot_column($conn);
$history_amount_column = parking_history_amount_column($conn);
$history_amount_sql = $history_amount_column ? "{$history_amount_column} AS cost" : "NULL AS cost";

// Use qr_code_path as parking_guid
$history_query = "SELECT id, plate_number, {$history_slot_column} AS slot_id, entry_time, exit_time, qr_code_path AS parking_guid, {$history_amount_sql} FROM parking_history $where ORDER BY id DESC LIMIT 20";
$history_result = $conn->query($history_query);
?>

<?php include 'navbar.php'; ?>

<div class="wrapper animate-fade-in">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="margin:0; color: var(--text-main);">
            <i class="fas fa-history"></i> <?php echo ($current_lang == 'ar' ? 'سجل المركبات' : 'Vehicle Log'); ?>
        </h2>
    </div>

    <!-- Search Section -->
    <div class="glass-card" style="padding: 20px; margin-bottom: 20px;">
        <h5 style="margin-top:0; color: var(--text-main);"><?php echo ($current_lang == 'ar' ? 'البحث عن مركبة' : 'Search for Vehicle'); ?></h5>
        <form method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="search-input" 
                   style="flex:1; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 12px; padding: 12px; color: var(--text-main);"
                   placeholder="<?php echo ($current_lang == 'ar' ? 'ابحث باللوحة...' : 'Search by plate...'); ?>" 
                   value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="icon-btn" style="width: auto; padding: 0 20px; border-radius: 12px;">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <div class="history-container">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th><?php echo ($current_lang == 'ar' ? 'اللوحة' : 'Plate'); ?></th>
                        <th><?php echo ($current_lang == 'ar' ? 'الموقع' : 'Slot'); ?></th>
                        <th><?php echo ($current_lang == 'ar' ? 'الحالة' : 'Status'); ?></th>
                        <th><?php echo ($current_lang == 'ar' ? 'الفاتورة' : 'Invoice'); ?></th>
                        <th><?php echo ($current_lang == 'ar' ? 'إجراء' : 'Action'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($history_result && $history_result->num_rows > 0): ?>
                        <?php while($row = $history_result->fetch_assoc()): ?>
                            <tr>
                                <td><strong style="color: var(--accent-color);"><?php echo htmlspecialchars($row['plate_number']); ?></strong></td>
                                <td><span class="badge" style="background: var(--glass-border); color: var(--text-main); padding: 4px 10px; border-radius: 8px;"><?php echo htmlspecialchars($row['slot_id']); ?></span></td>
                                <td>
                                    <?php if ($row['exit_time']): ?>
                                        <span style="color:#00b894; font-weight: 700; font-size: 13px;"><i class="fas fa-check-circle"></i> <?php echo ($current_lang == 'ar' ? 'مكتمل' : 'Completed'); ?></span>
                                    <?php else: ?>
                                        <span style="color:#fdcb6e; font-weight: 700; font-size: 13px;"><i class="fas fa-clock"></i> <?php echo ($current_lang == 'ar' ? 'نشط' : 'Active'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight: 700; color: #00b894;"><?php echo number_format((float) ($row['cost'] ?? 0), 2); ?> JOD</td>
                                <td>
                                    <a href="ticket.php?guid=<?php echo urlencode($row['parking_guid'] ?? ''); ?>" class="icon-btn" style="width: auto; padding: 5px 15px; height: auto; font-size: 12px;">
                                        <i class="fas fa-file-invoice"></i> <?php echo ($current_lang == 'ar' ? 'عرض' : 'View'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 40px;"><?php echo ($current_lang == 'ar' ? 'لا توجد سجلات' : 'No records found'); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
