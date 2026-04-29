<?php
/**
 * config/database.php
 * Robust database connection for InfinityFree (PHP 8.3+)
 */

// === DATABASE CREDENTIALS ===
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '112004ٌة#');
define('DB_NAME', 'parkingapp');

if (!isset($GLOBALS['db_connection'])) {
    // Disable strict error reporting temporarily to avoid site crash
    mysqli_report(MYSQLI_REPORT_OFF);
    
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        // Safe error display instead of crash
        die("<h2>Connection Status</h2><p style='color:red'>Database connection failed: " . $conn->connect_error . "</p><p>Please check your DB credentials in config/database.php</p>");
    }

    $conn->set_charset('utf8mb4');
    $GLOBALS['db_connection'] = $conn;
}

$conn = $GLOBALS['db_connection'];
?>
