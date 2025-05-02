<?php
session_start();
include '../../../server/config/koneksi.php';

// Cek login dan role superadmin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../index.php");
    exit;
}

$error = '';
$success = '';

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $nama = trim($_POST['nama_lengkap']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validasi form
    if ($username === '' || $nama === '' || $password === '' || $role === '') {
        $error = 'Semua field wajib diisi.';
    } else {
        // Cek apakah username sudah digunakan
        $cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Username sudah digunakan.';
        } else {
            // Hash password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insert ke database
            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssss", $username, $passwordHash, $nama, $role);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Akun berhasil ditambahkan.";
            } else {
                $error = "Gagal menambahkan akun.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Akun</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; }
        .card { max-width: 600px; margin: 50px auto; border-radius: 12px; }
    </style>
</head>
<body>

<div class="container">
    <div class="card shadow p-4">
        <h4 class="mb-4 text-center">Tambah Akun Baru</h4>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= $success; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" required value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-select" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="admin" <?= isset($_POST['role']) && $_POST['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="superadmin" <?= isset($_POST['role']) && $_POST['role'] == 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
                </select>
            </div>

            <div class="d-flex justify-content-between">
                <a href="monitoring_akun.php" class="btn btn-secondary">← Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
