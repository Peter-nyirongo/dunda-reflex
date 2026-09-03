<?php

// InfinityFree Database Configuration
$host = "sql310.infinityfree.com";  // Use the actual host from InfinityFree
$dbname = "if0_42822443_dundareflex";  // Your database name
$username = "if0_42822443";  // Your database username
$password = "engpeter96";  // Your database password

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    die("Database connection failed: " . $e->getMessage());

}
?>