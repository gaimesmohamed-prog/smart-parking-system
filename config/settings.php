<?php
/**
 * Application Settings - إعدادات التطبيق العام
 */

// URL الأساسي للتطبيق
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
define('BASE_URL', $protocol . '://' . $host . '/parking/');

// مراجع الملفات
define('CONFIG_PATH', __DIR__ . '/');
define('INCLUDES_PATH', dirname(__DIR__) . '/includes/');
define('PAGES_PATH', dirname(__DIR__) . '/pages/');
define('ADMIN_PATH', dirname(__DIR__) . '/admin/');

// الإعدادات الأمنية
define('APP_NAME', 'تطبيق ركن السيارات');
define('APP_VERSION', '1.0.0');

// صلاحيات المستخدم
define('ROLE_ADMIN', 'admin');
define('ROLE_USER', 'user');
define('ROLE_SCANNER', 'scanner');

// آخر تحديث للمشروع
define('LAST_UPDATE', date('Y-m-d H:i:s'));
?>

