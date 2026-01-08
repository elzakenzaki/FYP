<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php'; 

// Kawalan Akses: Hanya Pemohon (User)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: Login.php");
    exit();
}

$u_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

include 'header.php'; // Header tanpa butang Laman Utama & Aduan
?>

<style>
    :root {
        --primary-green: #28a745;
        --shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    .dashboard-wrapper {
        max-width: 1100px;
        margin: 40px auto;
        padding: 0 20px;
    }

    /* Welcome Header */
    .welcome-banner {
        margin-bottom: 40px;
        text-align: left;
    }
    .welcome-banner h1 {
        font-size: 32px;
        font-weight: 800;
        color: #333;
        margin-bottom: 10px;
    }
    .welcome-banner p {
        color: #666;
        font-size: 16px;
    }

    /* Layout Utama Dashboard User */
    .main-content-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 30px;
    }

    /* Card Permohonan Besar - DIPERBETULKAN WARNA TEKS */
    .apply-card-large {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        padding: 60px 40px;
        border-radius: 24px;
        color: #ffffff !important; /* Paksa teks menjadi putih */
        text-align: center;
        box-shadow: 0 15px 35px rgba(40, 167, 69, 0.25);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    
    .apply-card-large h2 { 
        font-size: 30px; 
        margin-bottom: 15px; 
        font-weight: 800; 
        color: #ffffff !important; /* Tajuk putih terang */
    }
    
    .apply-card-large p { 
        font-size: 16px; 
        margin-bottom: 30px; 
        color: #ffffff !important; /* Penerangan putih */
        opacity: 0.9;
        max-width: 400px;
        line-height: 1.6;
    }

    .btn-action-white {
        display: inline-block;
        background: white;
        color: #1e7e34 !important;
        padding: 15px 40px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 800;
        font-size: 16px;
        transition: 0.3s;
        text-transform: uppercase;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .btn-action-white:hover { 
        transform: scale(1.05); 
        box-shadow: 0 8px 20px rgba(0,0,0,0.2); 
    }

    /* Card Info & Profil */
    .side-info-card {
        background: white;
        padding: 30px;
        border-radius: 24px;
        box-shadow: var(--shadow);
        border: 1px solid #eee;
    }
    .side-info-card h4 {
        color: #333;
        margin-bottom: 20px;
        font-size: 18px;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }
    
    .quick-link-item {
        display: flex;
        align-items: center;
        padding: 18px;
        margin-bottom: 15px;
        background: #f8f9fa;
        border-radius: 12px;
        text-decoration: none;
        color: #444;
        font-weight: 700;
        transition: 0.3s;
    }
    .quick-link-item:hover { 
        background: #e9ecef; 
        color: var(--primary-green);
        padding-left: 25px;
    }
    
    .notice-box {
        background: #fff9db;
        border-left: 5px solid #ffc107;
        padding: 15px;
        border-radius: 8px;
        font-size: 13px;
        color: #856404;
        margin-top: 25px;
        line-height: 1.5;
    }
</style>

<div class="dashboard-wrapper">
    <div class="welcome-banner">
        <h1>Selamat Datang, <?php echo htmlspecialchars($full_name); ?>!</h1>
        <p>Anda boleh memulakan permohonan zakat atau menyemak status permohonan di sini.</p>
    </div>

    <div class="main-content-grid">
        <div class="apply-card-large">
            <h2>Mula Permohonan Zakat</h2>
            <p>Sila pastikan anda telah menyediakan dokumen sokongan yang diperlukan sebelum mengisi borang.</p>
            <a href="apply.php" class="btn-action-white">MOHON SEKARANG →</a>
        </div>

        <div class="side-info-card">
            <h4>PENGURUSAN AKAUN</h4>
            
            <a href="manage_account.php" class="quick-link-item">
                <span style="margin-right:12px;">👤</span> Kemaskini Profil Saya
            </a>
            <a href="status.php" class="quick-link-item">
                <span style="margin-right:12px;">📊</span> Semak Status Permohonan
            </a>

            <div class="notice-box">
                <strong>Peringatan Penting:</strong><br>
                Sila pastikan maklumat akaun bank anda adalah tepat bagi tujuan penyaluran bantuan sekiranya lulus.
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>