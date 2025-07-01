<?php
session_start();
include '../../../server/config/koneksi.php';

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
        .navbar-brand {
            font-weight: bold;
        }
        @media (min-width: 992px) {
            #mainContent {
                margin-left: 240px !important;
            }
        }
        @media (max-width: 991.98px) {
            #mainContent {
                margin-left: 0 !important;
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
        <main id="mainContent" class="col-lg-10 ms-auto px-4 pt-4">
            <h2 class="mb-4">Data Ubinan</h2>
            <!-- Card Statistik Ubinan -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 text-center h-100">
                        <div class="card-body">
                        <div class="fs-5 text-muted mb-1">Rata-rata ubinan</div>
                        <div class="fs-3 fw-bold"><?= $avg_berat_plot; ?></div>
                        <div class="small text-muted">kuintal beras</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 text-center h-100">
                        <div class="card-body">
                        <div class="fs-5 text-muted mb-1">Selesai</div>
                        <div class="fs-3 fw-bold"><?= $status_count['selesai'];; ?></div>
                        <div class="small text-muted">Data ubinan yang sudah selesai</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 text-center h-100">
                        <div class="card-body">
                        <div class="fs-5 text-muted mb-1">Belum Selesai</div>
                        <div class="fs-3 fw-bold"><?= $status_count['belum selesai'] + $status_count['sudah']; ?></div>
                        <div class="small text-muted">Data ubinan yang belum selesai diinput</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 text-center h-100">
                        <div class="card-body">
                        <div class="fs-5 text-muted mb-1">Tidak Bisa</div>
                        <div class="fs-3 fw-bold"><?= $status_count['tidak bisa'];; ?></div>
                        <div class="small text-muted">Data ubinan yang tidak dapat dilakukan</div>
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
                        <div class="col-md-4 mb-2 mb-md-0">
                            <select id="statusFilter" class="form-select">
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
                                    <th class="text-center align-middle">Lokasi</th>
                                    <th class="text-center align-middle">Berat Ubinan (kuintal)</th>
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
                                            <td><?= htmlspecialchars($row['desa'] . ', ' . $row['kecamatan']); ?></td>
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
    &copy; <?= date('Y'); ?> Monitoring Panen
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
});
</script>

</body>
</html>
