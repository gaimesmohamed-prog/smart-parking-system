<?php
require_once 'header.php';

// توجيه ذكي بناءً على حالة تسجيل الدخول
if (is_logged_in()) {
    $role = $_SESSION['role'] ?? 'user';
    if ($role === 'admin') {
        header("Location: app_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
} else {
    header("Location: login.php");
}
exit;
?>
