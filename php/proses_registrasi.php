<?php
// php/proses_registrasi.php

// Tidak perlu session_start() di sini jika fungsi_log sudah menanganinya, 
// atau jika tidak ada operasi session lain sebelum redirect.
// Namun, jika Anda ingin menampilkan pesan error/sukses via session (flash message), maka perlu.
// Untuk konsistensi dengan redirect via GET, kita tidak pakai session untuk pesan di sini.

require_once 'db_config.php';
require_once 'fungsi_log.php'; // Memanggil fungsi log kita

// 1. Pastikan request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Ambil dan sanitasi data dari form POST
    $nim = isset($_POST['nim']) ? trim($_POST['nim']) : '';
    $nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
    $tgl_lahir = isset($_POST['tgl_lahir']) ? trim($_POST['tgl_lahir']) : '';
    $alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';
    $telpon = isset($_POST['telpon']) ? trim($_POST['telpon']) : '';
    $kesukaan = isset($_POST['kesukaan']) ? trim($_POST['kesukaan']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : ''; // Password tidak di-trim
    $agree = isset($_POST['agree']) ? $_POST['agree'] : '';

    // 3. Validasi input dasar
    if (empty($nim) || empty($nama) || empty($tgl_lahir) || empty($password)) {
        header("location: ../register.php?error=NIM, Nama, Tanggal Lahir, dan Password tidak boleh kosong.");
        exit();
    }
    if (empty($agree)) {
        header("location: ../register.php?error=Anda harus menyetujui Syarat dan Ketentuan.");
        exit();
    }
    if (DateTime::createFromFormat('Y-m-d', $tgl_lahir) === false) {
        header("location: ../register.php?error=Format Tanggal Lahir tidak valid (gunakan YYYY-MM-DD).");
        exit();
    }

    // Cek apakah NIM sudah terdaftar di tabel_users (karena nim adalah PK)
    $sql_check_nim = "SELECT nim FROM tabel_users WHERE nim = ?";
    if ($stmt_check = $conn->prepare($sql_check_nim)) {
        $stmt_check->bind_param("s", $nim);
        $stmt_check->execute();
        $stmt_check->store_result();
        if ($stmt_check->num_rows > 0) {
            $stmt_check->close();
            header("location: ../register.php?error=NIM " . htmlspecialchars($nim) . " sudah terdaftar. Silakan gunakan NIM lain atau login.");
            exit();
        }
        $stmt_check->close();
    } else {
        header("location: ../register.php?error=Gagal memeriksa ketersediaan NIM: " . $conn->error);
        exit();
    }
    
    // Idealnya gunakan transaksi database di sini
    // $conn->begin_transaction();

    $insert_mahasiswa_success = false;
    $insert_user_success = false;

    // 4. Siapkan query INSERT untuk tabel_mahasiswa
    $sql_mahasiswa = "INSERT INTO tabel_mahasiswa (NIM, Nama, Tgl_Lahir, Alamat, Telpon, Kesukaan) VALUES (?, ?, ?, ?, ?, ?)";
    if ($stmt_mahasiswa = $conn->prepare($sql_mahasiswa)) {
        $stmt_mahasiswa->bind_param("ssssss", $nim, $nama, $tgl_lahir, $alamat, $telpon, $kesukaan);
        if ($stmt_mahasiswa->execute()) {
            $insert_mahasiswa_success = true;
        } else {
            // $conn->rollback();
            header("location: ../register.php?error=Gagal menyimpan data mahasiswa: " . $stmt_mahasiswa->error);
            $stmt_mahasiswa->close(); $conn->close(); exit();
        }
        $stmt_mahasiswa->close();
    } else {
        // $conn->rollback();
        header("location: ../register.php?error=Gagal persiapan query data mahasiswa: " . $conn->error);
        $conn->close(); exit();
    }

    // 5. Jika data mahasiswa berhasil disimpan, buat akun di tabel_users
    if ($insert_mahasiswa_success) {
        $role_mahasiswa = 'mahasiswa';
        // Ingat: password disimpan sebagai plaintext, yang SANGAT TIDAK AMAN
        $sql_user = "INSERT INTO tabel_users (nim, password_plaintext, role) VALUES (?, ?, ?)";
        
        if ($stmt_user = $conn->prepare($sql_user)) {
            $stmt_user->bind_param("sss", $nim, $password, $role_mahasiswa);
            if ($stmt_user->execute()) {
                $insert_user_success = true;
            } else {
                // $conn->rollback(); 
                // Mungkin perlu menghapus data mahasiswa yang sudah terlanjur masuk jika ini gagal
                header("location: ../register.php?error=Data mahasiswa berhasil disimpan, tetapi gagal membuat akun login: " . $stmt_user->error);
                $stmt_user->close(); $conn->close(); exit();
            }
            $stmt_user->close();
        } else {
            // $conn->rollback();
            header("location: ../register.php?error=Data mahasiswa berhasil disimpan, tetapi gagal persiapan query akun login: " . $conn->error);
            $conn->close(); exit();
        }
    }

    // 6. Jika semua berhasil
    if ($insert_mahasiswa_success && $insert_user_success) {
        // $conn->commit(); 
        
        // Catat log aktivitas registrasi
        // Kita override user_identifier dan role_pelaku karena belum ada session untuk user baru ini
        $deskripsi_log = "Mahasiswa baru dengan NIM " . htmlspecialchars($nim) . " (Nama: " . htmlspecialchars($nama) . ") telah mendaftar.";
        catat_log_aktivitas($conn, "REGISTRASI_MHS_BERHASIL", $deskripsi_log, $nim, 'mahasiswa');

        // Redirect ke halaman login dengan pesan sukses
        header("location: ../index.php?success=Registrasi berhasil untuk NIM " . htmlspecialchars($nim) . ". Silakan login.");
        exit();
    } else {
        // $conn->rollback();
        // Jika ada kegagalan yang tidak tertangkap di atas
        catat_log_aktivitas($conn, "REGISTRASI_MHS_GAGAL", "Percobaan registrasi gagal untuk NIM: " . htmlspecialchars($nim), $nim, 'calon_mahasiswa');
        header("location: ../register.php?error=Registrasi gagal karena kesalahan sistem. Silakan coba lagi.");
        exit();
    }
    
    $conn->close();

} else {
    // Jika bukan metode POST, redirect ke halaman registrasi
    header("location: ../register.php");
    exit();
}
?>