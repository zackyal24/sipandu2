<?php
session_start();
include '../../config/koneksi.php';

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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

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
      font-size: clamp(1rem, 2.5vw, 1.25rem);
    }

    .navbar-brand img {
      width: clamp(30px, 5vw, 40px);
      height: auto;
    }

    .card {
      width: 100%;
      max-width: 700px;
      padding: clamp(1rem, 3vw, 2rem);
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      background: #fff;
      margin: 0 auto;
    }

    h1 {
      font-weight: 600;
      color: #2c3e50;
      text-align: center;
      margin-bottom: 1.5rem;
      font-size: clamp(1.5rem, 4vw, 2rem);
    }

    .form-label {
      font-weight: 500;
      color: #495057;
      margin-bottom: 0.5rem;
      font-size: clamp(0.875rem, 1.8vw, 1rem);
    }

    .form-control, .form-select {
      border-radius: 8px;
      border: 1px solid #e3e6f0;
      padding: clamp(0.5rem, 1.5vw, 0.75rem) clamp(0.75rem, 2vw, 1rem);
      font-size: clamp(0.875rem, 1.8vw, 1rem);
      transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .btn-primary {
      border: none;
      border-radius: 8px;
      font-weight: 600;
      padding: clamp(0.6rem, 2vw, 0.75rem) clamp(1rem, 3vw, 1.5rem);
      font-size: clamp(0.875rem, 1.8vw, 1rem);
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background: linear-gradient(90deg, #005fa3, #0096c7);
      transform: translateY(-1px);
    }

    .btn-outline-secondary {
      border-radius: 8px;
      font-size: clamp(0.875rem, 1.8vw, 1rem);
      padding: clamp(0.4rem, 1.5vw, 0.5rem) clamp(0.8rem, 2vw, 1rem);
    }

    .dropdown-menu {
      font-size: clamp(0.875rem, 1.8vw, 1rem);
    }

    .dropdown-item {
      padding: clamp(0.4rem, 1.5vw, 0.5rem) clamp(0.8rem, 2vw, 1rem);
    }

    footer {
        margin-top: 60px;
        font-size: 14px;
        color: #888;
    }

    /* Subround info styling */
    .subround-info {
      font-size: 0.85rem;
      color: #6c757d;
      margin-top: 0.25rem;
      padding: 0.25rem 0.5rem;
      background-color: #f8f9fa;
      border-radius: 4px;
      border-left: 3px solid #0d6efd;
    }

    .date-info {
      font-size: 0.75rem;
      color: #28a745;
      margin-top: 0.25rem;
      font-style: italic;
    }

    /* Mobile optimizations */
    @media (max-width: 768px) {
      .container {
        padding-left: 1rem;
        padding-right: 1rem;
      }

      .card {
        margin: 1rem;
        padding: 1.5rem;
      }

      .navbar-brand {
        font-size: 1rem;
      }

      .navbar-brand img {
        width: 30px;
      }

      .form-control, .form-select {
        font-size: 0.9rem;
      }

      .btn-primary {
        padding: 0.7rem 1rem;
        font-size: 0.9rem;
      }

      .dropdown-menu {
        font-size: 0.9rem;
      }

      .subround-info {
        font-size: 0.8rem;
      }

      .date-info {
        font-size: 0.7rem;
      }
    }

    /* Extra small devices */
    @media (max-width: 576px) {
      .container {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .card {
        margin: 0.5rem;
        padding: 1rem;
        border-radius: 10px;
      }

      h1 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
      }

      .form-control, .form-select {
        padding: 0.6rem 0.8rem;
        font-size: 0.875rem;
      }

      .btn-primary {
        padding: 0.6rem 1rem;
        font-size: 0.875rem;
      }

      .btn-outline-secondary {
        padding: 0.4rem 0.8rem;
        font-size: 0.875rem;
      }

      .dropdown-menu {
        font-size: 0.875rem;
      }

      .dropdown-item {
        padding: 0.4rem 0.8rem;
      }

      footer {
        font-size: 0.75rem;
        padding: 0.5rem 0;
      }

      .subround-info {
        font-size: 0.75rem;
        padding: 0.2rem 0.4rem;
      }

      .date-info {
        font-size: 0.65rem;
      }
    }

    /* Landscape mobile optimization */
    @media (max-height: 600px) and (orientation: landscape) {
      .card {
        margin: 0.5rem;
        padding: 1rem;
      }

      h1 {
        font-size: 1.3rem;
        margin-bottom: 1rem;
      }

      .form-control, .form-select {
        padding: 0.5rem 0.7rem;
      }

      .btn-primary {
        padding: 0.5rem 1rem;
      }
    }

    /* Tablet optimization */
    @media (min-width: 768px) and (max-width: 991px) {
      .card {
        max-width: 600px;
        padding: 1.5rem;
      }

      .form-control, .form-select {
        font-size: 0.95rem;
      }

      .btn-primary {
        font-size: 0.95rem;
      }
    }

    /* Large screen optimization */
    @media (min-width: 1200px) {
      .card {
        max-width: 750px;
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
<div class="container my-3 my-md-5">
  <div class="card">
    <div class="text-center pb-3 pb-md-4">
      <div class="d-flex justify-content-start mb-3">
          <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-arrow-left me-1"></i>Kembali
          </a>
      </div>
      <h1>Data Rencana Ubinan</h1>
      <p class="text-muted mb-0" style="font-size: clamp(0.8rem, 1.5vw, 0.9rem);">
        Badan Pusat Statistik (BPS) Kabupaten Bekasi
      </p>
    </div>

    <form action="simpan_tambah_data.php" method="POST">
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">
            <i class="bi bi-person me-2"></i>Nama Petani
          </label>
          <input type="text" name="nama_petani" class="form-control" placeholder="Masukkan nama petani" required>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label">
            <i class="bi bi-geo-alt me-2"></i>Kecamatan
          </label>
          <select name="kecamatan" id="kecamatan" class="form-select" required>
            <option value="">-- Pilih Kecamatan --</option>
            <?php
              $result = mysqli_query($conn, 'SELECT id, nama_kecamatan FROM kecamatan ORDER BY nama_kecamatan ASC');
              while($kecamatan = mysqli_fetch_assoc($result)) {
                echo '<option value="'.$kecamatan['id'].'">'.htmlspecialchars($kecamatan['nama_kecamatan']).'</option>';
              }
            ?>
          </select>
        </div>
        
        <div class="col-12 col-md-6">
          <label class="form-label">
            <i class="bi bi-house me-2"></i>Desa
          </label>
          <select name="desa" id="desa" class="form-select" required>
            <option value="">-- Pilih Desa --</option>
          </select>
        </div>
        
        <div class="col-12 col-md-6">
          <label class="form-label">
            <i class="bi bi-list-ol me-2"></i>Subround
          </label>
          <select name="subround" id="subround" class="form-select" required>
            <option value="">-- Pilih Subround --</option>
            <option value="1">Subround 1</option>
            <option value="2">Subround 2</option>
            <option value="3">Subround 3</option>
          </select>
          <div id="subround-info" class="subround-info" style="display: none;">
            <i class="bi bi-info-circle me-1"></i>
            <span id="subround-period"></span>
          </div>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label">
            <i class="bi bi-calendar me-2"></i>Tanggal Ubinan
          </label>
          <input type="date" name="tanggal_panen" id="tanggal_panen" class="form-control" required disabled>
          <div id="date-info" class="date-info" style="display: none;">
            Pilih subround terlebih dahulu untuk mengaktifkan tanggal
          </div>
        </div>
        
        <div class="col-12 col-md-6">
          <label class="form-label">
            <i class="bi bi-diagram-3 me-2"></i>Nomor Segmen
          </label>
          <select name="nomor_segmen" id="nomor_segmen" class="form-select" required>
            <option value="">-- Pilih Nomor Segmen --</option>
            <?php
              $result = mysqli_query($conn, 'SELECT nomor_segmen FROM segmen ORDER BY nomor_segmen ASC');
              while($segmen = mysqli_fetch_assoc($result)) {
                echo '<option value="'.$segmen['nomor_segmen'].'">'.htmlspecialchars($segmen['nomor_segmen']).'</option>';
              }
            ?>
          </select>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label">
            <i class="bi bi-grid me-2"></i>Nomor Sub Segmen
          </label>
          <select name="nomor_sub_segmen" class="form-select" required>
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

        <div class="col-12">
          <label class="form-label">
            <i class="bi bi-flag me-2"></i>Status
          </label>
          <select name="status" class="form-select" required>
            <option value="">-- Pilih Status --</option>
            <option value="belum selesai">Belum dilakukan ubinan</option>
            <option value="sudah">Sudah dilakukan ubinan</option>
            <option value="tidak bisa">Tidak bisa dilakukan ubinan</option>
          </select>
        </div>

        <div class="col-12 pt-2 pt-md-3">
          <div class="d-grid">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-2"></i>Simpan Data
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<footer class="text-center mt-5 mb-3">
    &copy; <?= date('Y'); ?> SIPANTAU
</footer>

<script>
// Definisi periode subround (tahun berjalan)
const currentYear = new Date().getFullYear();
const subroundPeriods = {
    1: {
        name: 'Januari - April',
        startMonth: 0, // January (0-based)
        endMonth: 3,   // April (0-based)
        description: 'Periode pertama: Januari sampai April'
    },
    2: {
        name: 'Mei - Agustus', 
        startMonth: 4, // May (0-based)
        endMonth: 7,   // August (0-based)
        description: 'Periode kedua: Mei sampai Agustus'
    },
    3: {
        name: 'September - Desember',
        startMonth: 8,  // September (0-based)
        endMonth: 11,   // December (0-based)
        description: 'Periode ketiga: September sampai Desember'
    }
};

// Function untuk format tanggal ke YYYY-MM-DD
function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Function untuk mengatur batasan tanggal berdasarkan subround
function setDateLimits(subround) {
    const dateInput = document.getElementById('tanggal_panen');
    const dateInfo = document.getElementById('date-info');
    
    if (!subround || !subroundPeriods[subround]) {
        dateInput.disabled = true;
        dateInput.value = '';
        dateInput.removeAttribute('min');
        dateInput.removeAttribute('max');
        dateInfo.style.display = 'block';
        dateInfo.textContent = 'Pilih subround terlebih dahulu untuk mengaktifkan tanggal';
        dateInfo.className = 'date-info';
        return;
    }
    
    const period = subroundPeriods[subround];
    
    // Set tanggal minimum (awal bulan pertama)
    const minDate = new Date(currentYear, period.startMonth, 1);
    
    // Set tanggal maksimum (akhir bulan terakhir)
    const maxDate = new Date(currentYear, period.endMonth + 1, 0); // Hari terakhir bulan
    
    // Apply ke input date
    dateInput.min = formatDate(minDate);
    dateInput.max = formatDate(maxDate);
    dateInput.disabled = false;
    
    // Update info
    dateInfo.style.display = 'block';
    dateInfo.textContent = `Tanggal harus dalam periode ${period.name} ${currentYear}`;
    dateInfo.className = 'date-info';
    
    // Validasi jika sudah ada value
    if (dateInput.value) {
        const selectedDate = new Date(dateInput.value);
        if (selectedDate < minDate || selectedDate > maxDate) {
            dateInput.value = '';
        }
    }
}

// Event listener untuk perubahan subround
document.getElementById('subround').addEventListener('change', function() {
    const subround = parseInt(this.value);
    const subroundInfo = document.getElementById('subround-info');
    const subroundPeriod = document.getElementById('subround-period');
    
    if (subround && subroundPeriods[subround]) {
        const period = subroundPeriods[subround];
        subroundInfo.style.display = 'block';
        subroundPeriod.textContent = `${period.description} ${currentYear}`;
        
        // Set batasan tanggal
        setDateLimits(subround);
    } else {
        subroundInfo.style.display = 'none';
        setDateLimits(null);
    }
});

// Event listener untuk validasi tanggal real-time
document.getElementById('tanggal_panen').addEventListener('change', function() {
    const subround = parseInt(document.getElementById('subround').value);
    const selectedDate = new Date(this.value);
    const dateInfo = document.getElementById('date-info');
    
    if (subround && subroundPeriods[subround]) {
        const period = subroundPeriods[subround];
        const minDate = new Date(currentYear, period.startMonth, 1);
        const maxDate = new Date(currentYear, period.endMonth + 1, 0);
        
        if (selectedDate < minDate || selectedDate > maxDate) {
            this.value = '';
            dateInfo.style.display = 'block';
            dateInfo.textContent = `⚠️ Tanggal harus dalam periode ${period.name} ${currentYear}`;
            dateInfo.style.color = '#dc3545'; // Red color for error
        } else {
            dateInfo.style.display = 'block';
            dateInfo.textContent = `✅ Tanggal valid untuk ${period.name} ${currentYear}`;
            dateInfo.style.color = '#28a745'; // Green color for success
        }
    }
});

// Existing AJAX for kecamatan-desa
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

// Form validation before submit
document.querySelector('form').addEventListener('submit', function(e) {
    const subround = document.getElementById('subround').value;
    const tanggal = document.getElementById('tanggal_panen').value;
    
    if (subround && tanggal) {
        const selectedDate = new Date(tanggal);
        const period = subroundPeriods[parseInt(subround)];
        const minDate = new Date(currentYear, period.startMonth, 1);
        const maxDate = new Date(currentYear, period.endMonth + 1, 0);
        
        if (selectedDate < minDate || selectedDate > maxDate) {
            e.preventDefault();
            alert(`Tanggal harus dalam periode ${period.name} ${currentYear}`);
            return false;
        }
    }
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>