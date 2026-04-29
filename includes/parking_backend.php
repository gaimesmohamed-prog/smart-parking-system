<?php

function parking_table_exists(mysqli $conn, $tableName) {
    $safeName = $conn->real_escape_string($tableName);
    $result = $conn->query("SHOW TABLES LIKE '{$safeName}'");
    return $result && $result->num_rows > 0;
}

function parking_column_exists(mysqli $conn, $tableName, $columnName) {
    $safeTable = $conn->real_escape_string($tableName);
    $safeColumn = $conn->real_escape_string($columnName);
    $result = $conn->query("SHOW COLUMNS FROM `{$safeTable}` LIKE '{$safeColumn}'");
    return $result && $result->num_rows > 0;
}

function parking_users_primary_key(mysqli $conn) {
    return parking_column_exists($conn, 'users', 'user_id') ? 'user_id' : 'id';
}

function parking_cars_primary_key(mysqli $conn) {
    return parking_column_exists($conn, 'cars', 'car_id') ? 'car_id' : 'id';
}

function parking_default_hourly_rate() {
    return 10.0;
}

function parking_get_slot_price(mysqli $conn, $slotId) {
    $slotId = parking_normalize_slot($slotId);

    // Only query the price column if it actually exists in the table
    if (parking_column_exists($conn, 'parking_slots', 'price')) {
        $stmt = $conn->prepare("SELECT price FROM parking_slots WHERE slot_label = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('s', $slotId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $row = $result->fetch_assoc()) {
                $stmt->close();
                return (float)$row['price'];
            }
            $stmt->close();
        }
    }

    // Determine price based on first letter if column missing or slot not in DB
    $letter = substr($slotId, 0, 1);
    switch ($letter) {
        case 'A': return 15.0;
        case 'B': return 12.0;
        case 'C': return 10.0;
        case 'D': return 8.0;
        case 'E': return 5.0;
        default: return parking_default_hourly_rate();
    }
}

function parking_history_slot_column(mysqli $conn) {
    return parking_column_exists($conn, 'parking_history', 'slot_id') ? 'slot_id' : 'slot_label';
}

function parking_history_amount_column(mysqli $conn) {
    if (parking_column_exists($conn, 'parking_history', 'cost')) {
        return 'cost';
    }
    if (parking_column_exists($conn, 'parking_history', 'total_amount')) {
        return 'total_amount';
    }
    if (parking_column_exists($conn, 'parking_history', 'payment_amount')) {
        return 'payment_amount';
    }
    return null;
}

function parking_normalize_slot($slotId) {
    return strtoupper(trim((string) $slotId));
}

function parking_find_available_slot(mysqli $conn) {
    $sql = "SELECT slot_label FROM parking_slots WHERE status IN ('available', 'not-occupied') ORDER BY slot_label ASC LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        return $row['slot_label'];
    }
    return null;
}

function parking_ensure_slot(mysqli $conn, $slotId, $status = 'available') {
    $slotId = parking_normalize_slot($slotId);
    $stmt = $conn->prepare("SELECT id FROM parking_slots WHERE slot_label = ? LIMIT 1");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('s', $slotId);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result && $result->num_rows > 0;
    $stmt->close();

    if ($exists) {
        return true;
    }

    $price = parking_get_slot_price($conn, $slotId);
    $insert = $conn->prepare("INSERT INTO parking_slots (slot_label, status, price) VALUES (?, ?, ?)");
    if (!$insert) {
        // Fallback for missing price column
        $insert = $conn->prepare("INSERT INTO parking_slots (slot_label, status) VALUES (?, ?)");
        if (!$insert) return false;
        $insert->bind_param('ss', $slotId, $status);
    } else {
        $insert->bind_param('ssd', $slotId, $status, $price);
    }
    $ok = $insert->execute();
    $insert->close();
    return $ok;
}

function parking_update_slot_status(mysqli $conn, $slotId, $status) {
    $slotId = parking_normalize_slot($slotId);
    parking_ensure_slot($conn, $slotId, $status);
    $stmt = $conn->prepare("UPDATE parking_slots SET status = ? WHERE slot_label = ?");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('ss', $status, $slotId);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function parking_get_slot_status_map(mysqli $conn) {
    $slots = [];
    $result = $conn->query("SELECT slot_label, status FROM parking_slots");
    while ($result && $row = $result->fetch_assoc()) {
        $slots[$row['slot_label']] = $row['status'];
    }
    return $slots;
}

function parking_get_dashboard_slots(mysqli $conn, array $slotIds) {
    $slotMap = parking_get_slot_status_map($conn);
    $slots = [];
    $freeCount = 0;
    $occupiedCount = 0;

    foreach ($slotIds as $slotId) {
        if (!isset($slotMap[$slotId])) {
            parking_ensure_slot($conn, $slotId, 'available');
            $slotMap[$slotId] = 'available';
        }

        $dbStatus = $slotMap[$slotId];
        $statusClass = 'not-occupied';
        $statusText = 'Available';

        if ($dbStatus === 'occupied') {
            $statusClass = 'occupied';
            $statusText = 'Occupied';
            $occupiedCount++;
        } elseif ($dbStatus === 'reserved') {
            $statusClass = 'warning';
            $statusText = 'Reserved';
            $occupiedCount++;
        } else {
            $freeCount++;
        }

        $slots[] = [
            'id' => $slotId,
            'status' => $statusClass,
            'text' => $statusText,
        ];
    }

    foreach ($slots as &$slot) {
        if ($slot['id'] === 'B3' && $slot['status'] === 'not-occupied') {
            $slot['status'] = 'closest';
            $slot['text'] = 'Closest free';
        }
    }

    return [
        'slots' => $slots,
        'free_count' => $freeCount,
        'occupied_count' => $occupiedCount,
    ];
}

function parking_get_counts(mysqli $conn) {
    $counts = [
        'total_slots' => 0,
        'occupied_slots' => 0,
        'reserved_slots' => 0,
        'available_slots' => 0,
    ];

    $totalRes = $conn->query("SELECT COUNT(*) AS total FROM parking_slots");
    if ($totalRes && $row = $totalRes->fetch_assoc()) {
        $counts['total_slots'] = (int) $row['total'];
    }

    $occRes = $conn->query("SELECT COUNT(*) AS total FROM parking_slots WHERE status = 'occupied'");
    if ($occRes && $row = $occRes->fetch_assoc()) {
        $counts['occupied_slots'] = (int) $row['total'];
    }

    $reservedRes = $conn->query("SELECT COUNT(*) AS total FROM parking_slots WHERE status = 'reserved'");
    if ($reservedRes && $row = $reservedRes->fetch_assoc()) {
        $counts['reserved_slots'] = (int) $row['total'];
    }

    $counts['available_slots'] = max(0, $counts['total_slots'] - $counts['occupied_slots'] - $counts['reserved_slots']);
    return $counts;
}

function parking_create_reservation(mysqli $conn, array $payload) {
    $plate = trim((string) ($payload['plate_number'] ?? ''));
    $slotId = parking_normalize_slot($payload['slot_id'] ?? '');
    $carType = trim((string) ($payload['car_type'] ?? 'sedan'));
    $userId = (int) ($payload['user_id'] ?? 0);
    $deductBalance = (float) ($payload['deduct_balance'] ?? 0);
    $reservedHours = (int) ($payload['reserved_hours'] ?? 1);

    if ($plate === '' || $slotId === '') {
        return ['success' => false, 'message' => 'Plate number and slot are required.'];
    }

    parking_ensure_slot($conn, $slotId, 'available');

    $checkStmt = $conn->prepare("SELECT COUNT(*) AS total FROM cars WHERE slot_id = ? AND status IN ('reserved', 'occupied')");
    if (!$checkStmt) {
        return ['success' => false, 'message' => 'Unable to validate slot status.'];
    }
    $checkStmt->bind_param('s', $slotId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $isBusy = false;
    if ($checkResult && $row = $checkResult->fetch_assoc()) {
        $isBusy = ((int) $row['total']) > 0;
    }
    $checkStmt->close();

    if ($isBusy) {
        return ['success' => false, 'message' => "Slot {$slotId} is already reserved or occupied."];
    }

    $guid = generate_booking_guid();
    $model = $carType === 'truck' ? 'Heavy Duty Truck' : 'Sedan Car';
    $color = $payload['car_color'] ?? 'White';

    $conn->begin_transaction();
    try {
        if ($deductBalance > 0 && $userId > 0) {
            $balStmt = $conn->prepare("SELECT balance FROM users WHERE user_id = ? FOR UPDATE");
            $balStmt->bind_param('i', $userId);
            $balStmt->execute();
            $balRes = $balStmt->get_result();
            $userRow = $balRes->fetch_assoc();
            $balStmt->close();
            
            if (!$userRow || $userRow['balance'] < $deductBalance) {
                throw new RuntimeException('رصيدك غير كافٍ. يرجى شحن المحفظة أولاً.');
            }
            
            $updBal = $conn->prepare("UPDATE users SET balance = balance - ? WHERE user_id = ?");
            $updBal->bind_param('di', $deductBalance, $userId);
            $updBal->execute();
            $updBal->close();
        }

        $insertCar = $conn->prepare("INSERT INTO cars (plate_number, car_model, car_color, slot_id, parking_guid, status, user_id, reserved_hours) VALUES (?, ?, ?, ?, ?, 'reserved', ?, ?)");
        if (!$insertCar) {
            throw new RuntimeException('Unable to create reservation.');
        }
        $insertCar->bind_param('sssssii', $plate, $model, $color, $slotId, $guid, $userId, $reservedHours);
        $insertCar->execute();
        $carId = $insertCar->insert_id;
        $insertCar->close();

        $historyAmountColumn = parking_history_amount_column($conn);
        $hasHourlyRate = parking_column_exists($conn, 'parking_history', 'hourly_rate');
        $hasReservedHours = parking_column_exists($conn, 'parking_history', 'reserved_hours');

        $cols = "user_id, car_id, plate_number, slot_id, qr_code_path, status";
        $vals = "?, ?, ?, ?, ?, 'active'";
        $types = "iisss";
        $params = [$userId, $carId, $plate, $slotId, $guid];

        if ($historyAmountColumn) {
            $cols .= ", $historyAmountColumn";
            $vals .= ", ?";
            $types .= "d";
            $params[] = $deductBalance;
        }
        if ($hasReservedHours) {
            $cols .= ", reserved_hours";
            $vals .= ", ?";
            $types .= "i";
            $params[] = $reservedHours;
        }
        if ($hasHourlyRate) {
            $cols .= ", hourly_rate";
            $vals .= ", ?";
            $types .= "d";
            $params[] = parking_get_slot_price($conn, $slotId);
        }

        $histSql = "INSERT INTO parking_history ($cols) VALUES ($vals)";
        $insertHistory = $conn->prepare($histSql);
        if (!$insertHistory) {
            throw new RuntimeException('Unable to create history log: ' . $conn->error);
        }
        $insertHistory->bind_param($types, ...$params);
        $insertHistory->execute();
        $insertHistory->close();

        parking_update_slot_status($conn, $slotId, 'reserved');
        $conn->commit();

        return [
            'success' => true,
            'guid' => $guid,
            'slot_id' => $slotId,
            'plate_number' => $plate,
            'message' => "Slot {$slotId} reserved successfully.",
        ];
    } catch (Throwable $e) {
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function parking_find_car_by_guid(mysqli $conn, $guid) {
    $stmt = $conn->prepare("
        SELECT c.*, h.entry_time 
        FROM cars c 
        LEFT JOIN parking_history h ON h.plate_number = c.plate_number 
          AND h.slot_id = c.slot_id 
          AND h.exit_time IS NULL
        WHERE c.parking_guid = ? 
        LIMIT 1
    ");
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param('s', $guid);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result ? $result->fetch_assoc() : null;
    $stmt->close();
    return $car ?: null;
}

function parking_mark_entry(mysqli $conn, $guid) {
    $car = parking_find_car_by_guid($conn, $guid);
    if (!$car) {
        return ['success' => false, 'message' => 'Ticket not found.'];
    }

    if (($car['status'] ?? '') === 'occupied') {
        return ['success' => true, 'message' => 'Vehicle is already checked in.', 'car' => $car];
    }

    $slotId = $car['slot_id'];
    $plate = $car['plate_number'];

    $updateCar = $conn->prepare("UPDATE cars SET status = 'occupied' WHERE parking_guid = ?");
    if ($updateCar) {
        $updateCar->bind_param('s', $guid);
        $updateCar->execute();
        $updateCar->close();
    }

    parking_update_slot_status($conn, $slotId, 'occupied');

    $historySlotColumn = parking_history_slot_column($conn);
    $historyStmt = $conn->prepare("UPDATE parking_history SET entry_time = NOW() WHERE plate_number = ? AND {$historySlotColumn} = ? AND exit_time IS NULL ORDER BY id DESC LIMIT 1");
    if ($historyStmt) {
        $historyStmt->bind_param('ss', $plate, $slotId);
        $historyStmt->execute();
        $historyStmt->close();
    }

    return [
        'success' => true,
        'message' => "Vehicle {$plate} checked into slot {$slotId}.",
        'car' => parking_find_car_by_guid($conn, $guid),
    ];
}

function parking_complete_exit(mysqli $conn, $guid, $userId = 0) {
    $car = parking_find_car_by_guid($conn, $guid);
    if (!$car) {
        return ['success' => false, 'message' => 'Ticket not found.'];
    }

    $slotId = $car['slot_id'];
    $plate = $car['plate_number'];
    $historySlotColumn = parking_history_slot_column($conn);

    $historyStmt = $conn->prepare("SELECT * FROM parking_history WHERE plate_number = ? AND {$historySlotColumn} = ? AND exit_time IS NULL ORDER BY id DESC LIMIT 1");
    if (!$historyStmt) {
        return ['success' => false, 'message' => 'Unable to fetch parking history.'];
    }
    $historyStmt->bind_param('ss', $plate, $slotId);
    $historyStmt->execute();
    $historyResult = $historyStmt->get_result();
    $history = $historyResult ? $historyResult->fetch_assoc() : null;
    $historyStmt->close();

    if (!$history) {
        // If no entry_time, it was just a reservation. We treat exit as cancellation but maybe with reservation fee.
        return parking_cancel_reservation($conn, $car['car_id'], $userId ?: $car['user_id']);
    }

    $entryTimestamp = strtotime($history['entry_time']);
    $exitTimestamp = time();
    $durationSeconds = max(0, $exitTimestamp - $entryTimestamp);
    $durationHours = max(1, ceil($durationSeconds / 3600));
    
    $reservedHours = (int)($history['reserved_hours'] ?? 1);
    $baseRate = (float)($history['hourly_rate'] ?? 10.0);
    $penaltyRate = 15.0; 
    
    $cost = 0;
    $penaltyAmount = 0;
    
    if ($durationHours <= $reservedHours) {
        $cost = $durationHours * $baseRate;
    } else {
        $cost = $reservedHours * $baseRate;
        $overtimeHours = $durationHours - $reservedHours;
        $penaltyAmount = $overtimeHours * $penaltyRate;
        $cost += $penaltyAmount;
    }
    
    $exitTime = date('Y-m-d H:i:s', $exitTimestamp);

    $conn->begin_transaction();
    try {
        // Deduct from wallet if userId is provided
        $deducted = 0;
        if ($userId > 0) {
            $u = $conn->prepare("UPDATE users SET balance = balance - ? WHERE user_id = ?");
            $u->bind_param('di', $cost, $userId);
            $u->execute();
            $u->close();
            $deducted = $cost;
        }

        $historyAmountColumn = parking_history_amount_column($conn);
        if ($historyAmountColumn !== null) {
            $updateHistory = $conn->prepare("UPDATE parking_history SET exit_time = ?, {$historyAmountColumn} = ?, penalty_amount = ?, status = 'completed' WHERE id = ?");
            if (!$updateHistory) {
                throw new RuntimeException('Unable to update history.');
            }
            $historyId = (int) $history['id'];
            $updateHistory->bind_param('sddi', $exitTime, $cost, $penaltyAmount, $historyId);
            $updateHistory->execute();
            $updateHistory->close();
        }

        $deleteCar = $conn->prepare("DELETE FROM cars WHERE parking_guid = ?");
        if ($deleteCar) {
            $deleteCar->bind_param('s', $guid);
            $deleteCar->execute();
            $deleteCar->close();
        }

        parking_update_slot_status($conn, $slotId, 'available');
        $conn->commit();

        return [
            'success' => true,
            'message' => "Vehicle {$plate} checked out successfully.",
            'plate' => $plate,
            'slot_id' => $slotId,
            'hours' => $durationHours,
            'cost' => $cost,
            'deducted' => $deducted,
            'history_id' => (int)$history['id'],
        ];
    } catch (Throwable $e) {
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function parking_cancel_reservation(mysqli $conn, $carId, $userId) {
    $stmt = $conn->prepare("SELECT * FROM cars WHERE car_id = ? AND user_id = ? AND status = 'reserved'");
    $stmt->bind_param('ii', $carId, $userId);
    $stmt->execute();
    $car = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$car) {
        return ['success' => false, 'message' => 'Reservation not found or already active.'];
    }

    $slotId = $car['slot_id'];
    $plate = $car['plate_number'];

    $conn->begin_transaction();
    try {
        // Mark history as cancelled
        $historySlotColumn = parking_history_slot_column($conn);
        $updateHistory = $conn->prepare("UPDATE parking_history SET status = 'cancelled', exit_time = NOW() WHERE plate_number = ? AND {$historySlotColumn} = ? AND exit_time IS NULL");
        $updateHistory->bind_param('ss', $plate, $slotId);
        $updateHistory->execute();
        $updateHistory->close();

        // Remove from active cars
        $deleteCar = $conn->prepare("DELETE FROM cars WHERE car_id = ?");
        $deleteCar->bind_param('i', $carId);
        $deleteCar->execute();
        $deleteCar->close();

        // Release slot
        parking_update_slot_status($conn, $slotId, 'available');

        $conn->commit();
        return ['success' => true, 'message' => "Reservation for slot {$slotId} cancelled successfully."];
    } catch (Throwable $e) {
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function parking_get_ticket_data(mysqli $conn, $guid) {
    $car = parking_find_car_by_guid($conn, $guid);
    if (!$car) {
        return null;
    }

    $historySlotColumn = parking_history_slot_column($conn);
    $historyAmountColumn = parking_history_amount_column($conn);
    $costSql = $historyAmountColumn !== null ? "{$historyAmountColumn} AS cost" : "NULL AS cost";
    $historyStmt = $conn->prepare("SELECT entry_time, exit_time, {$costSql} FROM parking_history WHERE plate_number = ? AND {$historySlotColumn} = ? ORDER BY id DESC LIMIT 1");

    $history = null;
    if ($historyStmt) {
        $plate = $car['plate_number'];
        $slotId = $car['slot_id'];
        $historyStmt->bind_param('ss', $plate, $slotId);
        $historyStmt->execute();
        $result = $historyStmt->get_result();
        $history = $result ? $result->fetch_assoc() : null;
        $historyStmt->close();
    }

    return [
        'car' => $car,
        'history' => $history,
    ];
}

function parking_get_report_summary(mysqli $conn, $user_id = 0) {
    $summary = [
        'cars_today' => 0,
        'total_revenue' => 0.0,
        'most_used_slot' => 'N/A',
        'total_sessions' => 0,
    ];

    $whereUser = $user_id > 0 ? " AND user_id = " . intval($user_id) : "";
    $whereUserOnly = $user_id > 0 ? " WHERE user_id = " . intval($user_id) : "";

    $today = date('Y-m-d');
    $carsToday = $conn->query("SELECT COUNT(*) AS cnt FROM parking_history WHERE DATE(entry_time) = '{$today}' {$whereUser}");
    if ($carsToday && $row = $carsToday->fetch_assoc()) {
        $summary['cars_today'] = (int) $row['cnt'];
    }

    $historyAmountColumn = parking_history_amount_column($conn);
    $historySlotColumn = parking_history_slot_column($conn);

    if ($historyAmountColumn !== null) {
        $revenue = $conn->query("SELECT COALESCE(SUM({$historyAmountColumn}), 0) AS total FROM parking_history {$whereUserOnly}");
        if ($revenue && $row = $revenue->fetch_assoc()) {
            $summary['total_revenue'] = (float) $row['total'];
        }
    }

    $mostUsed = $conn->query("SELECT {$historySlotColumn} AS slot_id, COUNT(*) AS cnt FROM parking_history {$whereUserOnly} GROUP BY {$historySlotColumn} ORDER BY cnt DESC LIMIT 1");
    if ($mostUsed && $row = $mostUsed->fetch_assoc()) {
        $summary['most_used_slot'] = $row['slot_id'] ?: 'N/A';
    }

    $totalSessions = $conn->query("SELECT COUNT(*) AS cnt FROM parking_history {$whereUserOnly}");
    if ($totalSessions && $row = $totalSessions->fetch_assoc()) {
        $summary['total_sessions'] = (int) $row['cnt'];
    }

    return $summary;
}

function parking_get_recent_history(mysqli $conn, $limit = 20, $user_id = 0) {
    $limit = max(1, (int) $limit);
    $whereUser = $user_id > 0 ? " WHERE user_id = " . intval($user_id) : "";
    
    $historySlotColumn = parking_history_slot_column($conn);
    $historyAmountColumn = parking_history_amount_column($conn);
    $costSql = $historyAmountColumn !== null ? "{$historyAmountColumn} AS cost" : "NULL AS cost";
    $result = $conn->query("SELECT id, plate_number, {$historySlotColumn} AS slot_id, entry_time, exit_time, {$costSql} FROM parking_history {$whereUser} ORDER BY entry_time DESC LIMIT {$limit}");

    $rows = [];
    while ($result && $row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}
