<?php
session_start();

// 1. Cek apakah user sudah login dan rolenya admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../index.php?error=Akses ditolak. Anda harus login sebagai admin.");
    exit;
}

// 2. Include file konfigurasi database dan fungsi log
require_once 'db_config.php';
require_once 'fungsi_log.php';

// 3. Pastikan request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nim_mhs = isset($_POST['nim_mhs']) ? trim($_POST['nim_mhs']) : '';
    $nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
    $tgl_lahir = isset($_POST['tgl_lahir']) ? trim($_POST['tgl_lahir']) : '';
    $alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';
    $telpon = isset($_POST['telpon']) ? trim($_POST['telpon']) : '';
    $kesukaan = isset($_POST['kesukaan']) ? trim($_POST['kesukaan']) : '';
    $password_mhs = isset($_POST['password_mhs']) ? $_POST['password_mhs'] : '';

    if (empty($nim_mhs) || empty($nama) || empty($tgl_lahir) || empty($password_mhs)) {
        header("location: tambah_mahasiswa_form.php?error=NIM, Nama, Tanggal Lahir, dan Password Awal tidak boleh kosong.");
        exit();
    }

    if (DateTime::createFromFormat('Y-m-d', $tgl_lahir) === false) {
        header("location: tambah_mahasiswa_form.php?error=Format Tanggal Lahir tidak valid (gunakan YYYY-MM-DD).");
        exit();
    }

    $sql_check_nim = "SELECT nim FROM tabel_users WHERE nim = ?";
    if ($stmt_check = $conn->prepare($sql_check_nim)) {
        $stmt_check->bind_param("s", $nim_mhs);
        $stmt_check->execute();
        $stmt_check->store_result();
        if ($stmt_check->num_rows > 0) {
            $stmt_check->close();
            header("location: tambah_mahasiswa_form.php?error=NIM " . htmlspecialchars($nim_mhs) . " sudah terdaftar sebagai pengguna.");
            exit();
        }
        $stmt_check->close();
    } else {
        header("location: tambah_mahasiswa_form.php?error=Gagal memeriksa NIM: " . $conn->error);
        exit();
    }

    // $conn->begin_transaction(); // Idealnya gunakan transaksi

    $insert_mahasiswa_success = false;
    $insert_user_success = false;

    $sql_mahasiswa = "INSERT INTO tabel_mahasiswa (NIM, Nama, Tgl_Lahir, Alamat, Telpon, Kesukaan) VALUES (?, ?, ?, ?, ?, ?)";
    if ($stmt_mahasiswa = $conn->prepare($sql_mahasiswa)) {
        $stmt_mahasiswa->bind_param("ssssss", $nim_mhs, $nama, $tgl_lahir, $alamat, $telpon, $kesukaan);
        if ($stmt_mahasiswa->execute()) {
            $insert_mahasiswa_success = true;
        } else {
            header("location: tambah_mahasiswa_form.php?error=Gagal menyimpan data mahasiswa: " . $stmt_mahasiswa->error);
            $stmt_mahasiswa->close();
            $conn->close();
            exit();
        }
        $stmt_mahasiswa->close();
    } else {
        header("location: tambah_mahasiswa_form.php?error=Gagal persiapan query data mahasiswa: " . $conn->error);
        $conn->close();
        exit();
    }

    if ($insert_mahasiswa_success) {
        $role_mahasiswa = 'mahasiswa';
        $sql_user = "INSERT INTO tabel_users (nim, password_plaintext, role) VALUES (?, ?, ?)";
        
        if ($stmt_user = $conn->prepare($sql_user)) {
            $stmt_user->bind_param("sss", $nim_mhs, $password_mhs, $role_mahasiswa);
            if ($stmt_user->execute()) {
                $insert_user_success = true;
            } else {
                header("location: tambah_mahasiswa_form.php?error=Data mahasiswa berhasil disimpan, tetapi gagal membuat akun login: " . $stmt_user->error);
                $stmt_user->close();
                $conn->close();
                exit();
            }
            $stmt_user->close();
        } else {
            header("location: tambah_mahasiswa_form.php?error=Data mahasiswa berhasil disimpan, tetapi gagal persiapan query akun login: " . $conn->error);
            $conn->close();
            exit();
        }
    }

    if ($insert_mahasiswa_success && $insert_user_success) {
        // $conn->commit(); 
        
        // Catat log aktivitas
        $deskripsi_log = "Admin " . htmlspecialchars($_SESSION["nim"]) . " menambahkan mahasiswa baru NIM: " . htmlspecialchars($nim_mhs) . " (Nama: " . htmlspecialchars($nama) . ").";
        catat_log_aktivitas($conn, "TAMBAH_MAHASISWA_BY_ADMIN", $deskripsi_log);

        header("location: dashboard_admin.php?success=Data mahasiswa " . htmlspecialchars($nama) . " (NIM: " . htmlspecialchars($nim_mhs) . ") berhasil ditambahkan.");
        exit();
    } else {
        header("location: tambah_mahasiswa_form.php?error=Terjadi kesalahan yang tidak diketahui saat menyimpan data.");
        exit();
    }
    
    $conn->close();

} else {
    header("location: tambah_mahasiswa_form.php");
    exit();
}
?>