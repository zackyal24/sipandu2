<?php
include '../../../server/config/koneksi.php'; // Koneksi ke database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil input
    $nama_petani = htmlspecialchars($_POST['nama_petani']);
    $lokasi = htmlspecialchars($_POST['lokasi']);
    $tanggal_panen = $_POST['tanggal_panen'];
    $berat_panen = $_POST['berat_panen'];

    // Upload foto ke folder masing-masing
    $foto_petani = uploadFoto('foto_petani', 'petani');
    $foto_potong = uploadFoto('foto_potong', 'potong');
    $foto_timbangan = uploadFoto('foto_timbangan', 'timbangan');

    if (!$foto_petani || !$foto_potong || !$foto_timbangan) {
        echo "<script>alert('Upload foto gagal! Pastikan format file benar dan ukuran file tidak melebihi 2MB.'); window.history.back();</script>";
        exit;
    }

    // Simpan ke database dengan prepared statement
    $stmt = mysqli_prepare($conn, "INSERT INTO monitoring_data_panen 
        (nama_petani, lokasi, tanggal_panen, foto_petani, foto_potong, foto_timbangan, berat_panen) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssssd", 
            $nama_petani, 
            $lokasi, 
            $tanggal_panen, 
            $foto_petani, 
            $foto_potong, 
            $foto_timbangan, 
            $berat_panen
        );

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>window.location.href='succes.php';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan data!'); window.history.back();</script>";
        }
        

        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Gagal menyiapkan pernyataan database.'); window.history.back();</script>";
    }
}

// Fungsi upload foto
function uploadFoto($inputName, $folderName) {
    $targetDir = __DIR__ . "/../uploads/$folderName/"; // Path absolut ke folder sesuai parameter
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
    if ($fileSize > 8 * 1024 * 1024) {
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
