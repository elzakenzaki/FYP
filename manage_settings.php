<?php
// MESTI BARIS PERTAMA
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fail: manage_settings.php

include 'db_connect.php'; 

// Akses: Hanya untuk Admin KAGAT dan Super Admin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    header("Location: Login.php");
    exit();
}

$user_role = $_SESSION['role'];
// Tentukan pautan dashboard secara dinamik, dengan override untuk role admin
$dashboard_link = ($user_role === 'admin') ? "dashboard_kagat.php" : "dashboard_{$user_role}.php"; 
$message = ''; $message_type = '';

// ==========================================================
// LOGIK: AMBIL SEMUA TETAPAN DARI JADUAL 'settings'
// ==========================================================
$settings_data = [];
$sql_fetch = "SELECT setting_key, setting_value, description FROM settings";
$result_fetch = $conn->query($sql_fetch);

if ($result_fetch && $result_fetch->num_rows > 0) {
    while ($row = $result_fetch->fetch_assoc()) {
        $settings_data[$row['setting_key']] = $row;
    }
}

// ==========================================================
// LOGIK POST: KEMASKINI TETAPAN
// ==========================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $success_count = 0;
    $error_occurred = false;

    // Loop melalui semua input POST yang mungkin merupakan setting
    foreach ($_POST as $key => $value) {
        if (array_key_exists($key, $settings_data)) {
            $sql_update = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ss", $value, $key);
            
            if ($stmt_update->execute()) {
                $success_count++;
            } else {
                $error_occurred = true;
            }
            $stmt_update->close();
        }
    }

    if ($success_count > 0 && !$error_occurred) {
        set_session_message("✅ Tetapan sistem berjaya dikemaskini ({$success_count} item).", 'success');
        header("Location: manage_settings.php");
        exit();
    } elseif ($error_occurred) {
        $message = "❌ Gagal mengemaskini beberapa tetapan. Sila cuba lagi.";
        $message_type = 'error';
    } else {
        $message = "Tiada perubahan dibuat.";
        $message_type = 'info';
    }
    
    // Refresh data selepas POST
    $result_fetch = $conn->query($sql_fetch);
    $settings_data = [];
    if ($result_fetch && $result_fetch->num_rows > 0) {
        while ($row = $result_fetch->fetch_assoc()) {
            $settings_data[$row['setting_key']] = $row;
        }
    }
}
// TIADA TAG PENUTUP PHP
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urus Tetapan Sistem - E-Zakat ATM</title>
    <link rel="stylesheet" href="ui_styles.php"> 
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <img src="logo_atm.png" alt="Logo ATM" class="logo">
            <div class="title" style="color: var(--color-danger);">E-ZAKAT ATM (ADMIN)</div>
        </div>
        <div class="nav-menu">
            <a href="<?php echo $dashboard_link; ?>" class="dashboard-link">← DASHBOARD UTAMA</a>
        </div>
        <div class="header-buttons">
            <a href="logout.php" class="btn-keluar">KELUAR</a>
        </div>
    </div>
    
    <div class="content">
        <h1 style="color: var(--color-dark); text-align: center;">Urus Tetapan Kritikal Sistem</h1>
        <p style="text-align: center; color: var(--color-danger); margin-bottom: 30px;">Berhati-hati: Perubahan di halaman ini akan mempengaruhi operasi seluruh sistem.</p>
        
        <div class="statistic-card" style="max-width: 800px; margin: 0 auto;">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="message-box message-<?php echo $_SESSION['message_type']; ?>"><?php echo $_SESSION['message']; ?></div>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
            <?php endif; ?>
            <?php if (!empty($message)): ?>
                <div class="message-box message-<?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <form action="manage_settings.php" method="POST">
                
                <div class="card" style="margin-bottom: 30px; border-left: 5px solid var(--color-warning);">
                    <h3 style="color: var(--color-warning);">⚙️ Tetapan Keselamatan & Pendaftaran Staf</h3>
                    <div class="form-group">
                        <label for="SECRET_KEY">Kunci Rahsia Pendaftaran Staf (SECRET KEY):</label>
                        <input type="text" id="SECRET_KEY" name="SECRET_KEY" 
                               value="<?php echo htmlspecialchars($settings_data['SECRET_KEY']['setting_value'] ?? 'KAGAT_2026_UPNM'); ?>" required>
                        <small style="color: #6c757d;">*Kunci ini melindungi halaman `register_staf.php`.</small>
                    </div>
                </div>

                <div class="card" style="margin-bottom: 30px; border-left: 5px solid var(--color-secondary);">
                    <h3 style="color: var(--color-secondary);">💵 Tetapan Kadar Bantuan & Kelulusan</h3>
                    <div class="form-group">
                        <label for="MAX_AMOUNT_USER">Jumlah Maksimum Dipohon Pemohon (RM):</label>
                        <input type="number" id="MAX_AMOUNT_USER" name="MAX_AMOUNT_USER" step="0.01" min="1"
                               value="<?php echo htmlspecialchars($settings_data['MAX_AMOUNT_USER']['setting_value'] ?? '5000.00'); ?>" required>
                        <small style="color: #6c757d;">Jumlah maksimum yang boleh dimasukkan dalam borang permohonan.</small>
                    </div>
                    <div class="form-group">
                        <label for="MAX_AMOUNT_MARKAS">Had Kelulusan Markas Tanpa KAGAT (RM):</label>
                        <input type="number" id="MAX_AMOUNT_MARKAS" name="MAX_AMOUNT_MARKAS" step="0.01" min="1"
                               value="<?php echo htmlspecialchars($settings_data['MAX_AMOUNT_MARKAS']['setting_value'] ?? '1500.00'); ?>" required>
                        <small style="color: #6c757d;">Permohonan melebihi jumlah ini akan dihantar secara automatik ke KAGAT.</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">SIMPAN SEMUA PERUBAHAN</button>
            </form>
        </div>
    </div>
    
    <?php include 'footer.php'; ?> 
</body>
</html>