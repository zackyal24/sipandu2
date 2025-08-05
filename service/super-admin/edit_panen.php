<?php
// filepath: c:\xampp\htdocs\ubinan-monitoring\client\service\super-admin\edit_panen.php
session_start();
include '../../config/koneksi.php';

// Cek login dan role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'supervisor') {
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
    $berat_plot = floatval($_POST['berat_plot']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $update = mysqli_query($conn, "UPDATE monitoring_data_panen SET 
        nama_petani='$nama_petani',
        desa='$desa',
        kecamatan='$kecamatan',
        tanggal_panen='$tanggal_panen',
        berat_plot='$berat_plot',
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Data Ubinan</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body { 
            background-color: #f8f9fc; 
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            padding: clamp(1rem, 3vw, 2rem);
        }
        
        .card { 
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
            background: white;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .card-body {
            padding: clamp(1.5rem, 4vw, 2rem);
        }
        
        h4 {
            font-weight: 600;
            color: #2c3e50;
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: clamp(0.875rem, 1.8vw, 1rem);
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e3e6f0;
            padding: clamp(0.6rem, 1.5vw, 0.75rem) clamp(0.8rem, 2vw, 1rem);
            font-size: clamp(0.875rem, 1.8vw, 1rem);
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        .btn-custom { 
            border-radius: 8px; 
            transition: all 0.3s ease;
            font-weight: 500;
            padding: clamp(0.6rem, 1.5vw, 0.75rem) clamp(1rem, 2.5vw, 1.5rem);
            font-size: clamp(0.875rem, 1.8vw, 1rem);
            min-width: clamp(100px, 20vw, 120px);
        }
        
        .btn-custom:hover { 
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5c636a;
            border-color: #565e64;
        }
        
        .alert {
            border-radius: 8px;
            font-size: clamp(0.875rem, 1.8vw, 1rem);
            padding: clamp(0.6rem, 1.5vw, 0.75rem) clamp(0.8rem, 2vw, 1rem);
            margin-bottom: 1.5rem;
        }
        
        .mb-3 {
            margin-bottom: 1.5rem !important;
        }
        
        .d-flex {
            gap: clamp(0.5rem, 2vw, 1rem);
        }
        
        /* Mobile optimizations */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .card {
                margin: 0;
            }
            
            .card-body {
                padding: 1.5rem;
            }
            
            h4 {
                font-size: 1.25rem;
                margin-bottom: 1rem;
            }
            
            .form-control, .form-select {
                font-size: 0.9rem;
                padding: 0.6rem 0.8rem;
            }
            
            .btn-custom {
                font-size: 0.9rem;
                padding: 0.6rem 1rem;
                min-width: 100px;
            }
            
            .alert {
                font-size: 0.9rem;
                padding: 0.6rem 0.8rem;
            }
            
            .mb-3 {
                margin-bottom: 1rem !important;
            }
        }
        
        /* Extra small devices */
        @media (max-width: 576px) {
            body {
                padding: 0.5rem;
            }
            
            .card {
                border-radius: 10px;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            h4 {
                font-size: 1.1rem;
                margin-bottom: 0.8rem;
            }
            
            .form-control, .form-select {
                font-size: 0.875rem;
                padding: 0.5rem 0.7rem;
            }
            
            .btn-custom {
                font-size: 0.875rem;
                padding: 0.5rem 0.8rem;
                min-width: 80px;
            }
            
            .alert {
                font-size: 0.875rem;
                padding: 0.5rem 0.7rem;
            }
            
            .d-flex {
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .d-flex .btn {
                width: 100%;
            }
            
            .mb-3 {
                margin-bottom: 0.8rem !important;
            }
        }
        
        /* Landscape mobile optimization */
        @media (max-height: 600px) and (orientation: landscape) {
            body {
                padding: 0.5rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            h4 {
                font-size: 1.1rem;
                margin-bottom: 0.8rem;
            }
            
            .form-control, .form-select {
                padding: 0.5rem 0.7rem;
            }
            
            .btn-custom {
                padding: 0.5rem 1rem;
            }
            
            .mb-3 {
                margin-bottom: 0.8rem !important;
            }
        }
        
        /* Tablet optimization */
        @media (min-width: 768px) and (max-width: 991px) {
            .card {
                max-width: 550px;
            }
            
            .card-body {
                padding: 1.75rem;
            }
            
            .form-control, .form-select {
                font-size: 0.95rem;
            }
            
            .btn-custom {
                font-size: 0.95rem;
            }
        }
        
        /* Large screen optimization */
        @media (min-width: 1200px) {
            .card {
                max-width: 650px;
            }
            
            .card-body {
                padding: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>
                            <i class="bi bi-pencil-square me-2"></i>Edit Data Panen
                        </h4>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i><?= $error ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" autocomplete="off">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-person me-2"></i>Nama Petani
                                </label>
                                <input type="text" name="nama_petani" class="form-control" 
                                       value="<?= htmlspecialchars($row['nama_petani']); ?>" 
                                       placeholder="Masukkan nama petani" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-house me-2"></i>Desa
                                </label>
                                <input type="text" name="desa" class="form-control" 
                                       value="<?= htmlspecialchars($row['desa']); ?>" 
                                       placeholder="Masukkan nama desa" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-geo-alt me-2"></i>Kecamatan
                                </label>
                                <input type="text" name="kecamatan" class="form-control" 
                                       value="<?= htmlspecialchars($row['kecamatan']); ?>" 
                                       placeholder="Masukkan nama kecamatan" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-calendar3 me-2"></i>Tanggal Panen
                                </label>
                                <input type="date" name="tanggal_panen" class="form-control" 
                                       value="<?= htmlspecialchars($row['tanggal_panen']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-basket me-2"></i>Berat Panen Per Plot (kg)
                                </label>
                                <input type="number" step="0.01" name="berat_plot" class="form-control" 
                                       value="<?= htmlspecialchars($row['berat_plot']); ?>" 
                                       placeholder="Contoh: 5.25">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-flag me-2"></i>Status
                                </label>
                                <select name="status" class="form-select" required>
                                    <option value="selesai" <?= $row['status']=='selesai'?'selected':''; ?>>
                                        Selesai
                                    </option>
                                    <option value="belum selesai" <?= $row['status']=='belum selesai'?'selected':''; ?>>
                                        Belum Selesai
                                    </option>
                                    <option value="tidak bisa" <?= $row['status']=='tidak bisa'?'selected':''; ?>>
                                        Tidak Bisa
                                    </option>
                                    <option value="sudah" <?= $row['status']=='sudah'?'selected':''; ?>>
                                        Sudah
                                    </option>
                                </select>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="javascript:history.back()" class="btn btn-secondary btn-custom">
                                    <i class="bi bi-arrow-left me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary btn-custom">
                                    <i class="bi bi-save me-2"></i>Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>