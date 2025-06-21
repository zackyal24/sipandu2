<?php
session_start();
include '../../../server/config/koneksi.php';

// Hanya bisa diakses oleh superadmin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../index.php");
    exit;
}

// Ambil semua user dari database
$data = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, created_at DESC");

// Hitung jumlah user berdasarkan role
$jumlah_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='user'"))['total'] ?? 0;
$jumlah_admin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='admin'"))['total'] ?? 0;
$jumlah_superadmin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='superadmin'"))['total'] ?? 0;

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manajemen Akun</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fc;
      font-family: 'Poppins', sans-serif;
    }
    .btn-custom { border-radius: 8px; }
    @media (min-width: 992px) {
    #mainContent {
      margin-left: 240px !important; /* Lebar sidebar */
    }
    }
    @media (max-width: 991.98px) {
      #mainContent {
        margin-left: 0 !important;
      }
    }
    .sidebar {
      width: 240px;
    }
    .navbar-brand {
      font-weight: bold;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top" style="z-index:1040;">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="monitoring_panen.php">
            <img src="../../assets/logo.png" alt="Logo BPS" height="40" class="me-2">
            Monitoring Akun
        </a>
    </div>
</nav>

<!-- Main Content -->
 <div class="container-fluid" style="padding-top:70px;">
  <div class="row">
    <!-- Sidebar -->
    <nav class="col-md-3 col-lg-2 d-md-block bg-white border-end shadow-sm sidebar py-4 position-fixed" style="height:100vh; z-index:1030;">
      <div class="position-sticky">
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-primary text-decoration-none px-3">
          <span class="fs-5 fw-bold">Superadmin</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto px-2">
          <li class="nav-item mb-2">
            <a href="super_admin.php" class="nav-link text-primary" aria-current="page">
              <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
          </li>
          <li class="nav-item mb-2">
            <a href="monitoring_panen.php" class="nav-link text-primary">
              <i class="bi bi-basket-fill me-2"></i> Data Ubinan
            </a>
          </li>
          <li class="nav-item mb-2">
            <a href="monitoring_akun.php" class="nav-link active">
              <i class="bi bi-person-gear me-2"></i> Manajemen User
            </a>
          </li>
        </ul>
        <hr>
        <div class="px-2">
          <a href="../../auth/logout.php" class="btn btn-outline-danger w-100"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main id="mainContent" class="col-lg-10 ms-auto px-4" style="padding-top:70px;">
      <h2 class="mb-4">Manajemen User</h2>
      <!-- Card Statistik User -->
      <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body">
              <div class="fs-5 text-muted mb-1">Jumlah User</div>
              <div class="fs-3 fw-bold"><?= $jumlah_user ?? 0; ?></div>
              <div class="small text-muted">Total user terdaftar</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body">
              <div class="fs-5 text-muted mb-1">Jumlah Admin</div>
              <div class="fs-3 fw-bold"><?= $jumlah_admin ?? 0; ?></div>
              <div class="small text-muted">Total user terdaftar</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body">
              <div class="fs-5 text-muted mb-1">Jumlah Superadmin</div>
              <div class="fs-3 fw-bold"><?= $jumlah_superadmin ?? 0; ?></div>
              <div class="small text-muted">Total user terdaftar</div>
            </div>
          </div>
        </div>
      </div>
      <!-- Tabel User -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">Daftar User</h5>
            <a href="tambah_user.php" class="btn btn-primary btn-sm">
              <i class="bi bi-plus-lg me-1"></i>Tambah User
            </a>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th class="text-center">#</th>
                  <th class="text-center">Nama Lengkap</th>
                  <th class="text-center">Username</th>
                  <th class="text-center">Role</th>
                  <th class="text-center">Tanggal Daftar</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1;
                $q_user = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
                while ($row = mysqli_fetch_assoc($q_user)): ?>
                  <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['role']); ?></td>
                    <td class="text-center"><?= htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))); ?></td>
                    <td class="text-center">
                      <a href="edit_akun.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                      <a href="hapus_user.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus user ini?')"><i class="bi bi-trash"></i></a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

</body>
</html>
