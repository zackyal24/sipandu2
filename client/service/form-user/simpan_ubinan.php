<?php
session_start();
include '../../../server/config/koneksi.php'; // Koneksi ke database

// Cek login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil input
    $id = $_POST['id'];
    $nama_petani = htmlspecialchars($_POST['nama_petani']);
    $desa = htmlspecialchars($_POST['desa']);
    $kecamatan = htmlspecialchars($_POST['kecamatan']);
    $tanggal_panen = $_POST['tanggal_panen'];
    $berat_panen = $_POST['berat_panen'];
    $nomor_sub_segmen = $_POST['nomor_sub_segmen'];

    // Upload foto ke folder masing-masing
    $foto_petani = uploadFoto('foto_petani', 'petani');
    $foto_potong = uploadFoto('foto_potong', 'potong');
    $foto_timbangan = uploadFoto('foto_timbangan', 'timbangan');

    if (!$foto_petani || !$foto_potong || !$foto_timbangan) {
        echo "<script>alert('Upload foto gagal! Pastikan format file benar dan ukuran file tidak melebihi 2MB.'); window.history.back();</script>";
        exit;
    }

    // Update data di database
    $query = "UPDATE monitoring_data_panen 
            SET nama_petani = ?, desa = ?, kecamatan = ?, tanggal_panen = ?, berat_panen = ?,
                nomor_sub_segmen = ?, foto_petani = ?, foto_potong = ?, foto_timbangan = ?, status = 'selesai' 
            WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssssssssi", $nama_petani, $desa, $kecamatan, $tanggal_panen, $berat_panen, $nomor_sub_segmen, $foto_petani, $foto_potong, $foto_timbangan, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Data Berhasil Disimpan</title>
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
                            Data berhasil disimpan!
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

    mysqli_stmt_close($stmt);
}

// Fungsi upload foto
function uploadFoto($inputName, $folderName) {
    $targetDir = __DIR__ . "/../../uploads/$folderName/"; // Path absolut ke folder sesuai parameter
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); // buat folder kalau belum ada
    }

    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $fileTmpPath = $_FILES[$inputName]['tmp_name'];
    $fileName = basename($_FILES[$inputName]['name']);
    $fileSize = $_FILES[$inputName]['size'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedExt = array("jpg", "jpeg", "png");

    // Validasi ekstensi file
    if (!in_array($fileExt, $allowedExt)) {
        return false;
    }

    // Validasi ukuran file (maksimal 2MB)
    if ($fileSize > 2 * 1024 * 1024) {
        return false;
    }

    // Generate nama file baru supaya unik
    $newFileName = uniqid($inputName . "_") . "." . $fileExt;
    $targetFilePath = $targetDir . $newFileName;

    if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
        return "uploads/$folderName/" . $newFileName; // hanya simpan path relatif
    } else {
        return false;
    }
}
?>
