<?php
// filepath: c:\xampp\htdocs\ubinan-monitoring\client\service\super-admin\edit_panen.php
session_start();
include '../../../server/config/koneksi.php';

// Cek login dan role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pml') {
    header("Location: ../index.php");
    exit;
}

// Ambil data panen berdasarkan ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$q = mysqli_query($conn, "SELECT * FROM monitoring_data_panen WHERE id=$id");
$row = mysqli_fetch_assoc($q);

if (!$row) {
    echo "<div class='alert alert-danger m-5'>Data tidak ditemukan.</div>";
    exit;
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_petani = mysqli_real_escape_string($conn, $_POST['nama_petani']);
    $desa = mysqli_real_escape_string($conn, $_POST['desa']);
    $kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan']);
    $tanggal_panen = mysqli_real_escape_string($conn, $_POST['tanggal_panen']);
    $berat_panen = floatval($_POST['berat_panen']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $update = mysqli_query($conn, "UPDATE monitoring_data_panen SET 
        nama_petani='$nama_petani',
        desa='$desa',
        kecamatan='$kecamatan',
        tanggal_panen='$tanggal_panen',
        berat_panen='$berat_panen',
        status='$status'
        WHERE id=$id
    ");

    if ($update) {
        header("Location: monitoring_panen.php?edit=success");
        exit;
    } else {
        $error = "Gagal mengupdate data!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Ubinan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fc; font-family: 'Poppins', sans-serif; }
        .card { border-radius: 12px; }
        .btn-custom { border-radius: 8px; transition: all 0.3s ease; }
        .btn-custom:hover { transform: scale(1.05); }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="mb-4"><i class="bi bi-pencil-square me-2"></i>Edit Data Panen</h4>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <form method="post" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">Nama Petani</label>
                            <input type="text" name="nama_petani" class="form-control" value="<?= htmlspecialchars($row['nama_petani']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Desa</label>
                            <input type="text" name="desa" class="form-control" value="<?= htmlspecialchars($row['desa']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kecamatan</label>
                            <input type="text" name="kecamatan" class="form-control" value="<?= htmlspecialchars($row['kecamatan']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Panen</label>
                            <input type="date" name="tanggal_panen" class="form-control" value="<?= htmlspecialchars($row['tanggal_panen']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Berat Panen (kg)</label>
                            <input type="number" step="0.01" name="berat_panen" class="form-control" value="<?= htmlspecialchars($row['berat_panen']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="selesai" <?= $row['status']=='selesai'?'selected':''; ?>>Selesai</option>
                                <option value="belum selesai" <?= $row['status']=='belum selesai'?'selected':''; ?>>Belum Selesai</option>
                                <option value="tidak bisa" <?= $row['status']=='tidak bisa'?'selected':''; ?>>Tidak Bisa</option>
                                <option value="sudah" <?= $row['status']=='sudah'?'selected':''; ?>>Sudah</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="dashboard.php" class="btn btn-secondary btn-custom">
                                <i class="bi bi-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary btn-custom">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>