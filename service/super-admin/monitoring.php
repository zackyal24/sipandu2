<?php
// filepath: c:\xampp\htdocs\ubinan-monitoring\client\service\super-admin\monitoring.php
session_start();
include '../../config/koneksi.php';

// Cek login & role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../index.php");
    exit;
}

// Query data lengkap user yang belum isi form ubinan
$q = mysqli_query($conn, "
    SELECT u.nama_lengkap, u.no_hp, m.desa, m.kecamatan, m.nomor_sub_segmen, m.tanggal_panen
    FROM users u
    JOIN monitoring_data_panen m ON u.id = m.user_id
    WHERE (m.status = 'belum selesai' OR m.status = 'sudah')
    ORDER BY m.tanggal_panen DESC
");

// Hitung jumlah per status
// Hitung jumlah per kategori deadline
$q_user_all = mysqli_query($conn, "
    SELECT m.tanggal_panen
    FROM users u
    JOIN monitoring_data_panen m ON u.id = m.user_id
    WHERE (m.status = 'belum selesai' OR m.status = 'sudah')
");
$terlewat = $segera = $aman = 0;
$today = new DateTime();
while ($row = mysqli_fetch_assoc($q_user_all)) {
    $tgl_ubinan = new DateTime($row['tanggal_panen']);
    $diff = $today->diff($tgl_ubinan)->days;
    $isPast = $tgl_ubinan < $today;
    $isSoon = !$isPast && $diff <= 7;
    if ($isPast) {
        $terlewat++;
    } elseif ($isSoon) {
        $segera++;
    } else {
        $aman++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Ubinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
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
        .sidebar {
            width: 240px;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .card:hover {
            transform: translateY(-2px);
            transition: transform 0.3s ease;
        }
        
        /* Warna status deadline */
        .tr-deadline-lewat td {
            background-color: rgb(255, 146, 142) !important;
            color: black;
        }
        .tr-deadline-segera td {
            background-color: rgb(247, 237, 108) !important;
            color: black;
        }
        .tr-deadline-aman td {
            background-color: rgb(179, 252, 157) !important;
            color: black;
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
                font-weight: 600;
            }
            
            .card {
                margin-bottom: 0.75rem;
            }
            
            .card-body {
                padding: clamp(0.75rem, 2vw, 1rem) !important;
            }
            
            .fs-3 {
                font-size: clamp(1.2rem, 3vw, 1.75rem) !important;
            }
            
            .fs-5 {
                font-size: clamp(0.9rem, 2vw, 1.25rem) !important;
            }
            
            .table-responsive {
                font-size: clamp(0.75rem, 1.5vw, 0.875rem);
            }
            
            .btn-sm {
                font-size: clamp(0.7rem, 1.2vw, 0.875rem);
                padding: 0.2rem 0.4rem;
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
            SIPANTAU
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
                        <a href="monitoring.php" class="nav-link active">
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
                        <a href="super_admin.php" class="nav-link text-primary mobile-nav-link">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="monitoring.php" class="nav-link active mobile-nav-link">
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
                        <a class="nav-link text-primary d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#manajemenMenuMobile" role="button" aria-expanded="false" aria-controls="manajemenMenuMobile">
                            <span><i class="bi bi-gear me-2"></i> Manajemen</span>
                            <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="collapse ps-4" id="manajemenMenuMobile">
                            <ul class="nav flex-column">
                                <li class="nav-item mb-1">
                                    <a href="monitoring_akun.php" class="nav-link text-primary mobile-nav-link">
                                        <i class="bi bi-person-gear me-2"></i> User
                                    </a>
                                </li>
                                <li class="nav-item mb-1">
                                    <a href="manage_segmen.php" class="nav-link text-primary mobile-nav-link">
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
                <h2 class="mb-3 mb-md-4">Monitoring Ubinan</h2>
                
                <!-- Cards Statistics -->
                <div class="row g-2 g-md-3 g-lg-4 mb-3 mb-md-4">
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="card shadow-sm border-0 text-center h-100">
                            <div class="card-body p-2 p-md-3">
                                <div class="text-muted mb-1" style="font-size: clamp(0.8rem, 1.5vw, 1rem);">Terlewat</div>
                                <div class="fw-bold text-danger" style="font-size: clamp(1.2rem, 3vw, 1.75rem);"><?= $terlewat; ?></div>
                                <div class="small text-muted" style="font-size: clamp(0.7rem, 1.2vw, 0.875rem);">melewati tanggal deadline</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="card shadow-sm border-0 text-center h-100">
                            <div class="card-body p-2 p-md-3">
                                <div class="text-muted mb-1" style="font-size: clamp(0.8rem, 1.5vw, 1rem);">Segera</div>
                                <div class="fw-bold text-warning" style="font-size: clamp(1.2rem, 3vw, 1.75rem);"><?= $segera; ?></div>
                                <div class="small text-muted" style="font-size: clamp(0.7rem, 1.2vw, 0.875rem);">kurang dari 7 hari</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="card shadow-sm border-0 text-center h-100">
                            <div class="card-body p-2 p-md-3">
                                <div class="text-muted mb-1" style="font-size: clamp(0.8rem, 1.5vw, 1rem);">Aman</div>
                                <div class="fw-bold text-success" style="font-size: clamp(1.2rem, 3vw, 1.75rem);"><?= $aman; ?></div>
                                <div class="small text-muted" style="font-size: clamp(0.7rem, 1.2vw, 0.875rem);">lebih dari 7 hari</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Table Section -->
                <div class="card shadow-sm">
                    <div class="card-body p-2 p-md-3">
                        <h5 class="card-title mb-3" style="font-size: clamp(1rem, 2vw, 1.25rem);">Daftar Monitoring</h5>
                        
                        <!-- Filter -->
                        <div class="row mb-3">
                            <div class="col-12 col-md-4 mb-2 mb-md-0">
                                <select id="statusFilter" class="form-select form-select-sm">
                                    <option value="">Semua Status</option>
                                    <option value="Terlewat">Terlewat</option>
                                    <option value="Segera">Segera</option>
                                    <option value="Aman">Aman</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle" id="monitoringTable" style="font-size: clamp(0.75rem, 1.5vw, 0.875rem);">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th style="width:40px;">#</th>
                                        <th>Nama</th>
                                        <th class="d-none d-md-table-cell">Lokasi</th>
                                        <th>Sub Segmen</th>
                                        <th class="d-none d-lg-table-cell">Tanggal Ubinan</th>
                                        <th>Status</th>
                                        <th style="width:80px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $today = new DateTime();
                                    // Reset pointer query
                                    $q = mysqli_query($conn, "
                                        SELECT u.nama_lengkap, u.no_hp, m.desa, m.kecamatan, m.nomor_sub_segmen, m.tanggal_panen
                                        FROM users u
                                        JOIN monitoring_data_panen m ON u.id = m.user_id
                                        WHERE (m.status = 'belum selesai' OR m.status = 'sudah')
                                        ORDER BY m.tanggal_panen DESC
                                    ");
                                    while ($row = mysqli_fetch_assoc($q)):
                                        $tgl_ubinan = new DateTime($row['tanggal_panen']);
                                        $diff = $today->diff($tgl_ubinan)->days;
                                        $isPast = $tgl_ubinan < $today;
                                        $isSoon = !$isPast && $diff <= 7;
                                        
                                        if ($isPast) {
                                            $rowClass = 'tr-deadline-lewat';
                                            $statusText = 'Terlewat';
                                            $statusBadge = 'bg-danger';
                                        } elseif ($isSoon) {
                                            $rowClass = 'tr-deadline-segera';
                                            $statusText = 'Segera';
                                            $statusBadge = 'bg-warning';
                                        } else {
                                            $rowClass = 'tr-deadline-aman';
                                            $statusText = 'Aman';
                                            $statusBadge = 'bg-success';
                                        }
                                    ?>
                                    <tr class="<?= $rowClass ?>" data-status="<?= $statusText ?>">
                                        <td class="text-center"><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                                        <td class="d-none d-md-table-cell"><?= htmlspecialchars($row['desa'] . ', ' . $row['kecamatan']); ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row['nomor_sub_segmen']); ?></td>
                                        <td class="text-center d-none d-lg-table-cell"><?= htmlspecialchars($row['tanggal_panen']); ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= $statusBadge ?>" style="font-size: clamp(0.6rem, 1vw, 0.75rem);"><?= $statusText ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            if (!empty($row['no_hp'])) {
                                                // Pastikan format nomor Indonesia (62...), jika 08... ubah ke 628...
                                                $wa_number = preg_replace('/[^0-9]/', '', $row['no_hp']);
                                                if (strpos($wa_number, '0') === 0) {
                                                    $wa_number = '62' . substr($wa_number, 1);
                                                }
                                                $wa_message = rawurlencode("Halo " . $row['nama_lengkap'] . ", mohon segera lengkapi form ubinan Anda.");
                                                $wa_link = "https://wa.me/$wa_number?text=$wa_message";
                                            ?>
                                                <a href="<?= $wa_link ?>" target="_blank" class="btn btn-success btn-sm">
                                                    <i class="bi bi-whatsapp"></i> <span class="d-none d-sm-inline"></span>
                                                </a>
                                            <?php } else { ?>
                                                <span class="text-muted">-</span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <a href="super_admin.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<footer class="text-center mt-5 mb-3">
    &copy; <?= date('Y'); ?> SIPANTAU
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // DataTable initialization
    var table = $('#monitoringTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        },
        pageLength: 10,
        order: [[4, 'desc']], // Sort by tanggal ubinan (hidden column included)
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

    // Status filter
    $('#statusFilter').on('change', function() {
        var selectedStatus = this.value;
        if (selectedStatus) {
            table.column(5).search('^' + selectedStatus + '$', true, false).draw();
        } else {
            table.column(5).search('').draw();
        }
    });

    // Update row numbers on draw
    table.on('draw.dt', function () {
        var pageInfo = table.page.info();
        table.column(0, { search: 'applied', order: 'applied', page: 'current' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1 + pageInfo.start;
        });
    });
});
</script>

</body>
</html>