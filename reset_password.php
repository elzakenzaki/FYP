<?php
// MESTI BARIS PERTAMA
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fail: reset_password.php

include 'db_connect.php'; 

$message = ''; 
$message_type = '';
$token = $_GET['token'] ?? null;
$user_id_from_token = null; 

// ==========================================================
// LOGIK 1: PENGESAHAN TOKEN
// ==========================================================
if ($token) {
    // Cari pengguna berdasarkan token dan pastikan token belum luput
    $sql_check = "SELECT user_id, full_name, reset_token_expiry FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $token);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    $user = $result->fetch_assoc();
    $stmt_check->close();

    if ($user) {
        $user_id_from_token = $user['user_id'];
        $message = "Sila masukkan Kata Laluan Baharu anda.";
        $message_type = 'info';
    } else {
        $message = "❌ Pautan set semula tidak sah, tidak ditemui, atau telah luput. Sila buat permintaan set semula yang baharu.";
        $message_type = 'error';
        $token = null; // Batalkan pemprosesan POST
    }
} else {
    $message = "❌ Tiada token set semula disediakan.";
    $message_type = 'error';
}


// ==========================================================
// LOGIK 2: KEMASKINI KATA LALUAN (POST REQUEST)
// ==========================================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && $user_id_from_token) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($new_password) || $new_password !== $confirm_password) {
        $message = "❌ Kata Laluan Baharu tidak sepadan atau kosong.";
        $message_type = 'error';
    } elseif (strlen($new_password) < 6) {
        $message = "❌ Kata Laluan Baharu mesti sekurang-kurangnya 6 aksara.";
        $message_type = 'error';
    } else {
        // Berjaya: Kemas kini kata laluan dan padam token
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Padam token dan kemas kini kata laluan
        $sql_update = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE user_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $hashed_password, $user_id_from_token);

        if ($stmt_update->execute()) {
            set_session_message("✅ Kata Laluan anda berjaya diset semula. Sila log masuk.", 'success');
            header("Location: Login.php"); 
            exit();
        } else {
            $message = "❌ Gagal menetapkan kata laluan baharu. Ralat DB: " . $stmt_update->error;
            $message_type = 'error';
        }
        $stmt_update->close();
    }
}
// JANGAN ADA TAG PENUTUP PHP DI SINI
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Semula Kata Laluan - E-Zakat ATM</title>
    <link rel="stylesheet" href="ui_styles.php">
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <img src="logo_atm.png" alt="Logo ATM" class="logo">
            <div class="title">E-ZAKAT ATM</div>
        </div>
        <div class="nav-menu">
            <a href="Login.php" class="btn btn-primary" style="background-color: transparent; color: var(--color-dark);">← LOG MASUK</a>
        </div>
    </div>
    
    <div class="content" style="min-height: 80vh; display: flex; justify-content: center; align-items: center;">
        <div class="card" style="width: 450px; max-width: 100%; padding: 40px; text-align: center;">
            <h2 style="color: var(--color-primary);">SET SEMULA KATA LALUAN</h2>
            
            <?php if (!empty($message)): ?> 
                <div class="message-box message-<?php echo $message_type; ?>"><?php echo $message; ?></div> 
            <?php endif; ?>
            
            <?php if ($user_id_from_token): ?>
                <p style="color: #6c757d; margin-bottom: 25px;">Sila masukkan dan sahkan kata laluan baharu anda.</p>

                <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                    <div class="form-group"><label for="new_password">Kata Laluan Baharu:</label><input type="password" id="new_password" name="new_password" required></div>
                    <div class="form-group"><label for="confirm_password">Sahkan Kata Laluan Baharu:</label><input type="password" id="confirm_password" name="confirm_password" required></div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px; padding: 12px;">TUKAR KATA LALUAN</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'footer.php'; ?> 
</body>
</html>