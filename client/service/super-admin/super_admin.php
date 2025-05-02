<?php
session_start();
include '../../../server/config/koneksi.php';

// Cek login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../index.php");
    exit;
}


// Ambil data untuk dashboard
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

    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Poppins', sans-serif;
        }
        .card-stat {
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: bold;
        }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
    <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="#">
  <img src="../../assets/logo.png" alt="Logo BPS" height="40" class="me-2">
  Superadmin Panel
</a>
        <div class="d-flex align-items-center">
        <span class="text-white me-3">👋 Halo, <strong><?= htmlspecialchars($_SESSION['superadmin']); ?></strong></span>
            <a href="../../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container my-5">
    <h2 class="mb-4">Dashboard Monitoring Panen</h2>

    <div class="row g-4">
        <!-- Card Total Panen -->
        <div class="col-md-6 col-xl-3">
            <div class="card card-stat border-0 text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Total Panen</h5>
                    <h3><?= $jumlah_panen; ?> Data</h3>
                    <i class="bi bi-basket-fill fs-1"></i>
                </div>
            </div>
        </div>

        <!-- Card Total User -->
        <div class="col-md-6 col-xl-3">
            <div class="card card-stat border-0 text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Total User</h5>
                    <h3><?= $jumlah_user; ?> Admin</h3>
                    <i class="bi bi-people-fill fs-1"></i>
                </div>
            </div>
        </div>

        <!-- Card Navigasi Panen -->
        <div class="col-md-6 col-xl-3">
            <a href="monitoring_panen.php" class="text-decoration-none">
                <div class="card card-stat border-0 bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Monitoring Panen</h5>
                        <h3>Lihat</h3>
                        <i class="bi bi-eye-fill fs-1"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card Navigasi User -->
        <div class="col-md-6 col-xl-3">
            <a href="monitoring_akun.php" class="text-decoration-none">
                <div class="card card-stat border-0 bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Manajemen User</h5>
                        <h3>Kelola</h3>
                        <i class="bi bi-person-gear fs-1"></i>
                    </div>
                </div>
            </a>
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
