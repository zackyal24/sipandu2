<?php
// filepath: c:\xampp\htdocs\ubinanku-kab-bekasi\service\admin-biasa\detail_panen.php
session_start();
if (!isset($_SESSION['pml'])) {
    header("Location: login.php");
    exit;
}

include '../../config/koneksi.php';

// Ambil ID dari parameter URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query untuk mengambil data berdasarkan ID
$query = mysqli_query($conn, "SELECT * FROM monitoring_data_panen WHERE id = $id");
$data = mysqli_fetch_assoc($query);

// Jika data tidak ditemukan, redirect ke halaman dashboard
if (!$data) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Panen | Monitoring Panen</title>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 20px;
        }
        .card {
            border-radius: 12px;
        }
        .btn-custom {
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: scale(1.05);
        }
        
        /* Mobile optimizations */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 0.9rem;
            }
            
            .navbar-brand img {
                height: 30px !important;
            }
            
            h2, h3 {
                font-size: 1.3rem;
            }
            
            h5 {
                font-size: 1rem;
            }
            
            .card {
                margin-bottom: 0.75rem;
            }
            
            .card-body {
                padding: 0.75rem !important;
            }
            
            .table-responsive {
                font-size: 0.8rem;
            }
            
            .btn-sm {
                font-size: 0.7rem;
                padding: 0.2rem 0.4rem;
            }
            
            .badge {
                font-size: 0.65rem;
            }
        }
        
        /* Extra small devices */
        @media (max-width: 576px) {
            .container {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
            
            .table th, .table td {
                padding: 0.3rem !important;
                font-size: 0.75rem;
            }
        }
        
        img.preview {
            width: 250px;
            height: 180px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 5px;
            display: block;
            margin: 0 auto;
        }
        
        /* Mobile optimizations for images */
        @media (max-width: 768px) {
            img.preview {
                width: 150px !important;
                height: 120px !important;
                max-width: 100%;
            }
        }
        
        /* Extra small devices for images */
        @media (max-width: 576px) {
            img.preview {
                width: 120px !important;
                height: 100px !important;
            }
        }
        
        #detail-panen table, #detail-panen tr, #detail-panen td, #detail-panen th {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        
        #detail-panen img {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            max-width: 200px !important;
            height: 150px !important;
            object-fit: cover;
            display: block;
            margin: 0 auto;
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
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <img src="../../assets/logo.png" alt="Logo BPS" height="40" class="me-2">
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
<div class="container my-4" style="padding-top: 1rem;">
    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Header Actions -->
            <a href="javascript:history.back()" class="btn btn-outline-primary btn-custom mb-3">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
            
            <div class="d-flex justify-content-end mb-3">
                <button id="exportPDF" class="btn btn-outline-secondary btn-custom">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
                </button>
            </div>

            <div id="detail-panen">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold mb-0">Data Ubinan</h3>
                    <div class="dropdown">
                        <button class="btn btn-link p-0 border-0 text-secondary" type="button" id="aksiDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:1.7rem;">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="aksiDropdown">
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
                                    <i class="bi bi-journal-arrow-up me-2"></i>Revisi Data
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
                    <tr><th>Subround</th><td><?= htmlspecialchars($data['subround']); ?></td></tr>
                    <tr><th>Nomor Sub Segmen</th><td><?= htmlspecialchars($data['nomor_sub_segmen']); ?></td></tr>
                    <tr><th>Status</th>
                        <td>
                            <?php
                            $status = strtolower($data['status'] ?? '');
                            if ($status === 'selesai') {
                                echo '<span class="badge bg-success">Selesai</span>';
                            } elseif ($status === 'belum selesai') {
                                echo '<span class="badge bg-warning text-dark">Belum Selesai</span>';
                            } elseif ($status === 'revisi') {
                                echo '<span class="badge bg-info">Revisi</span>';
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
                    <tr><th>Berat Ubinan</th>
                        <td>
                            <?php if (!empty($data['berat_plot']) && $data['berat_plot'] != 0): ?>
                                <span class="fw-bold text-primary"><?= number_format($data['berat_plot'], 2); ?> kg</span>
                            <?php else: ?>
                                <span class="text-muted">Belum terdata</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr><th>Gabah Kering Panen</th>
                        <td>
                            <?php if (!empty($data['gkp']) && $data['gkp'] != 0): ?>
                                <span class="fw-bold text-primary"><?= number_format($data['gkp'], 2); ?> ku/ha</span>
                            <?php else: ?>
                                <span class="text-muted">Belum terdata</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr><th>Gabah Kering Giling</th>
                        <td>
                            <?php if (!empty($data['gkg']) && $data['gkg'] != 0): ?>
                                <span class="fw-bold text-primary"><?= number_format($data['gkg'], 2); ?> ku/ha</span>
                            <?php else: ?>
                                <span class="text-muted">Belum terdata</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr><th>Produksi Beras</th>
                        <td>
                            <?php if (!empty($data['ku']) && $data['ku'] != 0): ?>
                                <span class="fw-bold text-primary"><?= number_format($data['ku'], 2); ?> kuintal</span>
                            <?php else: ?>
                                <span class="text-muted">Belum terdata</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr><th>Foto Serah Terima Uang Pengganti Responden</th>
                        <td>
                            <?php if (!empty($data['foto_serah_terima'])): ?>
                                <img src="../../<?= htmlspecialchars($data['foto_serah_terima']); ?>" alt="Foto Serah Terima" class="img-fluid preview">
                            <?php else: ?>
                                <span class="text-muted">Belum terdata</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr><th>Foto Bukti Plot Ubinan</th>
                        <td>
                            <?php if (!empty($data['foto_bukti_plot_ubinan'])): ?>
                                <img src="../../<?= htmlspecialchars($data['foto_bukti_plot_ubinan']); ?>" alt="Foto Bukti Plot Ubinan" class="img-fluid preview">
                            <?php else: ?>
                                <span class="text-muted">Belum terdata</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr><th>Foto Berat Timbangan Gabah</th>
                        <td>
                            <?php if (!empty($data['foto_berat_timbangan'])): ?>
                                <img src="../../<?= htmlspecialchars($data['foto_berat_timbangan']); ?>" alt="Foto Berat Timbangan" class="img-fluid preview">
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

<!-- Footer -->
<footer class="text-center mt-5 mb-3">
    <p class="text-muted">&copy; <?= date('Y'); ?> Monitoring Panen</p>
</footer>

<!-- Modal Revisi -->
<div class="modal fade" id="revisiModal" tabindex="-1" aria-labelledby="revisiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form id="formRevisi" method="post" action="revisi_panen.php">
        <div class="modal-header">
          <h5 class="modal-title" id="revisiModalLabel">
            <i class="bi bi-journal-arrow-up me-2"></i>Catatan Revisi Data Panen
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="revisiId">
          <div class="mb-3">
            <label class="form-label fw-bold">Nama Petani</label>
            <input type="text" class="form-control" id="revisiNama" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Catatan Revisi <span class="text-danger">*</span></label>
            <textarea name="note" class="form-control" rows="4" required placeholder="Tulis catatan revisi di sini..."></textarea>
            <div class="form-text">Berikan instruksi yang jelas untuk PCL</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-info">
            <i class="bi bi-send me-2"></i>Kirim Revisi
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('exportPDF').addEventListener('click', function () {
        var element = document.getElementById('detail-panen');
        var nama = "<?= preg_replace('/[^a-zA-Z0-9_\-]/', '', $data['nama_petani']); ?>";
        var id = "<?= $data['id']; ?>";
        var filename = nama + '-' + id + '.pdf';
        
        // Show loading
        this.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Generating PDF...';
        this.disabled = true;
        
        html2pdf().from(element).set({
            margin: 0.5,
            filename: filename,
            html2canvas: { scale: 2 },
            jsPDF: { orientation: 'portrait', unit: 'cm', format: 'a4' }
        }).save().then(() => {
            // Reset button
            this.innerHTML = '<i class="bi bi-file-earmark-pdf me-2"></i>Export PDF';
            this.disabled = false;
        }).catch((error) => {
            console.error('PDF generation failed:', error);
            this.innerHTML = '<i class="bi bi-file-earmark-pdf me-2"></i>Export PDF';
            this.disabled = false;
            alert('Gagal generate PDF. Silakan coba lagi.');
        });
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
</body>
</html>