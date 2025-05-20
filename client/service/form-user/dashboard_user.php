<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo "Sesi tidak valid. user_id: " . ($_SESSION['user_id'] ?? 'tidak ada') . ", role: " . ($_SESSION['role'] ?? 'tidak ada');
    exit;
}

// Ambil data ubinan berdasarkan user yang login
include '../../../server/config/koneksi.php';
$user_id = $_SESSION['user_id'];
$query = "SELECT id, tanggal_panen, status FROM monitoring_data_panen WHERE user_id = ? ORDER BY tanggal_panen DESC";
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("Kesalahan pada query SQL: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard User | Monitoring Panen</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
    }
    .navbar-brand {
      font-weight: bold;
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
    <a class="navbar-brand" href="#">Dashboard User</a>
    <div class="d-flex align-items-center">
      <span class="text-white me-3">👋 Halo, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong></span>
      <a href="../../auth/logout.php" class="btn btn-outline-light btn-sm btn-custom">Logout</a>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="container my-5">
  <div class="card shadow-sm">
    <div class="card-body">
    <h3 class="fw-bold mb-4 d-flex justify-content-between align-items-center">
        Data Ubinan Anda
        <a href="tambah_data.php" class="btn btn-primary btn-sm btn-custom">Tambah Data</a>
    </h3>
      <div class="table-responsive">
      <table id="tabelUbinan" class="table table-hover table-striped align-middle">
      <thead class="table-primary text-center">
    <tr>
        <th class="text-center">No</th>
        <th class="text-center">Tanggal Panen</th>
        <th class="text-center"></th>
    </tr>
</thead>
<tbody>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php $no = 1; ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td class="text-center"><?= htmlspecialchars($row['tanggal_panen']); ?></td>
                <td class="text-center">
                  <?php if ($row['status'] === 'tidak bisa'): ?>
                      <span class="text-danger fst-italic">Tidak Bisa Ubinan</span>
                  <?php elseif ($row['status'] === 'selesai'): ?>
                      <span class="text-success fst-italic">Selesai</span>
                  <?php elseif ($row['status'] === 'belum selesai' || $row['status'] === 'sudah'): ?>
                      <a href="form_monitoring.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm btn-custom">Isi Form</a>
                  <?php endif; ?>
              </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td class="text-center">-</td>
            <td class="text-center">-</td>
            <td class="text-center">-</td>
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
  &copy; <?= date('Y'); ?> Monitoring Panen Ubinan
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
  $('#tabelUbinan').DataTable({
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
    },
    responsive: true,
    pageLength: 10,
    order: [[1, 'desc']], // Urutkan berdasarkan kolom Tanggal Panen secara descending
    columnDefs: [
      { orderable: false, targets: 0 } // Kolom nomor tidak dapat diurutkan
    ],
    rowCallback: function (row, data, displayIndex, displayIndexFull) {
  var table = $('#tabelUbinan').DataTable();
  var pageInfo = table.page.info();
  var nomor = pageInfo.start + displayIndex + 1;
  $('td:eq(0)', row).html(nomor);
}
  });
});
</script>

</body>
</html>