<?php
session_start();
// Cek apakah user sudah login dan rolenya admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../index.php?error=Akses ditolak. Anda harus login sebagai admin.");
    exit;
}

// Include file konfigurasi database (opsional di sini, tapi mungkin berguna jika ada validasi awal dari DB)
// require_once 'db_config.php';

$error_message = "";
$success_message = "";

// Cek apakah ada pesan dari proses sebelumnya (jika ada redirect dengan error/sukses ke form ini lagi)
if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error']);
}
if (isset($_GET['success'])) {
    $success_message = htmlspecialchars($_GET['success']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tambah Mahasiswa Baru</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/my-login.css">
    <style>
        body.my-login-page { 
            padding-top: 20px; 
            background-color: #f7f9fb;
        }
        .container.form-container {
            max-width: 700px; 
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,.05);
            margin-top: 20px;
            margin-bottom: 40px;
        }
    </style>
</head>
<body class="my-login-page">
    <div class="container form-container">
        <div class="text-center mb-4">
            <img src="../img/logo.jpg" alt="logo" style="width:70px; border-radius:50%;">
        </div>
        <h4 class="card-title text-center">Tambah Data Mahasiswa Baru</h4>
        <p class="text-center">Admin: <?php echo htmlspecialchars($_SESSION["nim"]); ?></p>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <hr>

        <form action="proses_tambah_mahasiswa.php" method="POST" class="needs-validation" novalidate>
            <div class="form-group">
                <label for="nim_mhs">NIM Mahasiswa</label>
                <input type="text" class="form-control" id="nim_mhs" name="nim_mhs" required>
                <div class="invalid-feedback">NIM Mahasiswa tidak boleh kosong.</div>
            </div>
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
                <div class="invalid-feedback">Nama lengkap tidak boleh kosong.</div>
            </div>
            <div class="form-group">
                <label for="tgl_lahir">Tanggal Lahir</label>
                <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" required>
                <div class="invalid-feedback">Tanggal lahir tidak boleh kosong.</div>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="telpon">Nomor Telepon</label>
                <input type="text" class="form-control" id="telpon" name="telpon">
            </div>
            <div class="form-group">
                <label for="kesukaan">Kesukaan</label>
                <input type="text" class="form-control" id="kesukaan" name="kesukaan">
            </div>
            
            <hr>
            <h5>Buat Akun Login untuk Mahasiswa:</h5>
            <div class="form-group">
                <label for="password_mhs">Password Awal Mahasiswa</label>
                <input type="password" class="form-control" id="password_mhs" name="password_mhs" required data-eye> <div class="invalid-feedback">Password awal tidak boleh kosong.</div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Simpan Data Mahasiswa</button>
            <a href="dashboard_admin.php" class="btn btn-secondary btn-block mt-2">Kembali ke Dashboard</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="../js/my-login.js"></script> <script>
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