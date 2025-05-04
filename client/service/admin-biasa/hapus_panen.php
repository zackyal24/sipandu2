<?php
// session_start();
// if (!isset($_SESSION['admin'])) {
//     header("Location: login.php");
//     exit;
// }

include '../../../server/config/koneksi.php';

// Ambil ID dari parameter URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validasi ID
if ($id > 0) {
    // Query untuk menghapus data berdasarkan ID
    $query = mysqli_query($conn, "DELETE FROM monitoring_data_panen WHERE id = $id");

    if ($query) {
        header("Location: dashboard.php?status=success");
        exit;
    } else {
        echo "<script>
            alert('Gagal menghapus data: " . mysqli_error($conn) . "');
            window.history.back();
        </script>";
    }
} else {
    echo "<script>alert('ID tidak valid!'); window.history.back();</script>";
}
?>