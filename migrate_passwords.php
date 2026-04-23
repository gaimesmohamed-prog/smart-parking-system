<?php
require_once 'header.php';

function getUsersPrimaryKeyColumn(mysqli $conn) {
    static $column = null;

    if ($column !== null) {
        return $column;
    }

    $result = $conn->query("SHOW COLUMNS FROM users");
    $availableColumns = [];

    while ($result && $row = $result->fetch_assoc()) {
        $availableColumns[] = $row['Field'];
    }

    $column = in_array('user_id', $availableColumns, true) ? 'user_id' : 'id';
    return $column;
}

if (isset($_POST['migrate'])) {
    $primaryKey = getUsersPrimaryKeyColumn($conn);
    $users = $conn->query("SELECT {$primaryKey} AS user_pk, password FROM users");
    $updatedCount = 0;

    while ($users && $row = $users->fetch_assoc()) {
        $storedPassword = $row['password'] ?? '';

        if ($storedPassword === '' || password_get_info($storedPassword)['algo']) {
            continue;
        }

        $newHash = password_hash($storedPassword, PASSWORD_DEFAULT);
        $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE {$primaryKey} = ?");

        if ($updateStmt) {
            $userPk = (int) $row['user_pk'];
            $updateStmt->bind_param('si', $newHash, $userPk);

            if ($updateStmt->execute()) {
                $updatedCount++;
            }

            $updateStmt->close();
        }
    }

    echo "تم تحديث {$updatedCount} كلمة مرور.";
} else {
    echo "<form method='POST'><button name='migrate' value='1'>تحديث كلمات السر (plaintext إلى hash)</button></form>";
}
?>
