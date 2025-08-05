<?php
session_start();
include '../config/koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

// Gunakan prepared statement
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if ($data && password_verify($password, $data['password'])) {
    $_SESSION['user_id'] = $data['id']; 
    $_SESSION['username'] = $data['username'];
    $_SESSION['role'] = $data['role'];

    if ($data['role'] === 'supervisor') {
        $_SESSION['supervisor'] = $data['username'];
        header("Location: ../service/super-admin/super_admin.php");
    } else if ($data['role'] === 'pml') {
        $_SESSION['pml'] = $data['username'];
        header("Location: ../service/admin-biasa/dashboard.php");
    } else {
        header("Location: ../service/form-user/dashboard_user.php");
    }
    exit;
} else {
    echo "<script>alert('Username atau Password salah'); window.location='../index.php';</script>";
}
?>
