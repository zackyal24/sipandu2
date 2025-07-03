<?php
session_start();
include '../../../server/config/koneksi.php';

// Cek login & role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../index.php");
    exit;
}

// Tambah segmen
if (isset($_POST['tambah'])) {
    $no_segmen = trim($_POST['no_segmen']);
    if ($no_segmen !== '') {
        $stmt = mysqli_prepare($conn, "INSERT INTO segmen (nomor_segmen) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $no_segmen);
        @mysqli_stmt_execute($stmt);
    }
    header("Location: manage_segmen.php");
    exit;
}

// Hapus segmen satuan
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM segmen WHERE id = $id");
    header("Location: manage_segmen.php");
    exit;
}

// Hapus massal
if (isset($_POST['bulk_delete']) && !empty($_POST['ids'])) {
    $ids = array_map('intval', $_POST['ids']);
    $ids_str = implode(',', $ids);
    mysqli_query($conn, "DELETE FROM segmen WHERE id IN ($ids_str)");
    header("Location: manage_segmen.php");
    exit;
}

// Hapus semua data
if (isset($_GET['hapus_semua'])) {
    mysqli_query($conn, "TRUNCATE TABLE segmen");
    header("Location: manage_segmen.php");
    exit;
}

// Import CSV
if (isset($_POST['import_csv'])) {
    if (($handle = fopen($_FILES['file_csv']['tmp_name'], "r")) !== FALSE) {
        fgetcsv($handle); // Lewati header jika ada
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $nomor_segmen = trim($data[0]);
            if ($nomor_segmen !== '') {
                $stmt = mysqli_prepare($conn, "INSERT IGNORE INTO segmen (nomor_segmen) VALUES (?)");
                mysqli_stmt_bind_param($stmt, "s", $nomor_segmen);
                mysqli_stmt_execute($stmt);
            }
        }
        fclose($handle);
        header("Location: manage_segmen.php");
        exit;
    }
}

// Ambil data segmen
$segmen = mysqli_query($conn, "SELECT * FROM segmen ORDER BY nomor_segmen ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Nomor Segmen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
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
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.07);
        }
        h3 {
            font-weight: bold;
        }
        .form-label {
            font-weight: 500;
        }
        .table thead th {
            vertical-align: middle;
            text-align: center;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .btn {
            font-size: 0.95rem;
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
        .sidebar {
            width: 240px;
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top" style="z-index:1040;">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="super_admin.php">
            <img src="../../assets/logo.png" alt="Logo BPS" height="40" class="me-2">
            UBINANKU
        </a>
    </div>
</nav>

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
                        <a href="monitoring.php" class="nav-link">
                            <i class="bi bi-list-task me-2"></i> Monitoring
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="monitoring_panen.php" class="nav-link">
                            <i class="bi bi-basket-fill me-2"></i> Data Ubinan
                        </a>
                    </li>
                    <!-- Dropdown Manajemen -->
                    <li class="nav-item mb-2">
                        <a class="nav-link text-primary d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#manajemenMenu" role="button" aria-expanded="true" aria-controls="manajemenMenu">
                        <span><i class="bi bi-gear me-2"></i> Manajemen</span>
                        <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="collapse show ps-4" id="manajemenMenu">
                        <ul class="nav flex-column">
                            <li class="nav-item mb-1">
                            <a href="monitoring_akun.php" class="nav-link text-primary">
                                <i class="bi bi-person-gear me-2"></i> User
                            </a>
                            </li>
                            <li class="nav-item mb-1">
                            <a href="manage_segmen.php" class="nav-link active">
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
            <h2 class="mb-4">Manajemen Nomor Segmen</h2>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <!-- Form Tambah Segmen -->
                            <form method="post" class="d-flex align-items-end gap-2">
                                <div>
                                    <label class="form-label mb-1">Tambah Nomor Segmen Manual</label>
                                    <input type="text" name="no_segmen" class="form-control" maxlength="9" placeholder="Nomor Segmen (9 digit)" required>
                                </div>
                                <button type="submit" name="tambah" class="btn btn-primary mb-1">
                                    <i class="bi bi-plus-circle"></i> Tambah
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <!-- Form Import CSV -->
                            <form method="post" enctype="multipart/form-data" class="d-flex align-items-end gap-2">
                                <div>
                                    <label class="form-label mb-1">Import dari CSV</label>
                                    <input type="file" name="file_csv" class="form-control" accept=".csv" required>
                                </div>
                                <button type="submit" name="import_csv" class="btn btn-success mb-1">
                                    <i class="bi bi-upload"></i> Import CSV
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="tabelSegmen" class="table table-bordered table-hover align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th class="text-center" width="60">#</th>
                                    <th class="text-center">Nomor Segmen</th>
                                    <th class="text-center" width="100"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($segmen)): ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['nomor_segmen']); ?></td>
                                    <td class="text-center">
                                        <a href="#" 
                                        class="btn btn-sm btn-outline-danger deleteButton" 
                                        data-id="<?= $row['id']; ?>" 
                                        title="Hapus Segmen">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Tombol hapus semua di bawah tabel -->
                    <button type="button" class="btn btn-outline-danger mt-3" id="hapusSemuaBtn">
                        <i class="bi bi-trash"></i> Hapus Semua Data
                    </button>
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
                Apakah Anda yakin ingin menghapus segmen ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="deleteConfirmButton" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus Semua -->
<div class="modal fade" id="confirmDeleteAllModal" tabindex="-1" aria-labelledby="confirmDeleteAllModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Semua Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin <b>MENGHAPUS SEMUA</b> data segmen? Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="?hapus_semua=1" class="btn btn-danger">Hapus Semua</a>
            </div>
        </div>
    </div>
</div>

<!-- jQuery & DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<!-- Bootstrap JS (penting untuk modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        $('#tabelSegmen').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                },
                zeroRecords: "Data tidak ditemukan"
            }
        });

        // Modal hapus satuan
        $('.deleteButton').on('click', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $('#deleteConfirmButton').attr('href', '?hapus=' + id);
            var modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            modal.show();
        });

        // Select all checkbox
        $('#selectAll').on('click', function() {
            $('.selectItem').prop('checked', this.checked);
        });

        // Hapus semua data
        $('#hapusSemuaBtn').on('click', function () {
            var modal = new bootstrap.Modal(document.getElementById('confirmDeleteAllModal'));
            modal.show();
        });
    });
</script>
</body>
</html>
