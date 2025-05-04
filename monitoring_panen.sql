-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Bulan Mei 2025 pada 13.59
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
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `status` enum('selesai','belum selesai') DEFAULT 'belum selesai',
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `monitoring_data_panen`
--

INSERT INTO `monitoring_data_panen` (`id`, `nama_petani`, `lokasi`, `tanggal_panen`, `foto_petani`, `foto_potong`, `foto_timbangan`, `berat_panen`, `created_at`, `updated_at`, `status`, `user_id`) VALUES
(27, 'jaja', 'lkajsd', '2025-05-04', 'uploads/petani/foto_petani_68169e892801a.png', 'uploads/potong/foto_potong_68169e8928771.png', 'uploads/timbangan/foto_timbangan_68169e8928c3b.png', 5.00, '2025-05-03 22:53:40', '2025-05-03 22:54:01', 'selesai', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `role` enum('admin','superadmin','user') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `role`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', '$2y$10$U8U9yP9l5tDeJIlqrII8y.W7VnJL0oqKyrEF3yX57wfzKGgLdbuFu', 'Administrator Utama', 'superadmin', '2025-04-30 01:18:49', NULL),
(3, 'user', '$2y$10$uL7qg/tClj8CyhURQDZJiOAV1MtMfhR2dxFMKwo4UWXjPoNYEs5fq', 'user', 'user', '2025-05-01 13:25:09', NULL),
(6, 'admin', '$2y$10$ytTrQQzZZX3vliRTDxgbm.GGX8rIRL7BkmQQmjbVjIEgilnOFoUmS', 'admin', 'admin', '2025-05-01 13:44:59', NULL);

--
-- Indexes for dumped tables
--

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `monitoring_data_panen`
--
ALTER TABLE `monitoring_data_panen`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
