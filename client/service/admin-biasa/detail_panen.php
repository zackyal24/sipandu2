<?php
session_start();
if (!isset($_SESSION['pml'])) {
    header("Location: login.php");
    exit;
}

include '../../../server/config/koneksi.php';

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
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }
        .card {
            border-radius: 12px;
        }
        .btn-custom {
            border-radius: 8px;
        }
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
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">PML Panel</a>
        <div class="d-flex align-items-center">
            <span class="text-white me-3">👋 Halo, <strong><?= htmlspecialchars($_SESSION['pml']); ?></strong></span>
            <a href="../../auth/logout.php" class="btn btn-outline-light btn-sm btn-custom">Logout</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <a href="dashboard.php" class="btn btn-outline-primary btn-custom mb-3">
            <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <div class="d-flex justify-content-end mb-3">
                <button id="exportPDF" class="btn btn-outline-secondary mb-3">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </button>
            </div>
            <div id="detail-panen">
                <h3 class="fw-bold mb-4">Detail Data Panen</h3>
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
                    <tr><th>Berat Plot (kg)</th>
                        <td>
                            <?php if (!empty($data['berat_plot']) && $data['berat_plot'] != 0): ?>
                                <span class="fw-bold text-primary"><?= htmlspecialchars($data['berat_plot']); ?></span>
                            <?php else: ?>
                                <span class="text-muted">Belum terdata</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr><th>GKP</th>
                        <td>
                            <?php if (!empty($data['gkp']) && $data['gkp'] != 0): ?>
                                <span class="fw-bold text-primary"><?= htmlspecialchars($data['gkp']); ?></span>
                            <?php else: ?>
                                <span class="text-muted">Belum terdata</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr><th>GKG</th>
                        <td>
                            <?php if (!empty($data['gkg']) && $data['gkg'] != 0): ?>
                                <span class="fw-bold text-primary"><?= htmlspecialchars($data['gkg']); ?></span>
                            <?php else: ?>
                                <span class="text-muted">Belum terdata</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr><th>Hasil Ubinan (kuintal)</th>
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
    <!-- Tombol Hapus -->
    <div class="mt-4 d-flex justify-content-end">
        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalHapusPanen">
            <i class="bi bi-trash"></i> Hapus Data
        </button>
    </div>
</div>

<!-- Footer -->
<footer class="text-center mt-5 mb-3">
    &copy; <?= date('Y'); ?> Monitoring Panen
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
</script>
</body>
</html>