<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/parking_backend.php';

// Add price column if not exists
if (!parking_column_exists($conn, 'parking_slots', 'price')) {
    $conn->query("ALTER TABLE parking_slots ADD COLUMN price DECIMAL(10,2) DEFAULT 10.00");
    echo "Added price column to parking_slots.\n";
}

// Update some prices for variety
$conn->query("UPDATE parking_slots SET price = 15.00 WHERE slot_label LIKE 'A%'");
$conn->query("UPDATE parking_slots SET price = 12.00 WHERE slot_label LIKE 'B%'");
$conn->query("UPDATE parking_slots SET price = 10.00 WHERE slot_label LIKE 'C%'");
$conn->query("UPDATE parking_slots SET price = 8.00 WHERE slot_label LIKE 'D%'");
$conn->query("UPDATE parking_slots SET price = 5.00 WHERE slot_label LIKE 'E%'");

echo "Prices initialized for slots.\n";
