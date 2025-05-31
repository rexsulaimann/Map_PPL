<?php
session_start();

// 1. Cek apakah user sudah login dan rolenya admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../index.php?error=Akses ditolak. Anda harus login sebagai admin.");
    exit;
}

// 2. Include file konfigurasi database dan fungsi log
require_once 'db_config.php';
require_once 'fungsi_log.php'; // Pastikan ini ada

// 3. Pastikan request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nim_original = isset($_POST['nim_original']) ? trim($_POST['nim_original']) : '';
    $nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
    $tgl_lahir = isset($_POST['tgl_lahir']) ? trim($_POST['tgl_lahir']) : '';
    $alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';
    $telpon = isset($_POST['telpon']) ? trim($_POST['telpon']) : '';
    $kesukaan = isset($_POST['kesukaan']) ? trim($_POST['kesukaan']) : '';
    $new_password_mhs = isset($_POST['new_password_mhs']) ? $_POST['new_password_mhs'] : '';

    if (empty($nim_original) || empty($nama) || empty($tgl_lahir)) {
        header("location: edit_mahasiswa_form.php?nim=" . urlencode($nim_original) . "&error_update=NIM, Nama, dan Tanggal Lahir tidak boleh kosong.");
        exit();
    }

    if (DateTime::createFromFormat('Y-m-d', $tgl_lahir) === false) {
        header("location: edit_mahasiswa_form.php?nim=" . urlencode($nim_original) . "&error_update=Format Tanggal Lahir tidak valid (gunakan YYYY-MM-DD).");
        exit();
    }

    // Idealnya gunakan transaksi database di sini
    // $conn->begin_transaction(); 

    $update_mahasiswa_success = false;
    $update_password_success = true; 
    $password_changed_message = "Password tidak diubah."; // Default message

    // Update data di tabel_mahasiswa
    $sql_mahasiswa = "UPDATE tabel_mahasiswa SET Nama = ?, Tgl_Lahir = ?, Alamat = ?, Telpon = ?, Kesukaan = ? WHERE NIM = ?";
    
    if ($stmt_mahasiswa = $conn->prepare($sql_mahasiswa)) {
        $stmt_mahasiswa->bind_param("ssssss", $nama, $tgl_lahir, $alamat, $telpon, $kesukaan, $nim_original);
        
        if ($stmt_mahasiswa->execute()) {
            // affected_rows bisa 0 jika data yang diinput sama dengan data yang sudah ada, ini bukan error
            if ($stmt_mahasiswa->affected_rows >= 0) { 
                $update_mahasiswa_success = true;
            } else {
                // Ini kondisi error spesifik saat update, meskipun execute mungkin true tapi ada masalah lain
                 header("location: edit_mahasiswa_form.php?nim=" . urlencode($nim_original) . "&error_update=Gagal memperbarui data mahasiswa (affected rows < 0).");
                 $stmt_mahasiswa->close(); $conn->close(); exit();
            }
        } else {
            header("location: edit_mahasiswa_form.php?nim=" . urlencode($nim_original) . "&error_update=Gagal eksekusi update data mahasiswa: " . $stmt_mahasiswa->error);
             $stmt_mahasiswa->close(); $conn->close(); exit();
        }
        $stmt_mahasiswa->close();
    } else {
        header("location: edit_mahasiswa_form.php?nim=" . urlencode($nim_original) . "&error_update=Gagal persiapan query data mahasiswa: " . $conn->error);
         $conn->close(); exit();
    }

    // Jika password baru dimasukkan, update password di tabel_users
    if ($update_mahasiswa_success && !empty($new_password_mhs)) {
        $sql_user_pass = "UPDATE tabel_users SET password_plaintext = ? WHERE nim = ? AND role = 'mahasiswa'";
        if ($stmt_user_pass = $conn->prepare($sql_user_pass)) {
            $stmt_user_pass->bind_param("ss", $new_password_mhs, $nim_original);
            if ($stmt_user_pass->execute()) {
                if ($stmt_user_pass->affected_rows > 0) {
                    $password_changed_message = "Password mahasiswa berhasil diubah.";
                } elseif ($stmt_user_pass->affected_rows == 0) {
                    // Tidak ada user yang cocok atau password baru sama dengan yang lama
                    $password_changed_message = "Password mahasiswa tidak diubah (user tidak cocok atau tidak ada perubahan)."; 
                }
                // Jika execute true dan affected_rows >=0, $update_password_success tetap true
            } else {
                $update_password_success = false; // Error saat eksekusi
                $password_changed_message = "Gagal mengubah password mahasiswa: " . $stmt_user_pass->error;
            }
            $stmt_user_pass->close();
        } else {
            $update_password_success = false; // Error saat persiapan query
            $password_changed_message = "Gagal persiapan query ubah password mahasiswa: " . $conn->error;
        }
    }

    // Proses hasil dan redirect
    if ($update_mahasiswa_success && $update_password_success) {
        // Catat log aktivitas
        $deskripsi_log = "Admin " . htmlspecialchars($_SESSION["nim"]) . " mengedit data mahasiswa NIM: " . htmlspecialchars($nim_original) . ". " . $password_changed_message;
        catat_log_aktivitas($conn, "EDIT_MHS_BY_ADMIN", $deskripsi_log);
        
        // $conn->commit(); // Jika menggunakan transaksi
        header("location: dashboard_admin.php?success=Data mahasiswa NIM " . htmlspecialchars($nim_original) . " berhasil diperbarui. " . htmlspecialchars($password_changed_message));
        exit();
    } else {
        // Jika $update_mahasiswa_success true tapi $update_password_success false
        $final_error_message = $update_mahasiswa_success ? $password_changed_message : "Gagal memperbarui data mahasiswa utama.";
        // $conn->rollback(); // Jika menggunakan transaksi
        header("location: edit_mahasiswa_form.php?nim=" . urlencode($nim_original) . "&error_update=" . urlencode($final_error_message));
        exit();
    }
    
    $conn->close();

} else {
    // Jika bukan metode POST
    header("location: dashboard_admin.php");
    exit();
}
?>