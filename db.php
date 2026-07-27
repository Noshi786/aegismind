<?php
// db.php — sirf connection banata hai, kuch aur nahi
$host = 'localhost';
$db   = 'aegismind_db';
$user = 'root';
$pass = '';   // XAMPP/WAMP default mein root ka password khali hota hai

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage());
}