<?php
require '../../../vendor/autoload.php'; // Pastikan path ke autoload benar
include '../../../server/config/koneksi.php';

use Mpdf\Mpdf;

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$q = mysqli_query($conn, "SELECT * FROM monitoring_data_panen WHERE id=$id");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    die('Data tidak ditemukan.');
}

$html = '
<h2 style="text-align:center;">Detail Data Panen</h2>
<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <tr><th>Nama Petani</th><td>' . htmlspecialchars($data['nama_petani']) . '</td></tr>
    <tr><th>Desa</th><td>' . htmlspecialchars($data['desa']) . '</td></tr>
    <tr><th>Kecamatan</th><td>' . htmlspecialchars($data['kecamatan']) . '</td></tr>
    <tr><th>Status</th><td>' . htmlspecialchars($data['status']) . '</td></tr>
    <tr><th>Tanggal Panen</th><td>' . htmlspecialchars($data['tanggal_panen']) . '</td></tr>
    <!-- Tambahkan field lain sesuai kebutuhan -->
</table>
';

$mpdf = new Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output('data_panen_'.$id.'.pdf', 'I'); // 'I' = inline (langsung tampil di browser)