<?php
// php/proses_login.php

// Mulai session (WAJIB di paling atas sebelum output apapun)
session_start();

// Include file konfigurasi database dan fungsi log
require_once 'db_config.php'; 
require_once 'fungsi_log.php'; // Memanggil fungsi log kita

// Cek apakah data form sudah dikirim (metode POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $input_identifier = isset($_POST['nim']) ? trim($_POST['nim']) : '';
    $input_password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($input_identifier) || empty($input_password)) {
        header("location: ../index.php?error=NIM/Username dan Password tidak boleh kosong.");
        exit();
    }

    $sql = "SELECT nim, password_plaintext, role FROM tabel_users WHERE nim = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $param_identifier);
        $param_identifier = $input_identifier;
        
        if ($stmt->execute()) {
            $stmt->store_result();
            
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($db_nim_username, $db_password_plaintext, $db_role);
                if ($stmt->fetch()) {
                    if ($input_password === $db_password_plaintext) {
                        // Password cocok! Login berhasil.
                        
                        // Simpan data ke session
                        $_SESSION["loggedin"] = true;
                        $_SESSION["nim"] = $db_nim_username; 
                        $_SESSION["role"] = $db_role;
                        
                        // Catat log aktivitas
                        $deskripsi_log = "Pengguna " . htmlspecialchars($db_nim_username) . " berhasil login sebagai " . htmlspecialchars($db_role) . ".";
                        catat_log_aktivitas($conn, "LOGIN_BERHASIL", $deskripsi_log);
                        
                        // Arahkan pengguna berdasarkan role
                        if ($db_role == 'admin') {
                            header("location: dashboard_admin.php");
                            exit();
                        } elseif ($db_role == 'mahasiswa') {
                            header("location: dashboard_mahasiswa.php");
                            exit();
                        } else {
                            // Role tidak dikenali
                            catat_log_aktivitas($conn, "LOGIN_GAGAL", "Percobaan login dengan role tidak valid: " . htmlspecialchars($db_nim_username));
                            $_SESSION = array(); 
                            session_destroy();   
                            header("location: ../index.php?error=Role pengguna tidak valid. Silakan hubungi administrator.");
                            exit();
                        }
                    } else {
                        // Password tidak cocok
                        catat_log_aktivitas($conn, "LOGIN_GAGAL", "Percobaan login gagal (password salah) untuk: " . htmlspecialchars($input_identifier));
                        header("location: ../index.php?error=NIM/Username atau Password salah.");
                        exit();
                    }
                }
            } else {
                // NIM/Username tidak ditemukan
                catat_log_aktivitas($conn, "LOGIN_GAGAL", "Percobaan login gagal (user tidak ditemukan) untuk: " . htmlspecialchars($input_identifier));
                header("location: ../index.php?error=NIM/Username atau Password salah.");
                exit();
            }
        } else {
            error_log("MySQLi Execute Error (Login): " . $stmt->error);
            header("location: ../index.php?error=Terjadi kesalahan pada server. Silakan coba lagi nanti.");
            exit();
        }
        $stmt->close();
    } else {
        error_log("MySQLi Prepare Error (Login): " . $conn->error);
        header("location: ../index.php?error=Terjadi kesalahan pada persiapan query. Silakan coba lagi nanti.");
        exit();
    }
    
    $conn->close();

} else {
    header("location: ../index.php");
    exit();
}
?>