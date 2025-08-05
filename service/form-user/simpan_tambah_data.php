<?php
session_start();
include '../../config/koneksi.php';

// Cek login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pcl') {
    header("Location: ../index.php");
    exit;
}

// Ambil data dari form
$nama_petani = $_POST['nama_petani'];
// $lokasi = $_POST['lokasi'];
$tanggal_panen = $_POST['tanggal_panen'];
$subround = $_POST['subround'];
$user_id = $_SESSION['user_id'];
$nomor_segmen = $_POST['nomor_segmen'];
$nomor_sub_segmen = $_POST['nomor_sub_segmen'];
$status = $_POST['status'];

// Ambil id kecamatan & desa dari form
$id_kecamatan = $_POST['kecamatan'];
$id_desa = $_POST['desa'];

// Ambil nama kecamatan
$qKec = mysqli_query($conn, "SELECT nama_kecamatan FROM kecamatan WHERE id='$id_kecamatan'");
$kec = mysqli_fetch_assoc($qKec);
$nama_kecamatan = $kec['nama_kecamatan'] ?? '';

// Ambil nama desa
$qDesa = mysqli_query($conn, "SELECT nama_desa FROM desa WHERE id='$id_desa'");
$desa = mysqli_fetch_assoc($qDesa);
$nama_desa = $desa['nama_desa'] ?? '';

// Simpan data ke database (desa dan kecamatan dipisah)
$query = "INSERT INTO monitoring_data_panen 
    (user_id, nama_petani, desa, kecamatan, tanggal_panen, subround, nomor_segmen, nomor_sub_segmen, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "issssssss", $user_id, $nama_petani, $nama_desa, $nama_kecamatan, $tanggal_panen, $subround, $nomor_segmen, $nomor_sub_segmen, $status);

if (mysqli_stmt_execute($stmt)) {
    echo "<!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Data Berhasil Ditambahkan</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body>
        <div class='modal fade' id='successModal' tabindex='-1' aria-labelledby='successModalLabel' aria-hidden='true'>
            <div class='modal-dialog modal-dialog-centered'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='successModalLabel'>Berhasil</h5>
                    </div>
                    <div class='modal-body'>
                        Data berhasil ditambahkan!
                    </div>
                    <div class='modal-footer'>
                        <a href='dashboard_user.php' class='btn btn-primary'>OK</a>
                    </div>
                </div>
            </div>
        </div>
        <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
        <script>
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        </script>
    </body>
    </html>";
} else {
    echo "<script>
        alert('Gagal menyimpan data! Pastikan semua input sudah benar.');
        window.history.back();
    </script>";
}
?>