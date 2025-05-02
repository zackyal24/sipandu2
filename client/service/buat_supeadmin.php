<?php
include '../server/config/koneksi.php';

// Ganti data akun di bawah ini sesuai keinginan
$username = "superadmin";
$password_plain = "superadmin123";
$nama_lengkap = "Administrator Utama";

// Hash password
$password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

// Cek apakah user sudah ada
$cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
if (mysqli_num_rows($cek) > 0) {
    echo "Akun <strong>$username</strong> sudah ada.";
    exit;
}

// Buat akun superadmin
$stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, 'superadmin')");
mysqli_stmt_bind_param($stmt, "sss", $username, $password_hashed, $nama_lengkap);

if (mysqli_stmt_execute($stmt)) {
    echo "✅ Akun superadmin berhasil dibuat!<br>";
    echo "Username: <strong>$username</strong><br>";
    echo "Password: <strong>$password_plain</strong><br>";
} else {
    echo "❌ Gagal membuat akun superadmin.";
}
?>
