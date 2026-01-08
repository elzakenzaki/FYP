<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php';

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
$is_superadmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'superadmin');

define('SECRET_KEY', 'KAGAT_2026');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (($_POST['secret_key'] ?? '') !== SECRET_KEY) {
        $_SESSION['message'] = "Secret Key Pengesahan Salah!";
        $_SESSION['message_type'] = "error";
    } else {
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (full_name, email, password, no_kp_tentera, role, markas_id, initial_setup) VALUES (?, ?, ?, ?, ?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $_POST['full_name'], $_POST['email'], $pass, $_POST['no_kp_tentera'], $_POST['role'], $_POST['markas_id']);
        
        if ($stmt->execute()) { 
            $_SESSION['message'] = "Akaun staf berjaya didaftarkan!";
            $_SESSION['message_type'] = "success";
            header("Location: Login.php"); 
            exit(); 
        } else {
            $_SESSION['message'] = "Gagal mendaftar. ID mungkin telah wujud.";
            $_SESSION['message_type'] = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8"><title>Daftar Staf - E-ZAKAT ATM</title>
    <link rel="stylesheet" href="ui_styles.php">
    <style>
        body { background: #f8f9fa; margin: 0; font-family: 'Inter', sans-serif; }
        .header-nav { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .staf-card { max-width: 500px; margin: 40px auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-top: 6px solid #28a745; }
        
        /* Style untuk Label */
        .form-group { text-align: left; margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 700; margin-bottom: 5px; color: #64748b; font-size: 11px; padding-left: 15px; text-transform: uppercase; }
        
        .form-control { width: 100%; padding: 12px 20px; border-radius: 30px; border: 1px solid #eee; margin-bottom: 5px; box-sizing: border-box; background: #f9f9f9; }
        .btn-pill-green { width: 100%; padding: 15px; border-radius: 50px; background: #28a745; color: white; border: none; font-weight: bold; cursor: pointer; margin-top: 10px; }
    </style>
</head>
<body>
    <header class="header-nav">
        <div style="display: flex; align-items: center; gap: 12px;">
            <img src="logo_atm.png" alt="Logo" style="height: 50px;">
            <span style="font-weight: 800; color: #28a745; font-size: 22px;">E-ZAKAT ATM</span>
        </div>
        <div>
            <?php if ($is_admin): ?><a href="dashboard_kagat.php" style="background: #64748b; color: white; padding: 10px 20px; border-radius: 50px; text-decoration: none; margin-right: 10px;">KEMBALI KE DASHBOARD</a><?php endif; ?>
            <?php if ($is_superadmin): ?><a href="dashboard_superadmin.php" style="background: #1e7e34; color: white; padding: 10px 20px; border-radius: 50px; text-decoration: none; margin-right: 10px;">KEMBALI KE DASHBOARD</a><?php endif; ?>
        </div>
    </header>

    <div class="staf-card">
        <h2 style="text-align: center; color: #28a745; margin-bottom: 25px;">DAFTAR STAF</h2>
        <form method="POST">
            
            <div class="form-group">
                <label>Pengesan (Security)</label>
                <input type="password" name="secret_key" class="form-control" placeholder="SECRET KEY PENGESAHAN" required>
            </div>

            <div class="form-group">
                <label>Maklumat Peribadi</label>
                <input type="text" name="full_name" class="form-control" placeholder="NAMA PENUH PEGAWAI" required>
            </div>

            <div class="form-group">
                <label>Alamat Emel Rasmi</label>
                <input type="email" name="email" class="form-control" placeholder="CONTOH: PEGAWAI@MOD.GOV.MY" required>
            </div>

            <div class="form-group">
                <label>ID Login (No. Tentera/Awam)</label>
                <input type="text" name="no_kp_tentera" class="form-control" placeholder="ID LOGIN PEGAWAI" required>
            </div>

            <div class="form-group">
                <label>Peranan Akaun (Role)</label>
                <select name="role" class="form-control" required>
                    <option value="admin">Pegawai KAGAT (Admin)</option>
                    <option value="markas">Pegawai Markas</option>
                </select>
            </div>

            <div class="form-group">
                <label>Keselamatan (Password)</label>
                <input type="password" name="password" class="form-control" placeholder="KATA LALUAN" required>
            </div>

            <button type="submit" class="btn-pill-green">DAFTAR AKAUN STAF</button>
        </form>
    </div>
<?php include 'footer.php'; ?>