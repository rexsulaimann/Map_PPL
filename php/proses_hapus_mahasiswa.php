<?php
session_start();

// 1. Cek apakah user sudah login dan rolenya admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../index.php?error=Akses ditolak. Anda harus login sebagai admin.");
    exit;
}

// 2. Include file konfigurasi database
require_once 'db_config.php';

// 3. Pastikan parameter NIM ada di URL (dikirim via GET)
if (isset($_GET["nim"]) && !empty(trim($_GET["nim"]))) {
    $nim_to_delete = trim($_GET["nim"]);

    // Untuk konsistensi data yang lebih baik, idealnya gunakan transaksi database di sini.
    // $conn->begin_transaction();

    $delete_user_success = false;
    $delete_mahasiswa_success = false;

    // 4. Hapus dulu dari tabel_users (akun login mahasiswa)
    // Ini dilakukan terlebih dahulu untuk menghindari masalah foreign key jika ada (walaupun di skema kita saat ini tidak ada FK formal dari tabel_users ke tabel_mahasiswa)
    // dan juga karena akun login bergantung pada data mahasiswa.
    $sql_delete_user = "DELETE FROM tabel_users WHERE nim = ? AND role = 'mahasiswa'"; // Pastikan hanya role mahasiswa yang terhapus dengan NIM ini
    
    if ($stmt_user = $conn->prepare($sql_delete_user)) {
        $stmt_user->bind_param("s", $nim_to_delete);
        
        if ($stmt_user->execute()) {
            // Berhasil menghapus dari tabel_users atau NIM tersebut tidak ada sebagai 'mahasiswa' di tabel_users
            // $stmt_user->affected_rows akan > 0 jika ada yang dihapus, 0 jika tidak ada yang cocok.
            // Kita anggap ini sukses untuk melanjutkan ke penghapusan data mahasiswa.
            $delete_user_success = true; 
        } else {
            // Gagal menghapus dari tabel_users
            // $conn->rollback(); // Jika menggunakan transaksi
            header("location: dashboard_admin.php?error=Gagal menghapus akun login mahasiswa: " . $stmt_user->error);
            $stmt_user->close();
            $conn->close();
            exit();
        }
        $stmt_user->close();
    } else {
        // $conn->rollback(); // Jika menggunakan transaksi
        header("location: dashboard_admin.php?error=Gagal persiapan query hapus akun login: " . $conn->error);
        $conn->close();
        exit();
    }

    // 5. Jika penghapusan akun (atau pengecekan) di tabel_users berhasil, lanjut hapus dari tabel_mahasiswa
    // Atau, jika Anda yakin setiap mahasiswa PASTI punya akun user, maka $delete_user_success harus dicek apakah affected_rows > 0.
    // Namun, untuk skema ini, kita bisa asumsikan jika tidak ada error, maka bisa lanjut.
    if ($delete_user_success) { // Atau cek affected_rows jika perlu
        $sql_delete_mahasiswa = "DELETE FROM tabel_mahasiswa WHERE NIM = ?";
        if ($stmt_mahasiswa = $conn->prepare($sql_delete_mahasiswa)) {
            $stmt_mahasiswa->bind_param("s", $nim_to_delete);

            if ($stmt_mahasiswa->execute()) {
                if ($stmt_mahasiswa->affected_rows > 0) {
                    $delete_mahasiswa_success = true;
                } else {
                    // Tidak ada data mahasiswa dengan NIM tersebut yang terhapus (mungkin sudah dihapus sebelumnya atau NIM salah)
                    // Jika akun user sudah terhapus (atau tidak ada), ini bisa dianggap "sukses" atau perlu pesan khusus.
                    // Untuk kasus ini, jika akun user tidak ada/terhapus dan data mahasiswa juga tidak ada, kita anggap selesai.
                    // Jika akun user ada dan terhapus, tapi data mahasiswa tidak ada, ini aneh.
                    // Kita anggap jika sampai sini dan affected_rows == 0, berarti data mahasiswa memang tidak ada (atau sudah terhapus).
                    $delete_mahasiswa_success = true; // Anggap "sukses" jika tidak ada data untuk dihapus
                                                   // atau beri pesan spesifik jika $stmt_user->affected_rows > 0 tapi ini 0.
                }
            } else {
                // Gagal menghapus dari tabel_mahasiswa
                // $conn->rollback(); // Jika menggunakan transaksi
                header("location: dashboard_admin.php?error=Gagal menghapus data mahasiswa: " . $stmt_mahasiswa->error);
                $stmt_mahasiswa->close();
                $conn->close();
                exit();
            }
            $stmt_mahasiswa->close();
        } else {
            // $conn->rollback(); // Jika menggunakan transaksi
            header("location: dashboard_admin.php?error=Gagal persiapan query hapus data mahasiswa: " . $conn->error);
            $conn->close();
            exit();
        }
    }


    // 6. Jika semua berhasil (atau setidaknya tidak ada error fatal)
    if ($delete_user_success && $delete_mahasiswa_success) {
        // $conn->commit(); // Jika menggunakan transaksi
        header("location: dashboard_admin.php?success=Data mahasiswa dengan NIM " . htmlspecialchars($nim_to_delete) . " berhasil dihapus.");
        exit();
    } else {
        // Seharusnya kondisi ini sudah ditangani oleh error di atas.
        // $conn->rollback(); // Jika menggunakan transaksi
        // Mungkin ada kasus di mana $delete_user_success=true (tidak ada error) tapi $delete_mahasiswa_success=false karena data tidak ada,
        // tapi tidak ada error query.
        if (!$delete_mahasiswa_success && $delete_user_success){
             header("location: dashboard_admin.php?error=Akun login mahasiswa mungkin telah dihapus atau tidak ada, tetapi data profil mahasiswa tidak ditemukan untuk NIM " . htmlspecialchars($nim_to_delete) . ".");
        } else {
             header("location: dashboard_admin.php?error=Terjadi kesalahan yang tidak diketahui saat menghapus data.");
        }
        exit();
    }

    // Tutup koneksi database
    $conn->close();

} else {
    // Jika parameter NIM tidak ada atau kosong
    header("location: dashboard_admin.php?error=NIM mahasiswa untuk dihapus tidak valid atau tidak ditemukan.");
    exit();
}
?>