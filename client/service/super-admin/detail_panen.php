<?php
session_start();
include '../../../server/config/koneksi.php';

// Validasi akses hanya untuk supervisor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../index.php");
    exit;
}

// Ambil ID dari parameter
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data berdasarkan ID
$query = mysqli_query($conn, "SELECT * FROM monitoring_data_panen WHERE id = $id");
$data = mysqli_fetch_assoc($query);

// Jika tidak ditemukan
if (!$data) {
    header("Location: super_admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Panen - Supervisor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
        .card { border-radius: 12px; }
        .btn-custom { border-radius: 8px; }
        img.preview {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 5px;
        }
        #detail-panen table, #detail-panen tr, #detail-panen td, #detail-panen th {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        
        #detail-panen img {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0;
        }
        .modal-content {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        .modal-body textarea {
            resize: vertical;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top" style="z-index:1040;">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="monitoring_panen.php">
            <img src="../../assets/logo.png" alt="Logo BPS" height="40" class="me-2">
            Data Ubinan
        </a>
    </div>
</nav>

<!-- Main Layout -->
<div class="container-fluid" style="padding-top:70px;">
    <div class="row">
        
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-white border-end shadow-sm sidebar py-4 position-fixed" style="height:100vh; z-index:1030;">
            <div class="position-sticky">
                <a href="#" class="d-flex align-items-center mb-3 text-primary text-decoration-none px-3">
                    <span class="fs-5 fw-bold">Supervisor</span>
                </a>
                <hr>
                <ul class="nav nav-pills flex-column mb-auto px-2">
                    <li class="nav-item mb-2">
                        <a href="super_admin.php" class="nav-link text-primary">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="monitoring.php" class="nav-link">
                            <i class="bi bi-list-task me-2"></i> Monitoring
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="monitoring_panen.php" class="nav-link active">
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
                    <a href="../../auth/logout.php" class="btn btn-outline-danger w-100">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main id="mainContent" class="col-lg-10 ms-auto px-4">
            <div class="container my-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <a href="javascript:history.back()" class="btn btn-outline-primary btn-custom mb-3">
                        <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <div class="d-flex justify-content-end mb-3">
                            <button id="exportPDF" class="btn btn-outline-secondary mb-3">
                                <i class="bi bi-file-earmark-pdf"></i> Export PDF
                            </button>
                        </div>
                        <div id="detail-panen">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="fw-bold mb-0">Detail Data Panen</h3>
                                <div class="dropdown">
                                    <button class="btn btn-link p-0 border-0 text-secondary" type="button" id="aksiDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:1.7rem;">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="aksiDropdown">
                                        <li>
                                            <a class="dropdown-item" href="edit_panen.php?id=<?= $data['id']; ?>">
                                                <i class="bi bi-pencil-square me-2"></i> Edit Data
                                            </a>
                                        </li>
                                        <li>
                                            <button 
                                                class="dropdown-item" 
                                                type="button"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#revisiModal"
                                                data-id="<?= $data['id']; ?>"
                                                data-nama="<?= htmlspecialchars($data['nama_petani']); ?>"
                                                id="btnRevisiDropdown"
                                            >
                                                <i class="bi bi-journal-arrow-up me-2"></i> Revisi Data
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item text-danger" type="button" data-bs-toggle="modal" data-bs-target="#modalHapusPanen">
                                                <i class="bi bi-trash me-2"></i> Hapus Data
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <table class="table table-bordered">
                                <tr><th>Nama Petani</th><td><?= htmlspecialchars($data['nama_petani']); ?></td></tr>
                                <tr><th>Desa</th><td><?= htmlspecialchars($data['desa']); ?></td></tr>
                                <tr><th>Kecamatan</th><td><?= htmlspecialchars($data['kecamatan']); ?></td></tr>
                                <tr><th>Tanggal Panen</th><td><?= htmlspecialchars($data['tanggal_panen']); ?></td></tr>
                                <tr><th>Nomor Sub Segmen</th><td><?= htmlspecialchars($data['nomor_sub_segmen']); ?></td></tr>
                                <tr><th>Status</th>
                                    <td>
                                        <?php
                                        $status = strtolower($data['status'] ?? '');
                                        if ($status === 'selesai') {
                                            echo '<span class="badge bg-success">Selesai</span>';
                                        } elseif ($status === 'belum selesai') {
                                            echo '<span class="badge bg-warning text-dark">Belum Selesai</span>';
                                        } elseif ($status === 'tidak bisa') {
                                            echo '<span class="badge bg-danger">Tidak Bisa</span>';
                                        } elseif ($status === 'sudah') {
                                            echo '<span class="badge bg-primary">Sudah</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary">-</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr><th>Berat Ubinan (kg)</th>
                                    <td>
                                        <?php if (!empty($data['berat_plot']) && $data['berat_plot'] != 0): ?>
                                            <span class="fw-bold text-primary"><?= htmlspecialchars($data['berat_plot']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Belum terdata</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr><th>Gabah Kering Panen (Kuintal)</th>
                                    <td>
                                        <?php if (!empty($data['gkp']) && $data['gkp'] != 0): ?>
                                            <span class="fw-bold text-primary"><?= htmlspecialchars($data['gkp']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Belum terdata</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr><th>Gabah Kering Giling (Kuintal)</th>
                                    <td>
                                        <?php if (!empty($data['gkg']) && $data['gkg'] != 0): ?>
                                            <span class="fw-bold text-primary"><?= htmlspecialchars($data['gkg']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Belum terdata</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr><th>Produksi Beras (Kuintal Beras)</th>
                                    <td>
                                        <?php if (!empty($data['ku']) && $data['ku'] != 0): ?>
                                            <span class="fw-bold text-primary"><?= htmlspecialchars($data['ku']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Belum terdata</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr><th>Foto Petani</th>
                                    <td>
                                        <?php if (!empty($data['foto_petani'])): ?>
                                            <img src="../../<?= htmlspecialchars($data['foto_petani']); ?>" alt="Foto Petani" class="img-fluid preview">
                                        <?php else: ?>
                                            <span class="text-muted">Belum terdata</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr><th>Foto Potong</th>
                                    <td>
                                        <?php if (!empty($data['foto_potong'])): ?>
                                            <img src="../../<?= htmlspecialchars($data['foto_potong']); ?>" alt="Foto Potong" class="img-fluid preview">
                                        <?php else: ?>
                                            <span class="text-muted">Belum terdata</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr><th>Foto Timbangan</th>
                                    <td>
                                        <?php if (!empty($data['foto_timbangan'])): ?>
                                            <img src="../../<?= htmlspecialchars($data['foto_timbangan']); ?>" alt="Foto Timbangan" class="img-fluid preview">
                                        <?php else: ?>
                                            <span class="text-muted">Belum terdata</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalHapusPanen" tabindex="-1" aria-labelledby="modalHapusPanenLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalHapusPanenLabel">Konfirmasi Hapus Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin menghapus data panen ini? Tindakan ini tidak dapat dibatalkan.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <a href="hapus_panen.php?id=<?= $data['id']; ?>" class="btn btn-danger">Hapus</a>
      </div>
    </div>
  </div>
</div>

<!-- Modal Revisi-->
<div class="modal fade" id="revisiModal" tabindex="-1" aria-labelledby="revisiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form id="formRevisi" method="post" action="revisi_panen.php">
        <div class="modal-header">
          <h5 class="modal-title" id="revisiModalLabel">Catatan Revisi Data Panen</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="revisiId">
          <div class="mb-3">
            <label class="form-label">Nama Petani</label>
            <input type="text" class="form-control" id="revisiNama" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">Catatan Revisi</label>
            <textarea name="note" class="form-control" rows="4" required placeholder="Tulis catatan revisi di sini..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-info">Kirim Revisi</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="text-center mt-5 mb-4">
    &copy; <?= date('Y'); ?> Monitoring Panen | Supervisor
</footer>


<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    document.getElementById('exportPDF').addEventListener('click', function () {
        var element = document.getElementById('detail-panen');
        nama = "<?= preg_replace('/[^a-zA-Z0-9_\-]/', '', $data['nama_petani']); ?>";
        var id = "<?= $data['id']; ?>";
        var filename = nama + '-' + id + '.pdf';
        html2pdf().from(element).set({
            margin: 0.5,
            filename: filename,
            html2canvas: { scale: 2 },
            jsPDF: { orientation: 'portrait', unit: 'cm', format: 'a4' }
        }).save();
    });
    document.addEventListener('DOMContentLoaded', function () {
    // Untuk tombol revisi
    var btnRevisi = document.getElementById('btnRevisiDropdown');
    if (btnRevisi) {
        btnRevisi.addEventListener('click', function () {
            var id = this.getAttribute('data-id');
            var nama = this.getAttribute('data-nama');
            document.getElementById('revisiId').value = id;
            document.getElementById('revisiNama').value = nama;
        });
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
