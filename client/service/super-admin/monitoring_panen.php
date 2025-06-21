<?php
session_start();
include '../../../server/config/koneksi.php';

// Cek login dan role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../index.php");
    exit;
}

// Ambil data panen
$data = mysqli_query($conn, "SELECT * FROM monitoring_data_panen ORDER BY created_at DESC");
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
        @media (min-width: 992px) {
            #mainContent {
                margin-left: 240px;
            }
        }
        @media (max-width: 991.98px) {
            #mainContent {
                margin-left: 0;
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
                    <span class="fs-5 fw-bold">Superadmin</span>
                </a>
                <hr>
                <ul class="nav nav-pills flex-column mb-auto px-2">
                    <li class="nav-item mb-2">
                        <a href="super_admin.php" class="nav-link text-primary">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="monitoring_panen.php" class="nav-link active">
                            <i class="bi bi-basket-fill me-2"></i> Data Ubinan
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="monitoring_akun.php" class="nav-link text-primary">
                            <i class="bi bi-person-gear me-2"></i> Manajemen User
                        </a>
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
            <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                <a href="super_admin.php" class="btn btn-outline-primary btn-custom">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <a href="export_excel.php" class="btn btn-success btn-sm btn-custom">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="fw-bold mb-4 text-center">Data Monitoring Ubinan</h3>
                    <div class="table-responsive">
                        <table id="tabelPanen" class="table table-striped align-middle table-hover">
                            <thead class="table-primary text-center">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Tanggal Panen</th>
                                    <th class="text-center">Nama Petani</th>
                                    <th class="text-center">Lokasi</th>
                                    <th class="text-center">Berat Panen (kg)</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($data) > 0): ?>
                                    <?php while($row = mysqli_fetch_assoc($data)): ?>
                                        <tr class="table-row-link" data-href="detail_panen.php?id=<?= $row['id']; ?>">
                                            <td class="text-center"></td>
                                            <td class="text-center"><?= htmlspecialchars($row['tanggal_panen']); ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row['nama_petani']); ?></td>
                                            <td class=""><?= htmlspecialchars($row['desa'] . ', ' . $row['kecamatan']); ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row['berat_panen']); ?></td>
                                            <td class="text-center">
                                                <a href="#" class="btn btn-link text-danger p-0 deleteButton" data-id="<?= $row['id']; ?>" title="Hapus Data">
                                                    <i class="bi bi-trash3 fs-5 text-secondary"></i>
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
    var t = $('#tabelPanen').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
        responsive: true,
        pageLength: 10,
        columnDefs: [{ targets: 0, searchable: false, orderable: false }],
        order: [[1, 'desc']]
    });

    // Nomor otomatis
    t.on('order.dt search.dt', function () {
        t.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();

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
});
</script>

</body>
</html>
