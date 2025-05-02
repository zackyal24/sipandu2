<?php
include '../config/koneksi.php';

// Header untuk unduh file Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=data_monitoring_panen.xls");

$data = mysqli_query($conn, "SELECT * FROM monitoring_panen ORDER BY created_at DESC");
?>

<table border="1" cellpadding="5" cellspacing="0">
  <thead>
    <tr>
      <th>No</th>
      <th>Nama Petani</th>
      <th>Lokasi</th>
      <th>Tanggal Panen</th>
      <th>Berat (kg)</th>
      <th>Foto Petani</th>
      <th>Foto Potong</th>
      <th>Foto Timbangan</th>
      <th>Waktu Submit</th>
    </tr>
  </thead>
  <tbody>
    <?php $no = 1; while($row = mysqli_fetch_assoc($data)): ?>
    <tr>
      <td><?= $no++; ?></td>
      <td><?= $row['nama_petani']; ?></td>
      <td><?= $row['lokasi']; ?></td>
      <td><?= $row['tanggal_panen']; ?></td>
      <td><?= $row['berat_panen']; ?></td>
      <td><?= $row['foto_petani']; ?></td>
      <td><?= $row['foto_potong']; ?></td>
      <td><?= $row['foto_timbangan']; ?></td>
      <td><?= $row['created_at']; ?></td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
