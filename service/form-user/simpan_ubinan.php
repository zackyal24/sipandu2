<?php
session_start();
include '../../config/koneksi.php';

// Cek login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pcl') {
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
    $subround = $_POST['subround'];
    $berat_plot = $_POST['berat_plot'];
    $nomor_segmen = $_POST['nomor_segmen'];
    $nomor_sub_segmen = $_POST['nomor_sub_segmen'];

    // PERBAIKAN: Ambil data lama untuk cek status revisi DAN foto yang ada
    $query_check = "SELECT status, foto_serah_terima, foto_bukti_plot_ubinan, foto_berat_timbangan FROM monitoring_data_panen WHERE id = ?";
    $stmt_check = mysqli_prepare($conn, $query_check);
    mysqli_stmt_bind_param($stmt_check, "i", $id);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $data = mysqli_fetch_assoc($result_check);
    
    $isRevision = $data && $data['status'] === 'revisi';
    
    // Jika ini revisi, reset note revisi
    $additional_fields = "";
    if ($isRevision) {
        $additional_fields = ", note_revisi = NULL, revised_at = NULL, revised_by = NULL";
    }
    
    // Hitung nilai GKP, GKG, dan KU (TETAP PAKAI RUMUS YANG SUDAH SESUAI)
    $gkp = ($berat_plot / 100) / (6.25 / 10000);
    $gkg = $gkp * 0.8602;
    $ku = $gkg * 0.6274;

    try {
        // PERBAIKAN: Upload foto dengan fallback ke foto lama jika revisi
        $foto_serah_terima = uploadFotoRevisi('foto_serah_terima', 'serah_terima', $data['foto_serah_terima'] ?? '', $isRevision);
        $foto_bukti_plot_ubinan = uploadFotoRevisi('foto_bukti_plot_ubinan', 'bukti_plot_ubinan', $data['foto_bukti_plot_ubinan'] ?? '', $isRevision);
        $foto_berat_timbangan = uploadFotoRevisi('foto_berat_timbangan', 'berat_timbangan', $data['foto_berat_timbangan'] ?? '', $isRevision);

        // PERBAIKAN: Cek upload berdasarkan mode (revisi vs baru)
        if ($isRevision) {
            // Untuk revisi: OK selama ada foto (baru atau lama)
            if (!$foto_serah_terima || !$foto_bukti_plot_ubinan || !$foto_berat_timbangan) {
                throw new Exception("Error: Tidak ada foto yang tersedia. Silakan upload minimal satu foto atau hubungi administrator.");
            }
        } else {
            // Untuk data baru: harus upload semua foto
            if (!$foto_serah_terima || !$foto_bukti_plot_ubinan || !$foto_berat_timbangan) {
                throw new Exception("Gagal upload salah satu foto. Pastikan semua foto sudah dipilih dan formatnya benar (JPG/PNG).");
            }
        }

        // Update query dengan reset revisi info
        $query = "UPDATE monitoring_data_panen 
        SET nama_petani = ?, desa = ?, kecamatan = ?, tanggal_panen = ?, subround = ?, berat_plot = ?,
            nomor_segmen = ?, nomor_sub_segmen = ?, foto_serah_terima = ?, foto_bukti_plot_ubinan = ?, foto_berat_timbangan = ?, 
            gkp = ?, gkg = ?, ku = ?, status = 'selesai', updated_at = NOW() $additional_fields
        WHERE id = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        
        if (!$stmt) {
            throw new Exception("Error preparing database query: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "ssssssssssssddi", $nama_petani, $desa, $kecamatan, $tanggal_panen, $subround, $berat_plot, $nomor_segmen, $nomor_sub_segmen, $foto_serah_terima, $foto_bukti_plot_ubinan, $foto_berat_timbangan, $gkp, $gkg, $ku, $id);

        if (mysqli_stmt_execute($stmt)) {
            echo "<!DOCTYPE html>
            <html lang='id'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Data Berhasil Disimpan</title>
                <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap' rel='stylesheet'>
                <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
                <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css' rel='stylesheet'>
                <style>
                    body { font-family: 'Poppins', sans-serif; background: #f0f4f8; }
                    .modal-content { border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); border: none; }
                    .modal-header { background: linear-gradient(135deg, #28a745, #20c997); color: white; border-radius: 15px 15px 0 0; border-bottom: none; }
                    .modal-body { padding: 2rem; }
                    .modal-footer { border-top: none; padding: 0 2rem 2rem 2rem; }
                    .btn-primary { background: linear-gradient(135deg, #007bff, #0056b3); border: none; border-radius: 8px; padding: 0.75rem 2rem; font-weight: 600; transition: all 0.3s ease; }
                    .btn-primary:hover { background: linear-gradient(135deg, #0056b3, #004085); transform: translateY(-2px); }
                    .calculation-info { background: #f8f9fa; border-left: 4px solid #28a745; padding: 1rem; margin: 1rem 0; font-size: 0.9rem; border-radius: 5px; }
                    .success-icon { color: #28a745; font-size: 4rem; margin-bottom: 1rem; text-shadow: 0 2px 4px rgba(40, 167, 69, 0.3); }
                </style>
            </head>
            <body>
                <div class='modal fade show' style='display: block; background-color: rgba(0,0,0,0.5);'>
                    <div class='modal-dialog modal-dialog-centered modal-lg'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h5 class='modal-title'><i class='bi bi-check-circle me-2'></i>Data Berhasil " . ($isRevision ? 'Direvisi' : 'Disimpan') . "</h5>
                            </div>
                            <div class='modal-body text-center'>
                                <i class='bi bi-check-circle-fill success-icon d-block'></i>
                                <h6 class='mt-3'>Data ubinan berhasil " . ($isRevision ? 'direvisi' : 'disimpan') . "!</h6>
                                " . ($isRevision ? "<p class='text-success'><i class='bi bi-info-circle me-2'></i>Revisi selesai - Data siap untuk review ulang</p>" : "") . "
                                
                                <div class='calculation-info text-start'>
                                    <strong><i class='bi bi-calculator me-2'></i>Hasil Perhitungan:</strong><br>
                                    <small>
                                        • Berat Plot: <strong>" . number_format($berat_plot, 2) . " kg</strong><br>
                                        • GKP (Gabah Kering Panen): <strong>" . number_format($gkp, 2) . " ku/ha</strong><br>
                                        • GKG (Gabah Kering Giling): <strong>" . number_format($gkg, 2) . " ku/ha</strong><br>
                                        • KU (Kualitas Utama): <strong>" . number_format($ku, 2) . " ku/ha</strong>
                                    </small>
                                </div>
                                
                                <p class='text-muted'>Terima kasih telah mengisi data dengan lengkap.</p>
                            </div>
                            <div class='modal-footer justify-content-center'>
                                <a href='dashboard_user.php' class='btn btn-primary'>
                                    <i class='bi bi-arrow-left me-2'></i>Kembali ke Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    setTimeout(function() { window.location.href = 'dashboard_user.php'; }, 5000);
                </script>
            </body>
            </html>";
        } else {
            throw new Exception("Gagal menyimpan ke database: " . mysqli_error($conn));
        }

        mysqli_stmt_close($stmt);
        
    } catch (Exception $e) {
        echo "<script>
            alert('Upload foto gagal! " . addslashes($e->getMessage()) . "');
            window.history.back();
        </script>";
        exit;
    }
}

// PERBAIKAN: Fungsi upload foto yang mendukung revisi
function uploadFotoRevisi($inputName, $folderName, $oldPhoto = '', $isRevision = false) {
    try {
        // Cek apakah ada file baru yang diupload
        $hasNewFile = isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] !== UPLOAD_ERR_NO_FILE;
        
        // Jika tidak ada file baru
        if (!$hasNewFile) {
            if ($isRevision && !empty($oldPhoto)) {
                // Revisi tanpa upload baru: gunakan foto lama
                return $oldPhoto;
            } else {
                // Data baru tanpa upload: error
                throw new Exception("File $inputName harus diupload");
            }
        }

        // Ada file baru: proses upload
        return uploadFoto($inputName, $folderName);
        
    } catch (Exception $e) {
        error_log("Upload revisi error for $inputName: " . $e->getMessage());
        
        // Jika error upload tapi ini revisi dan ada foto lama, gunakan foto lama
        if ($isRevision && !empty($oldPhoto)) {
            return $oldPhoto;
        }
        
        return false;
    }
}

// Fungsi upload foto original (tidak berubah)
function uploadFoto($inputName, $folderName) {
    try {
        $targetDir = __DIR__ . "/../../uploads/$folderName/";
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0777, true)) {
                throw new Exception("Gagal membuat direktori upload untuk $folderName");
            }
        }

        // Cek apakah file ada dan tidak ada error
        if (!isset($_FILES[$inputName])) {
            throw new Exception("File $inputName tidak ditemukan");
        }

        $file = $_FILES[$inputName];
        
        // Cek error upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error_messages = [
                UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi upload_max_filesize)',
                UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi MAX_FILE_SIZE)',
                UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
                UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload',
                UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ada',
                UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
                UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh extension PHP'
            ];
            
            $error_msg = $error_messages[$file['error']] ?? 'Error upload tidak dikenal';
            throw new Exception("Error upload $inputName: " . $error_msg);
        }

        $fileTmpPath = $file['tmp_name'];
        $fileName = basename($file['name']);
        $fileSize = $file['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExt = array("jpg", "jpeg", "png");

        // Validasi ekstensi file
        if (!in_array($fileExt, $allowedExt)) {
            throw new Exception("Format file $inputName tidak diizinkan. Hanya JPG, JPEG, dan PNG yang diperbolehkan");
        }

        // Validasi ukuran file (5MB)
        if ($fileSize > 5 * 1024 * 1024) {
            throw new Exception("File $inputName terlalu besar. Maksimal 5MB. Ukuran file: " . number_format($fileSize / 1024 / 1024, 2) . "MB");
        }

        // Validasi apakah benar-benar file gambar
        $imageInfo = getimagesize($fileTmpPath);
        if (!$imageInfo) {
            throw new Exception("File $inputName bukan file gambar yang valid");
        }

        // Generate nama file baru supaya unik
        $newFileName = uniqid($inputName . "_") . ".jpg";
        $targetFilePath = $targetDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
            return "uploads/$folderName/" . $newFileName;
        } else {
            throw new Exception("Gagal memindahkan file $inputName ke direktori tujuan");
        }
        
    } catch (Exception $e) {
        error_log("Upload error for $inputName: " . $e->getMessage());
        return false;
    }
}
?>
