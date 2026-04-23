<?php
// languages.php - Translation System

$lang = $_SESSION['lang'] ?? 'ar';

$dict = [
    'ar' => [
        'back' => 'رجوع',
        'home' => 'الرئيسية',
        'login' => 'تسجيل الدخول',
        'occupied' => 'مشغول',
        'available' => 'متاح',
        'dashboard' => 'لوحة التحكم',
        'qr_entrance' => 'بوابة QR',
        'reports' => 'تقارير',
        'view_map' => 'الخريطة',
        'book_now' => 'احجز الآن',
        'invalid_ticket' => 'تذكرة غير صالحة'
    ],
    'en' => [
        'back' => 'Back',
        'home' => 'Home',
        'login' => 'Login',
        'occupied' => 'Occupied',
        'available' => 'Available',
        'dashboard' => 'Dashboard',
        'qr_entrance' => 'QR Entrance',
        'reports' => 'Reports',
        'view_map' => 'View Map',
        'book_now' => 'Book Now',
        'invalid_ticket' => 'Invalid Ticket'
    ]
];

function __($key) {
    global $dict, $lang;
    return $dict[$lang][$key] ?? $key;
}
?>

