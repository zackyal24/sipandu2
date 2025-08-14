<?php
session_start();
include '../../config/koneksi.php';

// Validasi akses hanya untuk supervisor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../../index.php");
    exit;
}

// Set header untuk download Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Data_Ubinan_' . date('Y-m-d_H-i-s') . '.xls"');
header('Cache-Control: max-age=0');

// Query data - gunakan kolom yang benar sesuai struktur tabel
$query = "SELECT * FROM monitoring_data_panen ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// Hitung statistik - sesuaikan dengan kolom yang ada
$stats_query = mysqli_query($conn, "
    SELECT 
        COUNT(*) as total_data,
        COUNT(CASE WHEN status = 'selesai' THEN 1 END) as selesai,
        COUNT(CASE WHEN status = 'belum selesai' THEN 1 END) as belum_selesai,
        COUNT(CASE WHEN status = 'tidak bisa' THEN 1 END) as tidak_bisa,
        COUNT(CASE WHEN status = 'sudah' THEN 1 END) as sudah,
        COUNT(CASE WHEN status = 'revisi' THEN 1 END) as revisi
    FROM monitoring_data_panen
");
$stats = mysqli_fetch_assoc($stats_query);

// Hitung statistik numerik untuk berat plot
$numeric_query = mysqli_query($conn, "
    SELECT 
        AVG(berat_plot) as rata_rata,
        MIN(berat_plot) as minimum,
        MAX(berat_plot) as maximum
    FROM monitoring_data_panen 
    WHERE berat_plot IS NOT NULL AND berat_plot != '' AND berat_plot > 0
");
$numeric_stats = mysqli_fetch_assoc($numeric_query);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Export Data Ubinan</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
        .center { text-align: center; }
        .number { text-align: right; }
        .header { font-size: 16px; font-weight: bold; margin-bottom: 10px; }
        .stats-table { margin-bottom: 20px; }
        .stats-table td { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        DATA UBINAN KABUPATEN BEKASI<br>
        Tanggal Export: <?= date('d/m/Y H:i:s'); ?>
    </div>

    <!-- Tabel Statistik -->
    <table class="stats-table">
        <tr>
            <th colspan="4">STATISTIK DATA UBINAN</th>
        </tr>
        <tr>
            <td><strong>Total Data</strong></td>
            <td class="center"><?= number_format($stats['total_data']); ?></td>
            <td><strong>Status Selesai</strong></td>
            <td class="center"><?= number_format($stats['selesai']); ?></td>
        </tr>
        <tr>
            <td><strong>Belum Selesai</strong></td>
            <td class="center"><?= number_format($stats['belum_selesai']); ?></td>
            <td><strong>Status Revisi</strong></td>
            <td class="center"><?= number_format($stats['revisi']); ?></td>
        </tr>
        <tr>
            <td><strong>Tidak Bisa</strong></td>
            <td class="center"><?= number_format($stats['tidak_bisa']); ?></td>
            <td><strong>Status Sudah</strong></td>
            <td class="center"><?= number_format($stats['sudah']); ?></td>
        </tr>
        <?php if ($numeric_stats && $numeric_stats['rata_rata']): ?>
        <tr>
            <td><strong>Rata-rata Berat Plot</strong></td>
            <td class="number"><?= number_format($numeric_stats['rata_rata'], 2); ?> kg</td>
            <td><strong>Min - Max</strong></td>
            <td class="number"><?= number_format($numeric_stats['minimum'], 2); ?> - <?= number_format($numeric_stats['maximum'], 2); ?> kg</td>
        </tr>
        <?php endif; ?>
    </table>

    <br>

    <!-- Tabel Data Detail -->
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
            if (mysqli_num_rows($result) > 0):
                while($row = mysqli_fetch_assoc($result)): 
            ?>
            <tr>
                <td class="center"><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['nama_petani'] ?? '-'); ?></td>
                <td><?= htmlspecialchars($row['desa'] ?? '-'); ?></td>
                <td><?= htmlspecialchars($row['kecamatan'] ?? '-'); ?></td>
                <td class="center"><?= htmlspecialchars($row['tanggal_panen'] ?? '-'); ?></td>
                <td class="center"><?= intval($row['subround'] ?? 0); ?></td>
                <td class="center"><?= intval($row['nomor_segmen'] ?? 0); ?></td>
                <td class="center"><?= htmlspecialchars($row['nomor_sub_segmen'] ?? '-'); ?></td>
                <td class="number"><?= $row['berat_plot'] ? number_format($row['berat_plot'], 2) : '-'; ?></td>
                <td class="number"><?= $row['gkp'] ? number_format($row['gkp'], 2) : '-'; ?></td>
                <td class="number"><?= $row['gkg'] ? number_format($row['gkg'], 2) : '-'; ?></td>
                <td class="number"><?= $row['ku'] ? number_format($row['ku'], 2) : '-'; ?></td>
                <td class="center">
                    <?php
                    $status = strtoupper($row['status'] ?? '-');
                    echo htmlspecialchars($status);
                    ?>
                </td>
                <td><?= htmlspecialchars($row['note'] ?? '-'); ?></td>
                <td class="center"><?= isset($row['created_at']) ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-'; ?></td>
            </tr>
            <?php 
                endwhile;
            else: 
            ?>
            <tr>
                <td colspan="15" class="center">Tidak ada data</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <div style="font-size: 12px; color: #666;">
        <strong>Keterangan:</strong><br>
        - Data ini diekspor dari sistem UBINANKU Kabupaten Bekasi<br>
        - Export dilakukan pada: <?= date('d/m/Y H:i:s'); ?><br>
        - Total data yang diekspor: <?= number_format($stats['total_data']); ?> records<br>
        - GKP = Gabah Kering Panen, GKG = Gabah Kering Giling, ku = kuintal
    </div>
</body>
</html>