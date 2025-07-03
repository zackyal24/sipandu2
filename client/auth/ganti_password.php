<?php
session_start();
include '../../server/config/koneksi.php';

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
        $dashboard = "../service/super-admin/super_admin.php";
    } elseif ($_SESSION['role'] === 'pml') {
        $dashboard = "../service/admin-biasa/dashboard.php";
    } elseif ($_SESSION['role'] === 'user' || $_SESSION['role'] === 'pcl') {
        $dashboard = "../service/form-user/dashboard_user.php";
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ganti Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Poppins', sans-serif; }
        .card { max-width: 400px; margin: 60px auto; border-radius: 12px; }
    </style>
</head>
<body>
<div class="card shadow">
    <div class="card-body">
        <h4 class="mb-3 text-center">Ganti Password</h4>
        <?= $pesan ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Password Lama</label>
                <input type="password" name="password_lama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password_baru" class="form-control" required minlength="6">
            </div>
            <div class="mb-3">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="konfirmasi_password" class="form-control" required minlength="6">
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
            <div class="mt-3 text-center">
                <?php if (strpos($pesan, 'berhasil') !== false): ?>
                    <a href="<?= $dashboard ?>" class="btn btn-link">Kembali</a>
                <?php else: ?>
                    <a href="javascript:history.back()" class="btn btn-link">Kembali</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
</body>
</html>