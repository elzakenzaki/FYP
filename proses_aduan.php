<?php
// Aktifkan laporan ralat untuk membantu anda jika berlaku masalah
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php';

if (isset($_POST['submit_aduan'])) {
    // Ambil data dari borang aduan.php
    $nama     = mysqli_real_escape_string($conn, $_POST['nama_pengadu']);
    $kontak   = mysqli_real_escape_string($conn, $_POST['kontak_pengadu']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori_aduan']);
    $mesej    = mysqli_real_escape_string($conn, $_POST['mesej_aduan']);
    $user_id  = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

    // Pastikan nama jadual ialah tbl_aduan
    $sql = "INSERT INTO tbl_aduan (user_id, nama, emel_telefon, kategori, mesej, status, tarikh_hantar) 
            VALUES (?, ?, ?, ?, ?, 'Baru', NOW())";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("issss", $user_id, $nama, $kontak, $kategori, $mesej);

        if ($stmt->execute()) {
            // Berjaya: Papar alert dan kembali ke aduan.php
            echo "<script>
                    alert('Aduan anda telah berjaya dihantar kepada Admin.');
                    window.location.href='aduan.php';
                  </script>";
            exit();
        } else {
            die("Gagal simpan data: " . $stmt->error);
        }
    } else {
        // Jika SQL masih ralat, MySQL akan beritahu puncanya di sini
        die("Ralat SQL (Prepare Failed): " . $conn->error);
    }
} else {
    header("Location: aduan.php");
    exit();
}
?>