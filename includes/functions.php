<?php
/**
 * Helper Functions - الدوال المساعدة المشتركة
 */

/**
 * تنظيف مدخلات المستخدم من الأخطار
 */
function sanitize_input($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

/**
 * التحقق من تسجيل الدخول
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) || isset($_SESSION['user']);
}

function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

function get_user_primary_key(array $user) {
    if (isset($user['user_id'])) return (int) $user['user_id'];
    if (isset($user['id'])) return (int) $user['id'];
    return 0;
}

/**
 * التحقق من صلاحيات المستخدم
 */
function check_role($required_role) {
    if (!is_logged_in()) return false;
    return isset($_SESSION['role']) && $_SESSION['role'] == $required_role;
}

/**
 * إعادة التوجيه إلى صفحة معينة - Relative Redirect
 */
function redirect($location) {
    header("Location: " . $location);
    exit();
}

/**
 * الحصول على رسالة الخطأ من الجلسة
 */
function get_error_message() {
    if (isset($_SESSION['error_message'])) {
        $message = $_SESSION['error_message'];
        unset($_SESSION['error_message']);
        return $message;
    }
    return '';
}

/**
 * الحصول على رسالة النجاح من الجلسة
 */
function get_success_message() {
    if (isset($_SESSION['success_message'])) {
        $message = $_SESSION['success_message'];
        unset($_SESSION['success_message']);
        return $message;
    }
    return '';
}

function set_error_message($message) { $_SESSION['error_message'] = $message; }
function set_success_message($message) { $_SESSION['success_message'] = $message; }

/**
 * تحويل التاريخ إلى صيغة عربية
 */
function format_arabic_date($date_string) {
    $months_ar = ['01' => 'يناير','02' => 'فبراير','03' => 'مارس','04' => 'أبريل','05' => 'مايو','06' => 'يونيو','07' => 'يوليو','08' => 'أغسطس','09' => 'سبتمبر','10' => 'أكتوبر','11' => 'نوفمبر','12' => 'ديسمبر'];
    $date = new DateTime($date_string);
    return $date->format('d') . ' ' . $months_ar[$date->format('m')] . ' ' . $date->format('Y');
}

function check_slot_exists($slot_id) {
    global $conn;
    $res = $conn->query("SELECT id FROM parking_slots WHERE slot_label = '" . sanitize_input($slot_id) . "'");
    return $res && $res->num_rows > 0;
}

function get_slot_status($slot_id) {
    global $conn;
    $res = $conn->query("SELECT status FROM parking_slots WHERE slot_label = '" . sanitize_input($slot_id) . "'");
    return ($res && $row = $res->fetch_assoc()) ? $row['status'] : 'unknown';
}

function log_activity($user_id, $action, $details = '') {
    global $conn;
    $u = sanitize_input($user_id); $a = sanitize_input($action); $d = sanitize_input($details);
    $ip = $_SERVER['REMOTE_ADDR'];
    return $conn->query("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES ('$u', '$a', '$d', '$ip')");
}

function generate_booking_guid() {
    return "PARK-" . strtoupper(uniqid()) . "-" . time();
}

function format_duration($hours) {
    $days = floor($hours / 24);
    $rem = $hours % 24;
    return ($days > 0) ? "$days يوم و $rem ساعة" : "$rem ساعة";
}

function calculate_parking_fee($hours, $rate = 10) {
    return $hours * $rate;
}

/**
 * تحديث بيانات الجلسة من قاعدة البيانات
 */
function refresh_user_session(mysqli $conn) {
    if (!is_logged_in()) return;
    $uid = $_SESSION['user_id'] ?? 0;
    if (!$uid) return;
    
    $res = $conn->query("SELECT balance, full_name FROM users WHERE user_id = $uid");
    if ($res && $row = $res->fetch_assoc()) {
        $_SESSION['balance'] = (float)$row['balance'];
        $_SESSION['full_name'] = $row['full_name'];
    }
}
?>
