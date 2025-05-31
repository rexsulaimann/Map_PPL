<?php
// php/db_config.php
$servername = "localhost";
$username = "root";   // Username default XAMPP Anda (sesuaikan jika berbeda)
$password = "";       // Password default XAMPP Anda (sesuaikan jika berbeda)
$dbname = "campus_navigation";  // Pastikan ini nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    // Untuk pengembangan, die() tidak masalah. 
    // Untuk produksi, sebaiknya tangani error dengan lebih baik (misalnya logging).
    die("Koneksi database gagal: " . $conn->connect_error);
}
// Anda bisa menambahkan baris ini jika ingin menggunakan encoding utf8 (opsional, tapi baik)
// $conn->set_charset("utf8mb4"); 
?>