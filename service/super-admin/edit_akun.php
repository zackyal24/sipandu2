<?php
session_start();
include '../../config/koneksi.php';

// Cek apakah user login dan supervisor
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../index.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';
$success = '';

// Ambil data user
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
$user = mysqli_fetch_assoc($query);

if (!$user) {
    header("Location: monitoring_akun.php");
    exit;
}

// Ambil daftar PML untuk dropdown
$pml_list = mysqli_query($conn, "SELECT id, nama_lengkap, username FROM users WHERE role = 'pml' ORDER BY nama_lengkap ASC");

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $no_hp = trim($_POST['no_hp']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $pml_id = isset($_POST['pml_id']) && !empty($_POST['pml_id']) ? intval($_POST['pml_id']) : null;

    // Validasi
    if ($username === '' || $nama_lengkap === '' || $no_hp === '' || $email === '' || $role === '') {
        $error = 'Semua field wajib diisi.';
    } 
    // Validasi khusus untuk PCL - harus ada PML
    elseif ($role === 'pcl' && $pml_id === null) {
        $error = 'PCL harus memilih PML yang mengawasi.';
    }
    // Validasi untuk PML dan Supervisor - tidak boleh ada PML ID
    elseif (($role === 'pml' || $role === 'supervisor') && $pml_id !== null) {
        $pml_id = null; // Force null untuk PML dan Supervisor
    }
    else {
        // Cek apakah username sudah digunakan oleh user lain
        $cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' AND id != $id");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Username sudah digunakan oleh akun lain.';
        } else {
            if ($password !== '') {
                // Jika password diisi, update juga password
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($conn, "UPDATE users SET username = ?, nama_lengkap = ?, no_hp = ?, email = ?, role = ?, password = ?, pml_id = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "ssssssii", $username, $nama_lengkap, $no_hp, $email, $role, $password_hash, $pml_id, $id);
            } else {
                // Jika password kosong, update tanpa password
                $stmt = mysqli_prepare($conn, "UPDATE users SET username = ?, nama_lengkap = ?, no_hp = ?, email = ?, role = ?, pml_id = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "sssssii", $username, $nama_lengkap, $no_hp, $email, $role, $pml_id, $id);
            }

            if (mysqli_stmt_execute($stmt)) {
                $success = 'Data akun berhasil diperbarui.';
                // Perbarui data lokal setelah update
                $user['username'] = $username;
                $user['nama_lengkap'] = $nama_lengkap;
                $user['no_hp'] = $no_hp;
                $user['email'] = $email;
                $user['role'] = $role;
                $user['pml_id'] = $pml_id;
            } else {
                $error = 'Gagal memperbarui data.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Akun | SIPANTAU</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #f8f9fc;
            min-height: 100vh;
            padding: clamp(1rem, 3vw, 2rem);
        }
        
        .card { 
            width: 100%;
            max-width: 600px; 
            margin: 0 auto;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
            background: white;
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
        
        .text-muted {
            font-size: clamp(0.75rem, 1.5vw, 0.875rem);
        }
        
        .required {
            color: #dc3545;
        }
        
        /* Field PML ID - Hidden by default */
        #pml-field {
            display: none;
            animation: fadeIn 0.3s ease-in;
        }
        
        #pml-field.show {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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
            
            .text-muted {
                font-size: 0.8rem;
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
            
            .text-muted {
                font-size: 0.75rem;
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
                        <i class="bi bi-pencil-square me-2"></i>Edit Akun: <?= htmlspecialchars($user['username']) ?>
                    </h4>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i><?= $error; ?>
                        </div>
                    <?php elseif ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i><?= $success; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-person me-2"></i>Username <span class="required">*</span>
                            </label>
                            <input type="text" name="username" class="form-control" required 
                                   value="<?= htmlspecialchars($user['username']); ?>" 
                                   placeholder="Masukkan username">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-person-badge me-2"></i>Nama Lengkap <span class="required">*</span>
                            </label>
                            <input type="text" name="nama_lengkap" class="form-control" required 
                                   value="<?= htmlspecialchars($user['nama_lengkap']); ?>" 
                                   placeholder="Masukkan nama lengkap">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-telephone me-2"></i>No HP <span class="required">*</span>
                            </label>
                            <input type="text" name="no_hp" class="form-control" required 
                                   value="<?= htmlspecialchars($user['no_hp']); ?>" 
                                   placeholder="Masukkan nomor HP">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-envelope me-2"></i>Email <span class="required">*</span>
                            </label>
                            <input type="email" name="email" class="form-control" required 
                                   value="<?= htmlspecialchars($user['email']); ?>" 
                                   placeholder="Masukkan email">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-shield-check me-2"></i>Role <span class="required">*</span>
                            </label>
                            <select name="role" id="role-select" class="form-select" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="pcl" <?= $user['role'] === 'pcl' ? 'selected' : '' ?>>PCL</option>
                                <option value="pml" <?= $user['role'] === 'pml' ? 'selected' : '' ?>>PML</option>
                                <option value="supervisor" <?= $user['role'] === 'supervisor' ? 'selected' : '' ?>>Supervisor</option>
                            </select>
                        </div>

                        <!-- Field PML (muncul hanya untuk PCL) -->
                        <div class="mb-3" id="pml-field" <?= $user['role'] === 'pcl' ? 'style="display: block;"' : '' ?>>
                            <label class="form-label">
                                <i class="bi bi-person-check me-2"></i>PML yang Mengawasi <span class="required">*</span>
                            </label>
                            <select name="pml_id" id="pml-select" class="form-select" <?= $user['role'] === 'pcl' ? 'required' : '' ?>>
                                <option value="">-- Pilih PML --</option>
                                <?php 
                                // Reset pointer untuk PML list
                                mysqli_data_seek($pml_list, 0);
                                while($pml = mysqli_fetch_assoc($pml_list)): 
                                ?>
                                    <option value="<?= $pml['id'] ?>" <?= $user['pml_id'] == $pml['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($pml['nama_lengkap']) ?> (<?= htmlspecialchars($pml['username']) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <div class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>PCL harus memilih PML yang akan mengawasi
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-key me-2"></i>Password Baru 
                                <span class="text-muted">(Kosongkan jika tidak ingin mengubah)</span>
                            </label>
                            <input type="password" name="password" class="form-control" 
                                   placeholder="Masukkan password baru">
                            <div class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>Minimal 6 karakter
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="monitoring_akun.php" class="btn btn-secondary btn-custom">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary btn-custom">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role-select');
    const pmlField = document.getElementById('pml-field');
    const pmlSelect = document.getElementById('pml-select');
    
    // Handle role change
    roleSelect.addEventListener('change', function() {
        const selectedRole = this.value;
        
        if (selectedRole === 'pcl') {
            // Show PML field for PCL
            pmlField.style.display = 'block';
            pmlField.classList.add('show');
            pmlSelect.setAttribute('required', 'required');
        } else {
            // Hide PML field for PML and Supervisor
            pmlField.style.display = 'none';
            pmlField.classList.remove('show');
            pmlSelect.removeAttribute('required');
            pmlSelect.value = ''; // Clear selection
        }
    });
    
    // Initialize on page load
    if (roleSelect.value === 'pcl') {
        pmlField.style.display = 'block';
        pmlField.classList.add('show');
        pmlSelect.setAttribute('required', 'required');
    }
});
</script>

</body>
</html>
