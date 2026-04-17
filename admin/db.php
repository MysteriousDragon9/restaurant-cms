<?php
// Database configuration
$host = 'localhost';
$dbname = 'restaurant_cms';
$username = 'cpoon4';  // XAMPP username
$password = 'Spring2026';      // XAMPP password
try {
    //PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    // throw exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // OSet default fetch mode
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    //errors handler
     die("Database connection failed: " . $e->getMessage());
}
?>