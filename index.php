<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Selamat Datang - Layanan Monitoring Ubinan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-image: url('assets/img/sawah.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      height: 100vh;
      margin: 0;
    }
    .main-box {
      background-color: rgba(255, 255, 255, 0.8);
      padding: 30px 20px;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
      max-width: 400px;
      width: 100%;
    }
    .main-box img {
      width: 80px;
      margin-bottom: 20px;
    }
    .main-box h4 {
      font-weight: 600;
      color: #333;
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
    .text-primary {
      font-weight: 500;
    }
    .whatsapp-link {
      color: #6c757d;
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      transition: color 0.3s ease;
      display: inline-flex;
      align-items: center;
    }
    .whatsapp-link:hover {
      color: #0d6efd;
      text-decoration: underline;
    }
    .whatsapp-link i {
      font-size: 1rem;
    }
  </style>
</head>
<body>
  <div class="d-flex align-items-center justify-content-center vh-100">
    <div class="main-box text-center">
      <img class="d-block mx-auto" src="assets/img/logo.png" alt="Logo">
      <h4 class="text-center mb-4">LOGIN SIPANTAU</h4>
      
      <!-- Alert untuk notifikasi -->
      <?php if (isset($_GET['message'])): ?>
        <?php if ($_GET['message'] == 'password_changed'): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>Password berhasil diubah! Silakan login dengan password baru.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php elseif ($_GET['message'] == 'invalid_credentials'): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>Username atau password salah!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php elseif ($_GET['message'] == 'password_reset_success'): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>Password berhasil direset! Silakan login dengan password baru.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <form method="post" action="auth/login_proses.php">
        <div class="mb-3 text-start">
          <label for="username" class="form-label">Username</label>
          <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required>
        </div>
        <div class="mb-3 text-start">
          <label for="password" class="form-label">Password</label>
          <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
        </div>
        <button class="btn btn-primary w-100 mb-3" type="submit">
          <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
        </button>
      </form>
      
      <div class="text-center">
        <a href="https://wa.me/6281280976144?text=Halo%2C%20saya%20lupa%20password%20akun%20SIPANTAU.%20Username%20saya%3A%20[TULIS_USERNAME_ANDA]" 
           target="_blank" class="whatsapp-link">
          <i class="bi bi-key me-2"></i>Lupa Password?
        </a>
      </div>

  </div>

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
