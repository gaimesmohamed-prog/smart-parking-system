<?php
require_once 'header.php';

$message = '';

if (isset($_POST['setup'])) {
    $conn = $GLOBALS['db_connection'];
    
    $tables = [
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(100),
            email VARCHAR(100) UNIQUE,
            password VARCHAR(255),
            role ENUM('user', 'admin') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS parking_slots (
            id INT AUTO_INCREMENT PRIMARY KEY,
            slot_label VARCHAR(10) UNIQUE,
            status ENUM('available', 'occupied', 'reserved') DEFAULT 'available'
        )",
        "CREATE TABLE IF NOT EXISTS cars (
            id INT AUTO_INCREMENT PRIMARY KEY,
            plate_number VARCHAR(20),
            slot_id VARCHAR(10),
            parking_guid VARCHAR(50) UNIQUE,
            status ENUM('reserved', 'occupied') DEFAULT 'reserved',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS parking_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            plate_number VARCHAR(20),
            slot_id VARCHAR(10),
            entry_time TIMESTAMP,
            exit_time TIMESTAMP NULL,
            cost DECIMAL(10,2) DEFAULT 0
        )"
    ];
    
    $success = true;
    foreach ($tables as $sql) {
        if (!$conn->query($sql)) {
            $success = false;
            break;
        }
    }
    
    if ($success) {
        // Admin user
        $admin_hash = password_hash('123', PASSWORD_DEFAULT);
        $conn->query("INSERT IGNORE INTO users (full_name, email, password, role) VALUES ('Admin', 'admin', '$admin_hash', 'admin')");
        
        // Slots
        $slot_labels = ['A1','A2','A3','A4','B1','B2','B3','B4'];
        foreach ($slot_labels as $label) {
            $conn->query("INSERT IGNORE INTO parking_slots (slot_label) VALUES ('$label')");
        }
        
        $message = "✅ Setup كامل! admin/123 login | <a href='login.php'>تسجيل دخول</a>";
    } else {
        $message = "❌ خطأ: " . $conn->error;
    }
}
?>

<h1>إعداد تطبيق الركن</h1>
<?php if ($message) echo "<p>$message</p>"; ?>
<form method='POST'>
    <button type='submit' name='setup' class='btn-setup'>إنشاء قاعدة البيانات والبيانات</button>
</form>

