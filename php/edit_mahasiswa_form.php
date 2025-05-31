<?php
session_start();
// Cek apakah user sudah login dan rolenya admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../index.php?error=Akses ditolak. Anda harus login sebagai admin.");
    exit;
}

require_once 'db_config.php';

$nim_to_edit = "";
$nama = $tgl_lahir = $alamat = $telpon = $kesukaan = "";
$error_message = "";
$success_message = "";

// 1. Ambil NIM dari parameter GET
if (isset($_GET['nim']) && !empty(trim($_GET['nim']))) {
    $nim_to_edit = trim($_GET['nim']);

    // 2. Ambil data mahasiswa yang akan diedit dari tabel_mahasiswa
    $sql_mhs = "SELECT Nama, Tgl_Lahir, Alamat, Telpon, Kesukaan FROM tabel_mahasiswa WHERE NIM = ?";
    if ($stmt_mhs = $conn->prepare($sql_mhs)) {
        $stmt_mhs->bind_param("s", $nim_to_edit);
        if ($stmt_mhs->execute()) {
            $stmt_mhs->store_result();
            if ($stmt_mhs->num_rows == 1) {
                $stmt_mhs->bind_result($nama, $tgl_lahir, $alamat, $telpon, $kesukaan);
                $stmt_mhs->fetch();
            } else {
                $error_message = "Data mahasiswa dengan NIM " . htmlspecialchars($nim_to_edit) . " tidak ditemukan.";
            }
        } else {
            $error_message = "Gagal mengambil data mahasiswa: " . $stmt_mhs->error;
        }
        $stmt_mhs->close();
    } else {
        $error_message = "Gagal persiapan query data mahasiswa: " . $conn->error;
    }
} else {
    // Jika tidak ada NIM, redirect atau tampilkan error
    header("location: dashboard_admin.php?error=NIM mahasiswa untuk diedit tidak ditemukan.");
    exit;
}

// Cek pesan dari redirect (jika ada)
if (isset($_GET['error_update'])) {
    $error_message = htmlspecialchars($_GET['error_update']);
}
if (isset($_GET['success_update'])) {
    $success_message = htmlspecialchars($_GET['success_update']);
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit Data Mahasiswa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/my-login.css">
    <style>
        body.my-login-page { padding-top: 20px; background-color: #f7f9fb; }
        .container.form-container { max-width: 700px; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,.05); margin-top: 20px; margin-bottom: 40px;}
    </style>
</head>
<body class="my-login-page">
    <div class="container form-container">
        <div class="text-center mb-4">
            <img src="../img/logo1.jpg" alt="logo" style="width:70px; border-radius:50%;">
        </div>
        <h4 class="card-title text-center">Edit Data Mahasiswa</h4>
        <p class="text-center">Mengedit data untuk NIM: <strong><?php echo htmlspecialchars($nim_to_edit); ?></strong></p>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <hr>

        <?php if (empty($error_message) || isset($_GET['success_update'])): // Tampilkan form hanya jika data awal berhasil diambil atau ada pesan sukses ?>
        <form action="proses_edit_mahasiswa.php" method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="nim_original" value="<?php echo htmlspecialchars($nim_to_edit); ?>">
            
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
            
            <hr>
            <h5>Ubah Password Login Mahasiswa (Opsional):</h5>
            <div class="form-group">
                <label for="new_password_mhs">Password Baru</label>
                <input type="password" class="form-control" id="new_password_mhs" name="new_password_mhs" data-eye>
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Update Data Mahasiswa</button>
            <a href="dashboard_admin.php" class="btn btn-secondary btn-block mt-2">Kembali ke Dashboard</a>
        </form>
        <?php elseif (!isset($_GET['success_update'])): ?>
            <a href="dashboard_admin.php" class="btn btn-secondary btn-block mt-2">Kembali ke Dashboard</a>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="../js/my-login.js"></script>
    <script>
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