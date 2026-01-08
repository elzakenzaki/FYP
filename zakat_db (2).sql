-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 31, 2025 at 05:54 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zakat_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `markas_id` varchar(100) NOT NULL,
  `pendapatan_diri` decimal(10,2) DEFAULT 0.00,
  `pendapatan_isteri_suami` decimal(10,2) DEFAULT 0.00,
  `perbelanjaan_asasi` decimal(10,2) DEFAULT 0.00,
  `perbelanjaan_sewa` decimal(10,2) DEFAULT 0.00,
  `jumlah_tanggungan_anak` int(5) DEFAULT 0,
  `jumlah_tanggungan_lain` int(5) DEFAULT 0,
  `tempat_kediaman` varchar(50) DEFAULT NULL,
  `asnaf` varchar(50) NOT NULL,
  `bantuan_tujuan` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `total_amount_approved` decimal(10,2) DEFAULT 0.00,
  `amount_approved` decimal(10,2) DEFAULT 0.00,
  `status` varchar(50) DEFAULT 'Semakan KAGAT',
  `kagat_notes` text DEFAULT NULL,
  `markas_comments` text DEFAULT NULL,
  `doc_ic` varchar(255) DEFAULT NULL,
  `doc_gaji` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `markas_review_status` tinyint(1) DEFAULT 0,
  `nama_bank` varchar(100) DEFAULT NULL,
  `no_akaun` varchar(50) DEFAULT NULL,
  `nama_pemegang` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `user_id`, `full_name`, `markas_id`, `pendapatan_diri`, `pendapatan_isteri_suami`, `perbelanjaan_asasi`, `perbelanjaan_sewa`, `jumlah_tanggungan_anak`, `jumlah_tanggungan_lain`, `tempat_kediaman`, `asnaf`, `bantuan_tujuan`, `amount`, `total_amount_approved`, `amount_approved`, `status`, `kagat_notes`, `markas_comments`, `doc_ic`, `doc_gaji`, `created_at`, `markas_review_status`, `nama_bank`, `no_akaun`, `nama_pemegang`) VALUES
(1, 7, 'AHMAD ZAHRAN ', 'Markas Latihan', 4000.00, 1000.00, 1000.00, 3000.00, 3, 5, 'Rumah Sendiri', 'Miskin', 'perlu', 4000.00, 4000.00, 0.00, 'Ditolak', '', NULL, '1766393223_apply.php', '1766393223_apply.php', '2025-12-22 08:47:03', 0, NULL, NULL, NULL),
(2, 7, 'AHMAD ZAHRAN ', 'Markas Latihan', 4000.00, 4000.00, 1000.00, 100.00, 4, 4, 'Rumah Sendiri', 'Miskin', 'perlu', 4000.00, 4000.00, 0.00, 'Ditolak', '', NULL, '1766393254_apply.php', '1766393254_apply.php', '2025-12-22 08:47:34', 0, NULL, NULL, NULL),
(3, 3, 'AMJAD AMRI', 'Markas Latihan', 4000.00, 1000.00, 1000.00, 400.00, 5, 9, 'Kuarters', 'Gharimin', 'memerlukan untuk anak sekolah', 4000.00, 2500.00, 0.00, 'Ditolak', 'GABUNGAN ISI RUMAH MELEBIHI SYARAT DITETAPAKAN', 'layak 2500 kerana gabungan isi rumah melebihi syarat ditetapkan', '1766413639_ic_Laporan_Unit_Markas Latihan.pdf', '1766413639_gaji_Laporan_Unit_Markas Latihan.pdf', '2025-12-22 14:27:19', 1, NULL, NULL, NULL),
(4, 9, 'AHMAD ZAFRAN', 'MARKAS LAUT', 4000.00, 8000.00, 400.00, 700.00, 5, 10, 'Rumah Sendiri', 'Fakir', 'MEMERLUKAN UNTUK ANAK MASUK BELAJAR KE OVERSEA', 2999.98, 2999.98, 0.00, 'Ditolak', '', NULL, '1766415235_ic_Assignment 2.docx', '1766415235_gaji_WhatsApp Image 2025-12-21 at 14.11.01.jpeg', '2025-12-22 14:53:55', 0, NULL, NULL, NULL),
(5, 8, 'ALEEYAH ZAHRAH', 'MARKAS DARAT', 4000.00, 6000.00, 500.00, 900.00, 10, 10, 'Rumah Sendiri', 'Fakir', 'MEMBIAYAI RUMAH TERBENGKALAI', 3000.00, 3000.00, 0.00, 'Ditolak', '', NULL, '1766415783_ic_WhatsApp Image 2025-12-21 at 14.11.02.pdf', '1766415783_gaji_BBI3427 - Assignment 1.pdf', '2025-12-22 15:03:03', 0, NULL, NULL, NULL),
(6, 9, 'AHMAD ZAFRAN', 'MARKAS LAUT', 4000.00, 2000.00, 500.00, 600.00, 9, 10, 'Rumah Sendiri', 'Fakir', 'memerlukan untuk anak sambung belajar', 5000.00, 4000.00, 0.00, 'Diluluskan', 'LAYAK RM4000 sahaja kerana isi rumah', NULL, '1766417702_ic_1766160586_SURAT PERMOHONAN PENGGUNAAN AUDITORIUM.pdf', '1766417702_gaji_1766160586_SURAT PERMOHONAN PENGGUNAAN AUDITORIUM.pdf', '2025-12-22 15:35:02', 0, NULL, NULL, NULL),
(7, 9, 'AHMAD ZAFRAN', 'MARKAS LAUT', 3000.00, 5000.00, 100.00, 600.00, 10, 10, 'Rumah Sendiri', 'Miskin', 'memerlukan anak sakit tenat', 9000.00, 5000.00, 0.00, 'Diluluskan', 'TERLALU BANYAK UNTUK ANGGOTA DAN PASANGAN BEKERJA', 'TERLALU BESAR UNTUK ANGGOTA MEMOHON RM9000, layak RM5000', '1766419242_ic_1766391547_Laporan_Unit_Markas Latihan.pdf', '1766419242_gaji_1766336976_Laporan_Unit_Markas Latihan.pdf', '2025-12-22 16:00:42', 1, NULL, NULL, NULL),
(8, 9, 'AHMAD ZAFRAN', 'MARKAS LAUT', 7000.00, 5000.00, 1000.00, 600.00, 11, 12, 'Rumah Sendiri', 'Gharimin', 'memerlukan anak sakit sangat', 9000.00, 9000.00, 0.00, 'Diluluskan', 'okay layak', 'LAYAK', '1766420760_ic_1766160594_SURAT PERMOHONAN PENGGUNAAN AUDITORIUM.pdf', '1766420760_gaji_1766160594_SURAT PERMOHONAN PENGGUNAAN AUDITORIUM.pdf', '2025-12-22 16:26:00', 1, NULL, NULL, NULL),
(9, 9, 'AHMAD ZAFRAN', 'MARKAS LAUT', 9000.00, 8000.00, 900.00, 1000.00, 11, 11, 'Rumah Sendiri', 'Miskin', 'perlu', 8000.00, 8000.00, 0.00, 'Diluluskan', 'okay', 'layak', '1766421137_ic_apply.php', '1766421137_gaji_dashboard_superadmin.php', '2025-12-22 16:32:17', 1, NULL, NULL, NULL),
(10, 9, 'AHMAD ZAFRAN', 'MARKAS LAUT', 5000.00, 4000.00, 1300.00, 500.00, 10, 10, 'Rumah Sendiri', 'Fakir', 'SAYA MEMERLUKAN UNTUK BELI ROBUX', 6000.00, 6000.00, 0.00, 'Diluluskan', 'hidup roblox!', 'layak kerana miskin takde duit beli robux', '1766501671_ic_1766160586_SURAT PERMOHONAN PENGGUNAAN AUDITORIUM.pdf', '1766501671_gaji_1766160594_SURAT PERMOHONAN PENGGUNAAN AUDITORIUM.pdf', '2025-12-23 14:54:31', 1, NULL, NULL, NULL),
(11, 9, 'AHMAD ZAFRAN', 'MARKAS LAUT', 4000.00, 1000.00, 100.00, 6000.00, 10, 10, 'Rumah Sewa', 'Fakir', 'perlu', 4000.00, 4000.00, 0.00, 'Diluluskan', '', '', '1767149848_ic_The Lottery.pdf', '1767149848_gaji_The Lottery.pdf', '2025-12-31 02:57:28', 1, 'Affin Bank', '13134214535', 'AHMAD ZAFRAN');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('MAX_AMOUNT_MARKAS', '11500.00', 'Jumlah maksimum yang boleh diluluskan oleh Markas tanpa kelulusan KAGAT.'),
('MAX_AMOUNT_USER', '15000.00', 'Jumlah maksimum yang boleh dipohon oleh Pemohon.'),
('SECRET_KEY', 'KAGAT_2026', 'Kunci Rahsia untuk pendaftaran staf baru.');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_aduan`
--

CREATE TABLE `tbl_aduan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `emel_telefon` varchar(255) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `mesej` text NOT NULL,
  `status` varchar(50) DEFAULT 'Baru',
  `tarikh_hantar` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_aduan`
--

INSERT INTO `tbl_aduan` (`id`, `user_id`, `nama`, `emel_telefon`, `kategori`, `mesej`, `status`, `tarikh_hantar`) VALUES
(1, 3, 'AMJAD AMRI', 'green123@gmail.com', 'Masalah Teknikal Sistem', 'ds', 'Baru', '2025-12-19 14:43:48'),
(2, 3, 'AMJAD AMRI', 'green123@gmail.com', 'Masalah Teknikal Sistem', 'ds', 'Baru', '2025-12-19 14:43:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `pangkat` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `no_tentera` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `no_kp_tentera` varchar(50) NOT NULL,
  `role` enum('user','markas','admin','superadmin') NOT NULL,
  `markas_id` varchar(50) DEFAULT NULL,
  `unit_kem` varchar(100) DEFAULT NULL,
  `initial_setup` tinyint(1) NOT NULL DEFAULT 0,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `nama_pasangan` varchar(255) DEFAULT NULL,
  `kp_pasangan` varchar(20) DEFAULT NULL,
  `pekerjaan_pasangan` varchar(255) DEFAULT NULL,
  `no_tel` varchar(20) DEFAULT NULL,
  `no_tel_pejabat` varchar(20) DEFAULT NULL,
  `no_fax` varchar(20) DEFAULT NULL,
  `alamat_rumah` text DEFAULT NULL,
  `tarikh_lahir` date DEFAULT NULL,
  `tahun_masuk` int(4) DEFAULT NULL,
  `taraf_kahwin` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `pangkat`, `email`, `no_tentera`, `password`, `no_kp_tentera`, `role`, `markas_id`, `unit_kem`, `initial_setup`, `reset_token`, `reset_token_expiry`, `created_at`, `nama_pasangan`, `kp_pasangan`, `pekerjaan_pasangan`, `no_tel`, `no_tel_pejabat`, `no_fax`, `alamat_rumah`, `tarikh_lahir`, `tahun_masuk`, `taraf_kahwin`, `is_active`) VALUES
(1, 'AHMAD ZAKUAN', NULL, 'zakuan091011@gmail.com', NULL, '$2y$10$panJh2dNO13P702So32ho.kXV93a0AEzcc3Fr0ZAvxzQE15jaVEbS', 'KAGAT001', 'admin', NULL, NULL, 1, 'e78eac18709a561c46ed504b061e849f70a033f667afd1189be4420777627728', '2025-12-09 18:44:33', '2025-12-08 17:00:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(3, 'AMJAD AMRI', 'Lt M', 'green123@gmail.com', NULL, '$2y$10$ZVec4fvrK5Lifc8ZsJFz3uxE9Vp3no4RsSgtanA7HdOYwEzcj8XIa', 'T3017272', 'user', 'Markas Latihan', NULL, 1, NULL, NULL, '2025-12-08 17:14:04', 'zubaidah', NULL, '', '019832344', NULL, NULL, 'lot123178414', '2001-01-03', 2019, 'Berkahwin', 1),
(5, 'AHMAD ZAKUAN BIN ZUHAIRY', NULL, 'zakuapeko@gmail.com', NULL, '$2y$10$jdFySJvXVIMJaOxwRaefrO08HUY7sLR.htG6tQ4dboE5Z/l5RwMVC', 'ADMIN001', 'superadmin', NULL, NULL, 1, NULL, NULL, '2025-12-09 13:45:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(6, 'AHMAD ZAEEM', NULL, 'zakuanpeko@gmail.com', NULL, '$2y$10$sJzrSeEJXqNmLgXp551Y.eE.EcS7/l90y.LvWD3mVB2z2gXpZOv9G', 'ADMIN002', 'superadmin', NULL, NULL, 1, NULL, NULL, '2025-12-09 16:37:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(7, 'AHMAD ZAHRAN ', NULL, 'minatozaki183@gmail.com', NULL, '$2y$10$nRjRrdcHG.127Q/IAJ.6O.uaz1y//GcaqXnimqVTEXMyAYUHtgrcO', '030404100225', 'user', NULL, NULL, 1, NULL, NULL, '2025-12-21 16:55:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(8, 'ALEEYAH ZAHRAH', NULL, 'aleeyah0511@gmail.com', NULL, '$2y$10$pr9XIWTGIIEH5biGf/1As.3Xunz4XdYl096KSK0C75uy4kcFlGHjW', '051104140226', 'user', 'MARKAS DARAT', NULL, 1, NULL, NULL, '2025-12-21 17:08:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(9, 'AHMAD ZAFRAN', NULL, 'untecedok@gmail.com', NULL, '$2y$10$fflghXlYsU1D5YpdxpiG1uTUh9F9kSm4BlNTcVxVSbbCpmlLnijvK', 'T8017877', 'user', 'MARKAS LAUT', NULL, 1, NULL, NULL, '2025-12-22 14:29:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(11, 'JAAFAR', NULL, 'Jaafar123@gmail.com', NULL, '$2y$10$mCR3WLiCbHKK6eFv3GBk3OZasBkZIo3ZUdyhaslrgyjTaIZ7tQxKq', 'MARKAS002', 'markas', NULL, NULL, 1, NULL, NULL, '2025-12-22 15:53:24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(12, 'AMIRUL ASYRAF', NULL, 'aasyraf580@gmail.com', NULL, '$2y$10$C7ss11SmwqKSmGp2QvME7uVr41dfm0klGpA3UP2R5F4N1SFgt.pJa', 'KAGAT002', 'admin', NULL, NULL, 1, NULL, NULL, '2025-12-22 15:55:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `tbl_aduan`
--
ALTER TABLE `tbl_aduan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `no_kp_tentera` (`no_kp_tentera`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_aduan`
--
ALTER TABLE `tbl_aduan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
