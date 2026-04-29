<?php
// header.php - المرفق المركزي كامل
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Security headers
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}

// Core Files
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/parking_backend.php';
require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/languages.php';

// Handle Language Change
if (isset($_GET['set_lang'])) {
    $new_lang = $_GET['set_lang'] === 'en' ? 'en' : 'ar';
    $_SESSION['lang'] = $new_lang;
    $clean_url = strtok($_SERVER['REQUEST_URI'], '?');
    header("Location: " . $clean_url);
    exit();
}

$current_lang = $_SESSION['lang'] ?? 'ar';
$dir = ($current_lang == 'ar') ? 'rtl' : 'ltr';
$base_url = BASE_URL;

$public_pages = ['login.php', 'signup.php', 'index.php', 'setup.php', 'test_db.php', 'migrate_passwords.php', 'setup_database.php'];
$current_page = strtolower(basename($_SERVER['PHP_SELF']));

// Auth Middleware - Safe Implementation
if (!in_array($current_page, $public_pages) && !is_logged_in()) {
    header("Location: login.php");
    exit();
}

// Role-based Access Control
if (is_logged_in()) {
    $role = $_SESSION['role'] ?? 'user';
    $admin_only = ['app_dashboard.php', 'admin_dashboard.php', 'admin_scanner.php', 'admin_notifications.php', 'admin_verify.php', 'view_cars.php', 'add_car.php'];
    
    if ($role !== 'admin' && in_array($current_page, $admin_only)) {
        header("Location: dashboard.php");
        exit();
    }
    // Refresh balance/data from DB
    refresh_user_session($conn);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo APP_NAME; ?></title>
    <script src="assets/js/theme.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<?php
$body_class = in_array($current_page, ['login.php', 'signup.php']) ? 'auth-page' : '';
?>
<body class="<?php echo $body_class; ?>">
<div class="toast-container" id="toastContainer"></div>
<script>
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if(!container) return;
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    const icon = type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';
    toast.innerHTML = `<i class="fa-solid ${icon}"></i> <span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('fade-out');
        setTimeout(() => toast.remove(), 400);
    }, 4000);
}

// Auto-show session messages
<?php if (isset($_SESSION['success_message'])): ?>
    window.addEventListener('load', () => showToast("<?php echo $_SESSION['success_message']; ?>", 'success'));
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    window.addEventListener('load', () => showToast("<?php echo $_SESSION['error_message']; ?>", 'error'));
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>
</script>
