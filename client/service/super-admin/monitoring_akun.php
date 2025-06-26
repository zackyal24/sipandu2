<?php
session_start();
include '../../../server/config/koneksi.php';

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
              <a href="monitoring.php" class="nav-link">
                  <i class="bi bi-list-task me-2"></i> Monitoring
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
    <main id="mainContent" class="col-lg-10 ms-auto px-4 pt-4">
      <h2 class="mb-4">Manajemen User</h2>
      <!-- Card Statistik User -->
      <div class="row g-4 mb-4 d-flex justify-content-center flex-wrap">
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body">
              <div class="fs-5 text-muted mb-1">Jumlah PCL</div>
              <div class="fs-3 fw-bold"><?= $jumlah_user ?? 0; ?></div>
              <div class="small text-muted">Total user terdaftar</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body">
              <div class="fs-5 text-muted mb-1">Jumlah PML</div>
              <div class="fs-3 fw-bold"><?= $jumlah_admin ?? 0; ?></div>
              <div class="small text-muted">Total user terdaftar</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body">
              <div class="fs-5 text-muted mb-1">Jumlah Supervisor</div>
              <div class="fs-3 fw-bold"><?= $jumlah_supervisor ?? 0; ?></div>
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
          <!-- Filter dan Search -->
          <div class="row mb-3">
            <div class="col-md-4 mb-2 mb-md-0">
              <select id="roleFilter" class="form-select">
                <option value="">Semua Role</option>
                <option value="pcl">pcl</option>
                <option value="pml">pml</option>
                <option value="supervisor">Supervisor</option>
              </select>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered align-middle" id="userTable">
              <thead class="table-light">
                <tr>
                  <th class="text-center" style="width:40px;">#</th>
                  <th class="text-center">Nama Lengkap</th>
                  <th class="text-center">Username</th>
                  <th class="text-center">Role</th>
                  <th class="text-center">No HP</th>
                  <th class="text-center">Email</th>
                  <th class="text-center" style="width:60px;"></th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1;
                $q_user = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
                while ($row = mysqli_fetch_assoc($q_user)): ?>
                  <tr data-role="<?= htmlspecialchars($row['role']); ?>">
                    <td class="text-center"></td>
                    <td class="text-center"><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['username']); ?></td>
                    <td class="text-center">
                      <?php
                        $role = strtolower($row['role']);
                        if ($role === 'supervisor') {
                          echo '<span class="badge bg-danger">supervisor</span>';
                        } elseif ($role === 'pml') {
                          echo '<span class="badge bg-primary">PML</span>';
                        } else {
                          echo '<span class="badge bg-secondary">PCL</span>';
                        }
                      ?>
                    </td>
                    <td class="text-center"><?= htmlspecialchars($row['no_hp']); ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['email']); ?></td>
                    <td class="text-center">
                      <a href="edit_akun.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                      <a href="#" 
                        class="btn btn-sm btn-outline-danger deleteButton" 
                        data-id="<?= $row['id']; ?>" 
                        data-username="<?= htmlspecialchars($row['username']); ?>">
                        <i class="bi bi-trash"></i>
                      </a>
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

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus user <span id="deleteUsername" class="fw-bold"></span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="deleteConfirmButton" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>
</div>
</body>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var t = $('#userTable').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
    responsive: true,
    pageLength: 10,
    order: [[1, 'asc']]
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
      t.column(3).search('^' + val + '$', true, false).draw(); // index 3 = kolom Role
    } else {
      t.column(3).search('').draw();
    }
  });

  // nomor otomatis
    t.on('draw.dt', function () {
    var pageInfo = t.page.info();
    t.column(0, { search: 'applied', order: 'applied', page: 'current' }).nodes().each(function (cell, i) {
      cell.innerHTML = i + 1 + pageInfo.start;
    });
  });
});
</script>
</html>
