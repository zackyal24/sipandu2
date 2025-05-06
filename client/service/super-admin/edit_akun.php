<?php
session_start();
include '../../../server/config/koneksi.php';

// Cek apakah user login dan superadmin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'superadmin') {
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
    header("Location: users.php");
    exit;
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $role = $_POST['role'];

    // Validasi
    if ($username === '' || $nama_lengkap === '' || $role === '') {
        $error = 'Semua field wajib diisi.';
    } else {
        // Cek apakah username sudah digunakan oleh user lain
        $cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' AND id != $id");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Username sudah digunakan oleh akun lain.';
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE users SET username = ?, nama_lengkap = ?, role = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "sssi", $username, $nama_lengkap, $role, $id);

            if (mysqli_stmt_execute($stmt)) {
                $success = 'Data akun berhasil diperbarui.';
                // Perbarui data lokal setelah update
                $user['username'] = $username;
                $user['nama_lengkap'] = $nama_lengkap;
                $user['role'] = $role;
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
    <title>Edit Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .card { max-width: 600px; margin: 50px auto; border-radius: 12px; }
    </style>
</head>
<body>

<div class="container">
    <div class="card shadow p-4">
        <h4 class="mb-4 text-center">Edit Akun: <?= htmlspecialchars($user['username']) ?></h4>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= $success; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($user['username']); ?>">
            </div>

            <div class="mb-3">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" required value="<?= htmlspecialchars($user['nama_lengkap']); ?>">
            </div>

            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-select" required>
                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="superadmin" <?= $user['role'] === 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
                </select>
            </div>

            <div class="d-flex justify-content-between">
                <a href="monitoring_akun.php" class="btn btn-secondary">← Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
