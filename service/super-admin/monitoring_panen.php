<?php
session_start();
include '../../config/koneksi.php';

// Cek login dan role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../index.php");
    exit;
}

// Ambil data panen
$data = mysqli_query($conn, "SELECT * FROM monitoring_data_panen ORDER BY created_at DESC");

// Query rata-rata ubinan (berat_plot)
$q_avg = mysqli_query($conn, "SELECT AVG(berat_plot) AS avg_berat_plot FROM monitoring_data_panen WHERE berat_plot IS NOT NULL AND berat_plot != ''");
$avg = mysqli_fetch_assoc($q_avg);
$avg_berat_plot = $avg['avg_berat_plot'] !== null ? number_format($avg['avg_berat_plot'], 2) : '-';

// Hitung jumlah per status
$q_status = mysqli_query($conn, "
    SELECT status, COUNT(*) as jumlah
    FROM monitoring_data_panen
    WHERE status IN ('selesai', 'belum selesai', 'tidak bisa', 'sudah')
    GROUP BY status
");
$status_count = [
    'selesai' => 0,
    'belum selesai' => 0,
    'tidak bisa' => 0,
    'sudah' => 0
];
while ($row = mysqli_fetch_assoc($q_status)) {
    $status_count[strtolower($row['status'])] = $row['jumlah'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Monitoring Panen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
        
        /* Desktop layout */
        @media (min-width: 992px) {
            #mainContent {
                margin-left: 240px !important;
            }
            .sidebar {
                width: 240px;
            }
        }
        
        /* Tablet and mobile layout */
        @media (max-width: 991.98px) {
            #mainContent {
                margin-left: 0 !important;
            }
            .sidebar {
                display: none !important;
            }
        }
        
        /* Mobile optimizations */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 0.9rem;
            }
            
            .navbar-brand img {
                height: 30px !important;
            }
            
            h2 {
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
            
            .fs-3 {
                font-size: 1.5rem !important;
            }
            
            .fs-5 {
                font-size: 0.9rem !important;
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
            .container-fluid {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
            
            #mainContent {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
            
            .card-body .fs-3 {
                font-size: 1.2rem !important;
            }
            
            .table th, .table td {
                padding: 0.3rem !important;
            }
        }
        
        footer {
            margin-top: 60px;
            font-size: 14px;
            color: #888;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top" style="z-index:1040;">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="monitoring_panen.php">
            <img src="../../assets/logo.png" alt="Logo BPS" height="40" class="me-2">
            UBINANKU
        </a>
        
        <!-- Mobile menu button -->
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<!-- Main Layout -->
<div class="container-fluid" style="padding-top:70px;">
    <div class="row">
        
        <!-- Desktop Sidebar -->
        <nav class="col-lg-2 d-none d-lg-block bg-white border-end shadow-sm sidebar py-4 position-fixed" style="height:100vh; z-index:1030;">
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

        <!-- Mobile Sidebar (Offcanvas) -->
        <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
            <div class="offcanvas-header bg-primary text-white">
                <h5 class="offcanvas-title" id="mobileSidebarLabel">
                    <i class="bi bi-person-circle me-2"></i>Supervisor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="nav nav-pills flex-column mb-auto">
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
                    <!-- Dropdown Manajemen Mobile -->
                    <li class="nav-item mb-2">
                        <a class="nav-link text-primary d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#manajemenMenuMobile" role="button" aria-expanded="false" aria-controls="manajemenMenuMobile">
                        <span><i class="bi bi-gear me-2"></i> Manajemen</span>
                        <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="collapse ps-4" id="manajemenMenuMobile">
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
                <div>
                    <a href="../../auth/logout.php" class="btn btn-outline-danger w-100">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main id="mainContent" class="col-12 col-lg-10 px-2 px-md-4 pt-3 pt-md-4" style="margin-left:240px;">
            <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4">
                <h2 class="mb-0">Data Ubinan</h2>
            </div>
            
            <!-- Card Statistik Ubinan - Layout seperti dashboard admin-biasa -->
            <div class="row g-2 g-md-3 g-lg-4 mb-3 mb-md-4">
                <!-- Mobile: 1 card per baris (col-12), Desktop: 4 cards per baris (col-lg-3) -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 text-center h-100">
                        <div class="card-body p-2 p-md-3">
                            <div class="text-muted mb-1" style="font-size: clamp(0.8rem, 1.5vw, 1rem);">Rata-rata ubinan</div>
                            <div class="fw-bold text-primary" style="font-size: clamp(1.2rem, 3vw, 1.75rem);"><?= $avg_berat_plot; ?></div>
                            <div class="small text-muted" style="font-size: clamp(0.7rem, 1.2vw, 0.875rem);">kuintal beras</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 text-center h-100">
                        <div class="card-body p-2 p-md-3">
                            <div class="text-muted mb-1" style="font-size: clamp(0.8rem, 1.5vw, 1rem);">Selesai</div>
                            <div class="fw-bold text-success" style="font-size: clamp(1.2rem, 3vw, 1.75rem);"><?= $status_count['selesai']; ?></div>
                            <div class="small text-muted" style="font-size: clamp(0.7rem, 1.2vw, 0.875rem);">Data ubinan yang sudah selesai</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 text-center h-100">
                        <div class="card-body p-2 p-md-3">
                            <div class="text-muted mb-1" style="font-size: clamp(0.8rem, 1.5vw, 1rem);">Belum Selesai</div>
                            <div class="fw-bold text-warning" style="font-size: clamp(1.2rem, 3vw, 1.75rem);"><?= $status_count['belum selesai'] + $status_count['sudah']; ?></div>
                            <div class="small text-muted" style="font-size: clamp(0.7rem, 1.2vw, 0.875rem);">Data ubinan yang belum selesai diinput</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 text-center h-100">
                        <div class="card-body p-2 p-md-3">
                            <div class="text-muted mb-1" style="font-size: clamp(0.8rem, 1.5vw, 1rem);">Tidak Bisa</div>
                            <div class="fw-bold text-danger" style="font-size: clamp(1.2rem, 3vw, 1.75rem);"><?= $status_count['tidak bisa']; ?></div>
                            <div class="small text-muted" style="font-size: clamp(0.7rem, 1.2vw, 0.875rem);">Data ubinan yang tidak dapat dilakukan</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end align-items-center mt-3 mt-md-4 mb-3">
                <a href="export_excel.php" class="btn btn-success btn-sm btn-custom" style="font-size: clamp(0.7rem, 1.5vw, 0.875rem);">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-2 p-md-3">
                    <h5 class="card-title mb-3 mb-md-4" style="font-size: clamp(1rem, 2.5vw, 1.25rem);">Data Monitoring Ubinan</h5>
                    <div class="row mb-3">
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <select id="statusFilter" class="form-select" style="font-size: clamp(0.8rem, 1.5vw, 0.875rem);">
                                <option value="">Semua Status</option>
                                <option value="selesai">Selesai</option>
                                <option value="belum selesai">Belum Selesai</option>
                                <option value="tidak bisa">Tidak Bisa</option>
                                <option value="sudah">Sudah</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="tabelPanen" class="table table-bordered align-middle table-hover" style="font-size: clamp(0.75rem, 1.5vw, 0.875rem);">
                            <thead class="table-light text-center">
                                <tr>
                                    <th class="text-center align-middle" style="width:40px;">No</th>
                                    <th class="text-center align-middle">Tanggal Panen</th>
                                    <th class="text-center align-middle">Nama Petani</th>
                                    <th class="text-center align-middle d-none d-md-table-cell">Lokasi</th>
                                    <th class="text-center align-middle">Berat Ubinan</th>
                                    <th class="text-center align-middle">Status</th>
                                    <th class="text-center align-middle" style="width:60px;"></th>
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
                                                    echo '<span class="badge bg-warning text-dark">Belum </span>';
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
                                                <a href="edit_panen.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-warning me-1" title="Edit Data">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="#" 
                                                    class="btn btn-sm btn-outline-danger deleteButton" 
                                                    data-id="<?= $row['id']; ?>" 
                                                    title="Hapus Data">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
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

<!-- Footer -->
<footer class="text-center mt-5 mb-3">
    &copy; <?= date('Y'); ?> UBINANKU
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    // Custom filter status
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var selected = $('#statusFilter').val().toLowerCase();
        var row = settings.aoData[dataIndex].nTr; // get the <tr> element
        var rowStatus = $(row).data('status') || '';

        if (!selected) return true;
        if (selected === rowStatus) return true;
        return false;
    });

    var t = $('#tabelPanen').DataTable({
        language: { 
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
            emptyTable: "Belum ada data panen."
        },
        responsive: true,
        pageLength: 10,
        columnDefs: [{ targets: 0, searchable: false, orderable: false }],
        order: [[1, 'desc']]
    });

    // Nomor otomatis
    t.on('draw.dt', function () {
        t.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    });
    t.draw();

    // Tampilkan modal hapus
    $('.deleteButton').on('click', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        $('#deleteConfirmButton').attr('href', 'hapus_panen.php?id=' + id);
        $('#confirmDeleteModal').modal('show');
    });

    // Klik baris tabel ke detail
    $('#tabelPanen').on('click', '.table-row-link', function(e) {
        if (!$(e.target).closest('a').length) {
            window.location.href = $(this).data('href');
        }
    });

    // Efek hover baris
    $('#tabelPanen').on('mouseenter', '.table-row-link', function() {
        $(this).css('background', 'linear-gradient(90deg, #e0f7fa 0%, #e3f2fd 100%)');
    }).on('mouseleave', '.table-row-link', function() {
        $(this).css('background', '');
    });

    // Filter
    $('#statusFilter').on('change', function() {
        t.draw();
    });

    // Auto close mobile sidebar when clicking menu items
    const offcanvasElement = document.getElementById('mobileSidebar');
    if (offcanvasElement) {
        const bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
        
        // Close offcanvas when clicking menu links
        document.querySelectorAll('#mobileSidebar .nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                // Don't close if it's a dropdown toggle
                if (!this.hasAttribute('data-bs-toggle')) {
                    bsOffcanvas.hide();
                }
            });
        });
    }
});
</script>

</body>
</html>
