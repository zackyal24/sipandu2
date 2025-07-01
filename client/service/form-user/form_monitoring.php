<?php
session_start();
include '../../../server/config/koneksi.php';

// Cek login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pcl') {
  header("Location: ../index.php");
  exit;
}

// Ambil ID dari parameter URL
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('ID tidak ditemukan!'); window.location.href='dashboard_user.php';</script>";
    exit;
}

// Ambil data berdasarkan ID
$query = "SELECT * FROM monitoring_data_panen WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='dashboard_user.php';</script>";
    exit;
}

// Cek apakah data sudah selesai
$isCompleted = $data['status'] === 'selesai';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Monitoring Panen Umbinan - Kabupaten Bekasi</title>

  <!-- Google Fonts + Bootstrap -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f0f4f8;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .card {
      width: 100%;
      max-width: 700px;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      background: #fff;
    }

    h1 {
      font-weight: 600;
      color: #2c3e50;
      text-align: center;
      margin-bottom: 20px;
    }

    .form-control, .form-select {
      border-radius: 8px;
    }

    .btn-primary {
      border: none;
      border-radius: 8px;
      font-weight: 600;
    }

    .btn-primary:hover {
      background: linear-gradient(90deg, #005fa3, #0096c7);
    }

    .logo {
      width: 90px;
      margin-bottom: 15px;
    }

    footer {
      text-align: center;
      margin-top: auto;
      font-size: 13px;
      color: #6c757d;
      padding: 15px 0;
      border-top: 1px solid #dee2e6;
      background: linear-gradient(to right, #f8f9fa, #e9ecef);
    }
  </style>
</head>

<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="../../assets/logo.png" alt="Logo" width="40" class="me-2">
            Form Panel
        </a>
        <div class="d-flex align-items-center">
            <span class="text-white me-3"><strong><?= htmlspecialchars($_SESSION['username']); ?></strong></span>
            <a href="../../auth/logout.php" class="btn btn-outline-light btn-sm btn-custom">Logout</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container my-5">
  <div class="card mx-auto">
    <div class="text-center pb-5">
      <div class="d-flex justify-content-start mb-3">
          <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
              ←
          </a>
      </div>
      <!-- <img src="../../assets/logo.png" alt="Logo BPS" class="logo"> -->
      <h1>Form Data Ubinan</h1>
      <h5 class="text-muted">Badan Pusat Statistik (BPS) Kabupaten Bekasi</h5>
    </div>

    <!-- Spinner (sembunyi di awal) -->
    <div id="loadingSpinner" class="text-center mb-3" style="display: none;">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2">Menyimpan data, mohon tunggu...</p>
    </div>

    <form action="simpan_ubinan.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= htmlspecialchars($data['id']); ?>">

      <div class="mb-3">
        <label class="form-label">Nama Petani</label>
        <input type="text" name="nama_petani" class="form-control" value="<?= htmlspecialchars($data['nama_petani']); ?>" readonly required>
      </div>

      <div class="mb-3">
        <label class="form-label">Desa</label>
        <input type="text" name="desa" class="form-control" value="<?= htmlspecialchars($data['desa']); ?>" readonly required>
      </div>
      <div class="mb-3">
        <label class="form-label">Kecamatan</label>
        <input type="text" name="kecamatan" class="form-control" value="<?= htmlspecialchars($data['kecamatan']); ?>" readonly required>
      </div>

      <div class="mb-3">
        <label class="form-label">Tanggal Ubinan</label>
        <input type="date" name="tanggal_panen" class="form-control" value="<?= htmlspecialchars($data['tanggal_panen']); ?>" readonly required>
      </div>

      <div class="mb-3">
        <label class="form-label">Subround</label>
        <input type="text" name="subround" class="form-control" value="<?= htmlspecialchars($data['subround']); ?>" readonly required>
      </div>

      <div class="mb-3">
        <label class="form-label">Nomor Segmen</label>
        <input type="text" name="nomor_segmen" class="form-control" value="<?= htmlspecialchars($data['nomor_segmen']); ?>" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label">Nomor Sub Segmen</label>
        <input type="text" name="nomor_sub_segmen" class="form-control" value="<?= htmlspecialchars($data['nomor_sub_segmen']); ?>" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label">Upload Foto Serah Terima Uang Pengganti Responden</label>
        <input type="file" name="foto_petani" class="form-control" accept="image/*" <?= $isCompleted ? 'disabled' : ''; ?> required>
      </div>

      <div class="mb-3">
        <label class="form-label">Upload Foto Setelah Diotong</label>
        <input type="file" name="foto_potong" class="form-control" accept="image/*" <?= $isCompleted ? 'disabled' : ''; ?> required>
      </div>

      <div class="mb-3">
        <label class="form-label">Upload Foto Berat Gabah yang Ditimbang</label>
        <input type="file" name="foto_timbangan" class="form-control" accept="image/*" <?= $isCompleted ? 'disabled' : ''; ?> required>
      </div>

      <div class="mb-3">
        <label class="form-label">Berat Panen Per Plot (kg)</label>
        <input type="number" name="berat_plot" class="form-control" value="<?= htmlspecialchars($data['berat_plot'] ?? ''); ?>" step="0.01" placeholder="Contoh: 5.25" <?= $isCompleted ? 'readonly' : ''; ?> required>
      </div>

      <?php if (!$isCompleted): ?>
        <div class="d-grid pt-3">
          <button type="submit" class="btn btn-primary">Simpan Data ubinan</button>
        </div>
      <?php else: ?>
        <div class="alert alert-success text-center mt-3">Data sudah selesai dan tidak dapat diedit.</div>
      <?php endif; ?>
    </form>
  </div>
</div>

<footer>
  &copy; <?php echo date("Y"); ?> Monitoring Panen Umbinan | Kabupaten Bekasi
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script Loading Spinner -->
<script>
document.querySelector('form').addEventListener('submit', function() {
  document.getElementById('loadingSpinner').style.display = 'block';
  document.getElementById('submitButton').disabled = true;
  document.getElementById('submitButton').innerText = 'Menyimpan...';
});

function confirmSubmit() {
    return confirm("Apakah Anda yakin semua data sudah benar?");
}
</script>

</body>
</html>
