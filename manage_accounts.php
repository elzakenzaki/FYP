<?php
// Fail: manage_account.php
include 'db_connect.php'; 

// Akses: Memerlukan sebarang log masuk
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$current_role = $_SESSION['role'];
$message = ''; $message_type = '';

// 1. Dapatkan data pengguna semasa dari DB
$sql_fetch = "SELECT full_name, email, no_kp_tentera, markas_id FROM users WHERE user_id = ?";
$stmt_fetch = $conn->prepare($sql_fetch);
$stmt_fetch->bind_param("i", $user_id);
$stmt_fetch->execute();
$user = $stmt_fetch->get_result()->fetch_assoc();
$stmt_fetch->close();

// ==========================================================
// LOGIK POST: KEMASKINI PROFIL (Nama & E-mel)
// ==========================================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $full_name_new = $_POST['full_name_new'] ?? $user['full_name'];
    $email_new = $_POST['email_new'] ?? $user['email'];

    $sql_update = "UPDATE users SET full_name = ?, email = ? WHERE user_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssi", $full_name_new, $email_new, $user_id);

    if ($stmt_update->execute()) {
        $_SESSION['full_name'] = $full_name_new; // Kemas kini sesi
        $message = "✅ Butiran profil berjaya dikemaskini.";
        $message_type = 'success';
        $user['full_name'] = $full_name_new; // Refresh data paparan
        $user['email'] = $email_new;
    } else {
        $message = "❌ Gagal mengemaskini profil. E-mel mungkin sudah digunakan. Ralat: " . $stmt_update->error;
        $message_type = 'error';
    }
    $stmt_update->close();
}

// ==========================================================
// LOGIK POST: TUKAR KATA LALUAN
// ==========================================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // 1. Dapatkan hash kata laluan semasa untuk pengesahan
    $sql_check_pw = "SELECT password FROM users WHERE user_id = ?";
    $stmt_check_pw = $conn->prepare($sql_check_pw);
    $stmt_check_pw->bind_param("i", $user_id);
    $stmt_check_pw->execute();
    $result_pw = $stmt_check_pw->get_result()->fetch_assoc();
    $stmt_check_pw->close();
    $hashed_current_password = $result_pw['password'] ?? '';

    // 2. Pengesahan
    if (!password_verify($current_password, $hashed_current_password)) {
        $message = "❌ Kata Laluan Semasa salah.";
        $message_type = 'error';
    } elseif ($new_password !== $confirm_password) {
        $message = "❌ Kata Laluan Baharu tidak sepadan.";
        $message_type = 'error';
    } elseif (strlen($new_password) < 6) {
        $message = "❌ Kata Laluan Baharu mesti sekurang-kurangnya 6 aksara.";
        $message_type = 'error';
    } else {
        // 3. Kemas kini kata laluan
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql_update_pw = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt_update_pw = $conn->prepare($sql_update_pw);
        $stmt_update_pw->bind_param("si", $hashed_new_password, $user_id);
        
        if ($stmt_update_pw->execute()) {
            $message = "✅ Kata Laluan berjaya ditukar. Sila gunakan kata laluan baharu untuk log masuk seterusnya.";
            $message_type = 'success';
        } else {
            $message = "❌ Gagal menukar kata laluan. Sila cuba lagi.";
            $message_type = 'error';
        }
        $stmt_update_pw->close();
    }
}

$dashboard_link = "dashboard_{$current_role}.php"; // Tentukan pautan kembali
// JANGAN ADA TAG PENUTUP PHP DI SINI
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urus Akaun - E-Zakat ATM</title>
    <link rel="stylesheet" href="ui_styles.php"> 
</head>
<body>
    <div class="header">
        <div class="nav-menu">
            <a href="<?php echo $dashboard_link; ?>" class="btn btn-secondary">← DASHBOARD</a>
        </div>
        </div>
    <div class="content">
        <h1 style="color: var(--color-dark); text-align: center;">Urus Akaun & Tetapan Keselamatan</h1>
        
        <div class="statistic-card" style="max-width: 700px; margin: 30px auto;">
            <?php if (!empty($message)): ?>
                <div class="message-box message-<?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="card" style="margin-bottom: 30px;">
                <h3>Kemaskini Butiran Profil</h3>
                <form action="manage_account.php" method="POST">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="form-group"><label>Nama Penuh:</label><input type="text" name="full_name_new" value="<?php echo htmlspecialchars($user['full_name']); ?>" required></div>
                    <div class="form-group"><label>E-mel:</label><input type="email" name="email_new" value="<?php echo htmlspecialchars($user['email']); ?>" required></div>
                    <div class="form-group"><label>No. KP/Tentera (ID):</label><input type="text" value="<?php echo htmlspecialchars($user['no_kp_tentera']); ?>" readonly style="background: #e9ecef;"></div>
                    <div class="form-group"><label>Peranan:</label><input type="text" value="<?php echo htmlspecialchars(ucfirst($current_role)); ?>" readonly style="background: #e9ecef;"></div>
                    <?php if ($current_role === 'markas' || $current_role === 'user'): ?>
                        <div class="form-group"><label>Markas:</label><input type="text" value="<?php echo htmlspecialchars($user['markas_id']); ?>" readonly style="background: #e9ecef;"></div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary">SIMPAN PROFIL</button>
                </form>
            </div>
            
            <div class="card" style="border: 1px solid var(--color-danger);">
                <h3 style="color: var(--color-danger);">Tukar Kata Laluan</h3>
                <form action="manage_account.php" method="POST">
                    <input type="hidden" name="action" value="change_password">
                    <div class="form-group"><label>Kata Laluan Semasa:</label><input type="password" name="current_password" required></div>
                    <div class="form-group"><label>Kata Laluan Baharu:</label><input type="password" name="new_password" required></div>
                    <div class="form-group"><label>Sahkan Kata Laluan Baharu:</label><input type="password" name="confirm_password" required></div>
                    <button type="submit" class="btn btn-danger">TUKAR KATA LALUAN</button>
                </form>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?> 
</body>
</html>