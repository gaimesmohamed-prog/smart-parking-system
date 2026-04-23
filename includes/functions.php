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
    if (isset($user['user_id'])) {
        return (int) $user['user_id'];
    }

    if (isset($user['id'])) {
        return (int) $user['id'];
    }

    return 0;
}

/**
 * التحقق من صلاحيات المستخدم
 */
function check_role($required_role) {
    if (!is_logged_in()) {
        return false;
    }
    return isset($_SESSION['role']) && $_SESSION['role'] == $required_role;
}

/**
 * إعادة التوجيه إلى صفحة معينة
 */
function redirect($location) {
    header("Location: " . BASE_URL . $location);
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

/**
 * تعيين رسالة خطأ في الجلسة
 */
function set_error_message($message) {
    $_SESSION['error_message'] = $message;
}

/**
 * تعيين رسالة نجاح في الجلسة
 */
function set_success_message($message) {
    $_SESSION['success_message'] = $message;
}

/**
 * تحويل التاريخ إلى صيغة عربية
 */
function format_arabic_date($date_string) {
    $months_ar = [
        '01' => 'يناير',
        '02' => 'فبراير',
        '03' => 'مارس',
        '04' => 'أبريل',
        '05' => 'مايو',
        '06' => 'يونيو',
        '07' => 'يوليو',
        '08' => 'أغسطس',
        '09' => 'سبتمبر',
        '10' => 'أكتوبر',
        '11' => 'نوفمبر',
        '12' => 'ديسمبر'
    ];

    $date = new DateTime($date_string);
    $day = $date->format('d');
    $month = $months_ar[$date->format('m')];
    $year = $date->format('Y');
    
    return $day . ' ' . $month . ' ' . $year;
}

/**
 * التحقق من وجود سجل الركن
 */
function check_slot_exists($slot_id) {
    global $conn;
    $slot_id = sanitize_input($slot_id);
    
    $query = "SELECT * FROM parking_slots WHERE slot_label = '$slot_id'";
    $result = $conn->query($query);
    
    return $result && $result->num_rows > 0;
}

/**
 * الحصول على حالة الركن
 */
function get_slot_status($slot_id) {
    global $conn;
    $slot_id = sanitize_input($slot_id);
    
    $query = "SELECT status FROM parking_slots WHERE slot_label = '$slot_id'";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['status'];
    }
    return 'unknown';
}

/**
 * إدراج سجل نشاط جديد
 */
function log_activity($user_id, $action, $details = '') {
    global $conn;
    
    $user_id = sanitize_input($user_id);
    $action = sanitize_input($action);
    $details = sanitize_input($details);
    $timestamp = date('Y-m-d H:i:s');
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    $query = "INSERT INTO activity_logs (user_id, action, details, timestamp, ip_address) 
              VALUES ('$user_id', '$action', '$details', '$timestamp', '$ip_address')";
    
    return $conn->query($query);
}

/**
 * إنشاء معرف فريد للحجز
 */
function generate_booking_guid() {
    return "PARK-" . uniqid() . "-" . time();
}

/**
 * تحويل الساعات إلى ساعات وأيام
 * Graduation Demo: Always shows 10 hours
 */
function format_duration($hours) {
    // Demo mode: Force 10 hours for graduation presentation
    $demo_mode = true;

    if ($demo_mode) {
        $hours = 10; // Fixed 10 hours for demo
    }

    $days = floor($hours / 24);
    $remaining_hours = $hours % 24;

    if ($days > 0) {
        return $days . " يوم و " . $remaining_hours . " ساعة";
    }
    return $remaining_hours . " ساعة";
}

/**
 * حساب رسوم الركن
 * Graduation Demo: Always returns 10 hours calculation
 */
function calculate_parking_fee($hours, $price_per_hour = 10) {
    // Demo mode: Force 10 hours for graduation presentation
    $demo_mode = true;

    if ($demo_mode) {
        $hours = 10; // Fixed 10 hours for demo
    }

    return $hours * $price_per_hour;
}
?>
