<?php
require_once 'header.php';
// Use existing $conn from header.php

// 2. كود الحذف
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // جلب الـ slot_id قبل الحذف عشان نحرره
    $res = $conn->query("SELECT slot_id, parking_guid FROM cars WHERE car_id = $id");
    $car = $res->fetch_assoc();
    
    if ($car) {
        $sid = $car['slot_id'];
        $guid = $car['parking_guid'];
        
        // استخدام المنطق الموحد لإكمال الخروج (أو الحذف البسيط إذا فضلنا)
        $conn->query("DELETE FROM cars WHERE car_id = $id");
        parking_update_slot_status($conn, $sid, 'available');
        
        $_SESSION['success_message'] = "تم حذف بيانات السيارة وتحرير المكان $sid بنجاح";
    }
    
    header("Location: view_cars.php");
    exit;
}

// 3. جلب البيانات
$result = $conn->query("SELECT * FROM cars ORDER BY car_id DESC");
?>

<?php include 'navbar.php'; ?>

<div class="wrapper animate-fade-in">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin:0; color: var(--text-main);">
            <i class="fas fa-car"></i> سجل السيارات المحجوزة
        </h2>
    </div>
    
    <div class="history-container">
        <div style="overflow-x: auto;">
            <table class="table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>رقم اللوحة</th>
                        <th>الموديل</th>
                        <th>اللون</th>
                        <th>الموقف (Slot)</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td data-label="ID"><?php echo $row['car_id']; ?></td>
                        <td data-label="رقم اللوحة" style="font-weight: 700; color: var(--accent-color);"><?php echo $row['plate_number']; ?></td>
                        <td data-label="الموديل"><?php echo $row['car_model']; ?></td>
                        <td data-label="اللون"><?php echo $row['car_color']; ?></td>
                        <td data-label="الموقف">
                            <span class="badge" style="background: var(--accent-color); color: white; padding: 4px 10px; border-radius: 8px; font-weight: 700;">
                                <?php echo $row['slot_id']; ?>
                            </span>
                        </td>
                        <td data-label="إجراءات">
                            <a href="view_cars.php?delete=<?php echo $row['car_id']; ?>" 
                               class="icon-btn" 
                               style="color: #ff7675; border: 1px solid rgba(255,118,117,0.3); display: inline-flex; width: auto; padding: 5px 12px; height: auto;"
                               onclick="return confirm('هل أنت متأكد من خروج هذه السيارة؟')">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($result->num_rows == 0): ?>
            <p class="text-center text-muted" style="margin-top: 20px;">لا توجد سيارات مسجلة حالياً.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>