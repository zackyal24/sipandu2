-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 26 Jun 2025 pada 05.17
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
  `foto_petani` varchar(255) NOT NULL,
  `foto_potong` varchar(255) NOT NULL,
  `foto_timbangan` varchar(255) NOT NULL,
  `berat_plot` decimal(10,2) DEFAULT NULL,
  `gkp` decimal(10,2) DEFAULT NULL,
  `gkg` decimal(10,2) DEFAULT NULL,
  `ku` decimal(10,2) DEFAULT NULL,
  `status` enum('selesai','belum selesai','tidak bisa','sudah') DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `nomor_sub_segmen` varchar(11) NOT NULL,
  `note` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `monitoring_data_panen`
--

INSERT INTO `monitoring_data_panen` (`id`, `nama_petani`, `desa`, `kecamatan`, `tanggal_panen`, `foto_petani`, `foto_potong`, `foto_timbangan`, `berat_plot`, `gkp`, `gkg`, `ku`, `status`, `user_id`, `nomor_sub_segmen`, `note`, `created_at`, `updated_at`) VALUES
(39, 'coaba ', 'coba', '', '2025-05-30', 'uploads/petani/foto_petani_682571c206db0.jpeg', 'uploads/potong/foto_potong_682571c2075be.jpeg', 'uploads/timbangan/foto_timbangan_682571c207d37.jpeg', 4.06, 0.00, 0.00, 0.00, 'selesai', 3, '777777777', '', '2025-05-15 03:58:11', '2025-05-15 04:46:58'),
(48, 'vvv', 'Sumbersari', 'Pebayuran', '2025-06-02', '', '', '', NULL, 0.00, 0.00, 0.00, 'selesai', 3, '88888888888', '', '2025-05-20 02:16:59', NULL),
(49, 'update', 'Pantai Mekar', 'Muaragembong', '2025-06-02', 'uploads/petani/foto_petani_682c0686c95db.jpeg', 'uploads/potong/foto_potong_682c0686c9c45.jpeg', 'uploads/timbangan/foto_timbangan_682c0686ca23c.jpeg', 5.00, 0.00, 0.00, 0.00, 'selesai', 3, '999999999aa', '', '2025-05-20 03:25:50', '2025-05-20 04:35:18'),
(54, 'bintang', 'Kalijaya', 'Cikarang Barat', '2025-06-10', 'uploads/petani/foto_petani_685a288c02bde.jpeg', 'uploads/potong/foto_potong_685a288c02f3f.jpeg', 'uploads/timbangan/foto_timbangan_685a288c0331e.jpeg', 4.98, 79.68, 68.54, 68.54, 'selesai', 3, '819273892A1', '', '2025-06-10 07:41:19', '2025-06-22 15:37:35'),
(57, 'jasjsajas', 'Sumberreja', 'Pebayuran', '2025-06-23', 'uploads/petani/foto_petani_685a298df2d0a.jpeg', 'uploads/potong/foto_potong_685a298df3080.jpeg', 'uploads/timbangan/foto_timbangan_685a298df3670.jpeg', 4.46, 71.36, 61.38, 38.51, 'selesai', 3, '819273892A1', '', '2025-06-22 15:03:52', NULL),
(59, 'jajam22', 'Pantai Sederhana', 'Muaragembong', '2025-06-26', 'uploads/petani/foto_petani_685a29b04d351.jpeg', 'uploads/potong/foto_potong_685a29b04d675.jpeg', 'uploads/timbangan/foto_timbangan_685a29b04d97b.jpeg', 4.98, 79.68, 68.54, 43.00, 'selesai', 3, '819273892Aa', '', '2025-06-24 00:54:57', NULL),
(60, 'adsjadslkj', 'Babelan Kota', 'Babelan', '2025-07-22', '', '', '', NULL, 0.00, 0.00, 0.00, 'belum selesai', 3, '819273892A1', 'halo', '2025-06-24 01:00:30', NULL);

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
  `role` enum('admin','superadmin','user') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `no_hp`, `email`, `role`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', '$2y$10$U8U9yP9l5tDeJIlqrII8y.W7VnJL0oqKyrEF3yX57wfzKGgLdbuFu', 'Administrator Utama', '', '', 'superadmin', '2025-04-30 01:18:49', NULL),
(3, 'user', '$2y$10$7lJVUuIm4Rt6xB7KB2Hix.zF2mQnYSiIYOm7AhqBdglChsb5Mlf0K', 'user', '0895393690365', 'gilang@sda', 'user', '2025-05-01 13:25:09', '2025-06-25 03:11:45'),
(6, 'admin', '$2y$10$ytTrQQzZZX3vliRTDxgbm.GGX8rIRL7BkmQQmjbVjIEgilnOFoUmS', 'admin', '', '', 'admin', '2025-05-01 13:44:59', NULL),
(8, 'zacky', '$2y$10$TVjvYVxzUKi.dydnnKwv2.62sOw7aKmZpKmQIhzTuPqy5wtAniRqS', 'azacky', '', '', 'user', '2025-06-18 04:53:02', '2025-06-20 22:57:02'),
(10, 'baba', '$2y$10$OwbE7qh9gm64snRwtTmf2.vy/k29LY.djGqgvY/TgUMqR1TQiwn6S', 'bibi', '', '', 'admin', '2025-06-21 22:57:48', NULL),
(12, 'gaga', '$2y$10$upX2JuNBzu5957ade6IkIOpvJaM3uMtLXCpGQrMqNBINbcoFpJGNu', 'gggg', '08237423', 'klaskhd@asd', 'admin', '2025-06-22 13:39:15', NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
