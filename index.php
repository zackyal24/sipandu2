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
      background-image: url('assets/img/sawah.jpg'); /* Path gambar */
      background-size: cover; /* Gambar memenuhi layar */
      background-position: center; /* Gambar dipusatkan */
      background-repeat: no-repeat; /* Tidak mengulang gambar */
      height: 100vh; /* Tinggi halaman penuh */
      margin: 0; /* Hilangkan margin default */
    }
    .main-box {
      background-color: rgba(255, 255, 255, 0.7); /* Transparansi ditambahkan */
      padding: 30px 20px;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      max-width: 400px; /* Lebar kotak */
      width: 100%;
    }
    .main-box img {
      width: 80px; /* Ukuran logo */
      margin-bottom: 20px;
    }
    .main-box h4 {
      font-weight: 600;
      color: #333;
    }
    .btn-primary {
      border-radius: 8px;
    }
    .text-primary {
      font-weight: 500;
    }
  </style>
</head>
<body>
  <div class="d-flex align-items-center justify-content-center vh-100">
    <div class="main-box text-center">
      <img class="d-block mx-auto" src="assets/img/logo.png" alt="Logo">
      <h4 class="text-center mb-4">LOGIN UBINANKU</h4>
      <form method="post" action="auth/login_proses.php">
        <div class="mb-3 text-start">
          <label for="username" class="form-label">Username</label>
          <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required>
        </div>
        <div class="mb-3 text-start">
          <label for="password" class="form-label">Password</label>
          <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
        </div>
        <button class="btn btn-primary w-100" type="submit">Masuk</button>
      </form>
    </div>
  </div>
</body>
</html>
