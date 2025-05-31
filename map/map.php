<?php
// map/map.php (bagian paling atas file)
$link_kembali = "../index.php"; // Default kembali ke login
$teks_tombol_kembali = "KEMBALI KE LOGIN";

if (isset($_GET['from']) && $_GET['from'] === 'admin_dashboard') {
    // Jika datang dari dashboard admin, arahkan kembali ke sana.
    $link_kembali = "../php/dashboard_admin.php"; 
    $teks_tombol_kembali = "KEMBALI KE DASHBOARD ADMIN";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UIN Jakarta Campus Navigation</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="styles.css"> 

</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h1>UIN MAPS</h1>

            <?php
            if (isset($_GET['error_lokasi'])) {
                echo '<div class="alert alert-danger" role="alert" style="padding: 0.5rem 1rem; font-size: 0.9rem;">' . htmlspecialchars($_GET['error_lokasi']) . '</div>';
            }
            if (isset($_GET['success_lokasi'])) {
                echo '<div class="alert alert-success" role="alert" style="padding: 0.5rem 1rem; font-size: 0.9rem;">' . htmlspecialchars($_GET['success_lokasi']) . '</div>';
            }
            ?>

            <form id="routeForm">
                <label for="origin">LOKASI ASAL:</label>
                <select id="origin" name="origin" required>
                    <option value="">Pilih Lokasi</option>
                </select>
                <br><br>

                <label for="destination">LOKASI TUJUAN:</label>
                <select id="destination" name="destination" required>
                    <option value="">Pilih Lokasi</option>
                </select>
                <br><br>

                <label for="travelMode">MODE TRANSPORTASI:</label>
                <select id="travelMode" name="travelMode" required>
                    <option value="foot-walking">Jalan Kaki</option>
                    <option value="driving-car">Kendaraan</option>
                </select>
                <br><br>
                <button type="submit">TAMPILKAN RUTE</button>
            </form>
            <hr> 
            
            <form action="<?php echo htmlspecialchars($link_kembali); ?>" method="get" style="margin-top: 20px;">
                <button type="submit" class="btn-kembali" style="background-color: #6c757d; color: white; border: none; text-align: center; text-decoration: none; display: inline-block; font-size: 14px; cursor: pointer; border-radius: 4px; padding: 10px; width:100%;">
                    <?php echo htmlspecialchars($teks_tombol_kembali); ?>
                </button>
            </form>
            <div class="info" style="margin-top: 20px;">
                <p><strong>ESTIMASI WAKTU</strong></p>
                <div id="timeEstimates" style="font-size: 0.9em;"></div>

                <p><strong>PETUNJUK ARAH</strong></p>
                <div id="instructions" style="font-size: 0.9em; max-height: 200px; overflow-y: auto;"></div>
            </div>
        </div>

        <div class="map-container">
            <div class="search-bar">
                <input type="text" id="searchLocation" placeholder="CARI LOKASI DI DAFTAR POI">
            </div>
            <div id="map"></div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="scripts.js"></script>
</body>
</html>