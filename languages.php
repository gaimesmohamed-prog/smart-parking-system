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
        'invalid_ticket' => 'تذكرة غير صالحة',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'signup' => 'إنشاء حساب',
        'full_name' => 'الاسم الكامل',
        'phone' => 'رقم الهاتف',
        'car_plate' => 'رقم اللوحة',
        'car_model' => 'نوع السيارة',
        'car_color' => 'اللون',
        'confirm_email' => 'تأكيد البريد الإلكتروني',
        'confirm_password' => 'تأكيد كلمة المرور',
        'ticket_details' => 'تفاصيل التذكرة'
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
        'invalid_ticket' => 'Invalid Ticket',
        'email' => 'Email',
        'password' => 'Password',
        'signup' => 'Sign Up',
        'full_name' => 'Full Name',
        'phone' => 'Phone',
        'car_plate' => 'Car Plate',
        'car_model' => 'Car Model',
        'car_color' => 'Car Color',
        'confirm_email' => 'Confirm Email',
        'confirm_password' => 'Confirm Password',
        'ticket_details' => 'Ticket Details'
    ]
];

function __($key) {
    global $dict, $lang;
    return $dict[$lang][$key] ?? $key;
}
?>

