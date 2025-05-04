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
  <title>Tambah Data Ubinan</title>

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

    .form-control {
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
            Tambah Data
        </a>
        <div class="d-flex align-items-center">
            <span class="text-white me-3">👋 Halo, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong></span>
            <a href="../../auth/logout.php" class="btn btn-outline-light btn-sm btn-custom">Logout</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container my-5">
  <div class="card mx-auto">
    <div class="text-center pb-4">
      <div class="d-flex justify-content-start mb-3">
          <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
              ←
          </a>
      </div>
      <!-- <img src="../../assets/logo.png" alt="Logo BPS" class="logo"> -->
      <h1>Data Rencana Ubinan</h1>
    </div>

    <form action="simpan_tambah_data.php" method="POST">
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

      <div class="d-grid pt-3">
        <button type="submit" class="btn btn-primary">Simpan Data</button>
      </div>
    </form>
  </div>
</div>

<footer>
  &copy; <?php echo date("Y"); ?> Monitoring Panen Umbinan | Kabupaten Bekasi
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>