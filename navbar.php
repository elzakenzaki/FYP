<?php
// navbar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once 'db_connect.php';

// Tentukan pautan dashboard secara dinamik mengikut peranan pengguna
$dashboard_url = "Homepage.php"; 
if (isset($_SESSION['role'])) {
    $dashboard_url = "dashboard_" . $_SESSION['role'] . ".php";
}
?>

<style>
    .header {
        background: #ffffff;
        padding: 12px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 4px solid var(--color-primary);
    }

    .logo-section { display: flex; align-items: center; gap: 15px; }
    .logo-section img { height: 50px; width: auto; }
    .header-title { font-size: 20px; font-weight: 800; color: var(--color-primary); letter-spacing: 1px; }

    .nav-menu { display: flex; gap: 12px; align-items: center; }

    /* Gaya Butang Pil Premium mengikut rujukan imej */
    .nav-btn {
        padding: 8px 22px;
        border-radius: 30px; /* Melengkung penuh */
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        border: none;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        cursor: pointer;
    }

    .nav-btn-green { background-color: var(--color-primary); color: white; }
    .nav-btn-blue { background-color: var(--color-secondary); color: white; }
    .nav-btn-orange { background-color: var(--color-warning); color: var(--color-dark); }
    .nav-btn-red { background-color: var(--color-danger); color: white; }

    .nav-btn:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 6px 12px rgba(0,0,0,0.15); 
        filter: brightness(1.1); 
    }

    .user-info-box {
        margin-left: 15px;
        padding-left: 15px;
        border-left: 2px solid #eee;
        display: flex;
        align-items: center;
    }
</style>

<header class="header">
    <div class="logo-section">
        <img src="logo_atm.png" alt="Logo ATM">
        <span class="header-title">E-ZAKAT ATM</span>
    </div>

    <div class="nav-menu">
        <a href="<?php echo $dashboard_url; ?>" class="nav-btn nav-btn-green">← DASHBOARD</a>
        <a href="manage_account.php" class="nav-btn nav-btn-blue">URUS AKAUN</a>
        <a href="aduan.php" class="nav-btn nav-btn-orange">PUSAT ADUAN</a>
        
        <div class="user-info-box">
            <?php if(isset($_SESSION['full_name'])): ?>
                <span style="font-weight: 700; font-size: 13px; margin-right: 15px; color: var(--color-dark);">
                    <?php echo strtoupper(htmlspecialchars($_SESSION['full_name'])); ?>
                </span>
            <?php endif; ?>
            <a href="logout.php" class="nav-btn nav-btn-red" style="padding: 8px 15px;">KELUAR</a>
        </div>
    </div>
</header>