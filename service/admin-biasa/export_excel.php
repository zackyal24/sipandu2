<?php
session_start();
include '../../config/koneksi.php';

// Cek login dan role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pml') {
    header("Location: ../index.php");
    exit;
}

// Ambil ID PML yang sedang login
$pml_username = $_SESSION['username'];
$pml_query = mysqli_query($conn, "SELECT id FROM users WHERE username = '$pml_username' AND role = 'pml'");
$pml_data = mysqli_fetch_assoc($pml_query);

if (!$pml_data) {
    echo "Error: User PML tidak ditemukan";
    exit;
}

$pml_id = $pml_data['id'];

// Query export dengan filter PML - hanya data PCL yang diawasi
$query = "
    SELECT mdp.* 
    FROM monitoring_data_panen mdp
    INNER JOIN users pcl ON mdp.user_id = pcl.id
    WHERE pcl.pml_id = '$pml_id' AND pcl.role = 'pcl'
    ORDER BY mdp.created_at DESC
";
$data = mysqli_query($conn, $query);

// Nama file dengan timestamp dan nama PML
$filename = "monitoring_panen_" . $pml_username . "_" . date('Y-m-d') . ".xls";

// Set header untuk download Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: max-age=0");

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid black; padding: 5px; }
        th { background-color: #4CAF50; color: white; font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Petani</th>
                <th>Desa</th>
                <th>Kecamatan</th>
                <th>Tanggal Panen</th>
                <th>Subround</th>
                <th>Nomor Segmen</th>
                <th>Sub Segmen</th>
                <th>Berat Plot (kg)</th>
                <th>GKP (ku/ha)</th>
                <th>GKG (ku/ha)</th>
                <th>Produksi Beras (ku)</th>
                <th>Status</th>
                <th>Note</th>
                <th>Tanggal Input</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if (mysqli_num_rows($data) > 0):
                while($row = mysqli_fetch_assoc($data)): 
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['nama_petani']); ?></td>
                <td><?= htmlspecialchars($row['desa']); ?></td>
                <td><?= htmlspecialchars($row['kecamatan']); ?></td>
                <td><?= date('d/m/Y', strtotime($row['tanggal_panen'])); ?></td>
                <td><?= $row['subround']; ?></td>
                <td><?= htmlspecialchars($row['nomor_segmen']); ?></td>
                <td><?= htmlspecialchars($row['nomor_sub_segmen']); ?></td>
                <td><?= $row['berat_plot'] ? number_format($row['berat_plot'], 2) : '-'; ?></td>
                <td><?= $row['gkp'] ? number_format($row['gkp'], 2) : '-'; ?></td>
                <td><?= $row['gkg'] ? number_format($row['gkg'], 2) : '-'; ?></td>
                <td><?= $row['ku'] ? number_format($row['ku'], 2) : '-'; ?></td>
                <td><?= ucfirst($row['status']); ?></td>
                <td><?= htmlspecialchars($row['note']); ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
            </tr>
            <?php 
                endwhile;
            else: 
            ?>
            <tr>
                <td colspan="15">Tidak ada data</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>