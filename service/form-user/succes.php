<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Berhasil Disimpan</title>

  <!-- Google Fonts + Bootstrap -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #e0f7fa;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      text-align: center;
      padding: 30px;
    }

    .success-icon {
      font-size: 80px;
      color: #28a745;
      margin-bottom: 20px;
    }

    h1 {
      font-weight: 700;
      color: #2c3e50;
    }

    p {
      color: #555;
    }

    .btn {
      margin-top: 20px;
      border-radius: 8px;
    }
  </style>

  <!-- Auto Redirect Setelah 5 Detik -->
  <meta http-equiv="refresh" content="30;url=form_monitoring.php">
</head>

<body>

<div class="container">
  <div class="success-icon">✅</div>
  <h1>Data Berhasil Disimpan!</h1>
  <p>Terima kasih telah mengisi data monitoring panen.</p>

  <p class="text-muted">Anda akan diarahkan kembali ke form dalam 30 detik...</p>

  <a href="form_monitoring.php" class="btn btn-primary">Kembali ke Form</a>
  <a href="lihat_data.php" class="btn btn-success">Lihat Data Panen</a> <!-- Tombol Lihat Data -->
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
