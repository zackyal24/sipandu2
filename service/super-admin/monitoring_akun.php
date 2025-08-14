<?php
session_start();
include '../../config/koneksi.php';

// Hanya bisa diakses oleh supervisor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../index.php");
    exit;
}

// Ambil semua user dari database
$data = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, created_at DESC");

// Hitung jumlah user berdasarkan role
$jumlah_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='pcl'"))['total'] ?? 0;
$jumlah_admin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='pml'"))['total'] ?? 0;
$jumlah_supervisor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='supervisor'"))['total'] ?? 0;

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manajemen Akun</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fc;
      font-family: 'Poppins', sans-serif;
      overflow-x: hidden;
    }
    .btn-custom { 
      border-radius: 8px; 
      transition: all 0.3s ease;
    }
    .btn-custom:hover {
      transform: scale(1.05);
    }
    .sidebar {
      width: 240px;
    }
    .navbar-brand {
      font-weight: bold;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .card:hover {
      transform: translateY(-2px);
      transition: transform 0.3s ease;
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
        font-size: clamp(0.9rem, 2vw, 1.2rem);
      }
      
      .navbar-brand img {
        height: 30px !important;
      }
      
      h2 {
        font-size: clamp(1.3rem, 3vw, 1.8rem);
      }
      
      h5 {
        font-size: clamp(1rem, 2vw, 1.25rem);
      }
      
      .card {
        margin-bottom: 0.75rem;
      }
      
      .card-body {
        padding: clamp(0.75rem, 2vw, 1rem) !important;
      }
      
      .fs-3 {
        font-size: clamp(1.2rem, 3vw, 1.75rem) !important;
      }
      
      .fs-5 {
        font-size: clamp(0.9rem, 2vw, 1.25rem) !important;
      }
      
      .table-responsive {
        font-size: clamp(0.75rem, 1.5vw, 0.875rem);
      }
      
      .btn-sm {
        font-size: clamp(0.7rem, 1.2vw, 0.875rem);
        padding: 0.2rem 0.4rem;
      }
      
      .badge {
        font-size: clamp(0.65rem, 1.2vw, 0.75rem);
      }
      
      .modal-dialog {
        margin: 0.5rem;
      }
      
      .card-title {
        font-size: clamp(1rem, 2vw, 1.25rem) !important;
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
      
      .table th, .table td {
        padding: 0.3rem !important;
        font-size: 0.7rem !important;
      }
      
      .btn-sm {
        padding: 0.1rem 0.3rem !important;
      }
      
      .card-body .fs-3 {
        font-size: 1.2rem !important;
      }
      
      .card-body .fs-5 {
        font-size: 0.8rem !important;
      }
      
      .small {
        font-size: 0.7rem !important;
      }
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
        <a class="navbar-brand d-flex align-items-center" href="monitoring_panen.php">
            <img src="../../assets/logo.png" alt="Logo BPS" height="40" class="me-2">
            SIPANTAU
        </a>
        
        <!-- Mobile menu button -->
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<!-- Main Content -->
<div class="container-fluid" style="padding-top:70px;">
  <div class="row">
    <!-- Desktop Sidebar -->
    <nav class="col-lg-2 d-none d-lg-block bg-white border-end shadow-sm sidebar py-4 position-fixed" style="height:100vh; z-index:1030;">
      <div class="position-sticky">
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-primary text-decoration-none px-3">
          <span class="fs-5 fw-bold">Supervisor</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto px-2">
          <li class="nav-item mb-2">
            <a href="super_admin.php" class="nav-link text-primary" aria-current="page">
              <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
          </li>
          <li class="nav-item mb-2">
              <a href="monitoring.php" class="nav-link text-primary">
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
            <a class="nav-link text-primary d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#manajemenMenu" role="button" aria-expanded="true" aria-controls="manajemenMenu">
              <span><i class="bi bi-gear me-2"></i> Manajemen</span>
              <i class="bi bi-chevron-down"></i>
            </a>
            <div class="collapse show ps-4" id="manajemenMenu">
              <ul class="nav flex-column">
                <li class="nav-item mb-1">
                  <a href="monitoring_akun.php" class="nav-link active">
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
            <a href="super_admin.php" class="nav-link text-primary mobile-nav-link" aria-current="page">
              <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
          </li>
          <li class="nav-item mb-2">
            <a href="monitoring.php" class="nav-link text-primary mobile-nav-link">
              <i class="bi bi-list-task me-2"></i> Monitoring
            </a>
          </li>
          <li class="nav-item mb-2">
            <a href="monitoring_panen.php" class="nav-link text-primary mobile-nav-link">
              <i class="bi bi-basket-fill me-2"></i> Data Ubinan
            </a>
          </li>
          <!-- Dropdown Manajemen Mobile -->
          <li class="nav-item mb-2">
            <a class="nav-link text-primary d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#manajemenMenuMobile" role="button" aria-expanded="true" aria-controls="manajemenMenuMobile">
              <span><i class="bi bi-gear me-2"></i> Manajemen</span>
              <i class="bi bi-chevron-down"></i>
            </a>
            <div class="collapse show ps-4" id="manajemenMenuMobile">
              <ul class="nav flex-column">
                <li class="nav-item mb-1">
                  <a href="monitoring_akun.php" class="nav-link active mobile-nav-link">
                    <i class="bi bi-person-gear me-2"></i> User
                  </a>
                </li>
                <li class="nav-item mb-1">
                  <a href="manage_segmen.php" class="nav-link text-primary mobile-nav-link">
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
      <div class="pt-3 pt-md-4">
        <h2 class="mb-3 mb-md-4">Manajemen User</h2>
        
        <!-- Card Statistik User -->
        <div class="row g-2 g-md-3 g-lg-4 mb-3 mb-md-4">
          <div class="col-12 col-sm-6 col-lg-4">
            <div class="card shadow-sm border-0 text-center h-100">
              <div class="card-body p-2 p-md-3">
                <div class="text-muted mb-1" style="font-size: clamp(0.8rem, 1.5vw, 1rem);">Jumlah PCL</div>
                <div class="fw-bold text-secondary" style="font-size: clamp(1.2rem, 3vw, 1.75rem);"><?= $jumlah_user ?? 0; ?></div>
                <div class="small text-muted" style="font-size: clamp(0.7rem, 1.2vw, 0.875rem);">Total PCL terdaftar</div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-lg-4">
            <div class="card shadow-sm border-0 text-center h-100">
              <div class="card-body p-2 p-md-3">
                <div class="text-muted mb-1" style="font-size: clamp(0.8rem, 1.5vw, 1rem);">Jumlah PML</div>
                <div class="fw-bold text-primary" style="font-size: clamp(1.2rem, 3vw, 1.75rem);"><?= $jumlah_admin ?? 0; ?></div>
                <div class="small text-muted" style="font-size: clamp(0.7rem, 1.2vw, 0.875rem);">Total PML terdaftar</div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-lg-4">
            <div class="card shadow-sm border-0 text-center h-100">
              <div class="card-body p-2 p-md-3">
                <div class="text-muted mb-1" style="font-size: clamp(0.8rem, 1.5vw, 1rem);">Jumlah Supervisor</div>
                <div class="fw-bold text-danger" style="font-size: clamp(1.2rem, 3vw, 1.75rem);"><?= $jumlah_supervisor ?? 0; ?></div>
                <div class="small text-muted" style="font-size: clamp(0.7rem, 1.2vw, 0.875rem);">Total Supervisor terdaftar</div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Tabel User -->
        <div class="card shadow-sm border-0 mb-4">
          <div class="card-body p-2 p-md-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
              <h5 class="card-title mb-0">Daftar User</h5>
              <a href="tambah_user.php" class="btn btn-primary btn-sm btn-custom">
                <i class="bi bi-plus-lg me-1"></i>Tambah User
              </a>
            </div>
            
            <!-- Filter dan Search -->
            <div class="row mb-3">
              <div class="col-12 col-md-4 mb-2 mb-md-0">
                <select id="roleFilter" class="form-select form-select-sm">
                  <option value="">Semua Role</option>
                  <option value="pcl">PCL</option>
                  <option value="pml">PML</option>
                  <option value="supervisor">Supervisor</option>
                </select>
              </div>
            </div>
            
            <div class="table-responsive">
              <table class="table table-bordered align-middle" id="userTable" style="font-size: clamp(0.75rem, 1.5vw, 0.875rem);">
                <thead class="table-light">
                  <tr>
                    <th class="text-center" style="width:40px;">#</th>
                    <th class="text-center">Nama Lengkap</th>
                    <th class="text-center">Username</th>
                    <th class="text-center">Role</th>
                    <th class="text-center d-none d-md-table-cell">No HP</th>
                    <th class="text-center d-none d-lg-table-cell">Email</th>
                    <th class="text-center" style="width:80px;"></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  $q_user = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
                  while ($row = mysqli_fetch_assoc($q_user)): ?>
                    <tr data-role="<?= htmlspecialchars($row['role']); ?>">
                      <td class="text-center"><?= $no++; ?></td>
                      <td class="text-center"><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                      <td class="text-center"><?= htmlspecialchars($row['username']); ?></td>
                      <td class="text-center">
                        <?php
                          $role = strtolower($row['role']);
                          if ($role === 'supervisor') {
                            echo '<span class="badge bg-danger">Supervisor</span>';
                          } elseif ($role === 'pml') {
                            echo '<span class="badge bg-primary">PML</span>';
                          } else {
                            echo '<span class="badge bg-secondary">PCL</span>';
                          }
                        ?>
                      </td>
                      <td class="text-center d-none d-md-table-cell"><?= htmlspecialchars($row['no_hp']); ?></td>
                      <td class="text-center d-none d-lg-table-cell"><?= htmlspecialchars($row['email']); ?></td>
                      <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                          <a href="edit_akun.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i>
                          </a>
                          <a href="#" 
                            class="btn btn-sm btn-outline-danger deleteButton" 
                            data-id="<?= $row['id']; ?>" 
                            data-username="<?= htmlspecialchars($row['username']); ?>">
                            <i class="bi bi-trash"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Modal Konfirmasi Delete -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="font-size: clamp(1rem, 2vw, 1.25rem);">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="font-size: clamp(0.9rem, 1.5vw, 1rem);">
                Apakah Anda yakin ingin menghapus user <span id="deleteUsername" class="fw-bold"></span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="deleteConfirmButton" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>
</div>

<footer class="text-center mt-5 mb-3">
    &copy; <?= date('Y'); ?> SIPANTAU
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // DataTable initialization
  var t = $('#userTable').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
    responsive: true,
    pageLength: 10,
    order: [[1, 'asc']],
    columnDefs: [
      { targets: 0, orderable: false },
      { targets: -1, orderable: false }
    ]
  });

  // Auto-close mobile sidebar when clicking nav links
  document.querySelectorAll('.mobile-nav-link').forEach(function(link) {
    link.addEventListener('click', function() {
      var offcanvasElement = document.getElementById('mobileSidebar');
      var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
      if (offcanvas) {
        offcanvas.hide();
      }
    });
  });

  // Modal hapus
  document.querySelectorAll('.deleteButton').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      var id = this.getAttribute('data-id');
      var username = this.getAttribute('data-username');
      document.getElementById('deleteConfirmButton').href = 'hapus_user.php?id=' + id;
      document.getElementById('deleteUsername').textContent = username;
      var modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
      modal.show();
    });
  });

  // Filter role
  $('#roleFilter').on('change', function() {
    var val = this.value;
    if(val) {
      t.column(3).search('^' + val + '$', true, false).draw();
    } else {
      t.column(3).search('').draw();
    }
  });

  // Nomor otomatis
  t.on('draw.dt', function () {
    var pageInfo = t.page.info();
    t.column(0, { search: 'applied', order: 'applied', page: 'current' }).nodes().each(function (cell, i) {
      cell.innerHTML = i + 1 + pageInfo.start;
    });
  });
});
</script>
</body>
</html>
