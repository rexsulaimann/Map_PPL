<?php
session_start();

// 1. Cek apakah user sudah login dan rolenya mahasiswa
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'mahasiswa') {
    // Mengarahkan ke index.php karena Anda menyebutkan login page sekarang adalah index.php
    header("location: ../index.php?error=Anda harus login sebagai mahasiswa untuk mengakses halaman ini."); 
    exit;
}

// 2. Include file konfigurasi database
require_once 'db_config.php'; // Pastikan path ini benar

// 3. Ambil NIM mahasiswa dari session
$nim_mahasiswa = $_SESSION["nim"];

// 4. Siapkan variabel untuk menampung data mahasiswa dan pesan
$nama = $tgl_lahir = $alamat = $telpon = $kesukaan = "";
$db_error_message = ""; // Pesan error spesifik dari operasi database
$get_success_message = ""; // Pesan sukses dari parameter GET
$get_error_message = "";   // Pesan error dari parameter GET

// 5. Ambil data mahasiswa dari database
$sql = "SELECT Nama, Tgl_Lahir, Alamat, Telpon, Kesukaan FROM tabel_mahasiswa WHERE NIM = ?";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $nim_mahasiswa);
    
    if ($stmt->execute()) {
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($nama, $tgl_lahir, $alamat, $telpon, $kesukaan);
            $stmt->fetch();
        } else {
            $db_error_message = "Data mahasiswa dengan NIM " . htmlspecialchars($nim_mahasiswa) . " tidak ditemukan.";
        }
    } else {
        $db_error_message = "Oops! Terjadi kesalahan saat menjalankan query untuk mengambil data.";
    }
    $stmt->close();
} else {
    $db_error_message = "Oops! Terjadi kesalahan pada persiapan query: " . $conn->error;
}

// 6. Cek apakah ada pesan sukses dari proses update (setelah redirect dari proses_update_profil.php)
if (isset($_GET['success'])) {
    $get_success_message = htmlspecialchars($_GET['success']);
}
// Cek apakah ada pesan error dari proses update (setelah redirect)
if (isset($_GET['update_error'])) {
    $get_error_message = htmlspecialchars($_GET['update_error']);
}

// Koneksi akan ditutup otomatis di akhir skrip PHP, atau Anda bisa menutupnya di sini jika mau
// $conn->close(); 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa - Edit Profil</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/my-login.css"> 
    <style>
        body.my-login-page { 
            padding-top: 20px; 
            background-color: #f7f9fb; /* Menyesuaikan dengan body login jika diperlukan */
        }
        .container.dashboard-container { /* Class baru untuk styling khusus dashboard */
            max-width: 700px; 
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,.05);
        }
    </style>
</head>
<body class="my-login-page"> 
    <div class="container dashboard-container">
        <div class="text-center mb-4">
            <img src="../img/logo1.jpg" alt="logo" style="width:70px; border-radius:50%;"> </div>
        <h4 class="card-title text-center">Profil Mahasiswa</h4>
        <p class="text-center">Selamat datang, <strong><?php echo htmlspecialchars($nama); ?></strong> (NIM: <?php echo htmlspecialchars($nim_mahasiswa); ?>)!</p>
        
        <?php if (!empty($get_success_message)): ?>
            <div class="alert alert-success"><?php echo $get_success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($get_error_message)): // Pesan error dari proses update ?>
            <div class="alert alert-danger"><?php echo $get_error_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($db_error_message) && empty($get_success_message) && empty($get_error_message)): // Pesan error dari pengambilan data, hanya tampil jika tidak ada pesan dari GET ?>
            <div class="alert alert-warning"><?php echo $db_error_message; ?></div>
        <?php endif; ?>

        <hr>
        <h5>Edit Data Profil Anda:</h5>
        <form action="proses_update_profil.php" method="POST" class="needs-validation" novalidate>
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($nama); ?>" required>
                <div class="invalid-feedback">Nama lengkap tidak boleh kosong.</div>
            </div>
            <div class="form-group">
                <label for="tgl_lahir">Tanggal Lahir</label>
                <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" value="<?php echo htmlspecialchars($tgl_lahir); ?>" required>
                <div class="invalid-feedback">Tanggal lahir tidak boleh kosong.</div>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="3"><?php echo htmlspecialchars($alamat); ?></textarea>
            </div>
            <div class="form-group">
                <label for="telpon">Nomor Telepon</label>
                <input type="text" class="form-control" id="telpon" name="telpon" value="<?php echo htmlspecialchars($telpon); ?>">
            </div>
            <div class="form-group">
                <label for="kesukaan">Kesukaan</label>
                <input type="text" class="form-control" id="kesukaan" name="kesukaan" value="<?php echo htmlspecialchars($kesukaan); ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Simpan Perubahan</button>
            <a href="logout.php" class="btn btn-danger btn-block mt-2">Logout</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        // Script untuk validasi Bootstrap (jika belum ada di my-login.js atau terpisah)
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html>