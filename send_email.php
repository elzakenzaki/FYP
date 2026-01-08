<?php
// MESTI BARIS PERTAMA
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fail: send_email.php - Fungsi pusat untuk menghantar e-mel

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Gantikan path ini mengikut struktur folder anda!
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// === FUNGSI UTAMA PENGHANTAR E-MEL ===
function send_notification_email($to_email, $to_name, $subject, $body_html) {
    
    $mail = new PHPMailer(true);

    try {
        // Konfigurasi Server SMTP
        $mail->isSMTP();
        $mail->SMTPDebug = 0; // Set kepada 2 untuk debug penuh jika gagal
        
        // GANTIKAN DENGAN BUTIRAN SMTP ANDA YANG SEBENAR
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'zakuan091011@gmail.com'; // GANTIKAN: E-mel Penghantar anda
        $mail->Password   = 'gbgx itnt chpl tolk';    // GANTIKAN: Kata Laluan Aplikasi (App Password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465; 

        // Penerima
        $mail->setFrom('zakuan091011@gmail.com', 'E-ZAKAT ATM NOTIFIKASI'); 
        $mail->addAddress($to_email, $to_name);

        // Kandungan
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body_html;
        $mail->AltBody = strip_tags($body_html); 

        $mail->send();
        return true;
        
    } catch (Exception $e) {
        // Jika penghantaran gagal, output atau log ralat penuh
        echo "PHPMailer Ralat: Gagal menghantar kepada {$to_email}. Ralat Penuh: {$mail->ErrorInfo}";
        error_log("PHPMailer Ralat: {$mail->ErrorInfo}"); // Catat ralat ke log server
        return false;
    }
}
// TIADA TAG PENUTUP PHP