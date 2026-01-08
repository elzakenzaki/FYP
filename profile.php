<?php
session_start();
include 'db_connect.php';

// Semakan Sesi (Sesiapa yang log masuk boleh lihat profil mereka)
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'] ?? 'user'; // Dapatkan role dari sesi

$final_message = ''; // Ubah nama pembolehubah mesej
$message_type = ''; // Jenis mesej: 'success' atau 'error'

// Dapatkan Maklumat Pengguna (Admin atau Biasa)
$user_sql = "SELECT user_id, full_name, email, no_tentera, no_kad_pengenalan, user_type FROM users WHERE user_id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("s", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_stmt->close();

if (!$user) {
     // Jika pengguna tidak ditemui (jarang berlaku jika sesi wujud)
     session_destroy();
     header("Location: login.php");
     exit();
}

// Proses kemas kini jika borang dihantar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $temp_message = ''; // Mesej sementara
    $temp_error = ''; // Ralat sementara

    // 1. Kemas kini Maklumat Asas
    $new_full_name = $_POST['fullName'] ?? $user['full_name'];
    $new_email = $_POST['email'] ?? $user['email'];

    if ($new_full_name != $user['full_name'] || $new_email != $user['email']) {
        $update_basic_sql = "UPDATE users SET full_name = ?, email = ? WHERE user_id = ?";
        $update_basic_stmt = $conn->prepare($update_basic_sql);
        $update_basic_stmt->bind_param("sss", $new_full_name, $new_email, $user_id);
        if ($update_basic_stmt->execute()) {
            $_SESSION['full_name'] = $new_full_name; // Kemas kini nama dalam sesi
            $user['full_name'] = $new_full_name; // Kemas kini data semasa
            $user['email'] = $new_email;
            $temp_message .= "Maklumat asas berjaya dikemas kini. ";
        } else {
            // Semak jika e-mel sudah wujud
             if ($conn->errno == 1062) {
                 $temp_error .= "E-mel '$new_email' telah digunakan. ";
            } else {
                $temp_error .= "Gagal mengemas kini maklumat asas. ";
            }
        }
        $update_basic_stmt->close();
    }

    // 2. Proses Tukar Kata Laluan (jika diisi)
    $current_password = $_POST['currentPassword'] ?? '';
    $new_password = $_POST['newPassword'] ?? '';
    $confirm_password = $_POST['confirmPassword'] ?? '';

    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        // Hanya proses jika SEMUA medan kata laluan diisi
        if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
            // Dapatkan hash kata laluan semasa dari DB
            $pass_sql = "SELECT password FROM users WHERE user_id = ?";
            $pass_stmt = $conn->prepare($pass_sql);
            $pass_stmt->bind_param("s", $user_id);
            $pass_stmt->execute();
            $pass_result = $pass_stmt->get_result();
            $pass_data = $pass_result->fetch_assoc();
            $pass_stmt->close();

            if ($pass_data && password_verify($current_password, $pass_data['password'])) {
                // Kata laluan semasa betul
                if ($new_password === $confirm_password) {
                    // Kata laluan baru sepadan
                    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_pass_sql = "UPDATE users SET password = ? WHERE user_id = ?";
                    $update_pass_stmt = $conn->prepare($update_pass_sql);
                    $update_pass_stmt->bind_param("ss", $new_hashed_password, $user_id);
                    if ($update_pass_stmt->execute()) {
                        $temp_message .= "Kata laluan berjaya ditukar. ";
                    } else {
                        $temp_error .= "Gagal menukar kata laluan. ";
                    }
                    $update_pass_stmt->close();
                } else {
                    $temp_error .= "Kata laluan baru dan pengesahan tidak sepadan. ";
                }
            } else {
                $temp_error .= "Kata laluan semasa salah. ";
            }
        } else {
            // Jika salah satu medan kata laluan kosong tetapi yang lain diisi
            $temp_error .= "Sila isi semua medan kata laluan (semasa, baru, pengesahan) jika mahu menukar kata laluan. ";
        }
    }
    
    // Tentukan mesej akhir
     if (!empty($temp_error)) {
        $final_message = $temp_error; // Utamakan ralat
        $message_type = 'error';
    } elseif (!empty($temp_message)) {
        $final_message = $temp_message; // Papar mesej berjaya jika tiada ralat
        $message_type = 'success';
    } elseif (empty($temp_message) && empty($temp_error)) {
        // Tiada perubahan dibuat
        $final_message = "Tiada perubahan dikesan.";
        $message_type = 'info'; // Guna gaya neutral
    }

}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Sistem Pengurusan Zakat ATM</title>
    <style>
        /* === SEMUA KOD CSS BERADA DI SINI === */
         * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            color: #fff; line-height: 1.6;
        }
        .header { 
            background: #000; padding: 15px 30px; display: flex;
            justify-content: space-between; align-items: center;
            border-bottom: 3px solid #00ff00;
            box-shadow: 0 2px 10px rgba(0, 255, 0, 0.3);
        }
        .logo-section { display: flex; align-items: center; gap: 15px; }
        .logo { height: 60px; width: auto; }
        .title { 
            color: #00ff00; font-size: 20px; font-weight: bold;
            text-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
        }
        .nav-menu { display: flex; gap: 20px; align-items: center; }
        .nav-menu a { 
            color: #fff; text-decoration: none; padding: 8px 16px;
            border-radius: 5px; transition: all 0.3s ease; font-weight: 500;
        }
        .nav-menu a:hover, .nav-menu a.active { 
            background: #00ff00; color: #000; transform: translateY(-2px);
        }
        .user-menu { display: flex; align-items: center; gap: 15px; }
        .user-info { text-align: right; }
        .user-name { color: #00ff00; font-weight: bold; }
        .user-id { font-size: 12px; color: #ccc; }
        .logout-btn { 
            background: #ff4444; color: #fff; border: none; padding: 8px 16px;
            border-radius: 5px; cursor: pointer; transition: all 0.3s ease;
            font-weight: 500; text-decoration: none; display: inline-block;
        }
        .logout-btn:hover { background: #ff6666; transform: translateY(-2px); }
        .content { padding: 30px; max-width: 800px; margin: 0 auto; } /* Kecilkan max-width untuk profil */
         .content h1 { /* Tambah gaya untuk tajuk utama halaman */
             color: #00ff00; 
             margin-bottom: 20px; 
             text-align: center;
             border-bottom: 2px solid #00ff00;
             padding-bottom: 10px;
        }
        .card { 
            background: linear-gradient(135deg, #111 0%, #222 100%);
            padding: 25px; border-radius: 15px; border: 1px solid #333;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease; margin-bottom: 30px;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 255, 0, 0.2);
            border-color: #00ff00;
        }
        .card h3 { 
            color: #00ff00; margin-bottom: 20px; font-size: 20px;
            border-bottom: 2px solid #00ff00; padding-bottom: 10px;
        }
        /* Gaya untuk borang */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; font-size: 14px; }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%; padding: 10px; background-color: #333; color: white;
            border: 1px solid #555; border-radius: 4px; box-sizing: border-box; font-size: 15px;
        }
         .form-group input[readonly] { background-color: #444; cursor: not-allowed; } /* Gaya untuk medan readonly */
         
         .btn-submit { /* Gaya butang hantar */
             background: linear-gradient(135deg, #00ff00 0%, #00cc00 100%); color: #000;
             padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer;
             font-weight: 600; transition: all 0.3s ease; display: block; width: 100%;
             margin-top: 20px;
         }
         .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 255, 0, 0.4); }

        /* Mesej maklum balas */
         .message-box {
             padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center;
         }
         .message-success { background-color: #28a745; color: white; }
         .message-error { background-color: #dc3545; color: white; }
         .message-info { background-color: #17a2b8; color: white; } /* Gaya neutral */


        /* Footer styles (sama seperti dashboard) */
         .footer { 
            background: #000; padding: 20px 30px; text-align: center;
            border-top: 3px solid #00ff00; margin-top: 50px; color: #888;
        }
        .footer-title { 
            color: #00ff00; font-size: 18px; margin-bottom: 15px; font-weight: bold;
        }
        .footer-logos {
            display: flex; justify-content: center; gap: 20px;
            margin: 15px 0; flex-wrap: wrap;
        }
        .footer-logo { height: 35px; width: auto; opacity: 0.8; transition: opacity 0.3s ease; }
        .footer-logo:hover { opacity: 1; }
        .footer-links { margin: 15px 0; }
        .footer-links a { 
            color: #ccc; text-decoration: none; margin: 0 12px;
            font-size: 12px; transition: color 0.3s ease;
        }
        .footer-links a:hover { color: #00ff00; }
        .footer-info { 
            margin-top: 15px; font-size: 12px; line-height: 1.5;
        }
        .footer-browser { margin-top: 10px; font-size: 11px; }

         @media (max-width: 768px) {
            .header { flex-direction: column; gap: 15px; padding: 15px; }
            .nav-menu { flex-wrap: wrap; justify-content: center; }
            .content { padding: 15px; }
            .content h1 { font-size: 24px; } /* Kecilkan tajuk utama */
         }
        /* === TAMAT SEMUA KOD CSS === */
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <img src="logo_atm.png" alt="Logo ATM" class="logo">
            <div class="title">E-ZAKAT ATM <?php echo ($user_role == 'admin') ? '(PENTADBIR)' : ''; ?></div>
        </div>
        <div class="nav-menu">
            <?php if ($user_role == 'admin'): ?>
                 <a href="admin_dashboard.php">DASHBOARD</a>
                 <a href="manage_applications.php">URUS PERMOHONAN</a> 
                 <a href="manage_users.php">URUS PENGGUNA</a>
                 <a href="profile.php" class="active">PROFIL</a>
             <?php else: ?>
                 <a href="dashboard_user.php">DASHBOARD</a>
                 <a href="apply.php">BORANG PERMOHONAN</a>
                 <a href="status.php">STATUS PERMOHONAN</a>
                 <a href="profile.php" class="active">PROFIL</a>
             <?php endif; ?>
        </div>
        <div class="user-menu">
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                <div class="user-id">ID: <?php echo htmlspecialchars($user['user_id']); ?> <?php echo ($user_role == 'admin') ? '(Admin)' : ''; ?></div>
            </div>
            <a href="logout.php" class="logout-btn">LOG KELUAR</a>
        </div>
    </div>
    
    <div class="content">
        <h1>Profil Saya</h1>

        <?php if (!empty($final_message)): ?>
            <div class="message-box <?php echo ($message_type == 'success') ? 'message-success' : (($message_type == 'error') ? 'message-error' : 'message-info'); ?>">
                <?php echo $final_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="profile.php">
            <div class="card">
                 <h3>Maklumat Asas</h3>
                 <div class="form-group">
                    <label for="userId">User ID:</label>
                    <input type="text" id="userId" name="userId" value="<?php echo htmlspecialchars($user['user_id']); ?>" readonly>
                 </div>
                 <div class="form-group">
                    <label for="fullName">Nama Penuh:</label>
                    <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                 </div>
                 <div class="form-group">
                    <label for="email">E-mel:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                 </div>
                 <div class="form-group">
                    <label>Jenis Pengguna:</label>
                     <input type="text" value="<?php echo ucfirst(htmlspecialchars($user['user_type'])); ?>" readonly>
                 </div>
                 <div class="form-group">
                    <label>ID Pengguna (Tentera/KP):</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['user_type'] == 'anggota' ? $user['no_tentera'] : $user['no_kad_pengenalan']); ?>" readonly>
                 </div>
            </div>

            <div class="card">
                <h3>Tukar Kata Laluan (Isi jika mahu tukar)</h3>
                <div class="form-group">
                    <label for="currentPassword">Kata Laluan Semasa:</label>
                    <input type="password" id="currentPassword" name="currentPassword" placeholder="Masukkan kata laluan semasa anda">
                </div>
                 <div class="form-group">
                    <label for="newPassword">Kata Laluan Baru:</label>
                    <input type="password" id="newPassword" name="newPassword" placeholder="Minimum 6 aksara">
                </div>
                 <div class="form-group">
                    <label for="confirmPassword">Sahkan Kata Laluan Baru:</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Taip semula kata laluan baru">
                </div>
            </div>

            <button type="submit" class="btn-submit">Kemaskini Profil</button>
        </form>
    </div>
    
    <div class="footer">
        <div class="footer-title">SPGATM | JOM MASUK TENTERA | SPPIM | JAKIM</div>
        <div class="footer-logos">
            <img src="logo_mindef.png" alt="Logo MINDEF" class="footer-logo">
            <img src="logo_darat.png" alt="Logo DARAT" class="footer-logo">
            <img src="logo_navy.png" alt="Logo NAVY" class="footer-logo">
            <img src="logo_efos.png" alt="Logo EFOS" class="footer-logo">
            <img src="logo_kagat.png" alt="Logo KAGAT" class="footer-logo">
            <img src="logo_atm.png" alt="Logo ATM" class="footer-logo">
        </div>
        <div class="footer-links">
            <a href="privacy.php">DASAR PRIVASI</a>
            <a href="security.php">DASAR KESELAMATAN</a>
            <a href="disclaimer.php">PENAFIAN</a>
            <a href="copyright.php">NOTIS HAK CIPTA</a>
        </div>
        <div class="footer-info">
            JABATAN ARAH KOR AGAMA ANGKATAN TENTERA (KAGAT) Markas Angkatan Tentera Malaysia,<br>
            Bahagian Perkhidmatan Anggota, Kementerian Pertahanan, Jalan Padang Tembak,<br>
            50634 Kuala Lumpur | Telefon: +6 03 - 4013 1616 | Email: kagat@mod.gov.my
        </div>
        <div class="footer-browser">
            © 2010 - 2025 Unit IT © Selangorunikasi | Paparan Terbaik Versi Terkini Microsoft Edge, Mozilla Firefox, Google Chrome, Safari
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>