<?php
// config/koneksi.php
// Konfigurasi koneksi database MySQLi (Sesuai Modul 11A & 11B)

$host = "localhost";
$user = "root";
$pass = "";
$db   = "ajax_lab";

// Mengaktifkan pelaporan kesalahan MySQLi
mysqli_report(MYSQLI_REPORT_OFF);

// Koneksi ke server MySQL
$conn = @new mysqli($host, $user, $pass, $db);

// Cek status koneksi
if ($conn->connect_error) {
    // Jika diakses dari request API, berikan output JSON
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) {
        http_response_code(500);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode([
            "success" => false,
            "message" => "Koneksi database MySQL gagal: " . $conn->connect_error,
            "hint" => "Pastikan MySQL di Laragon/XAMPP sudah aktif dan database 'ajax_lab' sudah di-import melalui database.sql"
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Jika diakses dari halaman web biasa
    die("Koneksi database gagal: " . $conn->connect_error . ". Pastikan Apache & MySQL di Laragon/XAMPP sudah dijalankan.");
}

// Set charset ke utf8mb4 agar mendukung multibyte dan simbol
$conn->set_charset("utf8mb4");
