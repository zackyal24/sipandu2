<?php
session_start();
include '../../config/koneksi.php';

// Cek login PML
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pml') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $note = htmlspecialchars($_POST['note']);
    $revised_by = $_SESSION['username'];
    
    // Cek apakah ID valid
    if (empty($id) || !is_numeric($id)) {
        echo "<script>
            alert('ID tidak valid!');
            window.history.back();
        </script>";
        exit;
    }
    
    // Cek apakah note tidak kosong
    if (empty(trim($_POST['note']))) {
        echo "<script>
            alert('Catatan revisi tidak boleh kosong!');
            window.history.back();
        </script>";
        exit;
    }
    
    // Update status ke 'revisi' dan simpan note
    $query = "UPDATE monitoring_data_panen 
              SET status = 'revisi', 
                  note_revisi = ?, 
                  revised_at = NOW(),
                  revised_by = ?
              WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    
    // Cek apakah prepare berhasil
    if (!$stmt) {
        echo "<script>
            alert('Error database: " . mysqli_error($conn) . "');
            window.history.back();
        </script>";
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, "ssi", $note, $revised_by, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Revisi Berhasil Dikirim</title>
            <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap' rel='stylesheet'>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css' rel='stylesheet'>
            <style>
                body {
                    font-family: 'Poppins', sans-serif;
                    background: #f0f4f8;
                }
                
                .modal-content {
                    border-radius: 15px;
                    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                    border: none;
                }
                
                .modal-header {
                    background: linear-gradient(135deg, #ffc107, #ff8f00);
                    color: white;
                    border-radius: 15px 15px 0 0;
                    border-bottom: none;
                    padding: 1.5rem;
                }
                
                .modal-title {
                    font-weight: 600;
                    font-size: 1.25rem;
                }
                
                .modal-body {
                    padding: 2rem;
                    text-align: center;
                }
                
                .modal-body h6 {
                    font-weight: 600;
                    color: #2c3e50;
                    margin-bottom: 0.5rem;
                }
                
                .modal-body p {
                    color: #6c757d;
                    font-size: 0.95rem;
                }
                
                .modal-footer {
                    border-top: none;
                    padding: 0 2rem 2rem 2rem;
                    justify-content: center;
                }
                
                .btn-primary {
                    background: linear-gradient(135deg, #007bff, #0056b3);
                    border: none;
                    border-radius: 8px;
                    padding: 0.75rem 2rem;
                    font-weight: 600;
                    font-size: 1rem;
                    transition: all 0.3s ease;
                    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
                }
                
                .btn-primary:hover {
                    background: linear-gradient(135deg, #0056b3, #004085);
                    transform: translateY(-2px);
                    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
                }
                
                .warning-icon {
                    color: #ffc107;
                    font-size: 4rem;
                    margin-bottom: 1rem;
                    text-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
                }
                
                /* Responsive */
                @media (max-width: 576px) {
                    .modal-dialog {
                        margin: 1rem;
                    }
                    
                    .modal-body {
                        padding: 1.5rem;
                    }
                    
                    .modal-footer {
                        padding: 0 1.5rem 1.5rem 1.5rem;
                    }
                    
                    .btn-primary {
                        padding: 0.6rem 1.5rem;
                        font-size: 0.9rem;
                    }
                    
                    .warning-icon {
                        font-size: 3rem;
                    }
                    
                    .modal-title {
                        font-size: 1.1rem;
                    }
                }
            </style>
        </head>
        <body>
            <div class='modal fade show' id='successModal' tabindex='-1' aria-labelledby='successModalLabel' style='display: block; background-color: rgba(0,0,0,0.5);'>
                <div class='modal-dialog modal-dialog-centered'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title' id='successModalLabel'>
                                <i class='bi bi-exclamation-triangle me-2'></i>Revisi Dikirim
                            </h5>
                        </div>
                        <div class='modal-body'>
                            <i class='bi bi-exclamation-triangle-fill warning-icon d-block'></i>
                            <h6>Revisi berhasil dikirim ke PCL!</h6>
                            <p class='mb-0'>PCL akan menerima notifikasi untuk memperbaiki data sesuai catatan yang Anda berikan.</p>
                        </div>
                        <div class='modal-footer'>
                            <a href='dashboard.php' class='btn btn-primary'>
                                <i class='bi bi-arrow-left me-2'></i>Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
            <script>
                // Auto redirect setelah 3 detik jika user tidak klik button
                setTimeout(function() {
                    window.location.href = 'dashboard.php';
                }, 5000);
                
                // Prevent backdrop click to close modal
                document.addEventListener('click', function(e) {
                    if (e.target.classList.contains('modal')) {
                        e.preventDefault();
                    }
                });
            </script>
        </body>
        </html>";
    } else {
        echo "<script>
            alert('Gagal mengirim revisi: " . mysqli_error($conn) . "');
            window.history.back();
        </script>";
    }
    
    mysqli_stmt_close($stmt);
} else {
    header("Location: dashboard.php");
    exit;
}
?>