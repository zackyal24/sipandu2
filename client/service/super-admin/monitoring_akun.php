<?php
session_start();
include '../../../server/config/koneksi.php';

// Hanya bisa diakses oleh superadmin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../index.php");
    exit;
}

// Ambil semua user dari database
$data = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manajemen Akun</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; background: #f8fafc; }
    .btn-custom { border-radius: 8px; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="#">Manajemen Akun</a>
    <div class="d-flex align-items-center">
      <span class="text-white me-3">👤 <?= htmlspecialchars($_SESSION['username']); ?> (Superadmin)</span>
      <a href="super_admin.php" class="btn btn-outline-light btn-sm">Kembali</a>
    </div>
  </div>
</nav>

<!-- Konten -->
<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Daftar Akun User</h4>
    <a href="tambah_user.php" class="btn btn-success btn-sm btn-custom">+ Tambah User</a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-primary text-center">
          <tr>
            <th>No</th>
            <th>Username</th>
            <th>Nama Lengkap</th>
            <th>Role</th>
            <th>Dibuat Pada</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php $no = 1; while($user = mysqli_fetch_assoc($data)): ?>
          <tr>
            <td class="text-center"><?= $no++; ?></td>
            <td><?= htmlspecialchars($user['username']); ?></td>
            <td><?= htmlspecialchars($user['nama_lengkap']); ?></td>
            <td class="text-center">
              <span class="badge bg-<?= $user['role'] === 'superadmin' ? 'danger' : 'secondary'; ?>">
                <?= strtoupper($user['role']); ?>
              </span>
            </td>
            <td class="text-center"><?= date('d M Y H:i', strtotime($user['created_at'])); ?></td>
            <td class="text-center">
              <a href="edit_user.php?id=<?= $user['id']; ?>" class="btn btn-warning btn-sm btn-custom">Edit</a>
              <a href="reset_password.php?id=<?= $user['id']; ?>" class="btn btn-info btn-sm btn-custom">Reset PW</a>
              <a href="hapus_user.php?id=<?= $user['id']; ?>" class="btn btn-danger btn-sm btn-custom"
                 onclick="return confirm('Yakin ingin menghapus akun ini?')">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>
