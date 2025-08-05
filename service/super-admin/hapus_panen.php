<?php
session_start();
include '../../config/koneksi.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../index.php");
    exit;
}

// Ambil ID dari parameter URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validasi ID
if ($id > 0) {
    // Ambil data foto sebelum menghapus dari database
    $query_foto = mysqli_query($conn, "SELECT foto_serah_terima, foto_bukti_plot_ubinan, foto_berat_timbangan FROM monitoring_data_panen WHERE id = $id");
    $data_foto = mysqli_fetch_assoc($query_foto);
    
    if ($data_foto) {
        // Hapus data dari database terlebih dahulu
        $query_delete = mysqli_query($conn, "DELETE FROM monitoring_data_panen WHERE id = $id");
        
        if ($query_delete) {
            // Jika berhasil hapus dari database, hapus juga file fotonya
            $foto_list = [
                $data_foto['foto_serah_terima'],
                $data_foto['foto_bukti_plot_ubinan'],
                $data_foto['foto_berat_timbangan']
            ];
            
            foreach ($foto_list as $foto_path) {
                if (!empty($foto_path)) {
                    // Buat path absolut ke file foto
                    $full_path = __DIR__ . "/../../" . $foto_path;
                    
                    // Hapus file jika ada
                    if (file_exists($full_path)) {
                        unlink($full_path);
                    }
                }
            }
            
            // Redirect dengan pesan sukses
            header("Location: monitoring_panen.php?status=success&message=" . urlencode("Data dan foto berhasil dihapus"));
            exit;
        } else {
            echo "<script>
                alert('Gagal menghapus data: " . mysqli_error($conn) . "');
                window.history.back();
            </script>";
        }
    } else {
        echo "<script>
            alert('Data tidak ditemukan!');
            window.history.back();
        </script>";
    }
} else {
    echo "<script>
        alert('ID tidak valid!');
        window.history.back();
    </script>";
}

// Fungsi untuk menghapus file foto (optional - untuk keamanan ekstra)
function hapusFotoAman($foto_path) {
    if (empty($foto_path)) {
        return true;
    }
    
    // Pastikan path dimulai dengan 'uploads/' untuk keamanan
    if (strpos($foto_path, 'uploads/') !== 0) {
        return false;
    }
    
    $full_path = __DIR__ . "/../../" . $foto_path;
    
    // Cek apakah file ada dan dalam folder uploads
    if (file_exists($full_path) && is_file($full_path)) {
        return unlink($full_path);
    }
    
    return true; // File tidak ada, anggap berhasil
}
?>