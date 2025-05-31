<?php
// php/logout.php
session_start();

// Include koneksi dan fungsi log sebelum session dihancurkan
// Hanya catat log jika pengguna memang sedang login
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Pastikan file-file ini ada dan path-nya benar
    require_once 'db_config.php'; 
    require_once 'fungsi_log.php';

    $user_identifier_logout = isset($_SESSION["nim"]) ? $_SESSION["nim"] : 'Pengguna Tidak Dikenal';
    $role_logout = isset($_SESSION["role"]) ? $_SESSION["role"] : 'Tidak Diketahui';
    $deskripsi_log = "Pengguna " . htmlspecialchars($user_identifier_logout) . " (" . htmlspecialchars($role_logout) . ") telah logout.";
    catat_log_aktivitas($conn, "LOGOUT", $deskripsi_log);
    
    if (isset($conn) && $conn instanceof mysqli) { // Cek apakah $conn adalah objek mysqli yang valid
        $conn->close();
    }
}

// Hapus semua variabel session
$_SESSION = array();

// Hancurkan session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Redirect ke halaman login dengan pesan sukses
header("location: ../index.php?success=Anda telah berhasil logout.");
exit;
?>