<?php
// Fail: email_function.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Pastikan ejaan folder PHPMailer anda tepat (sama ada PHPMailer atau phpmailer)
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// --- KONFIGURASI SMTP GMAIL BARU ---
define('EMAIL_HOST', 'smtp.gmail.com');
define('EMAIL_USERNAME', 'jabatanarahkagat@gmail.com'); // Gantikan dengan emel baru anda
define('EMAIL_PASSWORD', 'gvfr lqer uezo nnor');      // Gantikan dengan App Password 16-digit baru
define('EMAIL_PORT', 587); 
define('EMAIL_FROM', 'noreply@ezakatatm.my');
define('EMAIL_FROM_NAME', 'E-Zakat ATM');

/**
 * Fungsi Utama Penghantaran Emel
 */
function send_ezakat_notification($recipient_email, $type, $data = []) {
    $mail = new PHPMailer(true);
    
    try {
        // Tetapan Server
        $mail->isSMTP();
        $mail->Host = EMAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = EMAIL_USERNAME;
        $mail->Password = EMAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port = EMAIL_PORT;

        // Tetapan Penghantar & Penerima
        $mail->setFrom(EMAIL_USERNAME, EMAIL_FROM_NAME);
        $mail->addAddress($recipient_email);
        $mail->isHTML(true);

        // Tentukan Tajuk dan Kandungan berdasarkan Jenis (Type)
        switch ($type) {
            case 'register':
                $mail->Subject = 'Pendaftaran Akaun Baru - E-Zakat ATM';
                $mail->Body = "<h3>Terima Kasih, " . htmlspecialchars($data['name']) . "</h3>
                               <p>Pendaftaran anda telah diterima. Akaun anda sedang menunggu pengesahan Admin.</p>";
                break;

            case 'approved':
                $mail->Subject = 'Akaun Anda Telah Disahkan - E-Zakat ATM';
                $mail->Body = "<h3>Tahniah, " . htmlspecialchars($data['name']) . "!</h3>
                               <p>Akaun anda telah disahkan. Anda kini boleh log masuk ke dalam sistem.</p>";
                break;

            case 'decision':
                $mail->Subject = 'Keputusan Permohonan Zakat - E-Zakat ATM';
                $status_text = ($data['status'] == 'Lulus') 
                    ? "<b style='color:green;'>LULUS</b>" 
                    : "<b style='color:red;'>GAGAL</b>";
                
                $mail->Body = "<h3>Keputusan Permohonan Zakat</h3>
                               <p>Assalamualaikum " . htmlspecialchars($data['name']) . ",</p>
                               <p>Permohonan zakat anda bagi rujukan " . htmlspecialchars($data['ref_no']) . " telah: $status_text</p>
                               <p>Sila log masuk ke dashboard untuk maklumat lanjut dan ulasan rasmi.</p>
                               <br><p>Terima kasih,<br>Pentadbiran E-Zakat ATM</p>";
                break;

            case 'reset_password':
                $mail->Subject = 'Tetapan Semula Kata Laluan - E-Zakat ATM';
                $mail->Body = "<h3>Permintaan Kata Laluan Baru</h3>
                               <p>Sila klik pautan di bawah untuk menetapkan semula kata laluan anda:</p>
                               <p><a href='" . $data['reset_link'] . "'>Klik Sini untuk Reset Password</a></p>";
                break;
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log ralat jika perlu untuk tujuan debugging
        error_log("Ralat PHPMailer: " . $mail->ErrorInfo);
        return false;
    }
}