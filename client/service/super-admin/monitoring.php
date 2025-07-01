<?php
// filepath: c:\xampp\htdocs\ubinan-monitoring\client\service\super-admin\monitoring.php
session_start();
include '../../../server/config/koneksi.php';

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
    <title>Belum Isi Form Ubinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Poppins', sans-serif;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 20px;
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
        .navbar-brand {
            font-weight: bold;
        }
        /* Warna status deadline */
        .tr-deadline-lewat td {
            background-color: rgb(255, 146, 142) !important;
            color: blank;
        }
        .tr-deadline-segera td {
            background-color: rgb(247, 237, 108) !important;
            color: black;
        }
        .tr-deadline-aman td {
            background-color: rgb(179, 252, 157) !important;
            color: black;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top" style="z-index:1040;">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="monitoring_panen.php">
            <img src="../../assets/logo.png" alt="Logo BPS" height="40" class="me-2">
            Monitoring
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
                        <a href="monitoring.php" class="nav-link active">
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
            <h2 class="mb-4">Monitoring Ubinan</h2>
            <div class="row g-4 mb-4 d-flex justify-content-center flex-wrap">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 text-center h-100">
                        <div class="card-body">
                            <div class="fs-5 text-muted mb-1 fw-bold">Terlewat</div>
                            <div class="fs-3 fw-bold text-danger"><?= $terlewat; ?></div>
                            <div class="small text-muted">melewati tanggal deadline</div>
                        </div>
                    </div>
                    </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 text-center h-100">
                        <div class="card-body">
                            <div class="fs-5 text-muted mb-1 fw-bold">Segera</div>
                            <div class="fs-3 fw-bold text-warning"><?= $segera; ?></div>
                            <div class="small text-muted">melewati tanggal deadline</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card shadow-sm border-0 text-center h-100">
                        <div class="card-body">
                            <div class="fs-5 text-muted mb-1 fw-bold">Aman</div>
                            <div class="fs-3 fw-bold text-success"><?= $aman; ?></div>
                            <div class="small text-muted">lebih dari 7 hari deadline</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Lokasi</th>
                                    <th>Sub Segmen</th>
                                    <th>Tanggal Ubinan</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $today = new DateTime();
                                while ($row = mysqli_fetch_assoc($q)):
                                    $tgl_ubinan = new DateTime($row['tanggal_panen']);
                                    $diff = $today->diff($tgl_ubinan)->days;
                                    $isPast = $tgl_ubinan < $today;
                                    $isSoon = !$isPast && $diff <= 7;
                                    if ($isPast) {
                                        $rowClass = 'tr-deadline-lewat';
                                    } elseif ($isSoon) {
                                        $rowClass = 'tr-deadline-segera';
                                    } else {
                                        $rowClass = 'tr-deadline-aman';
                                    }
                                    // Nomor WhatsApp dummy, ganti dengan $row['no_wa'] jika ada di database
                                    $wa_number = '6281234567890';
                                    $wa_message = rawurlencode("Halo " . $row['nama_lengkap'] . ", mohon segera lengkapi form ubinan Anda.");
                                    $wa_link = "https://wa.me/$wa_number?text=$wa_message";
                                ?>
                                <tr class="<?= $rowClass ?>">
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                                    <td><?= htmlspecialchars($row['desa'] . ', ' . $row['kecamatan']); ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['nomor_sub_segmen']); ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['tanggal_panen']); ?></td>
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
                                                <i class="bi bi-whatsapp"></i> Hubungi
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
                    <a href="super_admin.php" class="btn btn-secondary mt-3">&larr; Kembali ke Dashboard</a>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>