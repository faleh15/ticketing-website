<?php
// Konfigurasi database
$host = '127.0.0.1'; // atau alamat IP server database Anda
$dbname = 'eduwisata'; // nama database Anda
$username = 'root'; // username MySQL Anda
$password = ''; // password MySQL Anda

try {
    // Membuat koneksi ke database menggunakan PDO
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $username, $password, $options);
    //echo "Koneksi ke database berhasil!";
} catch (PDOException $e) {
    // Menangani kesalahan koneksi
    //echo "Koneksi ke database gagal: " . $e->getMessage();
    exit;
}
?>
