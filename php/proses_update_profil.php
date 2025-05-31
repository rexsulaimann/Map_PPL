<?php
session_start();

// 1. Cek apakah user sudah login dan rolenya mahasiswa
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'mahasiswa') {
    header("location: ../index.php?error=Akses ditolak. Silakan login terlebih dahulu.");
    exit;
}

// 2. Include file konfigurasi database dan fungsi log
require_once 'db_config.php';
require_once 'fungsi_log.php'; // Pastikan ini ada

// 3. Pastikan request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nim_mahasiswa = $_SESSION["nim"]; // Ambil NIM dari session
    $nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
    $tgl_lahir = isset($_POST['tgl_lahir']) ? trim($_POST['tgl_lahir']) : '';
    $alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';
    $telpon = isset($_POST['telpon']) ? trim($_POST['telpon']) : '';
    $kesukaan = isset($_POST['kesukaan']) ? trim($_POST['kesukaan']) : '';

    // Validasi input dasar
    if (empty($nama) || empty($tgl_lahir)) {
        header("location: dashboard_mahasiswa.php?update_error=Nama dan Tanggal Lahir tidak boleh kosong.");
        exit();
    }

    if (DateTime::createFromFormat('Y-m-d', $tgl_lahir) === false) {
        header("location: dashboard_mahasiswa.php?update_error=Format Tanggal Lahir tidak valid (gunakan YYYY-MM-DD).");
        exit();
    }

    // Siapkan query UPDATE
    $sql = "UPDATE tabel_mahasiswa SET Nama = ?, Tgl_Lahir = ?, Alamat = ?, Telpon = ?, Kesukaan = ? WHERE NIM = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssss", $nama, $tgl_lahir, $alamat, $telpon, $kesukaan, $nim_mahasiswa);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                // Catat log aktivitas jika ada perubahan
                $deskripsi_log = "Mahasiswa NIM " . htmlspecialchars($nim_mahasiswa) . " memperbarui profilnya.";
                catat_log_aktivitas($conn, "UPDATE_PROFIL_MHS", $deskripsi_log);

                header("location: dashboard_mahasiswa.php?success=Profil berhasil diperbarui.");
                exit();
            } elseif ($stmt->affected_rows == 0) {
                // Tidak ada perubahan data
                header("location: dashboard_mahasiswa.php?success=Tidak ada perubahan pada data profil Anda.");
                exit();
            } else {
                // affected_rows < 0 menunjukkan error
                header("location: dashboard_mahasiswa.php?update_error=Gagal memperbarui profil (affected rows < 0).");
                exit();
            }
        } else {
            // Error saat eksekusi
            error_log("MySQLi Execute Error (Update Profil Mhs): " . $stmt->error);
            header("location: dashboard_mahasiswa.php?update_error=Gagal memperbarui profil. Kesalahan eksekusi query.");
            exit();
        }
        $stmt->close();
    } else {
        // Error saat persiapan query
        error_log("MySQLi Prepare Error (Update Profil Mhs): " . $conn->error);
        header("location: dashboard_mahasiswa.php?update_error=Gagal memperbarui profil. Kesalahan persiapan query.");
        exit();
    }
    
    $conn->close();

} else {
    // Jika bukan metode POST
    header("location: dashboard_mahasiswa.php");
    exit();
}
?>