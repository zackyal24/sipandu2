<?php
// filepath: c:\xampp\htdocs\ubinan-monitoring\client\service\admin-biasa\revisi_panen.php
include '../../../server/config/koneksi.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $note = trim($_POST['note']);
    // Update note dan status
    $q = mysqli_query($conn, "UPDATE monitoring_data_panen SET note='" . mysqli_real_escape_string($conn, $note) . "', status='sudah' WHERE id=$id");
    header("Location: dashboard.php");
    exit;
}
?>