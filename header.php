<?php
// header.php - المرفق المركزي كامل

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Development mode: always serve fresh dynamic pages.
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}

// DB
require_once __DIR__ . '/config/database.php';

// Functions
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/parking_backend.php';

// Settings
require_once __DIR__ . '/config/settings.php';

// Lang
if (isset($_GET['lang']) && in_array($_GET['lang'], ['ar','en'])) {
    $_SESSION['lang'] = $_GET['lang'];
    header('Location: ' . basename($_SERVER['PHP_SELF']));
    exit;
}

require_once __DIR__ . '/languages.php';

$current_lang = $_SESSION['lang'] ?? 'ar';
$dir = $current_lang == 'ar' ? 'rtl' : 'ltr';
$base_url = BASE_URL;

// Auth middleware
$public_pages = ['login.php', 'signup.php', 'index.php', 'setup.php', 'test_db.php', 'migrate_passwords.php'];
if (!in_array(basename($_SERVER['PHP_SELF']), $public_pages) && !is_logged_in()) {
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $dir; ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo APP_NAME; ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.getRegistrations().then(function(registrations) {
        registrations.forEach(function(registration) {
            registration.unregister();
        });
    });
    if (window.caches && caches.keys) {
        caches.keys().then(function(keys) {
            keys.forEach(function(key) {
                caches.delete(key);
            });
        });
    }
}
</script>
</head>
<body>
<?php
?>
