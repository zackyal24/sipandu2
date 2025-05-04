<?php
session_start();
include '../../../server/config/koneksi.php';

// Cek login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Set header untuk file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=data_monitoring_panen.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Query untuk mengambil data dari tabel
$query = "SELECT tanggal_panen, nama_petani, lokasi, berat_panen FROM monitoring_data_panen ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// Tampilkan data dalam format tabel HTML
echo "<table border='1'>";
echo "<tr>
        <th>Tanggal Panen</th>
        <th>Nama Petani</th>
        <th>Lokasi</th>
        <th>Berat Panen (kg)</th>
      </tr>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>" . htmlspecialchars($row['tanggal_panen']) . "</td>
            <td>" . htmlspecialchars($row['nama_petani']) . "</td>
            <td>" . htmlspecialchars($row['lokasi']) . "</td>
            <td>" . htmlspecialchars($row['berat_panen']) . "</td>
          </tr>";
}

echo "</table>";
?>