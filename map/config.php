<?php
// config.php
$servername = "localhost";
$username = "root";   // Username default XAMPP
$password = "";       // Password default XAMPP
$dbname = "campus_navigation";  // Nama database yang telah dibuat

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
