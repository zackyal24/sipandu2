<?php
// filepath: c:\xampp\htdocs\ubinan-monitoring\client\service\super-admin\export_excel.php
include '../../config/koneksi.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=monitoring_panen.xls");

echo "<table border='1'>";
echo "<tr>
<th>No</th>
<th>Nama Petani</th>
<th>Desa</th>
<th>Kecamatan</th>
<th>Tanggal Panen</th>
<th>Nomor Segmen</th>
<th>Nomor Sub Segmen</th>
<th>Berat Plot (kg)</th>
<th>GKP</th>
<th>GKG</th>
<th>Hasil Ubinan (kuintal)</th>
<th>Status</th>
</tr>";

$q = mysqli_query($conn, "SELECT * FROM monitoring_data_panen ORDER BY created_at DESC");
$no = 1;
while($row = mysqli_fetch_assoc($q)) {
    echo "<tr>";
    echo "<td>{$no}</td>";
    echo "<td>".htmlspecialchars($row['nama_petani'])."</td>";
    echo "<td>".htmlspecialchars($row['desa'])."</td>";
    echo "<td>".htmlspecialchars($row['kecamatan'])."</td>";
    echo "<td>".htmlspecialchars($row['tanggal_panen'])."</td>";
    echo "<td>".htmlspecialchars($row['nomor_segmen'])."</td>";
    echo "<td>".htmlspecialchars($row['nomor_sub_segmen'])."</td>";
    echo "<td>".htmlspecialchars($row['berat_plot'])."</td>";
    echo "<td>".htmlspecialchars($row['gkp'])."</td>";
    echo "<td>".htmlspecialchars($row['gkg'])."</td>";
    echo "<td>".htmlspecialchars($row['ku'])."</td>";
    echo "<td>".htmlspecialchars($row['status'])."</td>";
    echo "</tr>";
    $no++;
}
echo "</table>";
?>