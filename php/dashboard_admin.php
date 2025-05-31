<?php
session_start();
// Cek apakah user sudah login dan rolenya admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../index.php?error=Akses ditolak. Anda harus login sebagai admin.");
    exit;
}

// Include file konfigurasi database
require_once 'db_config.php';

$search_term = ""; 
$mahasiswa_list = []; 
$log_aktivitas_list = []; // Array baru untuk menampung data log
$error_message = "";
$success_message = ""; 

if (isset($_GET['success'])) {
    $success_message = htmlspecialchars($_GET['success']);
}

// --- Logika untuk mengambil data mahasiswa (pencarian atau semua) ---
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = trim($_GET['search']);
    $search_param = "%" . $search_term . "%";
    $sql_mhs = "SELECT NIM, Nama, Tgl_Lahir, Alamat, Telpon, Kesukaan FROM tabel_mahasiswa 
                WHERE NIM LIKE ? OR Nama LIKE ? 
                ORDER BY Nama ASC";
    
    if($stmt_mhs_search = $conn->prepare($sql_mhs)){
        $stmt_mhs_search->bind_param("ss", $search_param, $search_param);
        if($stmt_mhs_search->execute()){
            $result_mhs = $stmt_mhs_search->get_result();
            if ($result_mhs->num_rows > 0) {
                while ($row_mhs = $result_mhs->fetch_assoc()) {
                    $mahasiswa_list[] = $row_mhs;
                }
            }
        } else {
            $error_message .= " Error pencarian mahasiswa: " . $stmt_mhs_search->error;
        }
        $stmt_mhs_search->close();
    } else {
        $error_message .= " Error persiapan query pencarian mhs: " . $conn->error;
    }
} else {
    $sql_mhs_all = "SELECT NIM, Nama, Tgl_Lahir, Alamat, Telpon, Kesukaan FROM tabel_mahasiswa ORDER BY Nama ASC";
    $result_mhs_all = $conn->query($sql_mhs_all);
    if ($result_mhs_all) {
        if ($result_mhs_all->num_rows > 0) {
            while ($row_mhs_all = $result_mhs_all->fetch_assoc()) {
                $mahasiswa_list[] = $row_mhs_all;
            }
        }
    } else {
        $error_message .= " Gagal mengambil data mahasiswa: " . $conn->error;
    }
}

// --- Logika untuk mengambil data log aktivitas ---
$sql_log = "SELECT id_log, timestamp_aksi, user_identifier, role_pelaku, jenis_aksi, deskripsi_aksi 
            FROM tabel_log_aktivitas 
            ORDER BY timestamp_aksi DESC 
            LIMIT 50"; // Ambil 50 log terbaru

$result_log = $conn->query($sql_log);
if ($result_log) {
    if ($result_log->num_rows > 0) {
        while ($row_log = $result_log->fetch_assoc()) {
            $log_aktivitas_list[] = $row_log;
        }
    }
} else {
    $error_message .= " Gagal mengambil data log aktivitas: " . $conn->error;
}

$conn->close(); // Koneksi ditutup setelah semua data diambil
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/my-login.css">
    <style>
        body.my-login-page { 
            padding-top: 20px; 
            background-color: #f7f9fb;
        }
        .container.dashboard-container {
            max-width: 90%; 
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,.05);
            margin-top: 20px;
            margin-bottom: 40px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .action-buttons a {
            margin-right: 5px;
        }
        .form-inline { 
            display: inline-block;
        }
        .nav-tabs { 
            margin-bottom: 20px;
        }
        .tab-content { 
            padding-top: 10px;
        }
    </style>
</head>
<body class="my-login-page">
    <div class="container dashboard-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <img src="../img/logo1.jpg" alt="logo" style="width:50px; border-radius:50%; margin-right:10px;">
                <h4 class="card-title d-inline-block mb-0">Dashboard Admin</h4>
            </div>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
        
        <p>Selamat datang, Admin <strong><?php echo htmlspecialchars($_SESSION["nim"]); ?></strong>!</p>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <hr>

        <ul class="nav nav-tabs" id="adminTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="mahasiswa-tab" data-toggle="tab" href="#dataMahasiswa" role="tab" aria-controls="dataMahasiswa" aria-selected="true">Data Mahasiswa</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="log-tab" data-toggle="tab" href="#logAktivitas" role="tab" aria-controls="logAktivitas" aria-selected="false">Log Aktivitas</a>
            </li>
        </ul>

        <div class="tab-content" id="adminTabContent">
            <div class="tab-pane fade show active" id="dataMahasiswa" role="tabpanel" aria-labelledby="mahasiswa-tab">
                <h5>Daftar Mahasiswa</h5>
                <div class="mb-3 mt-3">
                    <a href="tambah_mahasiswa_form.php" class="btn btn-success">Tambah Mahasiswa Baru</a> 
                    <form action="dashboard_admin.php" method="GET" class="form-inline float-right">
                        <input type="text" name="search" class="form-control mr-sm-2" placeholder="Cari NIM/Nama..." value="<?php echo htmlspecialchars($search_term); ?>">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        <?php if (!empty($search_term)): ?>
                            <a href="dashboard_admin.php" class="btn btn-secondary ml-2">Reset Pencarian</a>
                        <?php endif; ?>
                    </form>
                    <div style="clear:both;"></div>
                </div>

                <?php if (!empty($mahasiswa_list)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Tgl Lahir</th>
                                    <th>Alamat (Klik untuk Peta)</th>
                                    <th>Telepon</th>
                                    <th>Kesukaan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mahasiswa_list as $mhs): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($mhs['NIM']); ?></td>
                                    <td><?php echo htmlspecialchars($mhs['Nama']); ?></td>
                                    <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($mhs['Tgl_Lahir']))); ?></td>
                                    <td>
                                        <?php 
                                        // Kondisi sebelumnya untuk mengecek latitude_mhs dan longitude_mhs bisa disederhanakan
                                        // karena kita sekarang akan mengirim alamatnya sebagai search query.
                                        ?>
                                        <a href="../map/map.php?search_query=<?php echo urlencode($mhs['Alamat']); ?>&from=admin_dashboard" target="_blank" title="Cari '<?php echo htmlspecialchars($mhs['Alamat']); ?>' di Peta Kustom">
                                            <?php echo htmlspecialchars($mhs['Alamat']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($mhs['Telpon']); ?></td>
                                    <td><?php echo htmlspecialchars($mhs['Kesukaan']); ?></td>
                                    <td class="action-buttons">
                                        <a href="edit_mahasiswa_form.php?nim=<?php echo htmlspecialchars($mhs['NIM']); ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="proses_hapus_mahasiswa.php?nim=<?php echo htmlspecialchars($mhs['NIM']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data mahasiswa ini?');">Hapus</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <?php if (empty($error_message) || !empty($search_term) ): ?>
                        <div class="alert alert-info mt-3">
                            <?php 
                            if (!empty($search_term)) {
                                echo "Tidak ada data mahasiswa yang cocok dengan pencarian \"<strong>" . htmlspecialchars($search_term) . "</strong>\".";
                            } else if (empty($error_message)) { 
                                echo "Belum ada data mahasiswa. Silakan tambahkan data baru.";
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="logAktivitas" role="tabpanel" aria-labelledby="log-tab">
                <h5>Log Aktivitas Pengguna (50 Terbaru)</h5>
                <?php if (!empty($log_aktivitas_list)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID Log</th>
                                    <th>Waktu</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Jenis Aksi</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($log_aktivitas_list as $log): ?>
                                <tr>
                                    <td><?php echo $log['id_log']; ?></td>
                                    <td><?php echo htmlspecialchars(date('d-m-Y H:i:s', strtotime($log['timestamp_aksi']))); ?></td>
                                    <td><?php echo htmlspecialchars($log['user_identifier']); ?></td>
                                    <td><?php echo htmlspecialchars($log['role_pelaku']); ?></td>
                                    <td><?php echo htmlspecialchars($log['jenis_aksi']); ?></td>
                                    <td><?php echo nl2br(htmlspecialchars($log['deskripsi_aksi'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                     <?php if (empty($error_message) || strpos($error_message, "Gagal mengambil data log aktivitas") === false): ?>
                        <div class="alert alert-info mt-3">Belum ada aktivitas yang tercatat.</div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        // Untuk mengaktifkan tab default atau tab yang diklik dari URL jika ada hash
        $(function(){
            var hash = window.location.hash;
            if (hash) {
                $('ul.nav a[href="' + hash + '"]').tab('show');
            }

            // Menyimpan tab yang aktif ke URL hash agar bisa di-bookmark atau di-refresh
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                if(history.pushState) {
                    history.pushState(null, null, e.target.hash);
                } else {
                    window.location.hash = e.target.hash;
                }
            });
        });
    </script>
</body>
</html>