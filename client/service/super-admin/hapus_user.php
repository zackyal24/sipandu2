<?php
// filepath: c:\xampp\htdocs\ubinan-monitoring\client\service\super-admin\hapus_user.php
session_start();
include '../../../server/config/koneksi.php';

// Hanya supervisor yang boleh hapus
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../index.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    $delete = mysqli_query($conn, "DELETE FROM users WHERE id = $id");
    if ($delete) {
        header("Location: monitoring_akun.php?hapus=success");
        exit;
    } else {
        header("Location: monitoring_akun.php?hapus=fail");
        exit;
    }
} else {
    header("Location: monitoring_akun.php?hapus=fail");
    exit;
}