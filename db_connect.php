<?php
// Database connection details
$host = 'sql103.infinityfree.com'; // MySQL Hostname
$dbname = 'if0_37981252_taskmanager'; // Database Name
$db_username = 'if0_37981252'; // MySQL Username
$password = 'lfKvWmpCa1y'; // MySQL Password



try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $db_username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
