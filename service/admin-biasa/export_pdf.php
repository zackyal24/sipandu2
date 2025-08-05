<?php
// filepath: c:\xampp\htdocs\ubinanku-kab-bekasi\service\admin-biasa\export_pdf_simple.php

session_start();
if (!isset($_SESSION['pml'])) {
    header("Location: login.php");
    exit;
}

include '../../config/koneksi.php';

// Ambil ID dari parameter URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query untuk mengambil data berdasarkan ID
$query = mysqli_query($conn, "SELECT * FROM monitoring_data_panen WHERE id = $id");
$data = mysqli_fetch_assoc($query);

// Jika data tidak ditemukan, redirect ke halaman dashboard
if (!$data) {
    die("Data tidak ditemukan");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export PDF - Detail Ubinan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            margin: 0;
            padding: 15px;
            background: white;
            color: black;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 16px;
            margin: 0;
            color: #333;
        }
        
        .header p {
            font-size: 12px;
            margin: 5px 0;
            color: #666;
        }
        
        .section {
            margin-bottom: 15px;
            border: 1px solid #ccc;
            padding: 10px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            background: #f0f0f0;
            padding: 5px;
            border-left: 3px solid #007bff;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .info-table td {
            padding: 4px 6px;
            border-bottom: 1px solid #eee;
            font-size: 10px;
        }
        
        .info-table .label {
            font-weight: bold;
            width: 30%;
            color: #666;
        }
        
        .info-table .value {
            width: 70%;
            color: black;
        }
        
        .metrics {
            display: table;
            width: 100%;
            border-spacing: 5px;
        }
        
        .metric-row {
            display: table-row;
        }
        
        .metric-cell {
            display: table-cell;
            width: 25%;
            text-align: center;
            border: 1px solid #ccc;
            padding: 8px 4px;
            background: #f8f8f8;
        }
        
        .metric-value {
            font-size: 11px;
            font-weight: bold;
            color: #007bff;
        }
        
        .metric-label {
            font-size: 8px;
            color: #666;
            margin-top: 2px;
        }
        
        .photos {
            margin-top: 10px;
        }
        
        .photo-item {
            margin-bottom: 15px;
            text-align: center;
            page-break-inside: avoid;
            border: 1px solid #ddd;
            padding: 8px;
        }
        
        .photo-item img {
            max-width: 200px;
            max-height: 150px;
            margin-bottom: 5px;
        }
        
        .photo-label {
            font-size: 9px;
            font-weight: bold;
            color: #333;
        }
        
        .status-badge {
            font-size: 8px;
            padding: 2px 6px;
            border: 1px solid #999;
            background: #f0f0f0;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <!-- Header PDF -->
    <div class="header">
        <h1>DETAIL DATA UBINAN</h1>
        <p>ID: #<?= $data['id']; ?> | Tanggal Export: <?= date('d M Y H:i'); ?></p>
    </div>

    <!-- Informasi Petani -->
    <div class="section">
        <div class="section-title">INFORMASI PETANI & LOKASI</div>
        <table class="info-table">
            <tr>
                <td class="label">Nama Petani</td>
                <td class="value"><?= htmlspecialchars($data['nama_petani']); ?></td>
            </tr>
            <tr>
                <td class="label">Desa</td>
                <td class="value"><?= htmlspecialchars($data['desa']); ?></td>
            </tr>
            <tr>
                <td class="label">Kecamatan</td>
                <td class="value"><?= htmlspecialchars($data['kecamatan']); ?></td>
            </tr>
            <tr>
                <td class="label">Tanggal Panen</td>
                <td class="value"><?= date('d M Y', strtotime($data['tanggal_panen'])); ?></td>
            </tr>
            <tr>
                <td class="label">Subround</td>
                <td class="value"><?= htmlspecialchars($data['subround']); ?></td>
            </tr>
            <tr>
                <td class="label">No. Sub Segmen</td>
                <td class="value"><?= htmlspecialchars($data['nomor_sub_segmen']); ?></td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td class="value">
                    <span class="status-badge"><?= strtoupper($data['status']); ?></span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Data Hasil -->
    <div class="section">
        <div class="section-title">DATA HASIL UBINAN</div>
        <div class="metrics">
            <div class="metric-row">
                <div class="metric-cell">
                    <div class="metric-value">
                        <?= !empty($data['berat_plot']) ? number_format($data['berat_plot'], 2) : '-'; ?>
                    </div>
                    <div class="metric-label">Berat Plot (kg)</div>
                </div>
                <div class="metric-cell">
                    <div class="metric-value">
                        <?= !empty($data['gkp']) ? number_format($data['gkp'], 2) : '-'; ?>
                    </div>
                    <div class="metric-label">GKP (ku/ha)</div>
                </div>
                <div class="metric-cell">
                    <div class="metric-value">
                        <?= !empty($data['gkg']) ? number_format($data['gkg'], 2) : '-'; ?>
                    </div>
                    <div class="metric-label">GKG (ku/ha)</div>
                </div>
                <div class="metric-cell">
                    <div class="metric-value">
                        <?= !empty($data['ku']) ? number_format($data['ku'], 2) : '-'; ?>
                    </div>
                    <div class="metric-label">Produksi Beras (kuintal)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dokumentasi Foto -->
    <div class="section">
        <div class="section-title">DOKUMENTASI FOTO</div>
        <div class="photos">
            <?php if (!empty($data['foto_serah_terima'])): ?>
            <div class="photo-item">
                <div class="photo-label">Foto Serah Terima Uang Pengganti Responden</div>
                <img src="../../<?= htmlspecialchars($data['foto_serah_terima']); ?>" alt="Foto Serah Terima">
            </div>
            <?php endif; ?>
            
            <?php if (!empty($data['foto_bukti_plot_ubinan'])): ?>
            <div class="photo-item">
                <div class="photo-label">Foto Bukti Plot Ubinan</div>
                <img src="../../<?= htmlspecialchars($data['foto_bukti_plot_ubinan']); ?>" alt="Foto Bukti Plot">
            </div>
            <?php endif; ?>
            
            <?php if (!empty($data['foto_berat_timbangan'])): ?>
            <div class="photo-item">
                <div class="photo-label">Foto Berat Timbangan Gabah</div>
                <img src="../../<?= htmlspecialchars($data['foto_berat_timbangan']); ?>" alt="Foto Berat Timbangan">
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        // Auto-generate PDF saat halaman di-load
        window.onload = function() {
            const element = document.body;
            const nama = "<?= preg_replace('/[^a-zA-Z0-9_\-]/', '', $data['nama_petani']); ?>";
            const id = "<?= $data['id']; ?>";
            const filename = 'Detail_Ubinan_' + nama + '_' + id + '.pdf';
            
            const opt = {
                margin: [0.5, 0.5, 0.5, 0.5],
                filename: filename,
                image: { 
                    type: 'jpeg', 
                    quality: 0.8
                },
                html2canvas: { 
                    scale: 1,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff'
                },
                jsPDF: { 
                    unit: 'cm', 
                    format: 'a4', 
                    orientation: 'portrait'
                }
            };
            
            html2pdf().from(element).set(opt).save();
        };
    </script>
</body>
</html>