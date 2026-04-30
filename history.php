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

$history_query = "SELECT id, plate_number, {$history_slot_column} AS slot_id, entry_time, exit_time, {$history_amount_sql} FROM parking_history $where ORDER BY entry_time DESC LIMIT 20";
$history_result = $conn->query($history_query);
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Log - Smart Parking</title>
    <style>
        .log-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .log-header h2 {
            font-size: 24px;
            margin: 0;
            font-family: 'Inter', sans-serif;
            font-weight: bold;
        }

        .search-container {
            background: white;
            padding: 20px;
            border-radius: var(--radius-lg);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            text-align: center;
        }
        .search-container h3 {
            font-size: 18px;
            margin-top: 0;
            margin-bottom: 15px;
            font-family: 'Inter', sans-serif;
        }
        .search-form {
            display: flex;
            gap: 10px;
        }
        .search-input {
            flex-grow: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        .search-btn {
            background-color: var(--primary-blue);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .log-table-container {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: <?php echo ($dir == 'rtl') ? 'right' : 'left'; ?>;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #f8f9fa;
            color: var(--text-dark);
            font-weight: bold;
            font-size: 14px;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-completed { background: #e8f5e9; color: #2e7d32; }
        .status-active { background: #e3f2fd; color: #1565c0; }
        
        .action-link {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="figma-container">
    <div class="log-header">
        <h2 class="serif-font"><?php echo ($current_lang == 'ar' ? 'سجل المركبات' : 'Vehicle Log'); ?></h2>
        <i class="fa-solid fa-list-check"></i>
    </div>

    <!-- Search Section (Matches Image 3) -->
    <div class="search-container">
        <h3><?php echo ($current_lang == 'ar' ? 'البحث عن مركبة' : 'Search for Vehicle'); ?></h3>
        <form class="search-form" method="GET">
            <input type="text" name="search" class="search-input" placeholder="<?php echo ($current_lang == 'ar' ? 'ابحث باللوحة...' : 'Search by plate...'); ?>" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="search-btn"><?php echo ($current_lang == 'ar' ? 'بحث' : 'Search'); ?></button>
        </form>
    </div>

    <!-- Log Table (Matches Image 1) -->
    <div class="log-table-container">
        <table>
            <thead>
                <tr>
                    <th><?php echo ($current_lang == 'ar' ? 'اللوحة' : 'Plate'); ?></th>
                    <th><?php echo ($current_lang == 'ar' ? 'الحالة' : 'Status'); ?></th>
                    <th><?php echo ($current_lang == 'ar' ? 'الفاتورة' : 'Invoice'); ?></th>
                    <th><?php echo ($current_lang == 'ar' ? 'إجراء' : 'Action'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($history_result && $history_result->num_rows > 0): ?>
                    <?php while($row = $history_result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['plate_number']); ?></strong></td>
                            <td>
                                <span class="status-badge <?php echo $row['exit_time'] ? 'status-completed' : 'status-active'; ?>">
                                    <?php echo $row['exit_time'] ? ($current_lang == 'ar' ? 'مكتمل' : 'Completed') : ($current_lang == 'ar' ? 'نشط' : 'Active'); ?>
                                </span>
                            </td>
                            <td><?php echo number_format((float) ($row['cost'] ?? 0), 0); ?> EGP</td>
                            <td>
                                <a href="ticket.php?guid=<?php echo urlencode($row['qr_code_path'] ?? ''); ?>" class="action-link">
                                    <?php echo ($current_lang == 'ar' ? 'عرض' : 'View'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #999; padding: 30px;">
                            <?php echo ($current_lang == 'ar' ? 'لا توجد سجلات' : 'No records found'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
