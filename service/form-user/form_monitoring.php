<?php
session_start();
include '../../config/koneksi.php';

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

// Cek apakah data sudah selesai atau dalam status revisi
$isCompleted = $data['status'] === 'selesai';
$isRevision = $data['status'] === 'revisi';
$canEdit = !$isCompleted; // PCL bisa edit jika belum selesai atau sedang revisi

// Ambil note revisi jika ada
$noteRevisi = $data['note_revisi'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Form Data Ubinan</title>

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

    .form-control[readonly] {
      background-color: #f8f9fa;
      opacity: 1;
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

    .btn-primary:disabled {
      background-color: #6c757d;
      border-color: #6c757d;
      transform: none;
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

    .alert {
      border-radius: 8px;
      font-size: clamp(0.875rem, 1.8vw, 1rem);
      padding: clamp(0.6rem, 2vw, 0.75rem) clamp(0.8rem, 2.5vw, 1rem);
    }

    .spinner-border {
      width: clamp(2rem, 4vw, 3rem);
      height: clamp(2rem, 4vw, 3rem);
    }

    footer {
        margin-top: 60px;
        font-size: 14px;
        color: #888;
    }

    .file-info {
      font-size: 0.85rem;
      color: #6c757d;
      margin-top: 0.25rem;
      padding: 0.25rem 0.5rem;
      background-color: #f8f9fa;
      border-radius: 4px;
      border-left: 3px solid #0d6efd;
    }

    .compression-info {
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

      .file-info {
        font-size: 0.8rem;
      }

      .compression-info {
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

      .file-info {
        font-size: 0.75rem;
        padding: 0.2rem 0.4rem;
      }

      .compression-info {
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
<?php include 'navbar_user.php'; ?>

<!-- Main Content -->
<div class="container my-3 my-md-5">
  <div class="card">
    <div class="text-center pb-3 pb-md-4">
      <div class="d-flex justify-content-start mb-3">
          <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-arrow-left me-1"></i>Kembali
          </a>
      </div>
      <h1>Form Data Ubinan</h1>
      <p class="text-muted mb-0" style="font-size: clamp(0.8rem, 1.5vw, 0.9rem);">
        Badan Pusat Statistik (BPS) Kabupaten Bekasi
      </p>
    </div>

    <!-- Spinner (sembunyi di awal) -->
    <div id="loadingSpinner" class="text-center mb-3" style="display: none;">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2" style="font-size: clamp(0.875rem, 1.8vw, 1rem);">Menyimpan data, mohon tunggu...</p>
    </div>

    <form action="simpan_ubinan.php" method="POST" enctype="multipart/form-data" id="ubinanlForm">
      <input type="hidden" name="id" value="<?= htmlspecialchars($data['id']); ?>">

      <?php if ($isRevision && !empty($noteRevisi)): ?>
          <div class="alert alert-warning">
              <h6><i class="bi bi-exclamation-triangle me-2"></i>Data Perlu Revisi</h6>
              <p class="mb-0"><strong>Catatan dari PML:</strong> <?= htmlspecialchars($noteRevisi); ?></p>
              <small class="text-muted">Silakan perbaiki data sesuai catatan di atas, kemudian submit ulang.</small>
          </div>
      <?php endif; ?>
      
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">
            <i class="bi bi-person me-2"></i>Nama Petani
          </label>
          <input type="text" name="nama_petani" class="form-control" value="<?= htmlspecialchars($data['nama_petani']); ?>" <?= $canEdit ? '' : 'readonly'; ?> required>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label">
            <i class="bi bi-house me-2"></i>Desa
          </label>
          <input type="text" name="desa" class="form-control" value="<?= htmlspecialchars($data['desa']); ?>" <?= $canEdit ? '' : 'readonly'; ?> required>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label">
            <i class="bi bi-geo-alt me-2"></i>Kecamatan
          </label>
          <input type="text" name="kecamatan" class="form-control" value="<?= htmlspecialchars($data['kecamatan']); ?>" <?= $canEdit ? '' : 'readonly'; ?> required>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label">
            <i class="bi bi-list-ol me-2"></i>Subround
          </label>
          <input type="text" name="subround" class="form-control" value="<?= htmlspecialchars($data['subround']); ?>" <?= $canEdit ? '' : 'readonly'; ?> required>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label">
            <i class="bi bi-calendar me-2"></i>Tanggal Ubinan
          </label>
          <input type="date" name="tanggal_panen" class="form-control" value="<?= htmlspecialchars($data['tanggal_panen']); ?>" <?= $canEdit ? '' : 'readonly'; ?> required>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label">
            <i class="bi bi-diagram-3 me-2"></i>Nomor Segmen
          </label>
          <input type="text" name="nomor_segmen" class="form-control" value="<?= htmlspecialchars($data['nomor_segmen']); ?>" <?= $canEdit ? '' : 'readonly'; ?> required>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label">
            <i class="bi bi-grid me-2"></i>Nomor Sub Segmen
          </label>
          <input type="text" name="nomor_sub_segmen" class="form-control" value="<?= htmlspecialchars($data['nomor_sub_segmen']); ?>" <?= $canEdit ? '' : 'readonly'; ?> required>
        </div>

        <div class="col-12">
          <label class="form-label">
            <i class="bi bi-camera me-2"></i>Upload Foto Serah Terima Uang Pengganti Responden
          </label>
          <input type="file" name="foto_serah_terima" id="foto_serah_terima" class="form-control" accept="image/*" <?= $canEdit ? '' : 'disabled'; ?> <?= $isRevision ? '' : 'required'; ?>>
          <div id="info_foto_serah_terima" class="file-info" style="display: none;">
            <i class="bi bi-info-circle me-1"></i>
            <span id="serah_terima_info"></span>
          </div>
        </div>

        <div class="col-12">
          <label class="form-label">
            <i class="bi bi-grid-3x3-gap me-2"></i>Upload Foto Bukti Plot Ubinan
          </label>
          <input type="file" name="foto_bukti_plot_ubinan" id="foto_bukti_plot_ubinan" class="form-control" accept="image/*" <?= $canEdit ? '' : 'disabled'; ?> <?= $isRevision ? '' : 'required'; ?>>
          <div id="info_foto_bukti_plot_ubinan" class="file-info" style="display: none;">
            <i class="bi bi-info-circle me-1"></i>
            <span id="bukti_plot_info"></span>
          </div>
        </div>

        <div class="col-12">
          <label class="form-label">
            <i class="bi bi-speedometer me-2"></i>Upload Foto Berat Gabah yang Ditimbang
          </label>
          <input type="file" name="foto_berat_timbangan" id="foto_berat_timbangan" class="form-control" accept="image/*" <?= $canEdit ? '' : 'disabled'; ?> <?= $isRevision ? '' : 'required'; ?>>
          <div id="info_foto_berat_timbangan" class="file-info" style="display: none;">
            <i class="bi bi-info-circle me-1"></i>
            <span id="berat_timbangan_info"></span>
          </div>
        </div>

        <div class="col-12">
          <label class="form-label">
            <i class="bi bi-basket me-2"></i>Berat Panen Per Plot (kg)
          </label>
          <input type="number" name="berat_plot" class="form-control" value="<?= htmlspecialchars($data['berat_plot'] ?? ''); ?>" step="0.01" placeholder="Contoh: 5.25" <?= $canEdit ? '' : 'readonly'; ?> required ?>
        </div>

        <?php if ($canEdit): ?>
            <div class="col-12 pt-2 pt-md-3">
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary" id="submitButton">
                        <i class="bi bi-save me-2"></i>
                        <?= $isRevision ? 'Submit Revisi' : 'Simpan Data Ubinan'; ?>
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-success text-center mt-3">
                    <i class="bi bi-check-circle me-2"></i>Data sudah selesai dan tidak dapat diedit.
                </div>
            </div>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<!-- footer -->
<?php include 'footer_user.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script untuk kompresi foto -->
<script>
// Fungsi untuk mengompres gambar
function compressImage(file, quality = 0.7, maxWidth = 1024, maxHeight = 768) {
    return new Promise((resolve) => {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();
        
        img.onload = function() {
            // Hitung dimensi baru dengan mempertahankan aspect ratio
            let { width, height } = img;
            
            if (width > height) {
                if (width > maxWidth) {
                    height = (height * maxWidth) / width;
                    width = maxWidth;
                }
            } else {
                if (height > maxHeight) {
                    width = (width * maxHeight) / height;
                    height = maxHeight;
                }
            }
            
            canvas.width = width;
            canvas.height = height;
            
            // Gambar image ke canvas dengan ukuran baru
            ctx.drawImage(img, 0, 0, width, height);
            
            // Konversi canvas ke blob dengan kompresi
            canvas.toBlob(resolve, 'image/jpeg', quality);
        };
        
        img.src = URL.createObjectURL(file);
    });
}

// Fungsi untuk format ukuran file
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Handle file input change untuk semua foto
['foto_serah_terima', 'foto_bukti_plot_ubinan', 'foto_berat_timbangan'].forEach(inputId => {
    document.getElementById(inputId).addEventListener('change', async function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        const infoDiv = document.getElementById('info_' + inputId);
        const infoSpan = infoDiv.querySelector('span');
        const originalSize = file.size;
        
        // Show info div and set loading state
        infoDiv.style.display = 'block';
        infoSpan.innerHTML = `Original: ${formatFileSize(originalSize)} <span class="text-warning">- Mengompres...</span>`;
        
        try {
            // Kompres gambar
            const compressedBlob = await compressImage(file, 0.7, 1024, 768);
            const compressedSize = compressedBlob.size;
            const compressionRatio = ((originalSize - compressedSize) / originalSize * 100).toFixed(1);
            
            // Buat file baru dari blob yang sudah dikompres
            const compressedFile = new File([compressedBlob], file.name, {
                type: 'image/jpeg',
                lastModified: Date.now()
            });
            
            // Ganti file di input dengan file yang sudah dikompres
            const dt = new DataTransfer();
            dt.items.add(compressedFile);
            e.target.files = dt.files;
            
            // Update info
            infoSpan.innerHTML = `Original: ${formatFileSize(originalSize)} → <span class="compression-info">Dikompres: ${formatFileSize(compressedSize)} (-${compressionRatio}%)</span>`;
            
        } catch (error) {
            console.error('Error compressing image:', error);
            infoSpan.innerHTML = `Original: ${formatFileSize(originalSize)} <span class="text-danger">- Gagal kompresi, menggunakan original</span>`;
        }
    });
});

// Handle form submit
document.getElementById('ubinanlForm').addEventListener('submit', function(e) {
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
