-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 31, 2025 at 09:21 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `campus_navigation`
--

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`, `latitude`, `longitude`) VALUES
(1, 'Gedung Rektorat', -6.306670665740967, 106.75615692138672),
(2, 'Gedung Student Center', -6.306464195251465, 106.75495147705078),
(3, 'Gedung FST', -6.306269645690918, 106.75251770019531),
(4, 'GH', -6.31958, 106.751707),
(5, 'Mabes', -6.318889, 106.754829),
(6, 'Auditorium Harun Nasution', -6.306178609314168, 106.75565676680256),
(7, 'FTIK', -6.307065790458128, 106.75527153748823),
(8, 'Masjid Al-Jamiah', -6.306346554332839, 106.75471026216435),
(9, 'FSH', -6.30670300226264, 106.75444411790403),
(10, 'FDIK', -6.306970507928787, 106.75385781051439),
(11, 'Pascasarjana FEB', -6.306572033553754, 106.75273525346049),
(12, 'PLT', -6.306088744568975, 106.75314169439352),
(13, 'Perpustakaan UIN', -6.30624118781749, 106.75369202662218),
(14, 'Kemahasiswaan', -6.306214251204273, 106.75542219409886),
(15, 'FDI', -6.306029508244877, 106.75630222030367),
(16, 'FISIP', -6.309126445896127, 106.75904808468482),
(17, 'FKIK', -6.311944740510548, 106.75964698189246),
(18, 'FAH', -6.3135370639042705, 106.75524557097333),
(19, 'FPSI', -6.309752373108173, 106.75894299476998),
(20, 'FEB', -6.311036786189609, 106.75649918727088),
(21, 'PPG', -6.386415903887706, 106.74515479581704),
(94, 'Lokasi Pengguna', -6.2291968, 107.0333952);

-- --------------------------------------------------------

--
-- Table structure for table `tabel_log_aktivitas`
--

CREATE TABLE `tabel_log_aktivitas` (
  `id_log` int NOT NULL,
  `timestamp_aksi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_identifier` varchar(50) NOT NULL,
  `role_pelaku` varchar(20) DEFAULT NULL,
  `jenis_aksi` varchar(100) NOT NULL,
  `deskripsi_aksi` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tabel_log_aktivitas`
--

INSERT INTO `tabel_log_aktivitas` (`id_log`, `timestamp_aksi`, `user_identifier`, `role_pelaku`, `jenis_aksi`, `deskripsi_aksi`) VALUES
(1, '2025-05-31 03:55:20', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(2, '2025-05-31 03:55:30', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(3, '2025-05-31 03:55:48', '23001001', 'mahasiswa', 'LOGIN_BERHASIL', 'Pengguna 23001001 berhasil login sebagai mahasiswa.'),
(4, '2025-05-31 03:55:58', '23001001', 'mahasiswa', 'LOGOUT', 'Pengguna 23001001 (mahasiswa) telah logout.'),
(5, '2025-05-31 03:56:58', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(6, '2025-05-31 03:57:17', 'admin', 'admin', 'TAMBAH_MAHASISWA_BY_ADMIN', 'Admin admin menambahkan mahasiswa baru NIM: 11220910000104 (Nama: Sulaiman Fikri).'),
(7, '2025-05-31 03:57:19', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(8, '2025-05-31 03:59:36', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(9, '2025-05-31 03:59:39', 'admin', 'admin', 'EDIT_MHS_BY_ADMIN', 'Admin admin mengedit data mahasiswa NIM: 24001011. Password tidak diubah.'),
(10, '2025-05-31 04:00:45', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(11, '2025-05-31 04:01:00', '23001001', 'mahasiswa', 'LOGIN_BERHASIL', 'Pengguna 23001001 berhasil login sebagai mahasiswa.'),
(12, '2025-05-31 04:01:11', '23001001', 'mahasiswa', 'UPDATE_PROFIL_MHS', 'Mahasiswa NIM 23001001 memperbarui profilnya.'),
(13, '2025-05-31 04:01:13', '23001001', 'mahasiswa', 'LOGOUT', 'Pengguna 23001001 (mahasiswa) telah logout.'),
(14, '2025-05-31 04:01:32', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(15, '2025-05-31 04:12:11', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(16, '2025-05-31 04:15:06', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(17, '2025-05-31 04:28:46', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(18, '2025-05-31 04:36:50', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(19, '2025-05-31 04:36:58', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(20, '2025-05-31 04:37:37', '1000', 'mahasiswa', 'REGISTRASI_MHS_BERHASIL', 'Mahasiswa baru dengan NIM 1000 (Nama: Andi Yusril) telah mendaftar.'),
(21, '2025-05-31 04:37:47', 'SISTEM', 'Tidak Diketahui', 'LOGIN_GAGAL', 'Percobaan login gagal (user tidak ditemukan) untuk: 100'),
(22, '2025-05-31 04:38:03', '1000', 'mahasiswa', 'LOGIN_BERHASIL', 'Pengguna 1000 berhasil login sebagai mahasiswa.'),
(23, '2025-05-31 04:38:08', '1000', 'mahasiswa', 'LOGOUT', 'Pengguna 1000 (mahasiswa) telah logout.'),
(24, '2025-05-31 04:38:23', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(25, '2025-05-31 04:39:22', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(26, '2025-05-31 04:54:58', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(27, '2025-05-31 04:55:13', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(28, '2025-05-31 06:27:01', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(29, '2025-05-31 06:54:27', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(30, '2025-05-31 07:02:29', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(31, '2025-05-31 07:02:33', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(32, '2025-05-31 07:14:16', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(33, '2025-05-31 07:16:43', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(34, '2025-05-31 07:17:29', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(35, '2025-05-31 07:18:01', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(36, '2025-05-31 07:35:06', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(37, '2025-05-31 07:37:05', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(38, '2025-05-31 07:47:38', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(39, '2025-05-31 07:47:41', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(40, '2025-05-31 07:48:47', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(41, '2025-05-31 07:48:51', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(42, '2025-05-31 08:04:27', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(43, '2025-05-31 08:19:17', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(44, '2025-05-31 08:20:43', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(45, '2025-05-31 08:21:51', '9999', 'mahasiswa', 'REGISTRASI_MHS_BERHASIL', 'Mahasiswa baru dengan NIM 9999 (Nama: Arsyandi Afdalla) telah mendaftar.'),
(46, '2025-05-31 08:21:58', '9999', 'mahasiswa', 'LOGIN_BERHASIL', 'Pengguna 9999 berhasil login sebagai mahasiswa.'),
(47, '2025-05-31 08:22:11', '9999', 'mahasiswa', 'LOGOUT', 'Pengguna 9999 (mahasiswa) telah logout.'),
(48, '2025-05-31 08:22:15', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(49, '2025-05-31 08:28:04', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(50, '2025-05-31 08:28:10', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(51, '2025-05-31 08:32:54', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(52, '2025-05-31 08:34:12', '20000', 'mahasiswa', 'REGISTRASI_MHS_BERHASIL', 'Mahasiswa baru dengan NIM 20000 (Nama: Muhammad Isbah Falaqiy) telah mendaftar.'),
(53, '2025-05-31 08:34:25', '20000', 'mahasiswa', 'LOGIN_BERHASIL', 'Pengguna 20000 berhasil login sebagai mahasiswa.'),
(54, '2025-05-31 08:34:32', '20000', 'mahasiswa', 'LOGOUT', 'Pengguna 20000 (mahasiswa) telah logout.'),
(55, '2025-05-31 08:34:35', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(56, '2025-05-31 08:35:29', 'admin', 'admin', 'EDIT_MHS_BY_ADMIN', 'Admin admin mengedit data mahasiswa NIM: 20000. Password tidak diubah.'),
(57, '2025-05-31 08:44:57', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(58, '2025-05-31 08:47:00', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(59, '2025-05-31 08:47:31', 'admin', 'admin', 'EDIT_MHS_BY_ADMIN', 'Admin admin mengedit data mahasiswa NIM: 24001011. Password tidak diubah.'),
(60, '2025-05-31 09:15:30', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(61, '2025-05-31 09:17:40', 'admin', 'admin', 'LOGIN_BERHASIL', 'Pengguna admin berhasil login sebagai admin.'),
(62, '2025-05-31 09:19:05', 'admin', 'admin', 'LOGOUT', 'Pengguna admin (admin) telah logout.'),
(63, '2025-05-31 09:19:18', '9999', 'mahasiswa', 'LOGIN_BERHASIL', 'Pengguna 9999 berhasil login sebagai mahasiswa.'),
(64, '2025-05-31 09:20:03', '9999', 'mahasiswa', 'LOGOUT', 'Pengguna 9999 (mahasiswa) telah logout.'),
(65, '2025-05-31 09:20:09', '20000', 'mahasiswa', 'LOGIN_BERHASIL', 'Pengguna 20000 berhasil login sebagai mahasiswa.'),
(66, '2025-05-31 09:20:13', '20000', 'mahasiswa', 'LOGOUT', 'Pengguna 20000 (mahasiswa) telah logout.'),
(67, '2025-05-31 09:20:33', '9999', 'mahasiswa', 'LOGIN_BERHASIL', 'Pengguna 9999 berhasil login sebagai mahasiswa.'),
(68, '2025-05-31 09:20:35', '9999', 'mahasiswa', 'LOGOUT', 'Pengguna 9999 (mahasiswa) telah logout.');

-- --------------------------------------------------------

--
-- Table structure for table `tabel_mahasiswa`
--

CREATE TABLE `tabel_mahasiswa` (
  `NIM` varchar(20) NOT NULL,
  `Nama` varchar(100) NOT NULL,
  `Tgl_Lahir` date NOT NULL,
  `Alamat` text,
  `Telpon` varchar(20) DEFAULT NULL,
  `Kesukaan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tabel_mahasiswa`
--

INSERT INTO `tabel_mahasiswa` (`NIM`, `Nama`, `Tgl_Lahir`, `Alamat`, `Telpon`, `Kesukaan`) VALUES
('1000', 'Andi Yusril', '2025-05-31', 'Jl. Kenanga No. 12, Jakarta', '098828371838', 'Menulis'),
('11220910000104', 'Sulaiman Fikri', '2025-05-31', 'Jl. Kenanga No. 12, Jakarta', '089923132381', 'Menulis'),
('20000', 'Muhammad Isbah Falaqiy', '2025-05-31', 'Pondok Hijau Permai, Bekasi Timur', '0082381834923', 'Mancing'),
('23001001', 'Budi Sanjaya', '2025-05-30', 'Jl. Merdeka No. 10, Jakarta', '0812345678', 'Membaca, Programming'),
('23001003', 'Ahmad Dahlan', '2003-05-10', 'Jl. Margonda Raya No. 20, Depok', '0813456789', 'Futsal, Debat'),
('24001004', 'Dewi Lestari test', '2025-05-31', 'Jl. Kenanga No. 12, Jakarta', '0812987654', 'Menulis'),
('24001005', 'Rizky Maulana', '2003-11-05', 'Jl. Serpong Boulevard No. 3, Tangerang Selatan', '0813123400', 'Bermain Gitar, Game Developer'),
('24001006', 'Citra Anindita', '2004-02-20', 'Jl. Patriot No. 8, Bekasi Barat', '0878112233', 'Melukis, Traveling'),
('24001007', 'Farhan Syahputra', '2003-09-18', 'Jl. Pajajaran No. 15, Bogor Tengah', '0852334455', 'Basket, Nonton Film'),
('24001008', 'Annisa Putri', '2005-01-30', 'Jl. Kemang Utara No. 1, Jakarta Selatan', '0811556677', 'Memasak, Menari'),
('24001009', 'Bayu Setiawan', '2004-06-25', 'Jl. Cinere Raya No. 7, Depok', '0815778899', 'Sepak Bola, Hiking'),
('24001010', 'Indah Permata', '2003-12-12', 'Jl. Galaxy Raya No. 19, Bekasi Selatan', '0896123456', 'Yoga, Berkebun'),
('24001011', 'Agung Wicaksono', '2004-04-03', 'Jl. Bintaro Utama Sektor 3, Tangerang Selatan', '0821654321', 'Bulu Tangkis, Koding'),
('24001012', 'Putri Amelia', '2005-03-08', 'Jl. Tebet Timur Dalam No. 22, Jakarta Selatan', '0818234567', 'Voly, Menyanyi'),
('24001013', 'Rendra Pratama', '2003-08-17', 'Jl. Ir. H. Juanda No. 30, Bogor', '0819876500', 'Catur, Sejarah'),
('9999', 'Arsyandi Afdalla', '2025-05-14', 'Pondok Hijau Golf, Gading Serpong, Tangerang', '0082381834923', 'Mancing');

-- --------------------------------------------------------

--
-- Table structure for table `tabel_users`
--

CREATE TABLE `tabel_users` (
  `nim` varchar(50) NOT NULL,
  `password_plaintext` varchar(100) NOT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tabel_users`
--

INSERT INTO `tabel_users` (`nim`, `password_plaintext`, `role`) VALUES
('1000', 'kosong123', 'mahasiswa'),
('11220910000104', 'kosong123', 'mahasiswa'),
('20000', 'kosong123', 'mahasiswa'),
('23001001', 'budiRahasia123', 'mahasiswa'),
('24001004', 'dewiAman123', 'mahasiswa'),
('24001005', 'rizkyPassWord', 'mahasiswa'),
('24001006', 'citraSecret99', 'mahasiswa'),
('24001007', 'farhanLogin07', 'mahasiswa'),
('24001008', 'annisaMyPass', 'mahasiswa'),
('24001009', 'bayuGameOn', 'mahasiswa'),
('24001010', 'indahFlower', 'mahasiswa'),
('24001011', 'agungCoding88', 'mahasiswa'),
('24001012', 'putriAmelSing', 'mahasiswa'),
('24001013', 'rendraHistoryX', 'mahasiswa'),
('9999', 'kosong123', 'mahasiswa'),
('admin', 'admin', 'admin'),
('staf_akademik_01', 'stafAk@demicPass', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tabel_log_aktivitas`
--
ALTER TABLE `tabel_log_aktivitas`
  ADD PRIMARY KEY (`id_log`);

--
-- Indexes for table `tabel_mahasiswa`
--
ALTER TABLE `tabel_mahasiswa`
  ADD PRIMARY KEY (`NIM`);

--
-- Indexes for table `tabel_users`
--
ALTER TABLE `tabel_users`
  ADD PRIMARY KEY (`nim`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `tabel_log_aktivitas`
--
ALTER TABLE `tabel_log_aktivitas`
  MODIFY `id_log` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
