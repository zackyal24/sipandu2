<?php
session_start();

// Proses form jika ada POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../config/koneksi.php';
    
    $username = trim($_POST['username']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validasi input
    if (empty($username) || empty($new_password) || empty($confirm_password)) {
        header("Location: reset_password.php?error=empty_fields&username=" . urlencode($username));
        exit;
    }

    // Validasi panjang password
    if (strlen($new_password) < 6) {
        header("Location: reset_password.php?error=password_too_short&username=" . urlencode($username));
        exit;
    }

    // Validasi konfirmasi password
    if ($new_password !== $confirm_password) {
        header("Location: reset_password.php?error=password_mismatch&username=" . urlencode($username));
        exit;
    }

    // Validasi password tidak boleh sama dengan username
    if (strtolower($new_password) === strtolower($username)) {
        header("Location: reset_password.php?error=same_password&username=" . urlencode($username));
        exit;
    }

    // Cek apakah username ada
    $check_user = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($check_user, "s", $username);
    mysqli_stmt_execute($check_user);
    $result = mysqli_stmt_get_result($check_user);

    if (mysqli_num_rows($result) === 0) {
        header("Location: reset_password.php?error=user_not_found&username=" . urlencode($username));
        exit;
    }

    $user_data = mysqli_fetch_assoc($result);
    $user_id = $user_data['id'];

    // Hash password baru
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password
    $update_query = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE username = ?");
    mysqli_stmt_bind_param($update_query, "ss", $hashed_password, $username);

    if (mysqli_stmt_execute($update_query)) {
        // Redirect ke halaman login dengan pesan sukses
        header("Location: ../index.php?message=password_reset_success");
        exit;
    } else {
        header("Location: reset_password.php?error=reset_failed&username=" . urlencode($username));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - UBINANKU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-image: url('../assets/img/sawah.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            margin: 0;
        }
        .main-box {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            max-width: 450px;
            width: 100%;
        }
        .main-box img {
            width: 60px;
            margin-bottom: 15px;
        }
        .btn-primary {
            border-radius: 8px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #0056b3, #004085);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .back-link {
            color: #6c757d;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        .back-link:hover {
            color: #0d6efd;
        }
        .info-box {
            background-color: rgba(13, 110, 253, 0.1);
            border: 1px solid rgba(13, 110, 253, 0.2);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="d-flex align-items-center justify-content-center vh-100">
        <div class="main-box">
            <div class="text-center mb-4">
                <img src="../assets/img/logo.png" alt="Logo">
                <h4 class="mt-2 mb-1">Lupa Password?</h4>
                <p class="text-muted">Buat password baru untuk akun Anda</p>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <div class="d-flex">
                    <i class="bi bi-info-circle text-primary me-2 mt-1"></i>
                    <div>
                        <small class="text-primary fw-bold">Catatan Penting:</small>
                        <br>
                        <small class="text-muted">
                            Fitur ini untuk reset password ketika Anda <strong>lupa password lama</strong>. 
                            Cukup masukkan username dan password baru yang diinginkan.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($_GET['error'])): ?>
                <?php if ($_GET['error'] == 'user_not_found'): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>Username tidak ditemukan!
                    </div>
                <?php elseif ($_GET['error'] == 'password_mismatch'): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi password tidak cocok!
                    </div>
                <?php elseif ($_GET['error'] == 'same_password'): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle me-2"></i>Password baru tidak boleh sama dengan username!
                    </div>
                <?php elseif ($_GET['error'] == 'reset_failed'): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-x-circle me-2"></i>Gagal mereset password. Silakan coba lagi.
                    </div>
                <?php elseif ($_GET['error'] == 'password_too_short'): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>Password minimal 6 karakter!
                    </div>
                <?php elseif ($_GET['error'] == 'empty_fields'): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>Semua field harus diisi!
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <form method="post" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="bi bi-person me-2"></i>Username
                    </label>
                    <input type="text" id="username" name="username" class="form-control" 
                           placeholder="Masukkan username Anda" 
                           value="<?= htmlspecialchars($_GET['username'] ?? '') ?>" required>
                    <div class="form-text">Username akun yang lupa password</div>
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">
                        <i class="bi bi-key me-2"></i>Password Baru
                    </label>
                    <input type="password" id="new_password" name="new_password" class="form-control" 
                           placeholder="Buat password baru" minlength="6" required>
                    <div class="form-text">Minimal 6 karakter</div>
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label">
                        <i class="bi bi-key-fill me-2"></i>Konfirmasi Password Baru
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                           placeholder="Ketik ulang password baru" minlength="6" required>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset Password Saya
                    </button>
                </div>
            </form>

            <div class="text-center">
                <a href="../index.php" class="back-link">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Halaman Login
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Validasi real-time untuk konfirmasi password
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && newPassword !== confirmPassword) {
                this.setCustomValidity('Password tidak cocok');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
                if (confirmPassword) {
                    this.classList.add('is-valid');
                }
            }
        });

        // Validasi agar password tidak sama dengan username
        document.getElementById('new_password').addEventListener('input', function() {
            const username = document.getElementById('username').value.toLowerCase();
            const password = this.value.toLowerCase();
            
            if (password && password === username) {
                this.setCustomValidity('Password tidak boleh sama dengan username');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
                if (this.value.length >= 6) {
                    this.classList.add('is-valid');
                }
            }
        });
    </script>
</body>
</html>