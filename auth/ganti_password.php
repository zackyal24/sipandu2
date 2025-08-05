<?php
session_start();
include '../config/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass_lama = $_POST['password_lama'];
    $pass_baru = $_POST['password_baru'];
    $pass_konfirmasi = $_POST['konfirmasi_password'];

    // Ambil password lama dari database
    $q = mysqli_query($conn, "SELECT password FROM users WHERE id = $user_id");
    $data = mysqli_fetch_assoc($q);

    // Cek password lama
    if (!password_verify($pass_lama, $data['password'])) {
        $pesan = '<div class="alert alert-danger">Password lama salah.</div>';
    } elseif ($pass_baru !== $pass_konfirmasi) {
        $pesan = '<div class="alert alert-danger">Konfirmasi password baru tidak cocok.</div>';
    } elseif (strlen($pass_baru) < 6) {
        $pesan = '<div class="alert alert-danger">Password baru minimal 6 karakter.</div>';
    } else {
        // Update password
        $hash = password_hash($pass_baru, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password='$hash' WHERE id=$user_id");
        $pesan = '<div class="alert alert-success">Password berhasil diubah.</div>';
    }
}

// Tentukan dashboard sesuai role
$dashboard = "#";
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'supervisor') {
        $dashboard = "../client/service/super-admin/super_admin.php";
    } elseif ($_SESSION['role'] === 'pml') {
        $dashboard = "../client/service/pml/dashboard_pml.php";
    } elseif ($_SESSION['role'] === 'user' || $_SESSION['role'] === 'pcl') {
        $dashboard = "../client/service/form-user/dashboard_user.php";
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password | UBINANKU</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body { 
            background: #f8f9fa; /* Background putih abu-abu seperti sebelumnya */
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        .card { 
            max-width: 400px;
            width: 100%;
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background: white; /* Card background putih */
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .card-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            font-size: clamp(1.25rem, 2.5vw, 1.5rem);
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: clamp(0.875rem, 1.5vw, 1rem);
        }
        
        .form-control {
            border-radius: 10px;
            border: 1px solid #e3e6f0;
            padding: 0.75rem 1rem;
            font-size: clamp(0.875rem, 1.5vw, 1rem);
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #0d6efd; /* Bootstrap blue */
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        .btn-primary {
            background-color: #0d6efd; /* Bootstrap blue seperti sebelumnya */
            border-color: #0d6efd;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            font-size: clamp(0.875rem, 1.5vw, 1rem);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #0b5ed7; /* Darker blue on hover */
            border-color: #0a58ca;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }
        
        .btn-link {
            color: #0d6efd; /* Bootstrap blue */
            text-decoration: none;
            font-weight: 500;
            font-size: clamp(0.875rem, 1.5vw, 1rem);
            transition: all 0.3s ease;
        }
        
        .btn-link:hover {
            color: #0b5ed7;
            transform: translateY(-1px);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            font-size: clamp(0.875rem, 1.5vw, 1rem);
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background-color: #d1e7dd; /* Bootstrap success green */
            color: #0f5132;
        }
        
        .alert-danger {
            background-color: #f8d7da; /* Bootstrap danger red */
            color: #842029;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            margin-bottom: 1rem;
        }
        
        .app-name {
            font-size: clamp(1.1rem, 2vw, 1.3rem);
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .app-subtitle {
            font-size: clamp(0.8rem, 1.5vw, 0.9rem);
            color: #6c757d;
            margin-bottom: 0;
        }
        
        /* Mobile optimizations */
        @media (max-width: 576px) {
            body {
                padding: 0.5rem;
            }
            
            .card {
                margin: 0;
                min-height: auto;
            }
            
            .card-body {
                padding: 1.5rem;
            }
            
            .form-control {
                padding: 0.6rem 0.8rem;
            }
            
            .btn-primary {
                padding: 0.6rem 1.2rem;
            }
        }
        
        /* Extra small devices */
        @media (max-width: 400px) {
            .card-body {
                padding: 1rem;
            }
            
            .logo {
                width: 50px;
                height: 50px;
            }
        }
        
        /* Landscape mobile optimization */
        @media (max-height: 600px) and (orientation: landscape) {
            body {
                align-items: flex-start;
                padding-top: 1rem;
            }
            
            .card {
                margin: 1rem 0;
            }
            
            .logo-container {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="card shadow">
        <div class="card-body">
            <div class="logo-container">
                <img src="../assets/logo.png" alt="Logo" class="logo">
                <div class="app-name">UBINANKU</div>
                <div class="app-subtitle">Ganti Password</div>
            </div>
            
            <?= $pesan ?>
            
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-lock me-2"></i>Password Lama
                    </label>
                    <input type="password" name="password_lama" class="form-control" placeholder="Masukkan password lama" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-key me-2"></i>Password Baru
                    </label>
                    <input type="password" name="password_baru" class="form-control" placeholder="Masukkan password baru" required minlength="6">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-shield-check me-2"></i>Konfirmasi Password Baru
                    </label>
                    <input type="password" name="konfirmasi_password" class="form-control" placeholder="Konfirmasi password baru" required minlength="6">
                </div>
                
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Simpan Password
                    </button>
                </div>
                
                <div class="text-center">
                    <?php if (strpos($pesan, 'berhasil') !== false): ?>
                        <a href="<?= $dashboard ?>" class="btn btn-link">
                            <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
                        </a>
                    <?php else: ?>
                        <a href="javascript:history.back()" class="btn btn-link">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Form validation -->
    <script>
        // Real-time password confirmation validation
        document.addEventListener('DOMContentLoaded', function() {
            const passwordBaru = document.querySelector('input[name="password_baru"]');
            const konfirmasiPassword = document.querySelector('input[name="konfirmasi_password"]');
            const submitBtn = document.querySelector('button[type="submit"]');
            
            function validatePassword() {
                if (passwordBaru.value && konfirmasiPassword.value) {
                    if (passwordBaru.value !== konfirmasiPassword.value) {
                        konfirmasiPassword.setCustomValidity('Password tidak cocok');
                        konfirmasiPassword.classList.add('is-invalid');
                    } else {
                        konfirmasiPassword.setCustomValidity('');
                        konfirmasiPassword.classList.remove('is-invalid');
                        konfirmasiPassword.classList.add('is-valid');
                    }
                }
            }
            
            passwordBaru.addEventListener('input', validatePassword);
            konfirmasiPassword.addEventListener('input', validatePassword);
        });
    </script>
</body>
</html>