-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Jul 2025 pada 14.01
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

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
-- Struktur dari tabel `desa`
--

CREATE TABLE `desa` (
  `id` int(11) NOT NULL,
  `id_kecamatan` int(11) DEFAULT NULL,
  `nama_desa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `desa`
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
-- Struktur dari tabel `kecamatan`
--

CREATE TABLE `kecamatan` (
  `id` int(11) NOT NULL,
  `nama_kecamatan` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kecamatan`
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
-- Struktur dari tabel `monitoring_data_panen`
--

CREATE TABLE `monitoring_data_panen` (
  `id` int(11) NOT NULL,
  `nama_petani` varchar(100) NOT NULL,
  `desa` varchar(100) NOT NULL,
  `kecamatan` varchar(100) NOT NULL,
  `tanggal_panen` date NOT NULL,
  `subround` int(11) NOT NULL,
  `foto_petani` varchar(255) NOT NULL,
  `foto_potong` varchar(255) NOT NULL,
  `foto_timbangan` varchar(255) NOT NULL,
  `berat_plot` decimal(10,2) DEFAULT NULL,
  `gkp` decimal(10,2) DEFAULT NULL,
  `gkg` decimal(10,2) DEFAULT NULL,
  `ku` decimal(10,2) DEFAULT NULL,
  `status` enum('selesai','belum selesai','tidak bisa','sudah') DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `nomor_segmen` varchar(9) NOT NULL,
  `nomor_sub_segmen` varchar(2) NOT NULL,
  `note` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `monitoring_data_panen`
--

INSERT INTO `monitoring_data_panen` (`id`, `nama_petani`, `desa`, `kecamatan`, `tanggal_panen`, `subround`, `foto_petani`, `foto_potong`, `foto_timbangan`, `berat_plot`, `gkp`, `gkg`, `ku`, `status`, `user_id`, `nomor_segmen`, `nomor_sub_segmen`, `note`, `created_at`, `updated_at`) VALUES
(63, 'riza', 'Karangmekar', 'Kdgwaringin', '2025-07-01', 0, 'uploads/petani/foto_petani_685cff516c77b.jpeg', 'uploads/potong/foto_potong_685cff516cdec.jpeg', 'uploads/timbangan/foto_timbangan_685cff516d3cc.jpeg', 4.42, 70.72, 60.83, 38.17, 'sudah', 16, '', '81', 'salah berat plot typo', '2025-06-26 08:01:09', NULL),
(64, 'jajang', 'Sarimukti', 'Cibitung', '2025-07-01', 1, '', '', '', NULL, NULL, NULL, NULL, 'belum selesai', 16, '', '81', '', '2025-06-27 01:51:45', NULL),
(65, 'iohjdas', 'Bahagia', 'Babelan', '2025-07-03', 2, 'uploads/petani/foto_petani_685dffe780bbf.jpeg', 'uploads/potong/foto_potong_685dffe780f6f.jpeg', 'uploads/timbangan/foto_timbangan_685dffe7812d0.jpeg', 5.67, 90.72, 78.04, 48.96, 'selesai', 16, '', '81', '', '2025-06-27 01:53:01', NULL),
(66, 'ihhad', 'Labansari', 'Cikarang Timur', '2025-07-02', 1, 'uploads/petani/foto_petani_686249171d1a7.jpeg', 'uploads/potong/foto_potong_686249171d581.jpeg', 'uploads/timbangan/foto_timbangan_686249171d908.jpeg', 5.42, 86.72, 74.60, 46.80, 'selesai', 16, '32160315', 'B1', '', '2025-06-30 07:56:13', NULL),
(67, 'coba baru', 'Babelan Kota', 'Babelan', '2025-07-02', 1, 'uploads/petani/foto_petani_68624962ec93e.jpeg', 'uploads/potong/foto_potong_68624962ecf83.jpeg', 'uploads/timbangan/foto_timbangan_68624962ed44c.jpeg', 4.82, 77.12, 66.34, 41.62, 'selesai', 16, '321602206', 'B2', '', '2025-06-30 08:22:22', NULL),
(68, 'yty', 'Bojongmangu', 'Bojongmangu', '0025-02-07', 1, '', '', '', NULL, NULL, NULL, NULL, 'belum selesai', 17, '321602103', 'A1', '', '2025-07-01 01:19:38', NULL),
(69, 'dodit', 'Pantai Harapanjaya', 'Muaragembong', '2025-06-30', 1, '', '', '', NULL, NULL, NULL, NULL, 'belum selesai', 16, '321602306', 'B2', '', '2025-07-01 02:43:44', NULL),
(70, 'mail', 'Tanjungsari', 'Cikarang Utara', '2025-07-12', 1, '', '', '', NULL, NULL, NULL, NULL, 'belum selesai', 18, '321601009', 'C1', '', '2025-07-03 00:39:19', NULL),
(71, 'jajang', 'Pantai Harapanjaya', 'Muaragembong', '2025-07-06', 1, 'uploads/petani/foto_petani_6865d1961d44d.jpeg', 'uploads/potong/foto_potong_6865d1961dd35.jpeg', 'uploads/timbangan/foto_timbangan_6865d1961e410.jpeg', 4.03, 64.48, 55.47, 34.80, 'selesai', 18, '321603005', 'B3', '', '2025-07-03 00:40:12', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `segmen`
--

CREATE TABLE `segmen` (
  `id` int(11) NOT NULL,
  `nomor_segmen` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `segmen`
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
-- Struktur dari tabel `users`
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
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `no_hp`, `email`, `role`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', '$2y$10$U8U9yP9l5tDeJIlqrII8y.W7VnJL0oqKyrEF3yX57wfzKGgLdbuFu', 'Administrator Utama', '', '', 'supervisor', '2025-04-30 01:18:49', NULL),
(14, 'jajang', '$2y$10$V3lo.5PCVOEJgPHM.yi2Ve4fTlW5.6ibwnEKasnClc8UbbrXi.XCm', 'jajang', '0897123', 'jajang@asd', 'pcl', '2025-06-26 03:55:53', NULL),
(15, 'wei', '$2y$10$6IZIiMTXH1AtQ188AXMZs.KGDjTs6FxBgz02/p5xIV3wR.9EAjsce', 'wei', '0891237', 'wei@123', 'pml', '2025-06-26 04:15:19', '2025-07-03 03:49:36'),
(16, 'riza', '$2y$10$qx987KNk3qXCrSAHKx/u2ubDnOnHbimHUDeQ2yHQ/GRU9tNRec07.', 'riza', '0891723892', 'riza@asdas', 'pcl', '2025-06-26 07:56:17', NULL),
(17, 'imaduddin', '$2y$10$woj0158R8eK1y.21NVR4pu7UKtMcw2HDyVQDwqNk7V15FMTQmVwRe', 'Muhammad Ikhwan Imaduddin', '8239382', 'ikhwanimaduddin908@gmail.com', 'pcl', '2025-07-01 01:17:49', NULL),
(18, 'zacky', '$2y$10$57pwpoaAd3O2wh0RGfwLYeNtOfJMBSeGwSiD/YG4uE2NHMxSbBgnO', 'zacky', '0891273', 'zacky@asd', 'pcl', '2025-07-03 00:38:29', '2025-07-03 03:48:27');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `desa`
--
ALTER TABLE `desa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kecamatan` (`id_kecamatan`);

--
-- Indeks untuk tabel `kecamatan`
--
ALTER TABLE `kecamatan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `monitoring_data_panen`
--
ALTER TABLE `monitoring_data_panen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indeks untuk tabel `segmen`
--
ALTER TABLE `segmen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_segmen` (`nomor_segmen`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `monitoring_data_panen`
--
ALTER TABLE `monitoring_data_panen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT untuk tabel `segmen`
--
ALTER TABLE `segmen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `desa`
--
ALTER TABLE `desa`
  ADD CONSTRAINT `desa_ibfk_1` FOREIGN KEY (`id_kecamatan`) REFERENCES `kecamatan` (`id`);

--
-- Ketidakleluasaan untuk tabel `monitoring_data_panen`
--
ALTER TABLE `monitoring_data_panen`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
