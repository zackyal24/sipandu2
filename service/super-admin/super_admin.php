<?php
session_start();
include '../../config/koneksi.php';

// Cek login & role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'supervisor') {
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
    <title>Dashboard Supervisor | Monitoring Panen</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
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
        
        /* Desktop layout */
        @media (min-width: 992px) {
          #mainContent {
            margin-left: 240px !important;
          }
          .sidebar {
            width: 240px;
          }
        }
        
        /* Tablet and mobile layout */
        @media (max-width: 991.98px) {
          #mainContent {
            margin-left: 0 !important;
          }
          .sidebar {
            display: none !important;
          }
        }
        
        /* Mobile optimizations */
        @media (max-width: 768px) {
          .navbar-brand {
            font-size: 0.9rem;
          }
          
          .navbar-brand img {
            height: 30px !important;
          }
          
          h2 {
            font-size: 1.3rem;
          }
          
          .card {
            margin-bottom: 0.75rem;
          }
          
          .card-body {
            padding: 0.75rem !important;
          }
          
          .card-footer {
            padding: 0.5rem !important;
            font-size: 0.75rem !important;
          }
          
          .table-responsive {
            font-size: 0.8rem;
          }
          
          .btn-sm {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
          }
          
          .card-header h5 {
            font-size: 0.9rem;
          }
          
          .small {
            font-size: 0.75rem !important;
          }
        }
        
        /* Extra small devices */
        @media (max-width: 576px) {
          .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
          }
          
          #mainContent {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
          }
          
          .card-body h3 {
            font-size: 1.1rem !important;
          }
          
          .card-body p {
            font-size: 0.7rem !important;
          }
          
          .bi {
            font-size: 1.2rem !important;
          }
        }
        /* Warna status deadline */
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
        footer {
            margin-top: 60px;
            font-size: 14px;
            color: #888;
        }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top" style="z-index:1040;">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="../../assets/logo.png" alt="Logo BPS" height="40" class="me-2">
            UBINANKU
        </a>
        
        <!-- Mobile menu button -->
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<!-- Layout Wrapper -->
<div class="container-fluid" style="padding-top:70px;">
  <div class="row">
    <!-- Desktop Sidebar -->
    <nav class="col-lg-2 d-none d-lg-block sidebar position-fixed bg-white border-end shadow-sm py-4" style="height:100vh; z-index:1030;">
      <div class="position-sticky">
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-primary text-decoration-none px-3">
          <span class="fs-5 fw-bold">Supervisor</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto px-2">
          <li class="nav-item mb-2">
            <a href="super_admin.php" class="nav-link active" aria-current="page">
              <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
          </li>
          <li class="nav-item mb-2">
              <a href="monitoring.php" class="nav-link">
                  <i class="bi bi-list-task me-2"></i> Monitoring
              </a>
          </li>
          <li class="nav-item mb-2">
            <a href="monitoring_panen.php" class="nav-link text-primary">
              <i class="bi bi-basket-fill me-2"></i> Data Ubinan
            </a>
          </li>
          <!-- Dropdown Manajemen -->
          <li class="nav-item mb-2">
            <a class="nav-link text-primary d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#manajemenMenu" role="button" aria-expanded="false" aria-controls="manajemenMenu">
              <span><i class="bi bi-gear me-2"></i> Manajemen</span>
              <i class="bi bi-chevron-down"></i>
            </a>
            <div class="collapse ps-4" id="manajemenMenu">
              <ul class="nav flex-column">
                <li class="nav-item mb-1">
                  <a href="monitoring_akun.php" class="nav-link text-primary">
                    <i class="bi bi-person-gear me-2"></i> User
                  </a>
                </li>
                <li class="nav-item mb-1">
                  <a href="manage_segmen.php" class="nav-link text-primary">
                    <i class="bi bi-123 me-2"></i> Segmen
                  </a>
                </li>
              </ul>
            </div>
          </li>
        </ul>
        <hr>
        <div class="px-2">
          <a href="../../auth/logout.php" class="btn btn-outline-danger w-100"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
        </div>
      </div>
    </nav>

    <!-- Mobile Sidebar (Offcanvas) -->
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
      <div class="offcanvas-header bg-primary text-white">
        <h5 class="offcanvas-title" id="mobileSidebarLabel">
          <i class="bi bi-person-circle me-2"></i>Supervisor
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="nav nav-pills flex-column mb-auto">
          <li class="nav-item mb-2">
            <a href="super_admin.php" class="nav-link active" aria-current="page">
              <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
          </li>
          <li class="nav-item mb-2">
              <a href="monitoring.php" class="nav-link">
                  <i class="bi bi-list-task me-2"></i> Monitoring
              </a>
          </li>
          <li class="nav-item mb-2">
            <a href="monitoring_panen.php" class="nav-link text-primary">
              <i class="bi bi-basket-fill me-2"></i> Data Ubinan
            </a>
          </li>
          <!-- Dropdown Manajemen Mobile -->
          <li class="nav-item mb-2">
            <a class="nav-link text-primary d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#manajemenMenuMobile" role="button" aria-expanded="false" aria-controls="manajemenMenuMobile">
              <span><i class="bi bi-gear me-2"></i> Manajemen</span>
              <i class="bi bi-chevron-down"></i>
            </a>
            <div class="collapse ps-4" id="manajemenMenuMobile">
              <ul class="nav flex-column">
                <li class="nav-item mb-1">
                  <a href="monitoring_akun.php" class="nav-link text-primary">
                    <i class="bi bi-person-gear me-2"></i> User
                  </a>
                </li>
                <li class="nav-item mb-1">
                  <a href="manage_segmen.php" class="nav-link text-primary">
                    <i class="bi bi-123 me-2"></i> Segmen
                  </a>
                </li>
              </ul>
            </div>
          </li>
        </ul>
        <hr>
        <div>
          <a href="../../auth/logout.php" class="btn btn-outline-danger w-100"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main id="mainContent" class="col-12 col-lg-10 px-2 px-md-4" style="margin-left:240px;">
      <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4 pt-3 pt-md-4">
        <h2 class="mb-0">Dashboard Monitoring Ubinan</h2>
      </div>
      
      <div class="row g-2 g-md-3 g-lg-4">
        <!-- Card 1: Total Panen Ubinan -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-2 p-md-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="text-success fw-bold mb-1" style="font-size: clamp(1rem, 2.5vw, 1.5rem);"><?= $jumlah_panen; ?></h3>
                            <p class="mb-0 text-muted" style="font-size: clamp(0.7rem, 1.5vw, 0.875rem);">Total Panen Ubinan</p>
                        </div>
                        <i class="bi bi-basket-fill text-success ms-2" style="font-size: clamp(1.5rem, 4vw, 2.5rem);"></i>
                    </div>
                </div>
                <div class="card-footer bg-success text-white fw-semibold p-2" style="font-size: clamp(0.6rem, 1.2vw, 0.8rem);">
                    Data hasil panen masuk <i class="bi bi-check-circle-fill ms-1"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: Rata-rata Ubinan -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-2 p-md-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="text-warning fw-bold mb-1" style="font-size: clamp(0.8rem, 2vw, 1.3rem);"><?= number_format($avg_ubinan, 2); ?> Kg</h3>
                            <p class="mb-0 text-muted" style="font-size: clamp(0.7rem, 1.5vw, 0.875rem);">Rata-rata Ubinan</p>
                        </div>
                        <i class="bi bi-bar-chart-line-fill text-warning ms-2" style="font-size: clamp(1.5rem, 4vw, 2.5rem);"></i>
                    </div>
                </div>
                <div class="card-footer bg-warning text-white fw-semibold p-2" style="font-size: clamp(0.6rem, 1.2vw, 0.8rem);">
                    Per hitungan luas ubinan <i class="bi bi-rulers ms-1"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Total User -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-2 p-md-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="text-primary fw-bold mb-1" style="font-size: clamp(1rem, 2.5vw, 1.5rem);"><?= $jumlah_user; ?></h3>
                            <p class="mb-0 text-muted" style="font-size: clamp(0.7rem, 1.5vw, 0.875rem);">Total User</p>
                        </div>
                        <i class="bi bi-people-fill text-primary ms-2" style="font-size: clamp(1.5rem, 4vw, 2.5rem);"></i>
                    </div>
                </div>
                <div class="card-footer bg-primary text-white fw-semibold p-2" style="font-size: clamp(0.6rem, 1.2vw, 0.8rem);">
                    Jumlah pengguna sistem <i class="bi bi-person-badge ms-1"></i>
                </div>
            </div>
        </div>

        <!-- Card 4: Belum Diubin -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-2 p-md-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="text-danger fw-bold mb-1" style="font-size: clamp(1rem, 2.5vw, 1.5rem);"><?= $belum_diubin; ?></h3>
                            <p class="mb-0 text-muted" style="font-size: clamp(0.7rem, 1.5vw, 0.875rem);">Belum Diubin</p>
                        </div>
                        <i class="bi bi-x-circle-fill text-danger ms-2" style="font-size: clamp(1.5rem, 4vw, 2.5rem);"></i>
                    </div>
                </div>
                <div class="card-footer bg-danger text-white fw-semibold p-2" style="font-size: clamp(0.6rem, 1.2vw, 0.8rem);">
                    Lokasi belum ada data <i class="bi bi-exclamation-circle ms-1"></i>
                </div>
            </div>
        </div>
      </div>

      <!-- Ringkasan Data Terbaru -->
      <div class="mt-3 mt-md-4 mt-lg-5">
        <div class="row g-2 g-md-3 g-lg-4">

          <!-- Kiri: Ubinan -->
          <div class="col-12 col-xl-8 mb-3 mb-xl-0">
            <div class="card shadow-sm h-100">
              <div class="card-header bg-success text-white d-flex justify-content-between align-items-center p-2 p-md-3">
                <h5 class="mb-0" style="font-size: clamp(0.9rem, 2vw, 1.1rem);">Ubinan Terbaru</h5>
                <a href="monitoring_panen.php" class="btn btn-outline-light btn-sm" style="font-size: clamp(0.7rem, 1.5vw, 0.8rem);">Lihat Semua</a>
              </div>
              <div class="card-body p-1 p-md-2 p-lg-3">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover mb-0" style="font-size: clamp(0.7rem, 1.5vw, 0.875rem);">
                    <thead class="table-light">
                      <tr class="text-center">
                        <th style="width: 8%;">#</th>
                        <th style="width: 30%;">Nama Petani</th>
                        <th class="d-none d-lg-table-cell" style="width: 30%;">Lokasi</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 17%;">Deadline</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $no = 1;
                      while ($row = mysqli_fetch_assoc($q_ubinan)): ?>
                      <tr class="table-row-link" data-href="detail_panen.php?id=<?= $row['id']; ?>">
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['nama_petani']); ?></td>
                        <td class="text-center d-none d-lg-table-cell"><?= htmlspecialchars($row['desa'] . ', ' . $row['kecamatan']); ?></td>
                        <td class="text-center">
                          <span class="badge <?= $row['status'] == 'selesai' ? 'bg-success' : 'bg-warning text-dark' ?>" style="font-size: 0.7rem;">
                            <?= htmlspecialchars($row['status']); ?>
                          </span>
                        </td>
                        <td class="text-center text-nowrap"><?= htmlspecialchars($row['tanggal_panen']); ?></td>
                      </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- Kanan: User -->
          <div class="col-12 col-xl-4">
            <div class="card shadow-sm h-100">
              <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center p-2 p-md-3">
                <h5 class="mb-0" style="font-size: clamp(0.9rem, 2vw, 1.1rem);">Belum Isi Form Ubinan</h5>
                <a href="monitoring.php" class="btn btn-outline-light btn-sm" style="font-size: clamp(0.7rem, 1.5vw, 0.8rem);">Lihat Semua</a>
              </div>
              <div class="card-body p-1 p-md-2 p-lg-3">
                <div class="table-responsive">
                  <table class="table table-sm table-hover mb-0" style="font-size: clamp(0.7rem, 1.5vw, 0.875rem);">
                    <thead class="table-light">
                      <tr>
                        <th class="text-center" style="width: 10%;">#</th>
                        <th class="text-center" style="width: 35%;">Nama</th>
                        <th class="text-center d-none d-md-table-cell" style="width: 25%;">SubSegmen</th>
                        <th class="text-center" style="width: 30%;">Tanggal Ubinan</th>
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
                      <td class="text-center d-none d-md-table-cell"><?= htmlspecialchars($row['nomor_sub_segmen']); ?></td>
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

    </div>

    </main>
  </div>
</div>

<!-- Footer -->
<footer class="text-center mt-5 mb-3">
    &copy; <?= date('Y'); ?> UBINANKU
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Click row functionality
  document.querySelectorAll('.table-row-link').forEach(function(row) {
    row.addEventListener('click', function(e) {
      // Jika klik pada tombol hapus, jangan redirect
      if (e.target.closest('.deleteButton')) return;
      window.location.href = this.getAttribute('data-href');
    });
  });

  // Auto close mobile sidebar when clicking menu items
  const offcanvasElement = document.getElementById('mobileSidebar');
  if (offcanvasElement) {
    const bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
    
    // Close offcanvas when clicking menu links
    document.querySelectorAll('#mobileSidebar .nav-link').forEach(function(link) {
      link.addEventListener('click', function() {
        // Don't close if it's a dropdown toggle
        if (!this.hasAttribute('data-bs-toggle')) {
          bsOffcanvas.hide();
        }
      });
    });
  }
});
</script>

</body>
</html>
