<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../../../server/config/koneksi.php';

// Ambil ID dari parameter URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query untuk mengambil data berdasarkan ID
$query = mysqli_query($conn, "SELECT * FROM monitoring_data_panen WHERE id = $id");
$data = mysqli_fetch_assoc($query);

// Jika data tidak ditemukan, redirect ke halaman dashboard
if (!$data) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Panen | Monitoring Panen</title>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }
        .card {
            border-radius: 12px;
        }
        .btn-custom {
            border-radius: 8px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
        <div class="d-flex align-items-center">
            <span class="text-white me-3">👋 Halo, <strong><?= htmlspecialchars($_SESSION['admin']); ?></strong></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm btn-custom">Logout</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="fw-bold mb-4">Detail Data Panen</h3>
            <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <td><?= htmlspecialchars($data['id']); ?></td>
                </tr>
                <tr>
                    <th>Nama Petani</th>
                    <td><?= htmlspecialchars($data['nama_petani']); ?></td>
                </tr>
                <tr>
                    <th>Lokasi</th>
                    <td><?= htmlspecialchars($data['lokasi']); ?></td>
                </tr>
                <tr>
                    <th>Tanggal Panen</th>
                    <td><?= htmlspecialchars($data['tanggal_panen']); ?></td>
                </tr>
                <tr>
                    <th>Berat Panen (kg)</th>
                    <td><?= htmlspecialchars($data['berat_panen']); ?></td>
                </tr>
                <tr>
                    <th>Foto Petani</th>
                    <td>
                        <img src="../<?= htmlspecialchars($data['foto_petani']); ?>" alt="Foto Petani" class="img-fluid" style="max-width: 200px;">
                    </td>
                </tr>
                <tr>
                    <th>Foto Potong</th>
                    <td>
                        <img src="../<?= htmlspecialchars($data['foto_potong']); ?>" alt="Foto Potong" class="img-fluid" style="max-width: 200px;">
                    </td>
                </tr>
                <tr>
                    <th>Foto Timbangan</th>
                    <td>
                        <img src="../<?= htmlspecialchars($data['foto_timbangan']); ?>" alt="Foto Timbangan" class="img-fluid" style="max-width: 200px;">
                    </td>
                </tr>
                <tr>
                    <th>Dibuat Pada</th>
                    <td><?= htmlspecialchars($data['created_at']); ?></td>
                </tr>
                <tr>
                    <th>Terakhir Diperbarui</th>
                    <td><?= htmlspecialchars($data['updated_at'] ?? '-'); ?></td>
                </tr>
            </table>
            <a href="dashboard.php" class="btn btn-secondary btn-custom mt-3">Kembali</a>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="text-center mt-5 mb-3">
    &copy; <?= date('Y'); ?> Monitoring Panen
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>