<?php
session_start();
include 'db_connect.php';

// Set header untuk memastikan encoding betul
header('Content-Type: text/html; charset=UTF-8');

$verification_status = "";
$message = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Semak token dalam database
    $sql = "SELECT * FROM users WHERE verification_token = ? AND verified = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
        
        // Update status pengesahan
        $update_sql = "UPDATE users SET verified = 1, verification_token = NULL WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $user_id);
        
        if ($update_stmt->execute()) {
            $verification_status = "success";
            $message = "Akaun anda telah berjaya disahkan. Anda kini boleh log masuk ke sistem.";
        } else {
            $verification_status = "error";
            $message = "Ralat teknikal. Sila cuba lagi atau hubungi admin.";
        }
        
        $update_stmt->close();
    } else {
        $verification_status = "error";
        $message = "Token pengesahan tidak sah atau telah digunakan.";
    }
    
    $stmt->close();
} else {
    $verification_status = "error";
    $message = "Tiada token pengesahan dibekalkan.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengesahan Akaun - Sistem Pengurusan Zakat ATM</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background-color: #000; 
            color: #fff; 
        }
        .header { 
            background-color: #000; 
            padding: 10px 20px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 2px solid #f00; 
        }
        .logo-section { 
            display: flex; 
            align-items: center; 
        }
        .logo { 
            height: 50px; 
            margin-right: 10px; 
        }
        .title { 
            color: #0f0; 
            font-size: 20px; 
        }
        .nav-menu { 
            display: flex; 
            align-items: center; 
        }
        .nav-menu a { 
            color: #fff; 
            text-decoration: none; 
            margin: 0 15px; 
            font-size: 14px; 
        }
        .content { 
            padding: 50px 20px; 
            background: url('background-pattern.jpg') repeat; 
            text-align: center; 
            min-height: 60vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .verification-container { 
            max-width: 600px; 
            margin: 0 auto; 
            background-color: #333; 
            padding: 30px; 
            border: 2px solid #0f0; 
            border-radius: 5px; 
            text-align: center;
        }
        .verification-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        .success { color: #0f0; }
        .error { color: #f00; }
        .verification-message {
            font-size: 18px;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .verification-button {
            padding: 12px 25px;
            background-color: #0f0;
            color: #000;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .verification-button:hover {
            background-color: #0c0;
        }
        .footer { 
            background-color: #000; 
            padding: 10px 20px; 
            text-align: center; 
            border-top: 2px solid #f00; 
            font-size: 12px; 
        }
        .footer .stats { 
            display: flex; 
            justify-content: center; 
            gap: 20px; 
            margin-bottom: 10px; 
        }
        .footer .stats span { 
            display: flex; 
            align-items: center; 
        }
        .footer .stats img { 
            height: 20px; 
            margin-right: 5px; 
        }
        .footer .title { 
            font-size: 16px; 
        }
        .footer .links a { 
            color: #fff; 
            text-decoration: none; 
            margin: 0 10px; 
        }
        .footer .info { 
            margin-top: 10px; 
        }
        .footer .browser { 
            margin-top: 5px; 
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <img src="logo_atm.png" alt="Logo ATM" class="logo">
            <div class="title">LAMAN WEB RASMI SISTEM PENGURUSAN ZAKAT ATM</div>
        </div>
        <div class="nav-menu">
            <a href="index.php">LAMAN UTAMA</a>
            <a href="#">MENGENAL KAMI</a>
            <a href="#">DAIE ASKARI</a>
            <a href="#">INFORMASI</a>
            <a href="#">E-KAGAT</a>
            <a href="#">HUBUNGI KAMI</a>
            <a href="#">PETA LAMAN</a>
        </div>
    </div>
    
    <div class="content">
        <div class="verification-container">
            <?php if ($verification_status == "success"): ?>
                <div class="verification-icon success">✓</div>
                <h2>Pengesahan Berjaya!</h2>
                <p class="verification-message"><?php echo $message; ?></p>
                <a href="login.php" class="verification-button">Log Masuk Sekarang</a>
            <?php else: ?>
                <div class="verification-icon error">✗</div>
                <h2>Pengesahan Gagal</h2>
                <p class="verification-message"><?php echo $message; ?></p>
                <a href="register.php" class="verification-button">Daftar Akaun Baru</a>
                <a href="contact.php" class="verification-button" style="background-color: #555; margin-left: 10px;">Hubungi Sokongan</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="footer">
        <div class="stats">
            <span><img src="hari_ini_icon.png" alt="Hari Ini"> Hari Ini: 281</span>
            <span><img src="minggu_ini_icon.png" alt="Minggu Ini"> Minggu Ini: 477</span>
            <span><img src="bulan_ini_icon.png" alt="Bulan Ini"> Bulan Ini: 477</span>
            <span><img src="keseluruhan_icon.png" alt="Keseluruhan"> Keseluruhan: 206749</span>
        </div>
        <div class="title">SPGATM | JOM MASUK TENTERA | SPPIM | JAKIM</div>
        <div class="logos">
            <img src="logo_mindef.png" alt="Logo MINDEF" style="height: 30px;">
            <img src="logo_darat.png" alt="Logo DARAT" style="height: 30px;">
            <img src="logo_navy.png" alt="Logo NAVY" style="height: 30px;">
            <img src="logo_efos.png" alt="Logo EFOS" style="height: 30px;">
            <img src="logo_kagat.png" alt="Logo KAGAT" style="height: 30px;">
            <img src="logo_atm.png" alt="Logo ATM" style="height: 30px;">
        </div>
        <div class="links">
            <a href="#">DASAR PRIVASI</a>
            <a href="#">DASAR KESELAMATAN</a>
            <a href="#">PENAFIAN</a>
            <a href="#">NOTIS HAK CIPTA</a>
        </div>
        <div class="info">
            JABATAN ARAH KOR AGAMA ANGKATAN TENTERA (KAGAT) Markas Angkatan Tentera Malaysia, Bahagian Perkhidmatan Anggota,
            Kementerian Pertahanan, Jalan Padang Tembak, 50634 Kuala Lumpur | Telefon: +6 03 - 4013 1616 | Email: kagat@mod.gov.my
        </div>
        <div class="browser">
            © 2010 - 2025 Unit IT © Selangorunikasi | Paparan Terbaik Versi Terkini Microsoft Edge, Mozilla Firefox, Google Chrome, Safari
        </div>
    </div>
</body>
</html>