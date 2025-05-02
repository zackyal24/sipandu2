<?php
session_start();
include '../../../server/config/koneksi.php';

// Cek login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: ../index.php");
    exit;
}
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
            <span class="text-white me-3">👋 Halo, <strong><?= htmlspecialchars($_SESSION['user']); ?></strong></span>
            <a href="../../auth/logout.php" class="btn btn-outline-light btn-sm btn-custom">Logout</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container my-5">
  <div class="card mx-auto">
    <div class="text-center pb-5">
      <img src="../../assets/logo.png" alt="Logo BPS" class="logo">
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

    <form action="simpan_panen.php" method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Nama Petani</label>
        <input type="text" name="nama_petani" class="form-control" placeholder="Nama Petani" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Lokasi Lahan (Desa/Kecamatan)</label>
        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Desa Sukamaju, Kec. Cikarang" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Tanggal Panen</label>
        <input type="date" name="tanggal_panen" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Upload Foto Petani</label>
        <input type="file" name="foto_petani" class="form-control" accept="image/*" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Upload Foto Setelah Potong Padi</label>
        <input type="file" name="foto_potong" class="form-control" accept="image/*" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Upload Foto Timbangan</label>
        <input type="file" name="foto_timbangan" class="form-control" accept="image/*" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Berat Hasil Panen (kg)</label>
        <input type="number" name="berat_panen" class="form-control" placeholder="Masukkan berat hasil panen" required>
      </div>

      <div class="d-grid pt-3">
        <button type="submit" class="btn btn-primary" id="submitButton">Simpan Data Panen</button>
      </div>
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
</script>

</body>
</html>
