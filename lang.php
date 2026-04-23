<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    if ($_GET['lang'] == 'ar' || $_GET['lang'] == 'en') {
        $_SESSION['lang'] = $_GET['lang'];
    }
}

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en'; // default language
}

$lang = $_SESSION['lang'];

$translations = [
    'en' => [
        'settings' => 'Settings',
        'back_to_map' => '⬅ Back to Map',
        'user_profile' => '👤 User Profile',
        'full_name' => 'Full Name',
        'phone_number' => 'Phone Number',
        'car_plate' => 'Default Car Plate Number',
        'license_number' => 'Driving License Number',
        'address' => 'Address',
        'save_changes' => 'Save Changes',
        'view_history' => '🕒 View Parking History',
        'lang_switch_text' => 'عربي', // Text to show for switching
        'lang_switch_code' => 'ar'   // Code to switch to
    ],
    'ar' => [
        'settings' => 'الإعدادات',
        'back_to_map' => '⬅ العودة للخريطة',
        'user_profile' => '👤 الملف الشخصي',
        'full_name' => 'الاسم الكامل',
        'phone_number' => 'رقم الهاتف',
        'car_plate' => 'رقم اللوحة الافتراضي',
        'license_number' => 'رقم رخصة القيادة',
        'address' => 'العنوان',
        'save_changes' => 'حفظ التغييرات',
        'view_history' => '🕒 عرض سجل الحجوزات',
        'lang_switch_text' => 'English',
        'lang_switch_code' => 'en'
    ]
];

function t($key) {
    global $translations, $lang;
    return $translations[$lang][$key] ?? $key;
}
?>
