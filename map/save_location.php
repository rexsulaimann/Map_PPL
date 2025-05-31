<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Menerima data dari permintaan POST
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $name = 'Lokasi Pengguna'; // Nama lokasi untuk pengguna (tetap sama untuk setiap pengguna)

    // Menghapus data lokasi pengguna yang lama
    $sql_delete = "DELETE FROM locations WHERE name = '$name'";
    if ($conn->query($sql_delete) === TRUE) {
        echo "Data lama berhasil dihapus.";
    } else {
        echo "Error saat menghapus data lama: " . $conn->error;
    }

    // Menyimpan lokasi terbaru pengguna
    $sql_insert = "INSERT INTO locations (name, latitude, longitude) VALUES ('$name', '$latitude', '$longitude')";
    
    if ($conn->query($sql_insert) === TRUE) {
        echo "Lokasi berhasil disimpan.";
    } else {
        echo "Error: " . $sql_insert . "<br>" . $conn->error;
    }

    $conn->close();
} else {
    echo "Request tidak valid.";
}
?>
