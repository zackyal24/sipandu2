<?php
session_start();
include '../../config/koneksi.php';

// Cek login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pml') {
    header("Location: ../index.php");
    exit;
}

// Ambil ID PML yang sedang login
$pml_username = $_SESSION['username'];
$pml_query = mysqli_query($conn, "SELECT id FROM users WHERE username = '$pml_username' AND role = 'pml'");
$pml_data = mysqli_fetch_assoc($pml_query);
$pml_id = $pml_data['id'];

// Query export dengan filter PML
$query = "
    SELECT mdp.* 
    FROM monitoring_data_panen mdp
    INNER JOIN users pcl ON mdp.user_id = pcl.id
    WHERE pcl.pml_id = '$pml_id' AND pcl.role = 'pcl'
    ORDER BY mdp.created_at DESC
";
$data = mysqli_query($conn, $query);

// Query untuk mengambil data HANYA dari PCL yang diawasi PML ini
$data = mysqli_query($conn, "
    SELECT mdp.* 
    FROM monitoring_data_panen mdp
    INNER JOIN users pcl ON mdp.user_id = pcl.id
    WHERE pcl.pml_id = '$pml_id' AND pcl.role = 'pcl'
    ORDER BY mdp.created_at DESC
");

// Query untuk status count - HANYA PCL yang diawasi
$status_query = mysqli_query($conn, "
    SELECT 
        mdp.status,
        COUNT(*) as count
    FROM monitoring_data_panen mdp
    INNER JOIN users pcl ON mdp.user_id = pcl.id
    WHERE pcl.pml_id = '$pml_id' AND pcl.role = 'pcl'
    GROUP BY mdp.status
");

$status_count = [];
while ($row = mysqli_fetch_assoc($status_query)) {
    $status_count[$row['status']] = $row['count'];
}

// Set default values jika tidak ada data
$status_count['selesai'] = isset($status_count['selesai']) ? $status_count['selesai'] : 0;
$status_count['belum selesai'] = isset($status_count['belum selesai']) ? $status_count['belum selesai'] : 0;
$status_count['tidak bisa'] = isset($status_count['tidak bisa']) ? $status_count['tidak bisa'] : 0;
$status_count['sudah'] = isset($status_count['sudah']) ? $status_count['sudah'] : 0;

// Query statistik - HANYA PCL yang diawasi
$q_stats = mysqli_query($conn, "
    SELECT 
        AVG(mdp.berat_plot) AS avg_berat_plot,
        COUNT(CASE WHEN mdp.berat_plot IS NOT NULL AND mdp.berat_plot != '' THEN 1 END) AS count_valid
    FROM monitoring_data_panen mdp
    INNER JOIN users pcl ON mdp.user_id = pcl.id
    WHERE pcl.pml_id = '$pml_id' AND pcl.role = 'pcl'
    AND mdp.berat_plot IS NOT NULL AND mdp.berat_plot != ''
");
$stats = mysqli_fetch_assoc($q_stats);
$avg_berat_plot = $stats['avg_berat_plot'] !== null ? number_format($stats['avg_berat_plot'], 2) : '-';

// Query untuk median - HANYA PCL yang diawasi
$q_median = mysqli_query($conn, "
    SELECT mdp.berat_plot 
    FROM monitoring_data_panen mdp
    INNER JOIN users pcl ON mdp.user_id = pcl.id
    WHERE pcl.pml_id = '$pml_id' AND pcl.role = 'pcl'
    AND mdp.berat_plot IS NOT NULL AND mdp.berat_plot != '' 
    ORDER BY mdp.berat_plot
");
$median_values = [];
while($row = mysqli_fetch_assoc($q_median)) {
    $median_values[] = $row['berat_plot'];
}
$median = '-';
if (count($median_values) > 0) {
    $count = count($median_values);
    if ($count % 2 == 0) {
        $median = number_format(($median_values[$count/2 - 1] + $median_values[$count/2]) / 2, 2);
    } else {
        $median = number_format($median_values[intval($count/2)], 2);
    }
}

// Query untuk modus - HANYA PCL yang diawasi
$q_modus = mysqli_query($conn, "
    SELECT mdp.berat_plot, COUNT(*) as frequency 
    FROM monitoring_data_panen mdp
    INNER JOIN users pcl ON mdp.user_id = pcl.id
    WHERE pcl.pml_id = '$pml_id' AND pcl.role = 'pcl'
    AND mdp.berat_plot IS NOT NULL AND mdp.berat_plot != '' 
    GROUP BY mdp.berat_plot 
    ORDER BY frequency DESC, mdp.berat_plot ASC 
    LIMIT 1
");
$modus = '-';
if (mysqli_num_rows($q_modus) > 0) {
    $modus_row = mysqli_fetch_assoc($q_modus);
    $modus = number_format($modus_row['berat_plot'], 2);
}

// Jumlah selesai
$jumlah_selesai = $status_count['selesai'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard PML | Monitoring Panen</title>

    <!-- Fonts & Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

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
        .table-row-link {
            cursor: pointer;
        }
        .sidebar {
            width: 240px;
        }
        .modal-content {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            border-radius: 12px;
        }
        .modal-body textarea {
            resize: vertical;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: clamp(0.9rem, 2vw, 1.2rem);
            }
            
            .navbar-brand img {
                width: 30px !important;
            }
            
            h2 {
                font-size: clamp(1.3rem, 3vw, 1.8rem);
                font-weight: 600;
            }
            
            .card-title {
                font-size: clamp(1rem, 2vw, 1.25rem) !important;
            }
            
            .table-responsive {
                font-size: clamp(0.75rem, 1.2vw, 0.875rem);
            }
            
            .btn-sm {
                font-size: clamp(0.7rem, 1.2vw, 0.875rem);
                padding: 0.2rem 0.4rem;
            }
            
            .badge {
                font-size: clamp(0.65rem, 1.2vw, 0.75rem);
            }
            
            .modal-dialog {
                margin: 0.5rem;
            }
            
            .modal-title {
                font-size: clamp(1rem, 2vw, 1.25rem) !important;
            }
            
            .form-label {
                font-size: clamp(0.8rem, 1.5vw, 1rem) !important;
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
                font-size: 0.7rem !important;
            }
            
            .btn {
                font-size: 0.7rem !important;
                padding: 0.3rem 0.6rem !important;
            }
            
            .btn-sm {
                font-size: 0.65rem !important;
                padding: 0.2rem 0.4rem !important;
            }
            
            .badge {
                font-size: 0.6rem !important;
            }
        }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="#">
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
<div class="container my-5" style="padding-top: 70px;">
    <h2 class="mb-4">Data Ubinan</h2>
    
        <!-- Card Statistik Ubinan - Updated sama seperti monitoring panen -->
    <div class="row g-2 g-md-3 g-lg-4 mb-3 mb-md-4">
        <!-- Card Rata-rata -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body p-2 p-md-3">
                    <div class="text-muted mb-1" style="font-size: clamp(0.8rem, 1.5vw, 1rem);">
                        <i class="bi bi-calculator me-1"></i>Rata-rata
                    </div>
                    <div class="fw-bold text-primary" style="font-size: clamp(1.2rem, 3vw, 1.75rem);"><?= $avg_berat_plot; ?></div>
                    <div class="small text-muted" style="font-size: clamp(0.7rem, 1.2vw, 0.875rem);">kuintal beras</div>
                </div>
            </div>
        </div>
        
        <!-- Card Median -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body p-2 p-md-3">
                    <div class="text-muted mb-1" style="font-size: clamp(0.8rem, 1.5vw, 1rem);">
                        <i class="bi bi-graph-up me-1"></i>Median
                    </div>
                    <div class="fw-bold text-info" style="font-size: clamp(1.2rem, 3vw, 1.75rem);"><?= $median; ?></div>
                    <div class="small text-muted" style="font-size: clamp(0.7rem, 1.2vw, 0.875rem);">nilai tengah</div>
                </div>
            </div>
        </div>
        
        <!-- Card Modus -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body p-2 p-md-3">
                    <div class="text-muted mb-1" style="font-size: clamp(0.8rem, 1.5vw, 1rem);">
                        <i class="bi bi-bar-chart me-1"></i>Modus
                    </div>
                    <div class="fw-bold text-warning" style="font-size: clamp(1.2rem, 3vw, 1.75rem);"><?= $modus; ?></div>
                    <div class="small text-muted" style="font-size: clamp(0.7rem, 1.2vw, 0.875rem);">paling sering muncul</div>
                </div>
            </div>
        </div>
        
        <!-- Card Selesai -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body p-2 p-md-3">
                    <div class="text-muted mb-1" style="font-size: clamp(0.8rem, 1.5vw, 1rem);">
                        <i class="bi bi-check-circle me-1"></i>Selesai
                    </div>
                    <div class="fw-bold text-success" style="font-size: clamp(1.2rem, 3vw, 1.75rem);"><?= $jumlah_selesai; ?></div>
                    <div class="small text-muted" style="font-size: clamp(0.7rem, 1.2vw, 0.875rem);">data sudah selesai</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end align-items-center mt-4 mb-3">
        <a href="export_excel.php" class="btn btn-success btn-sm btn-custom">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">Data Monitoring Ubinan</h5>
            <div class="row mb-3">
                <div class="col-12 col-md-4 mb-2 mb-md-0">
                    <select id="statusFilter" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="selesai">Selesai</option>
                        <option value="belum selesai">Belum Selesai</option>
                        <option value="tidak bisa">Tidak Bisa</option>
                        <option value="sudah">Sudah</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table id="tabelPanen" class="table table-bordered align-middle table-hover">
                    <thead class="table-light text-center">
                        <tr>
                            <th class="text-center align-middle" style="width:40px;">No</th>
                            <th class="text-center align-middle">Tanggal Panen</th>
                            <th class="text-center align-middle">Nama Petani</th>
                            <th class="text-center align-middle d-none d-md-table-cell">Lokasi</th>
                            <th class="text-center align-middle">Berat Ubinan</th>
                            <th class="text-center align-middle">Status</th>
                            <th class="text-center align-middle" style="width:80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($data) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($data)): ?>
                                <tr class="table-row-link"
                                    data-href="detail_panen.php?id=<?= $row['id']; ?>"
                                    data-status="<?= strtolower($row['status'] ?? ''); ?>">
                                    <td class="text-center"></td>
                                    <td class="text-center"><?= htmlspecialchars($row['tanggal_panen']); ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['nama_petani']); ?></td>
                                    <td class="d-none d-md-table-cell"><?= htmlspecialchars($row['desa'] . ', ' . $row['kecamatan']); ?></td>
                                    <td class="text-center">
                                        <?php
                                        if (!empty($row['berat_plot']) && $row['berat_plot'] != 0) {
                                            echo '<span class="fw-bold">' . htmlspecialchars($row['berat_plot']) . '</span>';
                                        } else {
                                            echo '<span class="text-muted">Belum terdata</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $status = isset($row['status']) ? strtolower($row['status']) : '';
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
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-warning revisiButton" 
                                            data-id="<?= $row['id']; ?>" 
                                            data-nama="<?= htmlspecialchars($row['nama_petani']); ?>"
                                            title="Revisi Data">
                                            <i class="bi bi-journal-arrow-up"></i>
                                            <span class="d-none d-sm-inline"> Revisi</span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="deleteConfirmButton" class="btn btn-danger">Hapus</a>
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
<footer class="text-center mt-5 mb-3" style="font-size: clamp(0.8rem, 1.5vw, 1rem);">
    &copy; <?= date('Y'); ?> Monitoring Panen
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    try {
        // Cek apakah DataTable sudah ada, jika ya maka destroy dulu
        if ($.fn.DataTable.isDataTable('#tabelPanen')) {
            $('#tabelPanen').DataTable().destroy();
        }
        
        // DataTable initialization
        var table = $('#tabelPanen').DataTable({
            responsive: true,
            language: { 
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
                emptyTable: "Belum ada data panen."
            },
            pageLength: 10,
            columnDefs: [
                { targets: 0, searchable: false, orderable: false },
                { targets: -1, orderable: false }
            ],
            order: [[1, 'desc']],
            destroy: true // Tambahan option untuk memungkinkan reinisialisasi
        });

        // Nomor otomatis
        table.on('draw.dt', function () {
            var pageInfo = table.page.info();
            table.column(0, { search: 'applied', order: 'applied', page: 'current' }).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1 + pageInfo.start;
            });
        });

        // Klik baris ke detail, kecuali klik tombol revisi
        $('#tabelPanen').on('click', '.table-row-link', function(e) {
            if ($(e.target).closest('.revisiButton').length) return;
            var href = $(this).data('href');
            if (href) window.location.href = href;
        });

        // Modal revisi
        $(document).on('click', '.revisiButton', function (e) {
            e.stopPropagation();
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            $('#revisiId').val(id);
            $('#revisiNama').val(nama);
            $('#revisiModal').modal('show');
        });

        // Status filter
        $('#statusFilter').on('change', function() {
            var selectedStatus = this.value;
            if (selectedStatus) {
                table.column(5).search('^' + selectedStatus + '$', true, false).draw();
            } else {
                table.column(5).search('').draw();
            }
        });

        // Efek hover baris
        $('#tabelPanen').on('mouseenter', '.table-row-link', function() {
            $(this).css('background', 'linear-gradient(90deg, #e0f7fa 0%, #e3f2fd 100%)');
        }).on('mouseleave', '.table-row-link', function() {
            $(this).css('background', '');
        });
        
    } catch (error) {
        console.error('Error initializing DataTable:', error);
        // Fallback jika DataTables gagal
        $('#tabelPanen').show();
    }
});
</script>

</body>
</html>
