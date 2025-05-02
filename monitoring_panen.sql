-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 30 Apr 2025 pada 03.51
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

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
-- Struktur dari tabel `monitoring_data_panen`
--

CREATE TABLE `monitoring_data_panen` (
  `id` int(11) NOT NULL,
  `nama_petani` varchar(100) NOT NULL,
  `lokasi` varchar(150) NOT NULL,
  `tanggal_panen` date NOT NULL,
  `foto_petani` varchar(255) NOT NULL,
  `foto_potong` varchar(255) NOT NULL,
  `foto_timbangan` varchar(255) NOT NULL,
  `berat_panen` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `monitoring_data_panen`
--

INSERT INTO `monitoring_data_panen` (`id`, `nama_petani`, `lokasi`, `tanggal_panen`, `foto_petani`, `foto_potong`, `foto_timbangan`, `berat_panen`, `created_at`, `updated_at`) VALUES
(1, 'Zaky Pratama Indra', 'Desa Sukamaju, Kecamatan Cikarang', '2025-04-29', 'panen/foto_petani_6810dbe7021c4.jpg', 'panen/foto_potong_6810dbe703093.jpg', 'panen/foto_timbangan_6810dbe703811.jpg', 100.00, '2025-04-29 14:02:15', NULL),
(2, 'Zaky Pratama Indra', 'Desa Sukamaju, Kecamatan Cikarang', '2025-04-29', 'panen/foto_petani_6810dc0ebf616.jpg', 'panen/foto_potong_6810dc0ebfa58.jpg', 'panen/foto_timbangan_6810dc0ebfe71.jpg', 100.00, '2025-04-29 14:02:54', NULL),
(3, 'Rizky Hidayat', 'Desa Sukamaju, Kecamatan Cikarang', '2025-04-28', 'panen/foto_petani_6810dc809027b.jpg', 'panen/foto_potong_6810dc809070d.jpg', 'panen/foto_timbangan_6810dc8090bac.jpg', 90.00, '2025-04-29 14:04:48', NULL),
(4, 'Gilang Irawan', 'Desa Sukamaju, Kecamatan Cikarang', '2025-04-27', 'panen/foto_petani_6810dd261dbe5.jpg', 'panen/foto_potong_6810dd261e1a6.jpg', 'panen/foto_timbangan_6810dd261e6e6.jpg', 80.00, '2025-04-29 14:07:34', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `role` enum('admin','superadmin') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `role`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', '$2y$10$e89gLVGJq61TRjTUOMI7XuWg8x8Yn8giV1eOay1jdfDqqkt31YLZu', 'Administrator Utama', 'superadmin', '2025-04-30 01:18:49', NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `monitoring_data_panen`
--
ALTER TABLE `monitoring_data_panen`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
