-- ========================================
-- قاعدة بيانات تطبيق الوقوف - البنية الموصى بها
-- ========================================

-- جدول المستخدمين
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    role ENUM('admin', 'user', 'scanner') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX(email),
    INDEX(phone),
    INDEX(role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول السيارات
CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plate_number VARCHAR(20) UNIQUE NOT NULL,
    car_model VARCHAR(50),
    car_color VARCHAR(30),
    owner_id INT,
    slot_id VARCHAR(10),
    parking_guid VARCHAR(50) UNIQUE,
    entry_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('parked', 'reserved', 'occupied', 'exited') DEFAULT 'parked',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id),
    INDEX(plate_number),
    INDEX(slot_id),
    INDEX(status),
    INDEX(created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول أماكن الوقوف
CREATE TABLE IF NOT EXISTS parking_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slot_label VARCHAR(10) UNIQUE NOT NULL,
    floor INT,
    section VARCHAR(5),
    car_id INT,
    status ENUM('available', 'reserved', 'occupied', 'maintenance') DEFAULT 'available',
    capacity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id),
    INDEX(floor),
    INDEX(section),
    INDEX(status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول السجل التاريخي
CREATE TABLE IF NOT EXISTS parking_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plate_number VARCHAR(20),
    slot_id VARCHAR(10),
    user_id INT,
    entry_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    exit_time TIMESTAMP NULL,
    duration_hours DECIMAL(5,2),
    payment_amount DECIMAL(8,2),
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX(plate_number),
    INDEX(user_id),
    INDEX(entry_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول الحجوزات
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    slot_id VARCHAR(10),
    car_id INT,
    booking_guid VARCHAR(50) UNIQUE,
    start_time TIMESTAMP,
    end_time TIMESTAMP,
    status ENUM('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (car_id) REFERENCES cars(id),
    INDEX(user_id),
    INDEX(status),
    INDEX(created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول سجل الأنشطة
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX(user_id),
    INDEX(action),
    INDEX(timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول الإخطارات
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100),
    message TEXT,
    type ENUM('info', 'warning', 'error', 'success') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX(user_id),
    INDEX(is_read),
    INDEX(created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول الدفعات
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    history_id INT,
    amount DECIMAL(8,2) NOT NULL,
    payment_method ENUM('card', 'cash', 'mobile_wallet') DEFAULT 'card',
    transaction_id VARCHAR(100) UNIQUE,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (history_id) REFERENCES parking_history(id),
    INDEX(status),
    INDEX(created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول التقارير
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_type ENUM('daily', 'weekly', 'monthly', 'custom') DEFAULT 'daily',
    total_vehicles INT DEFAULT 0,
    total_revenue DECIMAL(10,2) DEFAULT 0,
    occupied_slots INT DEFAULT 0,
    available_slots INT DEFAULT 0,
    report_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(report_type),
    INDEX(report_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول الإعدادات
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX(setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================
-- البيانات الأولية الأساسية
-- ========================================

-- مستخدم إدارة أساسي
INSERT IGNORE INTO users (full_name, password, email, role) 
VALUES ('Admin', 'admin123', 'admin@parkingapp.com', 'admin');

-- إعدادات افتراضية
INSERT IGNORE INTO settings (setting_key, setting_value, description) VALUES
('APP_NAME', 'تطبيق ركن السيارات', 'اسم التطبيق'),
('PARKING_FEE_PER_HOUR', '5', 'الرسم بالساعة'),
('MAX_PARKING_HOURS', '24', 'أقصى ساعات للوقوف المتتالي'),
('MAINTENANCE_MODE', 'false', 'وضع الصيانة'),
('LANGUAGE', 'ar', 'اللغة الافتراضية'),
('CURRENCY', 'EGP', 'العملة'),
('PHONE_SUPPORT', '+20XXXXXXXXXX', 'رقم الدعم'),
('EMAIL_SUPPORT', 'support@parkingapp.com', 'بريد الدعم');

-- ========================================
-- الفهارس المختلفة (يمكن إضافتها لتحسين الأداء)
-- ========================================

-- إنشاء فهرس مركب
CREATE INDEX idx_parking_history_user_date 
ON parking_history(user_id, entry_time);

CREATE INDEX idx_slots_status_floor 
ON parking_slots(status, floor);

CREATE INDEX idx_bookings_user_status 
ON bookings(user_id, status);

-- ========================================
-- آخر التحديث: 2026-04-18
-- ========================================
