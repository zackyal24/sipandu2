<?php
session_start();
include '../../../server/config/koneksi.php';

// Cek login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pcl') {
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

    .navbar-brand {
            font-weight: bold;
            font-size: 20px;
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
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="#">
      <img src="../../assets/logo.png" alt="Logo" width="40" class="me-2">
      UBINANKU
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
        <label class="form-label">Kecamatan</label>
        <select name="kecamatan" id="kecamatan" class="form-control" required>
          <option value="">-- Pilih Kecamatan --</option>
          <?php
            $result = mysqli_query($conn, 'SELECT id, nama_kecamatan FROM kecamatan ORDER BY nama_kecamatan ASC');
            while($kecamatan = mysqli_fetch_assoc($result)) {
              echo '<option value="'.$kecamatan['id'].'">'.htmlspecialchars($kecamatan['nama_kecamatan']).'</option>';
            }
          ?>
        </select>
      </div>
      
      <div class="mb-3">
        <label class="form-label">Desa</label>
        <select name="desa" id="desa" class="form-control" required>
          <option value="">-- Pilih Desa --</option>
          
        </select>
      </div>
      
      <div class="mb-3">
        <label class="form-label">Tanggal Ubinan</label>
        <input type="date" name="tanggal_panen" class="form-control" required>
      </div>
      
      <div class="mb-3">
        <label class="form-label">Subround</label>
        <select name="subround" class="form-control" required>
          <option value="">-- Pilih Subround --</option>
          <option value="1">Subround 1</option>
          <option value="2">Subround 2</option>
          <option value="3">Subround 3</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Nomor Segmen</label>
        <select name="nomor_segmen" id="nomor_segmen" class="form-control" required>
          <option value="">-- Pilih Nomor Segmen --</option>
          <?php
            $result = mysqli_query($conn, 'SELECT nomor_segmen FROM segmen ORDER BY nomor_segmen ASC');
            while($segmen = mysqli_fetch_assoc($result)) {
              echo '<option value="'.$segmen['nomor_segmen'].'">'.htmlspecialchars($segmen['nomor_segmen']).'</option>';
            }
          ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Nomor Sub Segmen</label>
        <select name="nomor_sub_segmen" class="form-control" required>
          <option value="">-- Pilih Nomor Sub Segmen --</option>
          <option value="A1">A1</option>
          <option value="A2">A2</option>
          <option value="A3">A3</option>
          <option value="B1">B1</option>
          <option value="B2">B2</option>
          <option value="B3">B3</option>
          <option value="C1">C1</option>
          <option value="C2">C2</option>
          <option value="C3">C3</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-control" required>
          <option value="">-- Pilih Status --</option>
          <option value="belum selesai">Belum dilakukan ubinan</option>
          <option value="sudah">Sudah dilakukan ubinan</option>
          <option value="tidak bisa">Tidak bisa dilakukan ubinan</option>
        </select>
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

<script>
document.getElementById('kecamatan').addEventListener('change', function() {
    var kecamatanId = this.value;
    var desaSelect = document.getElementById('desa');
    desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
    if (kecamatanId) {
        fetch('get_desa.php?id_kecamatan=' + kecamatanId)
            .then(response => response.json())
            .then(data => {
                data.forEach(function(desa) {
                    desaSelect.innerHTML += `<option value="${desa.id}">${desa.nama_desa}</option>`;
                });
            });
    }
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>