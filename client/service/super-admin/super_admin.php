<?php
session_start();
include '../../../server/config/koneksi.php';

// Cek login & role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../index.php");
    exit;
}

// Rata-rata hasil ubinan (hanya data yang sudah selesai)
$q_avg_ubinan = mysqli_query($conn, "SELECT AVG(ku) AS avg_ubinan FROM monitoring_data_panen WHERE status = 'selesai' AND ku IS NOT NULL AND ku != ''");
$avg_ubinan = mysqli_fetch_assoc($q_avg_ubinan)['avg_ubinan'] ?? 0;

// Jumlah status belum diubin (status 'belum selesai')
$q_belum_diubin = mysqli_query($conn, "SELECT COUNT(*) AS total FROM monitoring_data_panen WHERE status = 'belum selesai'");
$belum_diubin = mysqli_fetch_assoc($q_belum_diubin)['total'] ?? 0;

// Ambil nama pengguna
$nama_pengguna = htmlspecialchars($_SESSION['username']);

// Ambil data statistik
$jumlah_panen = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM monitoring_data_panen"))['total'];
$jumlah_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];

// Ambil data ubinan terbaru (misal 5 data terakhir)
$q_ubinan = mysqli_query($conn, "SELECT id, nama_petani, desa, kecamatan, status, tanggal_panen FROM monitoring_data_panen ORDER BY created_at DESC LIMIT 5");

$q_user = mysqli_query($conn, "
    SELECT DISTINCT u.nama_lengkap, m.nomor_sub_segmen, m.tanggal_panen
    FROM users u
    JOIN monitoring_data_panen m ON u.id = m.user_id
    WHERE (m.status = 'belum selesai' OR m.status = 'sudah')
    ORDER BY m.tanggal_panen DESC
    LIMIT 10
");

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
        .table-row-link {
          cursor: pointer;
          transition: background 0.15s, box-shadow 0.15s;
        }
        .table-row-link:hover {
          background: #f1f3f5;
          box-shadow: 0 2px 8px rgba(0,0,0,0.04);
          filter: brightness(0.98);
        }
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
        /* Warna lebih tegas untuk status deadline */
        .tr-deadline-lewat td {
            background-color: rgb(255, 146, 142) !important;
            color: blank;
        }
        .tr-deadline-segera td {
            background-color: rgb(247, 237, 108) !important;
            color: black;
        }
        .tr-deadline-aman td {
            background-color: rgb(179, 252, 157) !important;
            color: black;
        }

    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top" style="z-index:1040;">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="../../assets/logo.png" alt="Logo BPS" height="40" class="me-2">
            Dashboard
        </a>
    </div>
</nav>

<!-- Layout Wrapper -->
<div class="container-fluid" style="padding-top:70px;">
  <div class="row">
    <!-- Sidebar -->
    <nav class="col-lg-2 d-none d-lg-block sidebar position-fixed bg-white border-end shadow-sm py-4" style="height:100vh; z-index:1030;">
      <div class="position-sticky">
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-primary text-decoration-none px-3">
          <span class="fs-5 fw-bold">Superadmin</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto px-2">
          <li class="nav-item mb-2">
            <a href="super_admin.php" class="nav-link active" aria-current="page">
              <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
          </li>
          <li class="nav-item mb-2">
            <a href="monitoring_panen.php" class="nav-link text-primary">
              <i class="bi bi-basket-fill me-2"></i> Data Ubinan
            </a>
          </li>
          <li class="nav-item mb-2">
            <a href="monitoring_akun.php" class="nav-link text-primary">
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
    <main id="mainContent" class="col-md-9 ms-sm-auto col-lg-10 px-md-5 px-3" style="margin-left:240px;">
      <h2 class="mb-4 p-2 pt-4">Dashboard Monitoring Panen</h2>
      <div class="row g-4">
        <!-- Card 1: Total Panen Ubinan -->
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="text-success fw-bold"><?= $jumlah_panen; ?></h3>
                            <p class="mb-1 text-muted">Total Panen Ubinan</p>
                        </div>
                        <i class="bi bi-basket-fill fs-1 text-success"></i>
                    </div>
                </div>
                <div class="card-footer bg-success text-white fw-semibold">
                    Data hasil panen masuk <i class="bi bi-check-circle-fill ms-1"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: Rata-rata Ubinan -->
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="text-warning fw-bold"><?= number_format($avg_ubinan, 2); ?> Kg</h3>
                            <p class="mb-1 text-muted">Rata-rata Ubinan</p>
                        </div>
                        <i class="bi bi-bar-chart-line-fill fs-1 text-warning"></i>
                    </div>
                </div>
                <div class="card-footer bg-warning text-white fw-semibold">
                    Per hitungan luas ubinan <i class="bi bi-rulers ms-1"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Total User -->
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="text-primary fw-bold"><?= $jumlah_user; ?></h3>
                            <p class="mb-1 text-muted">Total User</p>
                        </div>
                        <i class="bi bi-people-fill fs-1 text-primary"></i>
                    </div>
                </div>
                <div class="card-footer bg-primary text-white fw-semibold">
                    Jumlah pengguna sistem <i class="bi bi-person-badge ms-1"></i>
                </div>
            </div>
        </div>

        <!-- Card 4: Belum Diubin -->
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="text-danger fw-bold"><?= $belum_diubin; ?></h3>
                            <p class="mb-1 text-muted">Belum Diubin</p>
                        </div>
                        <i class="bi bi-x-circle-fill fs-1 text-danger"></i>
                    </div>
                </div>
                <div class="card-footer bg-danger text-white fw-semibold">
                    Lokasi belum ada data <i class="bi bi-exclamation-circle ms-1"></i>
                </div>
            </div>
        </div>
    </div>

      <!-- Ringkasan Data Terbaru -->
      <div class="container-fluid mt-5">
        <div class="row align-items-stretch">

          <!-- Kiri: Ubinan -->
          <div class="col-md-7 mb-4">
            <div class="card shadow-sm h-100 d-flex flex-column">
              <div class="d-flex justify-content-between card-header bg-success text-white">
                <h5 class="mb-0">Ubinan Terbaru</h5>
                <a href="monitoring_panen.php" class="btn btn-outline-light btn-sm">Lihat Semua</a>
              </div>
              <div class="card-body flex-grow-1">
                <table class="table table-bordered table-hover mb-0">
                  <thead class="table-light">
                    <tr class="text-center">
                      <th>#</th>
                      <th>Nama Petani</th>
                      <th>Lokasi</th>
                      <th>Status</th>
                      <th>Deadline</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($q_ubinan)): ?>
                    <tr class="table-row-link" data-href="detail_panen.php?id=<?= $row['id']; ?>">
                      <td class="text-center"><?= $no++; ?></td>
                      <td class="text-center"><?= htmlspecialchars($row['nama_petani']); ?></td>
                      <td class="text-center"><?= htmlspecialchars($row['desa'] . ', ' . $row['kecamatan']); ?></td>
                      <td class="text-center"><?= htmlspecialchars($row['status']); ?></td>
                      <td class="text-center text-nowrap"><?= htmlspecialchars($row['tanggal_panen']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Kanan: User -->
          <div class="col-lg-5 mb-4">
            <div class="card shadow-sm h-100 d-flex flex-column">
              <div class="text-nowrap d-flex justify-content-between card-header bg-primary text-white">
                <h5 class="mb-0">Belum Isi Form Ubinan</h5>
                <a href="monitoring_akun.php" class="btn btn-outline-light btn-sm">Lihat Semua</a>
              </div>
              <div class="card-body flex-grow-1">
                <table class="table table-sm table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th class="text-center">#</th>
                      <th class="text-center">Nama</th>
                      <th class="text-center">SubSegmen</th>
                      <th class="text-center text-nowrap">Tanggal Ubinan</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $no = 1;
                      $today = new DateTime();
                      while ($row = mysqli_fetch_assoc($q_user)):
                        $tgl_ubinan = new DateTime($row['tanggal_panen']);
                        $diff = $today->diff($tgl_ubinan)->days;
                        $isPast = $tgl_ubinan < $today;
                        $isSoon = !$isPast && $diff <= 7;
                        // Tentukan class warna
                        if ($isPast) {
                          $rowClass = 'tr-deadline-lewat'; // Sudah lewat deadline
                        } elseif ($isSoon) {
                          $rowClass = 'tr-deadline-segera'; // Kurang dari atau sama dengan 7 hari
                        } else {
                          $rowClass = 'tr-deadline-aman'; // Masih lama
                        }
                    ?>
                  <tr class="<?= $rowClass ?>">
                    <td class="text-center"><?= $no++; ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['nomor_sub_segmen']); ?></td>
                    <td class="text-center text-nowrap"><?= htmlspecialchars($row['tanggal_panen']); ?></td>
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

    </main>
  </div>
</div>

<!-- Footer -->
<footer class="text-center mt-5 mb-4 text-muted">
    &copy; <?= date('Y'); ?> Monitoring Panen Kabupaten Bekasi
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Klik baris tabel ubinan ke detail_panen.php?id=...
const rowsUbinan = document.querySelectorAll('.table-row-link-ubinan');
rowsUbinan.forEach(row => {
    row.addEventListener('mouseenter', function() {
        this.style.background = 'linear-gradient(90deg, #e0f7fa 0%, #e3f2fd 100%)';
    });
    row.addEventListener('mouseleave', function() {
        this.style.background = '';
    });
    row.addEventListener('click', function() {
        window.location.href = this.getAttribute('data-href');
    });
});
// Klik baris tabel user ke edit_akun.php?id=...
const rowsUser = document.querySelectorAll('.table-row-link-user');
rowsUser.forEach(row => {
    row.addEventListener('mouseenter', function() {
        this.style.background = 'linear-gradient(90deg, #e0ffe8 0%, #e0f7fa 100%)';
    });
    row.addEventListener('mouseleave', function() {
        this.style.background = '';
    });
    row.addEventListener('click', function() {
        window.location.href = this.getAttribute('data-href');
    });
});

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.table-row-link').forEach(function(row) {
    row.addEventListener('click', function(e) {
      // Jika klik pada tombol hapus, jangan redirect
      if (e.target.closest('.deleteButton')) return;
      window.location.href = this.getAttribute('data-href');
    });
  });
});
</script>

</body>
</html>
