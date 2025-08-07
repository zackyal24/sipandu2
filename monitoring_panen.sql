-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 06, 2025 at 04:35 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `monitoring_panen`
--

-- --------------------------------------------------------

--
-- Table structure for table `desa`
--

CREATE TABLE `desa` (
  `id` int(11) NOT NULL,
  `id_kecamatan` int(11) DEFAULT NULL,
  `nama_desa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `desa`
--

INSERT INTO `desa` (`id`, `id_kecamatan`, `nama_desa`) VALUES
(1, 1, 'Ragemmanunggal'),
(2, 1, 'Muktijaya'),
(3, 1, 'Kertarahayu'),
(4, 1, 'Cikarageman'),
(5, 1, 'Tamansari'),
(6, 1, 'Tamanrahayu'),
(7, 1, 'Burangkeng'),
(8, 1, 'Cileduk'),
(9, 1, 'Cibening'),
(10, 1, 'Cijengkol'),
(11, 1, 'Lubangbuaya'),
(12, 2, 'Jayamulya'),
(13, 2, 'Sukaragam'),
(14, 2, 'Sirnajaya'),
(15, 2, 'Nagacipta'),
(16, 2, 'Nagasari'),
(17, 2, 'Cilangkara'),
(18, 2, 'Sukasari'),
(19, 2, 'Jayasampurna'),
(20, 3, 'Cicau'),
(21, 3, 'Sukamahi'),
(22, 3, 'Pasirranji'),
(23, 3, 'Pasirtanjung'),
(24, 3, 'Hegarmukti'),
(25, 3, 'Jayamukti'),
(26, 4, 'Sukasejati'),
(27, 4, 'Ciantra'),
(28, 4, 'Sukadami'),
(29, 4, 'Serang'),
(30, 4, 'Sukaresmi'),
(31, 4, 'Cibatu'),
(32, 4, 'Pasirsari'),
(33, 5, 'Cibarusahjaya'),
(34, 5, 'Cibarusahkota'),
(35, 5, 'Sindangmulya'),
(36, 5, 'Wibawamulya'),
(37, 5, 'Sirnajati'),
(38, 5, 'Ridogalih'),
(39, 5, 'Ridamanah'),
(40, 6, 'Karangindah'),
(41, 6, 'Karangmulya'),
(42, 6, 'Bojongmangu'),
(43, 6, 'Medalkrisna'),
(44, 6, 'Sukamukti'),
(45, 6, 'Sukabungah'),
(46, 7, 'Sertajaya'),
(47, 7, 'Hegarmanah'),
(48, 7, 'Cipayung'),
(49, 7, 'Jatireja'),
(50, 7, 'Jatibaru'),
(51, 7, 'Tanjungbaru'),
(52, 7, 'Labansari'),
(53, 7, 'Karangsari'),
(54, 8, 'Bojongsari'),
(55, 8, 'Kedungwaringin'),
(56, 8, 'Waringinjaya'),
(57, 8, 'Karangsambung'),
(58, 8, 'Karangharum'),
(59, 8, 'Mekarjaya'),
(60, 8, 'Karangmekar'),
(61, 9, 'Wangunharja'),
(62, 9, 'Harjamekar'),
(63, 9, 'Pasirgombong'),
(64, 9, 'Mekarmukti'),
(65, 9, 'Simpangan'),
(66, 9, 'Tanjungsari'),
(67, 9, 'Cikarang Kota'),
(68, 9, 'Karangbaru'),
(69, 9, 'Karangasih'),
(70, 9, 'Karangraharja'),
(71, 9, 'Waluya'),
(72, 10, 'Sukaraya'),
(73, 10, 'Karangrahayu'),
(74, 10, 'Karangsetia'),
(75, 10, 'Karangsatu'),
(76, 10, 'Karangmukti'),
(77, 10, 'Karanganyar'),
(78, 10, 'Karangbahagia'),
(79, 10, 'Karangsentosa'),
(80, 11, 'Cibuntu'),
(81, 11, 'Wanasari'),
(82, 11, 'Wanajaya'),
(83, 11, 'Sukajaya'),
(84, 11, 'Kertamukti'),
(85, 11, 'Muktiwari'),
(86, 11, 'Sarimukti'),
(87, 12, 'Telajung'),
(88, 12, 'Cikedokan'),
(89, 12, 'Jatiwangi'),
(90, 12, 'Mekarwangi'),
(91, 12, 'Gandamekar'),
(92, 12, 'Danauindah'),
(93, 12, 'Gandasari'),
(94, 12, 'Sukadanau'),
(95, 12, 'Telagaasih'),
(96, 12, 'Telagamurni'),
(97, 12, 'Kalijaya'),
(98, 13, 'Jatimulya'),
(99, 13, 'Lambangsari'),
(100, 13, 'Lambangjaya'),
(101, 13, 'Tambun'),
(102, 13, 'Setiadarma'),
(103, 13, 'Setiamekar'),
(104, 13, 'Mekarsari'),
(105, 13, 'Tridayasakti'),
(106, 13, 'Mangunjaya'),
(107, 13, 'Sumberjaya'),
(108, 14, 'Karangsatria'),
(109, 14, 'Satriajaya'),
(110, 14, 'Jalenjaya'),
(111, 14, 'Satriamekar'),
(112, 14, 'Sriamur'),
(113, 14, 'Srimukti'),
(114, 14, 'Srijaya'),
(115, 14, 'Srimahi'),
(116, 15, 'Bahagia'),
(117, 15, 'Kebalen'),
(118, 15, 'Babelan Kota'),
(119, 15, 'Kedungpengawas'),
(120, 15, 'Kedungjaya'),
(121, 15, 'Bunibakti'),
(122, 15, 'Muarabakti'),
(123, 15, 'Pantai Hurip'),
(124, 15, 'Huripjaya'),
(125, 16, 'Pusakarakyat'),
(126, 16, 'Setiaasih'),
(127, 16, 'Pahlawansetia'),
(128, 16, 'Setiamulya'),
(129, 16, 'Segaramakmur'),
(130, 16, 'Pantaimakmur'),
(131, 16, 'Segarajaya'),
(132, 16, 'Samudrajaya'),
(133, 17, 'Sukamaju'),
(134, 17, 'Sukaraja'),
(135, 17, 'Sukarapih'),
(136, 17, 'Sukarahayu'),
(137, 17, 'Sukabakti'),
(138, 17, 'Sukawijaya'),
(139, 17, 'Sukamantri'),
(140, 18, 'Sukamekar'),
(141, 18, 'Sukadaya'),
(142, 18, 'Sukabudi'),
(143, 18, 'Sukawangi'),
(144, 18, 'Sukakerta'),
(145, 18, 'Sukaringin'),
(146, 18, 'Sukatenang'),
(147, 19, 'Sukaasih'),
(148, 19, 'Sukarukun'),
(149, 19, 'Banjarsari'),
(150, 19, 'Sukahurip'),
(151, 19, 'Sukamanah'),
(152, 19, 'Sukamulya'),
(153, 19, 'Sukadarma'),
(154, 20, 'Sukajadi'),
(155, 20, 'Sukamakmur'),
(156, 20, 'Sukalaksana'),
(157, 20, 'Sukakersa'),
(158, 20, 'Sukakarya'),
(159, 20, 'Sukaindah'),
(160, 20, 'Sukamurni'),
(161, 21, 'Bantarsari'),
(162, 21, 'Bantarjaya'),
(163, 21, 'Kertasari'),
(164, 21, 'Kertajaya'),
(165, 21, 'Karanghaur'),
(166, 21, 'Karangpatri'),
(167, 21, 'Karangreja'),
(168, 21, 'Karangjaya'),
(169, 21, 'Sumbersari'),
(170, 21, 'Sumberreja'),
(171, 21, 'Sumberurip'),
(172, 21, 'Karangsegar'),
(173, 21, 'Karangharja'),
(174, 22, 'Jayabakti'),
(175, 22, 'Sindangjaya'),
(176, 22, 'Sindangsari'),
(177, 22, 'Jayalaksana'),
(178, 22, 'Setialaksana'),
(179, 22, 'Lenggahjaya'),
(180, 22, 'Setiajaya'),
(181, 22, 'Lenggahsari'),
(182, 23, 'Pantai Harapanjaya'),
(183, 23, 'Pantai Mekar'),
(184, 23, 'Pantai Sederhana'),
(185, 23, 'Pantai Bakti'),
(186, 23, 'Pantai Bahagia'),
(187, 23, 'Jayasakti');

-- --------------------------------------------------------

--
-- Table structure for table `kecamatan`
--

CREATE TABLE `kecamatan` (
  `id` int(11) NOT NULL,
  `nama_kecamatan` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kecamatan`
--

INSERT INTO `kecamatan` (`id`, `nama_kecamatan`) VALUES
(1, 'Setu'),
(2, 'Serang Baru'),
(3, 'Cikarang Pusat'),
(4, 'Cikarang Selatan'),
(5, 'Cibarusah'),
(6, 'Bojongmangu'),
(7, 'Cikarang Timur'),
(8, 'Kdgwaringin'),
(9, 'Cikarang Utara'),
(10, 'Karang Bahagia'),
(11, 'Cibitung'),
(12, 'Cikarang Barat'),
(13, 'Tambun Selatan'),
(14, 'Tambun Utara'),
(15, 'Babelan'),
(16, 'Tarumajaya'),
(17, 'Tambelang'),
(18, 'Sukawangi'),
(19, 'Sukatani'),
(20, 'Sukakarya'),
(21, 'Pebayuran'),
(22, 'Cabangbungin'),
(23, 'Muaragembong');

-- --------------------------------------------------------

--
-- Table structure for table `monitoring_data_panen`
--

CREATE TABLE `monitoring_data_panen` (
  `id` int(11) NOT NULL,
  `nama_petani` varchar(100) NOT NULL,
  `desa` varchar(100) NOT NULL,
  `kecamatan` varchar(100) NOT NULL,
  `tanggal_panen` date NOT NULL,
  `subround` int(11) NOT NULL,
  `foto_serah_terima` varchar(255) NOT NULL,
  `foto_bukti_plot_ubinan` varchar(255) NOT NULL,
  `foto_berat_timbangan` varchar(255) NOT NULL,
  `berat_plot` decimal(10,2) DEFAULT NULL,
  `gkp` decimal(10,2) DEFAULT NULL,
  `gkg` decimal(10,2) DEFAULT NULL,
  `ku` decimal(10,2) DEFAULT NULL,
  `status` enum('belum selesai','selesai','tidak bisa','revisi','sudah') DEFAULT 'belum selesai',
  `user_id` int(11) NOT NULL,
  `nomor_segmen` varchar(9) NOT NULL,
  `nomor_sub_segmen` varchar(2) NOT NULL,
  `note` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `note_revisi` text DEFAULT NULL,
  `revised_at` timestamp NULL DEFAULT NULL,
  `revised_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monitoring_data_panen`
--

INSERT INTO `monitoring_data_panen` (`id`, `nama_petani`, `desa`, `kecamatan`, `tanggal_panen`, `subround`, `foto_serah_terima`, `foto_bukti_plot_ubinan`, `foto_berat_timbangan`, `berat_plot`, `gkp`, `gkg`, `ku`, `status`, `user_id`, `nomor_segmen`, `nomor_sub_segmen`, `note`, `created_at`, `updated_at`, `note_revisi`, `revised_at`, `revised_by`) VALUES
(79, 'zacky', 'Kertasari', 'Pebayuran', '2025-07-31', 2, 'uploads/serah_terima/foto_serah_terima_688a188093fd7.jpg', 'uploads/bukti_plot_ubinan/foto_bukti_plot_ubinan_6882ff93a982f.jpg', 'uploads/berat_timbangan/foto_berat_timbangan_6882ff93a9c01.jpg', 4.90, 78.40, 67.44, 42.31, 'selesai', 18, '321603004', 'B3', 'salah beratnya', '2025-07-25 03:50:45', '2025-07-30 13:05:04', NULL, NULL, NULL),
(80, 'jkasd', 'Pantai Mekar', 'Muaragembong', '2025-07-27', 1, 'uploads/serah_terima/foto_serah_terima_6889f7b471ed9.jpg', 'uploads/bukti_plot_ubinan/foto_bukti_plot_ubinan_6889f7b472272.jpg', 'uploads/berat_timbangan/foto_berat_timbangan_6889f7b4725ad.jpg', 4.80, 76.80, 66.06, 41.45, 'selesai', 18, '321602309', 'C2', '', '2025-07-25 03:55:53', '2025-07-30 10:45:08', NULL, NULL, NULL),
(81, 'yo', 'Lenggahjaya', 'Cabangbungin', '2025-09-09', 3, 'uploads/serah_terima/foto_serah_terima_688a18b8e4856.jpg', 'uploads/bukti_plot_ubinan/foto_bukti_plot_ubinan_688a18b8e4ce6.jpg', '6889f58bb5282_1753871755.jpeg', 4.60, 73.60, 63.31, 39.72, 'selesai', 18, '321602310', 'B3', '', '2025-07-30 10:35:27', '2025-07-30 13:06:00', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `segmen`
--

CREATE TABLE `segmen` (
  `id` int(11) NOT NULL,
  `nomor_segmen` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `segmen`
--

INSERT INTO `segmen` (`id`, `nomor_segmen`) VALUES
(51, '321601007'),
(52, '321601008'),
(53, '321601009'),
(54, '321601010'),
(55, '321602101'),
(56, '321602103'),
(57, '321602105'),
(1, '321602206'),
(2, '321602207'),
(3, '321602306'),
(4, '321602309'),
(5, '321602310'),
(6, '321603004'),
(7, '321603005'),
(8, '321603006'),
(9, '321603007'),
(10, '321603110'),
(11, '321603154'),
(12, '321603155'),
(13, '321603156'),
(14, '321603157'),
(15, '321604103'),
(16, '321604104'),
(17, '321604105'),
(18, '321604107'),
(19, '321605002'),
(20, '321605004'),
(21, '321605005'),
(22, '321605006'),
(23, '321606107'),
(24, '321606109'),
(25, '321606110'),
(26, '321606201'),
(27, '321606202'),
(28, '321606203'),
(29, '321606208'),
(30, '321606209'),
(31, '321607003'),
(32, '321607005'),
(33, '321607007'),
(34, '321607112'),
(35, '321607115'),
(36, '321608101'),
(37, '321608105'),
(38, '321608204'),
(39, '321608205'),
(40, '321608206'),
(41, '321609001'),
(42, '321609007'),
(43, '321609008'),
(44, '321609009'),
(45, '321609010'),
(46, '321610007'),
(47, '321610008'),
(48, '321610009'),
(49, '321610010'),
(50, '321610011'),
(58, '321611001'),
(59, '321611002'),
(60, '321611003'),
(61, '321611004'),
(62, '321611005'),
(63, '321611006'),
(64, '321611101'),
(65, '321611102'),
(66, '321611105'),
(67, '321611107'),
(68, '321611120'),
(69, '321611121'),
(70, '321611122'),
(71, '321611123'),
(72, '321611124'),
(73, '321612001'),
(74, '321612002'),
(75, '321612005'),
(76, '321612006'),
(77, '321612103'),
(78, '321612106'),
(79, '321612107'),
(80, '321612108'),
(81, '321612109'),
(82, '321612110'),
(83, '321613001'),
(84, '321613002'),
(85, '321613003'),
(86, '321613004'),
(87, '321613005'),
(88, '321613006'),
(89, '321613007'),
(90, '321613008'),
(91, '321613030'),
(92, '321613031'),
(93, '321613032'),
(94, '321613033'),
(95, '321614001'),
(96, '321614004'),
(97, '321614008'),
(98, '321614009'),
(99, '321614011'),
(100, '321614013'),
(101, '321614014'),
(102, '321615001'),
(103, '321615004'),
(104, '321615005'),
(105, '321615007'),
(106, '321615008'),
(107, '321615009');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `role` enum('pcl','pml','supervisor') DEFAULT 'pcl',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `no_hp`, `email`, `role`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', '$2y$10$RfqDxPrQhT0M8H6dUcGO6uokpyN3MpL5tysmfXbd0H1XIVIwUqvyy', 'Administrator Utama', '089237483', 'admin@gmail.com', 'supervisor', '2025-04-30 01:18:49', '2025-07-13 13:45:03'),
(14, 'jajang', '$2y$10$V3lo.5PCVOEJgPHM.yi2Ve4fTlW5.6ibwnEKasnClc8UbbrXi.XCm', 'jajang', '0897123', 'jajang@asd', 'pcl', '2025-06-26 03:55:53', NULL),
(15, 'wei', '$2y$10$6IZIiMTXH1AtQ188AXMZs.KGDjTs6FxBgz02/p5xIV3wR.9EAjsce', 'wei', '0891237', 'wei@123', 'pml', '2025-06-26 04:15:19', '2025-07-03 03:49:36'),
(16, 'riza', '$2y$10$lVLD5TQ9lKezmbQfgZxi5eaKU9qXiOAq48vk9Bvkt1sYSyyZqgl.C', 'riza', '0891723892', 'riza@asdas', '', '2025-06-26 07:56:17', '2025-07-10 04:28:25'),
(17, 'imaduddin', '$2y$10$woj0158R8eK1y.21NVR4pu7UKtMcw2HDyVQDwqNk7V15FMTQmVwRe', 'Muhammad Ikhwan Imaduddin', '8239382', 'ikhwanimaduddin908@gmail.com', 'pcl', '2025-07-01 01:17:49', NULL),
(18, 'zacky', '$2y$10$jevoR8YtKyHV898XZyL5se.JAxldpGcx5GYstRkvi3S5FXO63QOsm', 'zacky', '0891273', 'zacky@asd', 'pcl', '2025-07-03 00:38:29', '2025-07-13 14:03:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `desa`
--
ALTER TABLE `desa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kecamatan` (`id_kecamatan`);

--
-- Indexes for table `kecamatan`
--
ALTER TABLE `kecamatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monitoring_data_panen`
--
ALTER TABLE `monitoring_data_panen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `segmen`
--
ALTER TABLE `segmen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_segmen` (`nomor_segmen`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `monitoring_data_panen`
--
ALTER TABLE `monitoring_data_panen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `segmen`
--
ALTER TABLE `segmen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `desa`
--
ALTER TABLE `desa`
  ADD CONSTRAINT `desa_ibfk_1` FOREIGN KEY (`id_kecamatan`) REFERENCES `kecamatan` (`id`);

--
-- Constraints for table `monitoring_data_panen`
--
ALTER TABLE `monitoring_data_panen`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
