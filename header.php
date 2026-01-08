<?php 
// 1. WAJIB: Memastikan sesi dimulakan untuk membaca data pengguna
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
} 
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-green: #28a745; --dark-green: #1a5928; --soft-gray: #f4f7f6; }
        body { background: var(--soft-gray); margin: 0; font-family: 'Inter', sans-serif; display: flex; flex-direction: column; min-height: 100vh; }
        .header-nav { display: flex; justify-content: space-between; align-items: center; padding: 12px 40px; background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 1000; border-bottom: 4px solid var(--primary-green); }
        .nav-container { display: flex; align-items: center; gap: 12px; }
        .btn-pill { text-decoration: none; color: white !important; font-weight: 700; font-size: 11px; padding: 10px 18px; border-radius: 50px; transition: 0.3s; display: flex; align-items: center; gap: 8px; text-transform: uppercase; }
        .btn-green { background: var(--primary-green); }
        .btn-blue { background: #007bff; }
        .btn-red { background: #dc3545; }
        .user-info-text { font-weight: 800; color: #333; font-size: 13px; text-transform: uppercase; margin-right: 5px; }
        .main-content-push { flex: 1 0 auto; } 
    </style>
</head>
<body>
    <header class="header-nav">
        <?php 
            // LOGIK PAUTAN DASHBOARD
            $role = $_SESSION['role'] ?? '';
            if ($role === 'admin') {
                $dash_link = "dashboard_kagat.php"; // Memastikan ke fail kagat
            } else {
                $dash_link = "dashboard_" . $role . ".php";
            }
        ?>

        <div style="display: flex; align-items: center; gap: 12px; cursor:pointer;" onclick="window.location.href='<?php echo $dash_link; ?>'">
            <img src="logo_atm.png" alt="Logo" style="height: 40px;">
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 900; color: var(--primary-green); font-size: 16px; letter-spacing: 1px; line-height: 1;">E-ZAKAT ATM</span>
                <small style="color: #666; font-size: 9px; font-weight: 700; text-transform: uppercase;">Portal <?php echo ($role === 'admin' ? 'KAGAT' : strtoupper($role)); ?></small>
            </div>
        </div>

        <div class="nav-container">
            <?php 
            // 2. SEMAKAN: Hanya tunjuk butang jika user_id wujud dalam sesi
            if(isset($_SESSION['user_id'])): 
            ?>
                <a href="<?php echo $dash_link; ?>" class="btn-pill btn-green">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                
                <a href="manage_account.php" class="btn-pill btn-blue">
                    <i class="fas fa-user-edit"></i> Urus Akaun
                </a>

                <span class="user-info-text"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Profil'); ?></span>
                
                <a href="Logout.php" class="btn-pill btn-red" title="Log Keluar">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            <?php else: ?>
                <a href="Login.php" class="btn-pill btn-green">LOG MASUK</a>
            <?php endif; ?>
        </div>
    </header>
    <div class="main-content-push">