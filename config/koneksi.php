<?php
// config/koneksi.php

$host = 'localhost';
$dbname = 'db_tata_usaha';
$username = 'root'; // Biasanya root
$password = ''; // Biasanya kosong kalau XAMPP bawaan

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Koneksi database jebol bos: " . $e->getMessage());
}
?>