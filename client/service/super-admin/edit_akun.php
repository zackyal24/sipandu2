<?php
session_start();
include '../../../server/config/koneksi.php';

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
    header("Location: users.php");
    exit;
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $no_hp = trim($_POST['no_hp']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validasi
    if ($username === '' || $nama_lengkap === '' || $role === '') {
        $error = 'Semua field wajib diisi.';
    } else {
        // Cek apakah username sudah digunakan oleh user lain
        $cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' AND id != $id");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Username sudah digunakan oleh akun lain.';
        } else {
            if ($password !== '') {
                // Jika password diisi, update juga password
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($conn, "UPDATE users SET username = ?, nama_lengkap = ?, no_hp = ?, email = ?, role = ?, password = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "ssssssi", $username, $nama_lengkap, $no_hp, $email, $role, $password_hash, $id);
            } else {
                // Jika password kosong, update tanpa password
                $stmt = mysqli_prepare($conn, "UPDATE users SET username = ?, nama_lengkap = ?, no_hp = ?, email = ?, role = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "sssssi", $username, $nama_lengkap, $no_hp, $email, $role, $id);
            }

            if (mysqli_stmt_execute($stmt)) {
                $success = 'Data akun berhasil diperbarui.';
                // Perbarui data lokal setelah update
                $user['username'] = $username;
                $user['nama_lengkap'] = $nama_lengkap;
                $user['no_hp'] = $no_hp;
                $user['email'] = $email;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .card { max-width: 600px; margin: 50px auto; border-radius: 12px; }
    </style>
</head>
<body>

<div class="container">
    <div class="card shadow p-4">
        <h4 class="mb-4"><i class="bi bi-pencil-square me-2"></i>Edit Akun: <?= htmlspecialchars($user['username']) ?></h4>
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
                <label>No HP</label>
                <input type="text" name="no_hp" class="form-control" required value="<?= htmlspecialchars($user['no_hp']); ?>">
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email']); ?>">
            </div>

            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-select" required>
                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="supervisor" <?= $user['role'] === 'supervisor' ? 'selected' : '' ?>>Supervisor</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Password Baru <span class="text-muted">(Kosongkan jika tidak ingin mengubah)</span></label>
                <input type="password" name="password" class="form-control" placeholder="Password baru">
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
