<?php
include '../../server/config/koneksi.php'; // Koneksi database
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Monitoring Panen</title>

  <!-- Bootstrap + Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f8f9fa;
      padding: 20px;
    }

    h1 {
      text-align: center;
      margin-bottom: 30px;
      font-weight: 700;
      color: #2c3e50;
    }

    table img {
      width: 80px;
      height: auto;
      border-radius: 8px;
    }

    .btn-back {
      margin-bottom: 20px;
      border-radius: 8px;
    }

    footer {
      text-align: center;
      margin-top: 40px;
      font-size: 13px;
      color: #6c757d;
      padding: 15px 0;
      border-top: 1px solid #dee2e6;
    }
  </style>
</head>

<body>

<div class="container">
  <h1>Data Monitoring Panen</h1>

  <a href="form_monitoring.php" class="btn btn-primary btn-back">Kembali ke Form</a>

  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th>No</th>
          <th>Nama Petani</th>
          <th>Lokasi</th>
          <th>Tanggal Panen</th>
          <th>Foto Petani</th>
          <th>Foto Potong</th>
          <th>Foto Timbangan</th>
          <th>Berat Panen (kg)</th>
          <th>Tanggal Input</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $query = "SELECT * FROM monitoring_data_panen ORDER BY created_at DESC";
        $result = mysqli_query($conn, $query);
        $no = 1;
        while ($row = mysqli_fetch_assoc($result)) :
        ?>
        <tr>
          <td><?= $no++; ?></td>
          <td><?= htmlspecialchars($row['nama_petani']); ?></td>
          <td><?= htmlspecialchars($row['lokasi']); ?></td>
          <td><?= htmlspecialchars($row['tanggal_panen']); ?></td>
          <td><img src="../../uploads/<?= htmlspecialchars($row['foto_petani']); ?>" alt="Foto Petani"></td>
          <td><img src="../../uploads/<?= htmlspecialchars($row['foto_potong']); ?>" alt="Foto Potong"></td>
          <td><img src="../../uploads/<?= htmlspecialchars($row['foto_timbangan']); ?>" alt="Foto Timbangan"></td>
          <td><?= htmlspecialchars($row['berat_panen']); ?></td>
          <td><?= htmlspecialchars($row['created_at']); ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <footer>
    &copy; <?php echo date("Y"); ?> Monitoring Panen Umbinan | Kabupaten Bekasi
  </footer>
</div>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
