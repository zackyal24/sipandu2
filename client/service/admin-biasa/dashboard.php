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

// Query rata-rata ubinan (berat_plot)
$q_avg = mysqli_query($conn, "SELECT AVG(berat_plot) AS avg_berat_plot FROM monitoring_data_panen WHERE berat_plot IS NOT NULL AND berat_plot != ''");
$avg = mysqli_fetch_assoc($q_avg);
$avg_berat_plot = $avg['avg_berat_plot'] !== null ? number_format($avg['avg_berat_plot'], 2) : 'Belum terdata';

// Hitung jumlah per status
$q_status = mysqli_query($conn, "
    SELECT status, COUNT(*) as jumlah
    FROM monitoring_data_panen
    WHERE status IN ('selesai', 'belum selesai', 'tidak bisa')
    GROUP BY status
");
$status_count = [
    'selesai' => 0,
    'belum selesai' => 0,
    'tidak bisa' => 0
];
while ($row = mysqli_fetch_assoc($q_status)) {
    $status_count[strtolower($row['status'])] = $row['jumlah'];
}
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
                        <div class="fs-3 fw-bold"><?= $status_count['belum selesai'];; ?></div>
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
                                    <th class="text-center align-middle">Hasil Ubinan (kuintal)</th>
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
                                                if (!empty($row['ku']) && $row['ku'] != 0) {
                                                    echo '<span class="fw-bold">' . htmlspecialchars($row['ku']) . '</span>';
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
                                                <button type="button" class="btn btn-sm btn-outline-info revisiButton" 
                                                    data-id="<?= $row['id']; ?>" 
                                                    data-nama="<?= htmlspecialchars($row['nama_petani']); ?>">
                                                    <i class="bi bi-pencil-square"></i> Revisi
                                                </button>
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


<!-- Modal Revisi -->
<div class="modal fade" id="revisiModal" tabindex="-1" aria-labelledby="revisiModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formRevisi" method="post" action="revisi_panen.php">
      <div class="modal-content">
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
            <textarea name="note" class="form-control" rows="3" required placeholder="Tulis catatan revisi di sini..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-info">Kirim Revisi</button>
        </div>
      </div>
    </form>
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

$(document).on('click', '.revisiButton', function () {
    var id = $(this).data('id');
    var nama = $(this).data('nama');
    $('#revisiId').val(id);
    $('#revisiNama').val(nama);
    $('#revisiModal').modal('show');
});
</script>

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

    t.on('draw.dt', function () {
        t.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    });
    t.draw();

    // Klik baris ke detail, kecuali klik tombol revisi/hapus
    $('#tabelPanen').on('click', '.table-row-link', function(e) {
        if ($(e.target).closest('.revisiButton, .deleteButton').length) return;
        var href = $(this).data('href');
        if (href) window.location.href = href;
    });

    // Modal revisi
    $(document).on('click', '.revisiButton', function (e) {
        e.stopPropagation(); // <-- Tambahkan ini agar event baris tidak ikut jalan
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        $('#revisiId').val(id);
        $('#revisiNama').val(nama);
        $('#revisiModal').modal('show');
    });

    // Modal hapus (jika masih ada)
    $('.deleteButton').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).data('id');
        $('#deleteConfirmButton').attr('href', 'hapus_panen.php?id=' + id);
        $('#confirmDeleteModal').modal('show');
    });

    // Filter status
    $('#statusFilter').on('change', function() {
        t.draw();
    });
});
</script>

</body>
</html>
