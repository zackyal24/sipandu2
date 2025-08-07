<?php
session_start();
include '../../config/koneksi.php';

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Nomor Segmen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .sidebar {
            width: 240px;
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
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
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
                font-size: clamp(0.9rem, 2vw, 1.2rem);
            }
            
            .navbar-brand img {
                height: 30px !important;
            }
            
            h2 {
                font-size: clamp(1.3rem, 3vw, 1.8rem);
            }
            
            .card {
                margin-bottom: 1rem;
            }
            
            .card-body {
                padding: clamp(1rem, 2vw, 1.5rem);
            }
            
            .form-control {
                font-size: clamp(0.8rem, 1.5vw, 1rem);
            }
            
            .btn {
                font-size: clamp(0.8rem, 1.2vw, 0.9rem);
                padding: 0.4rem 0.8rem;
            }
            
            .btn-sm {
                font-size: clamp(0.7rem, 1.2vw, 0.8rem);
                padding: 0.25rem 0.5rem;
            }
            
            .table-responsive {
                font-size: clamp(0.75rem, 1.2vw, 0.875rem);
            }
            
            .modal-dialog {
                margin: 0.5rem;
            }
            
            .form-label {
                font-size: clamp(0.8rem, 1.2vw, 0.9rem);
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
            
            .card-body {
                padding: 0.75rem !important;
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
            
            .form-control {
                font-size: 0.8rem !important;
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
        <a class="navbar-brand d-flex align-items-center" href="super_admin.php">
            <img src="../../assets/logo.png" alt="Logo BPS" height="40" class="me-2">
            UBINANKU
        </a>
        
        <!-- Mobile menu button -->
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

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
                        <a href="monitoring.php" class="nav-link text-primary">
                            <i class="bi bi-list-task me-2"></i> Monitoring
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="monitoring_panen.php" class="nav-link text-primary">
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
                        <a href="super_admin.php" class="nav-link text-primary mobile-nav-link">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="monitoring.php" class="nav-link text-primary mobile-nav-link">
                            <i class="bi bi-list-task me-2"></i> Monitoring
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="monitoring_panen.php" class="nav-link text-primary mobile-nav-link">
                            <i class="bi bi-basket-fill me-2"></i> Data Ubinan
                        </a>
                    </li>
                    <!-- Dropdown Manajemen Mobile -->
                    <li class="nav-item mb-2">
                        <a class="nav-link text-primary d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#manajemenMenuMobile" role="button" aria-expanded="true" aria-controls="manajemenMenuMobile">
                            <span><i class="bi bi-gear me-2"></i> Manajemen</span>
                            <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="collapse show ps-4" id="manajemenMenuMobile">
                            <ul class="nav flex-column">
                                <li class="nav-item mb-1">
                                    <a href="monitoring_akun.php" class="nav-link text-primary mobile-nav-link">
                                        <i class="bi bi-person-gear me-2"></i> User
                                    </a>
                                </li>
                                <li class="nav-item mb-1">
                                    <a href="manage_segmen.php" class="nav-link active mobile-nav-link">
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
        <main id="mainContent" class="col-12 col-lg-10 px-2 px-md-4" style="margin-left:240px;">
            <div class="pt-3 pt-md-4">
                <h2 class="mb-3 mb-md-4">Manajemen Nomor Segmen</h2>
                
                <div class="card shadow-sm">
                    <div class="card-body p-2 p-md-3">
                        <!-- Form Section -->
                        <div class="row g-2 g-md-3 mb-3 mb-md-4">
                            <div class="col-12 col-md-6">
                                <!-- Form Tambah Segmen -->
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0" style="font-size: clamp(0.8rem, 1.2vw, 1rem);">
                                            <i class="bi bi-plus-circle me-1"></i>Tambah Manual
                                        </h6>
                                    </div>
                                    <div class="card-body p-2 p-md-3">
                                        <form method="post" class="d-flex flex-column flex-sm-row gap-2">
                                            <div class="flex-grow-1">
                                                <label class="form-label mb-1" style="font-size: clamp(0.8rem, 1.2vw, 0.9rem);">
                                                    Nomor Segmen
                                                </label>
                                                <input type="text" name="no_segmen" class="form-control" maxlength="9" placeholder="9 digit" required>
                                            </div>
                                            <div class="d-flex align-items-end">
                                                <button type="submit" name="tambah" class="btn btn-primary w-100 w-sm-auto">
                                                    <i class="bi bi-plus-circle me-1"></i>Tambah
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12 col-md-6">
                                <!-- Form Import CSV -->
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0" style="font-size: clamp(0.8rem, 1.2vw, 1rem);">
                                            <i class="bi bi-upload me-1"></i>Import CSV
                                        </h6>
                                    </div>
                                    <div class="card-body p-2 p-md-3">
                                        <form method="post" enctype="multipart/form-data" class="d-flex flex-column flex-sm-row gap-2">
                                            <div class="flex-grow-1">
                                                <label class="form-label mb-1" style="font-size: clamp(0.8rem, 1.2vw, 0.9rem);">
                                                    File CSV
                                                </label>
                                                <input type="file" name="file_csv" class="form-control" accept=".csv" required>
                                            </div>
                                            <div class="d-flex align-items-end">
                                                <button type="submit" name="import_csv" class="btn btn-success w-100 w-sm-auto">
                                                    <i class="bi bi-upload me-1"></i>Import
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Table Section -->
                        <div class="table-responsive">
                            <table id="tabelSegmen" class="table table-bordered table-hover align-middle" style="font-size: clamp(0.75rem, 1.2vw, 0.875rem);">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width:60px;">#</th>
                                        <th class="text-center">Nomor Segmen</th>
                                        <th class="text-center" style="width:80px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; while ($row = mysqli_fetch_assoc($segmen)): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>
                                        <td class="text-center fw-bold"><?= htmlspecialchars($row['nomor_segmen']); ?></td>
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
                        
                        <!-- Action Buttons -->
                        <div class="d-flex flex-column flex-sm-row gap-2 mt-3">
                            <button type="button" class="btn btn-outline-danger" id="hapusSemuaBtn">
                                <i class="bi bi-trash me-1"></i>Hapus Semua Data
                            </button>
                        </div>
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
                <h5 class="modal-title" style="font-size: clamp(1rem, 2vw, 1.25rem);">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="font-size: clamp(0.9rem, 1.5vw, 1rem);">
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
                <h5 class="modal-title" style="font-size: clamp(1rem, 2vw, 1.25rem);">Konfirmasi Hapus Semua</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="font-size: clamp(0.9rem, 1.5vw, 1rem);">
                Apakah Anda yakin ingin <strong>MENGHAPUS SEMUA</strong> data segmen? <br>
                <span class="text-danger">Tindakan ini tidak dapat dibatalkan!</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="?hapus_semua=1" class="btn btn-danger">Hapus Semua</a>
            </div>
        </div>
    </div>
</div>

<footer class="text-center mt-5 mb-3">
    &copy; <?= date('Y'); ?> UBINANKU
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // DataTable initialization
    $('#tabelSegmen').DataTable({
        responsive: true,
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
            zeroRecords: "Data tidak ditemukan",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(disaring dari _MAX_ total data)"
        },
        pageLength: 10,
        order: [[1, 'asc']],
        columnDefs: [
            { targets: 0, orderable: false },
            { targets: -1, orderable: false }
        ]
    });

    // Auto-close mobile sidebar when clicking nav links
    document.querySelectorAll('.mobile-nav-link').forEach(function(link) {
        link.addEventListener('click', function() {
            var offcanvasElement = document.getElementById('mobileSidebar');
            var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
            if (offcanvas) {
                offcanvas.hide();
            }
        });
    });

    // Modal hapus satuan
    document.querySelectorAll('.deleteButton').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var id = this.getAttribute('data-id');
            document.getElementById('deleteConfirmButton').href = '?hapus=' + id;
            var modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            modal.show();
        });
    });

    // Modal hapus semua
    document.getElementById('hapusSemuaBtn').addEventListener('click', function() {
        var modal = new bootstrap.Modal(document.getElementById('confirmDeleteAllModal'));
        modal.show();
    });
});
</script>
</body>
</html>
