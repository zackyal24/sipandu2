<?php
include '../../../server/config/koneksi.php';

$id_kecamatan = isset($_GET['id_kecamatan']) ? intval($_GET['id_kecamatan']) : 0;
$result = mysqli_query($conn, "SELECT id, nama_desa FROM desa WHERE id_kecamatan = $id_kecamatan ORDER BY nama_desa ASC");
$desa = [];
while($row = mysqli_fetch_assoc($result)) {
    $desa[] = $row;
}
header('Content-Type: application/json');
echo json_encode($desa);
?>