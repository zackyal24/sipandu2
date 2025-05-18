<?php
session_start();
include '../../../server/config/koneksi.php';

// Cek login & role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../index.php");
    exit;
}

// Ambil nama pengguna
$nama_pengguna = htmlspecialchars($_SESSION['username']);

// Ambil data statistik
$jumlah_panen = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM monitoring_data_panen"))['total'];
$jumlah_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Superadmin | Monitoring Panen</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Poppins', sans-serif;
        }
        .card-stat {
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        .card-stat:hover {
            transform: translateY(-5px);
        }
        .navbar-brand {
            font-weight: bold;
        }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="../../assets/logo.png" alt="Logo BPS" height="40" class="me-2">
            Superadmin Panel
        </a>
        <div class="d-flex align-items-center">
            <span class="text-white me-3">👋 Halo, <strong><?= $nama_pengguna; ?></strong></span>
            <a href="../../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container my-5">
    <h2 class="mb-4">Dashboard Monitoring Panen</h2>

    <div class="row g-4">
        <!-- Statistik: Total Panen -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stat text-white bg-success">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title">Total Panen</h5>
                        <h3><?= $jumlah_panen; ?> Data</h3>
                    </div>
                    <i class="bi bi-basket-fill fs-1 align-self-end"></i>
                </div>
            </div>
        </div>

        <!-- Statistik: Total User -->
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stat text-white bg-info">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title">Total User</h5>
                        <h3><?= $jumlah_user; ?> Admin</h3>
                    </div>
                    <i class="bi bi-people-fill fs-1 align-self-end"></i>
                </div>
            </div>
        </div>

        <!-- Navigasi: Monitoring Panen -->
        <div class="col-sm-6 col-xl-3">
            <a href="monitoring_panen.php" class="text-decoration-none">
                <div class="card card-stat bg-warning text-dark">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title">Monitoring Panen</h5>
                            <h3>Lihat</h3>
                        </div>
                        <i class="bi bi-eye-fill fs-1 align-self-end"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Navigasi: Manajemen User -->
        <div class="col-sm-6 col-xl-3">
            <a href="monitoring_akun.php" class="text-decoration-none">
                <div class="card card-stat bg-danger text-white">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title">Manajemen User</h5>
                            <h3>Kelola</h3>
                        </div>
                        <i class="bi bi-person-gear fs-1 align-self-end"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Ringkasan Data Terbaru -->
    <div class="row mt-5">
        <!-- Kolom Kiri: Data Ubinan Terbaru -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>5 Data Ubinan Terbaru</span>
                    <a href="monitoring_panen.php" class="btn btn-sm btn-light">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Petani</th>
                                    <th>Lokasi</th>
                                    <th>Tanggal</th>
                                    <th>Hasil (kg)</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $sql = "SELECT nama_petani, lokasi, tanggal_panen, berat_panen
                                    FROM monitoring_data_panen
                                    ORDER BY id DESC LIMIT 5";
                            $result = mysqli_query($conn, $sql);
                            $no = 1;
                            while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['nama_petani']); ?></td>
                                    <td><?= htmlspecialchars($row['lokasi']); ?></td>
                                    <td><?= htmlspecialchars($row['tanggal_panen']); ?></td>
                                    <td><?= htmlspecialchars($row['berat_panen']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Kolom Kanan: User Baru Submit Ubinan -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <span>5 User Baru Submit Ubinan</span>
                    <a href="monitoring_akun.php" class="btn btn-sm btn-light">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama User</th>
                                    <th>Username</th>
                                    <th>Waktu Submit</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $sql = "SELECT u.nama_lengkap, u.username, m.tanggal_panen
                                    FROM monitoring_data_panen m
                                    LEFT JOIN users u ON m.user_id = u.id
                                    ORDER BY m.id DESC LIMIT 5";
                            $result = mysqli_query($conn, $sql);
                            $no = 1;
                            while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                                    <td><?= htmlspecialchars($row['username']); ?></td>
                                    <td><?= htmlspecialchars($row['tanggal_panen']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<!-- Footer -->
<footer class="text-center mt-5 mb-4 text-muted">
    &copy; <?= date('Y'); ?> Monitoring Panen Kabupaten Bekasi
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
