<?php
// routes.php
include 'config.php';

// Query untuk mengambil data lokasi
$sql = "SELECT id, name, latitude, longitude FROM locations";
$result = $conn->query($sql);

$locations = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $locations[] = $row; // Menyimpan data lokasi dalam array
    }
}

$conn->close();
echo json_encode($locations); // Mengembalikan data lokasi dalam format JSON
?>
