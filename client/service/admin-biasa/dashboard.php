<?php
session_start();
include '../../../server/config/koneksi.php';

// Cek login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Query untuk mengambil data dari tabel monitoring_data_panen
$data = mysqli_query($conn, "SELECT * FROM monitoring_data_panen ORDER BY created_at DESC");

// Ambil nilai min & max berat panen dari database
$q_minmax = mysqli_query($conn, "SELECT MIN(berat_panen) AS min_berat, MAX(berat_panen) AS max_berat FROM monitoring_data_panen WHERE berat_panen IS NOT NULL");
$minmax = mysqli_fetch_assoc($q_minmax);
$min_berat = $minmax['min_berat'] !== null ? $minmax['min_berat'] : '-';
$max_berat = $minmax['max_berat'] !== null ? $minmax['max_berat'] : '-';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Monitoring Panen</title>

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
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="../../assets/logo.png" alt="Logo" width="40" class="me-2">
            Admin Panel
        </a>
        <div class="d-flex align-items-center">
            <span class="text-white me-3">👋 Halo, <strong><?= htmlspecialchars($_SESSION['admin']); ?></strong></span>
            <a href="../../auth/logout.php" class="btn btn-outline-light btn-sm btn-custom">Logout</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container my-5">
    <h2 class="mb-4">Data Ubinan</h2>
    <!-- Card Statistik Ubinan -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body">
                <div class="fs-5 text-muted mb-1">Max Berat Panen</div>
                <div class="fs-3 fw-bold"><?= $min_berat ?? 0; ?></div>
                <div class="small text-muted">Total user terdaftar</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body">
                <div class="fs-5 text-muted mb-1">Min Berat Panen</div>
                <div class="fs-3 fw-bold"><?= $max_berat ?? 0; ?></div>
                <div class="small text-muted">Total user terdaftar</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body">
                <div class="fs-5 text-muted mb-1">Max Nilai Ubinan</div>
                <div class="fs-3 fw-bold"><?= $jumlah_superadmin ?? 0; ?></div>
                <div class="small text-muted">Total user terdaftar</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body">
                <div class="fs-5 text-muted mb-1">Min Nilai Ubinan</div>
                <div class="fs-3 fw-bold"><?= $jumlah_superadmin ?? 0; ?></div>
                <div class="small text-muted">Total user terdaftar</div>
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
                            <th style="width:40px;">No</th>
                            <th>Tanggal Panen</th>
                            <th>Nama Petani</th>
                            <th>Lokasi</th>
                            <th>Berat Panen (kg)</th>
                            <th>Status</th>
                            <th style="width:60px;"></th>
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
                                    <td class="text-center"><?= htmlspecialchars($row['berat_panen']); ?></td>
                                    <td class="text-center">
                                        <?php
                                        $status = isset($row['status']) ? strtolower($row['status']) : '';
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
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada data panen.</td>
                            </tr>
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

<!-- Footer -->
<footer class="text-center mt-5 mb-3">
    &copy; <?= date('Y'); ?> Monitoring Panen
</footer>

<!-- Scripts -->
<script>
$(document).ready(function () {
    // Custom filter status
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var selected = $('#statusFilter').val().toLowerCase();
        var rowStatus = (data[5] || '').toLowerCase();

        if (!selected) return true;
        if (selected === rowStatus) return true;
        return false;
    });

    var t = $('#tabelPanen').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    var t = $('#tabelPanen').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        },
        responsive: true,
        pageLength: 10,
        columnDefs: [{
            targets: 0,
            searchable: false,
            orderable: false,
        }],
        order: [[1, 'desc']]
    });

    t.on('order.dt search.dt', function () {
        t.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();

    $(document).ready(function () {
    // Tangkap klik tombol Hapus
    $('.deleteButton').on('click', function () {
        var id = $(this).data('id'); // Ambil ID dari tombol
        var deleteUrl = 'hapus_panen.php?id=' + id; // URL untuk hapus
        $('#deleteConfirmButton').attr('href', deleteUrl); // Set URL ke tombol konfirmasi
        $('#confirmDeleteModal').modal('show'); // Tampilkan modal
        });
    });

    $('#tabelPanen').on('click', '.table-row-link', function(e) {
        // Jika klik tombol hapus, jangan redirect
        if ($(e.target).closest('.deleteButton').length) return;
        var href = $(this).data('href');
        if (href) window.location.href = href;
    });
});
</script>

</body>
</html>
