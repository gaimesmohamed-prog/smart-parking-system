<?php
/**
 * Central MySQL connection settings.
 */

if (!isset($GLOBALS['db_connection'])) {
    define('DB_HOST', 'localhost');
    define('DB_PORT', 3305);
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'parkingapp');

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

    if ($conn->connect_error) {
        http_response_code(500);
        die('<h2>DB Error</h2>' . $conn->connect_error . '<br><a href="test_db.php">Test DB</a>');
    }

    $conn->set_charset('utf8');
    $GLOBALS['db_connection'] = $conn;
}

$conn = $GLOBALS['db_connection'];
