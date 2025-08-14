<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pcl') {
    echo "Sesi tidak valid. user_id: " . ($_SESSION['user_id'] ?? 'tidak ada') . ", role: " . ($_SESSION['role'] ?? 'tidak ada');
    exit;
}

// Ambil data ubinan berdasarkan user yang login
include '../../config/koneksi.php';
$user_id = $_SESSION['user_id'];

// UPDATE QUERY: Tambahkan note_revisi
$query = "SELECT id, tanggal_panen, nama_petani, desa, kecamatan, subround, status, note_revisi, revised_at 
          FROM monitoring_data_panen 
          WHERE user_id = ? 
          ORDER BY tanggal_panen DESC";

$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("Kesalahan pada query SQL: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard PCL | Monitoring Panen</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
    }

    .navbar-brand {
      font-weight: bold;
      font-size: clamp(1rem, 2.5vw, 1.25rem);
    }

    .navbar-brand img {
      width: clamp(30px, 5vw, 40px);
      height: auto;
    }

    .btn-custom {
      border-radius: 8px;
      transition: all 0.3s ease;
      font-size: clamp(0.8rem, 1.5vw, 0.95rem);
      padding: clamp(0.4rem, 1vw, 0.5rem) clamp(0.8rem, 2vw, 1rem);
    }

    .btn-custom:hover {
      transform: scale(1.05);
    }

    .card {
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .card-title {
      font-size: clamp(1.1rem, 2.5vw, 1.25rem);
      font-weight: 600;
      color: #2c3e50;
    }

    .ubinan-list { 
      display: block; 
    }

    .ubinan-card {
      margin-bottom: 1rem;
      border: 1px solid #e3e3e3;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.03);
      padding: clamp(0.8rem, 2vw, 1rem);
      background: #fff;
      transition: all 0.3s ease;
    }

    .ubinan-card:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      transform: translateY(-2px);
    }

    .ubinan-card .badge {
      font-size: clamp(0.7rem, 1.2vw, 0.8rem);
      padding: clamp(0.3rem, 0.8vw, 0.4rem) clamp(0.5rem, 1.2vw, 0.6rem);
    }

    .ubinan-card .btn { 
      font-size: clamp(0.7rem, 1.2vw, 0.8rem);
      padding: clamp(0.3rem, 0.8vw, 0.4rem) clamp(0.6rem, 1.5vw, 0.8rem);
    }

    .ubinan-info {
      font-size: clamp(0.75rem, 1.4vw, 0.85rem);
      color: #6c757d;
      margin-bottom: 0.5rem;
    }

    .ubinan-date {
      font-size: clamp(0.9rem, 1.8vw, 1rem);
      font-weight: 600;
      color: #2c3e50;
    }

    /* STYLE UNTUK REVISI ALERT */
    .revisi-alert {
      background-color: #fff3cd;
      border: 1px solid #ffeaa7;
      border-radius: 8px;
      padding: 0.75rem;
      margin-top: 0.75rem;
      font-size: clamp(0.75rem, 1.4vw, 0.85rem);
    }

    .revisi-alert strong {
      color: #856404;
    }

    .revisi-alert .text-warning {
      color: #f39c12 !important;
    }

    .dropdown-menu {
      font-size: clamp(0.8rem, 1.5vw, 0.9rem);
    }

    .dropdown-item {
      padding: clamp(0.4rem, 1vw, 0.5rem) clamp(0.8rem, 2vw, 1rem);
    }

    footer {
        margin-top: 60px;
        font-size: 14px;
        color: #888;
    }

    .empty-state {
      text-align: center;
      padding: clamp(2rem, 5vw, 3rem);
      color: #6c757d;
    }

    .empty-state i {
      font-size: clamp(2rem, 5vw, 3rem);
      margin-bottom: 1rem;
      opacity: 0.5;
    }

    .empty-state h5 {
      font-size: clamp(1rem, 2vw, 1.2rem);
      margin-bottom: 0.5rem;
    }

    .empty-state p {
      font-size: clamp(0.8rem, 1.5vw, 0.9rem);
      margin-bottom: 0;
    }

    /* Mobile optimizations */
    @media (max-width: 768px) {
      .container-fluid {
        padding-top: 1rem !important;
      }

      .navbar-brand {
        font-size: 1rem;
      }

      .navbar-brand img {
        width: 30px;
      }

      .card {
        margin: 0.5rem;
      }

      .card-body {
        padding: 1rem;
      }

      .card-title {
        font-size: 1.1rem;
      }

      .ubinan-card {
        padding: 0.8rem;
      }

      .ubinan-date {
        font-size: 0.9rem;
      }

      .ubinan-info {
        font-size: 0.75rem;
      }

      .ubinan-card .badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.5rem;
      }

      .ubinan-card .btn {
        font-size: 0.7rem;
        padding: 0.3rem 0.6rem;
      }

      .btn-custom {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
      }

      .revisi-alert {
        font-size: 0.75rem;
        padding: 0.6rem;
      }

      .dropdown-menu {
        font-size: 0.8rem;
      }

      .dropdown-item {
        padding: 0.4rem 0.8rem;
      }
    }

    /* Extra small devices */
    @media (max-width: 576px) {
      .container-fluid {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .card {
        margin: 0.25rem;
        border-radius: 10px;
      }

      .card-body {
        padding: 0.8rem;
      }

      .card-title {
        font-size: 1rem;
      }

      .ubinan-card {
        padding: 0.6rem;
        margin-bottom: 0.8rem;
      }

      .ubinan-date {
        font-size: 0.85rem;
      }

      .ubinan-info {
        font-size: 0.7rem;
      }

      .ubinan-card .badge {
        font-size: 0.65rem;
        padding: 0.25rem 0.4rem;
      }

      .ubinan-card .btn {
        font-size: 0.65rem;
        padding: 0.25rem 0.5rem;
      }

      .btn-custom {
        font-size: 0.75rem;
        padding: 0.35rem 0.7rem;
      }

      .revisi-alert {
        font-size: 0.7rem;
        padding: 0.5rem;
      }

      .d-flex.justify-content-between {
        flex-direction: column;
        gap: 0.5rem;
      }

      .d-flex.justify-content-between .btn {
        align-self: flex-end;
      }

      footer {
        font-size: 0.7rem;
        margin-top: 1rem;
        padding: 0.5rem 0;
      }
    }

    /* Landscape mobile optimization */
    @media (max-height: 600px) and (orientation: landscape) {
      .container-fluid {
        padding-top: 0.5rem !important;
      }

      .card-body {
        padding: 0.8rem;
      }

      .ubinan-card {
        padding: 0.6rem;
        margin-bottom: 0.6rem;
      }

      footer {
        margin-top: 1rem;
      }
    }

    /* Tablet optimization */
    @media (min-width: 768px) and (max-width: 991px) {
      .card-title {
        font-size: 1.2rem;
      }

      .ubinan-card {
        padding: 0.9rem;
      }

      .btn-custom {
        font-size: 0.9rem;
      }
    }

    /* Large screen optimization */
    @media (min-width: 1200px) {
      .container-fluid {
        padding-left: 2rem;
        padding-right: 2rem;
      }

      .col-lg-10 {
        max-width: 900px;
      }
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="#">
      <img src="../../assets/logo.png" alt="Logo" class="me-2">
      SIPANTAU
    </a>
    <div class="d-flex align-items-center">
      <div class="dropdown">
        <a href="#" class="text-white fw-bold text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <?= htmlspecialchars($_SESSION['username']); ?>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
          <li>
            <a class="dropdown-item" href="../../auth/ganti_password.php">
              <i class="bi bi-key me-2"></i>Ganti Password
            </a>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <a class="dropdown-item text-danger" href="../../auth/logout.php">
              <i class="bi bi-box-arrow-right me-2"></i>Logout
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="container-fluid" style="padding-top: 1.5rem;">
  <div class="row justify-content-center">
    <main id="mainContent" class="col-lg-10 px-2 px-md-4">
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">
              <i class="bi bi-clipboard-data me-2"></i>Data Ubinan Anda
            </h5>
            <a href="tambah_data.php" class="btn btn-primary btn-sm btn-custom">
              <i class="bi bi-plus-lg me-1"></i>Tambah Data
            </a>
          </div>
          
          <div class="ubinan-list">
            <?php
            if (mysqli_num_rows($result) > 0):
              mysqli_data_seek($result, 0);
              while ($row = mysqli_fetch_assoc($result)):
            ?>
              <div class="ubinan-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div>
                    <div class="ubinan-date">
                      <i class="bi bi-calendar3 me-2"></i>
                      <?= date('d M Y', strtotime($row['tanggal_panen'])); ?>
                    </div>
                    <div class="ubinan-info">
                      <i class="bi bi-person me-1"></i><?= htmlspecialchars($row['nama_petani']); ?> • 
                      <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($row['desa']); ?>, <?= htmlspecialchars($row['kecamatan']); ?> • 
                      <i class="bi bi-list-ol me-1"></i>Subround <?= htmlspecialchars($row['subround']); ?>
                    </div>
                  </div>
                  <div>
                    <?php if ($row['status'] === 'tidak bisa'): ?>
                      <span class="badge bg-danger">
                        <i class="bi bi-x-circle me-1"></i>Tidak Bisa Ubinan
                      </span>
                    <?php elseif ($row['status'] === 'selesai'): ?>
                      <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Selesai
                      </span>
                    <?php elseif ($row['status'] === 'revisi'): ?>
                      <a href="form_monitoring.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm btn-custom">
                        <i class="bi bi-exclamation-triangle me-1"></i>Perlu Revisi
                      </a>
                    <?php elseif ($row['status'] === 'belum selesai' || $row['status'] === 'sudah'): ?>
                      <a href="form_monitoring.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm btn-custom">
                        <i class="bi bi-pencil-square me-1"></i>Isi Form
                      </a>
                    <?php else: ?>
                      <span class="badge bg-secondary">
                        <i class="bi bi-question-circle me-1"></i>-
                      </span>
                    <?php endif; ?>
                  </div>
                </div>

                <!-- REVISI ALERT - FIXED STRUCTURE -->
                <?php if ($row['status'] === 'revisi' && !empty($row['note_revisi'])): ?>
                  <div class="revisi-alert">
                    <div class="d-flex align-items-start">
                      <i class="bi bi-exclamation-triangle text-warning me-2 mt-1"></i>
                      <div>
                        <strong>Perlu Revisi:</strong> <?= htmlspecialchars($row['note_revisi']); ?>
                        <?php if (!empty($row['revised_at'])): ?>
                          <br><small class="text-muted">
                            <i class="bi bi-clock me-1"></i>
                            <?= date('d M Y H:i', strtotime($row['revised_at'])); ?>
                          </small>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
            <?php endwhile; else: ?>
              <div class="empty-state">
                <i class="bi bi-clipboard-x"></i>
                <h5>Belum Ada Data</h5>
                <p>Anda belum memiliki data ubinan. Klik tombol "Tambah Data" untuk menambahkan data baru.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Footer -->
<footer class="text-center mt-5 mb-3">
    &copy; <?= date('Y'); ?> SIPANTAU
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>