<?php
// php/fungsi_log.php

if (session_status() == PHP_SESSION_NONE) { // Mulai session jika belum dimulai secara eksplisit
    session_start();
}

function catat_log_aktivitas($conn, $jenis_aksi, $deskripsi_aksi = "", $user_identifier_override = null, $role_pelaku_override = null) {
    // Ambil user_identifier dan role
    // Prioritaskan override jika diberikan (untuk kasus seperti registrasi)
    $user_identifier = $user_identifier_override ?? ($_SESSION["nim"] ?? 'SISTEM');
    $role_pelaku = $role_pelaku_override ?? ($_SESSION["role"] ?? 'Tidak Diketahui');

    $sql_log = "INSERT INTO tabel_log_aktivitas (user_identifier, role_pelaku, jenis_aksi, deskripsi_aksi) VALUES (?, ?, ?, ?)";
    
    if ($stmt_log = $conn->prepare($sql_log)) {
        $stmt_log->bind_param("ssss", $user_identifier, $role_pelaku, $jenis_aksi, $deskripsi_aksi);
        if (!$stmt_log->execute()) {
            error_log("Gagal mencatat log aktivitas: " . $stmt_log->error);
        }
        $stmt_log->close();
    } else {
        error_log("Gagal persiapan query log aktivitas: " . $conn->error);
    }
}
?>