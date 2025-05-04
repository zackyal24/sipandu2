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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Monitoring Panen</title>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 20px;
        }
        .card {
            border-radius: 12px;
        }
        footer {
            margin-top: 50px;
            font-size: 14px;
            color: #888;
        }
        .btn-custom {
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: scale(1.05);
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
    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Data Monitoring Panen</h3>
        <div>
            <a href="export_excel.php" class="btn btn-success btn-sm btn-custom me-2">Export Excel</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelPanen" class="table table-hover table-striped align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th>No</th>
                        <th>Tanggal Panen</th>
                        <th>Nama Petani</th>
                        <th>Lokasi</th>
                        <th>Berat Panen (kg)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td class="text-center"></td>
                            <td class="text-center"><?= htmlspecialchars($row['tanggal_panen']); ?></td>
                            <td><?= htmlspecialchars($row['nama_petani']); ?></td>
                            <td><?= htmlspecialchars($row['lokasi']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['berat_panen']); ?></td>
                            <td class="text-center">
                                <a href="detail_panen.php?id=<?= $row['id']; ?>" class="btn btn-info btn-sm btn-custom">Detail</a>
                                <a href="#" 
                                class="btn btn-danger btn-sm btn-custom deleteButton" 
                                data-id="<?= $row['id']; ?>">Hapus</a>
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
});
</script>

</body>
</html>
