<?php
// config/conn.php — PDO Database Connection

$host = 'localhost';
$db   = 'sisabsi';   // your existing database name
$user = 'root';      // your existing username
$pass = '';          // your existing password (empty)
$char = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$char";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Koneksi database gagal. Silakan coba lagi nanti.");
}
?>
