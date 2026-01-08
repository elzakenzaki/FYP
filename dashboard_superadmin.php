<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php'; 

// Kawalan Akses: Hanya Superadmin dibenarkan
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: Login.php");
    exit();
}

// AMBIL DATA STATISTIK KESELURUHAN (MASA NYATA)
$sql_stats = "SELECT 
    COUNT(*) as jumlah,
    SUM(CASE WHEN status = 'Diluluskan' THEN 1 ELSE 0 END) as lulus,
    SUM(CASE WHEN status = 'Ditolak' THEN 1 ELSE 0 END) as tolak,
    SUM(CASE WHEN status = 'Semakan KAGAT' OR status = 'Dalam Proses' THEN 1 ELSE 0 END) as proses
FROM applications";
$stats = $conn->query($sql_stats)->fetch_assoc();

include 'header.php'; 
?>

<style>
    /* Hero Section */
    .admin-hero { background: #1a1a1a; color: white; padding: 50px 0; margin-bottom: 60px; border-bottom: 5px solid #ffc107; text-align: center; }
    
    /* Grid 4 Kolum untuk Statistik */
    .stats-container { max-width: 1200px; margin: -80px auto 40px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; padding: 0 20px; }
    .stat-box { background: white; padding: 25px; border-radius: 15px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-bottom: 5px solid #ffc107; }
    .stat-val { font-size: 2.2rem; font-weight: 900; color: #333; }
    .stat-label { font-size: 0.8rem; font-weight: 700; color: #888; text-transform: uppercase; }
    
    /* Grid untuk Kad Menu Utama */
    .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; max-width: 1200px; margin: 0 auto; padding: 0 20px 100px; }
    .card-admin { background: white; border-radius: 12px; padding: 30px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: 0.3s; border-top: 5px solid #ffc107; display: flex; flex-direction: column; height: 100%; }
    .card-admin:hover { transform: translateY(-8px); box-shadow: 0 12px 30px rgba(0,0,0,0.1); }
    .btn-admin { margin-top: auto; background: #ffc107; color: #000; padding: 12px; border-radius: 50px; text-decoration: none; font-weight: 800; font-size: 11px; text-transform: uppercase; transition: 0.3s; }
    .btn-admin:hover { opacity: 0.8; transform: scale(1.05); }
</style>

<div class="admin-hero">
    <div class="container">
        <h2 style="margin:0; font-weight: 900;">PANEL KAWALAN UTAMA: <span style="color:#ffc107;">SUPERADMIN</span></h2>
        <p style="opacity: 0.8; margin-top: 5px;">Selamat Datang, <?php echo htmlspecialchars($_SESSION['full_name']); ?>.</p>
    </div>
</div>

<div class="stats-container">
    <div class="stat-box">
        <div class="stat-val"><?php echo $stats['jumlah'] ?? 0; ?></div>
        <div class="stat-label">Jumlah Permohonan</div>
    </div>
    <div class="stat-box" style="border-color:#28a745;">
        <div class="stat-val" style="color:#28a745;"><?php echo $stats['lulus'] ?? 0; ?></div>
        <div class="stat-label">Diluluskan</div>
    </div>
    <div class="stat-box" style="border-color:#dc3545;">
        <div class="stat-val" style="color:#dc3545;"><?php echo $stats['tolak'] ?? 0; ?></div>
        <div class="stat-label">Ditolak</div>
    </div>
    <div class="stat-box" style="border-color:#007bff;">
        <div class="stat-val" style="color:#007bff;"><?php echo $stats['proses'] ?? 0; ?></div>
        <div class="stat-label">Semakan KAGAT</div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card-admin" style="border-top-color: #007bff;">
        <i class="fas fa-hand-holding-heart" style="font-size:2.5rem; color:#007bff; margin-bottom:15px;"></i>
        <h3 style="font-weight: 800;">Urus Permohonan</h3>
        <p style="font-size: 13px; color: #666; margin-bottom: 25px;">Lihat, semak dan berikan kelulusan akhir.</p>
        <a href="urus_kelulusan.php" class="btn-admin" style="background: #007bff; color: white;">Buka Permohonan</a>
    </div>

    <div class="card-admin" style="border-top-color: #28a745;">
        <i class="fas fa-user-check" style="font-size:2.5rem; color:#28a745; margin-bottom:15px;"></i>
        <h3 style="font-weight: 800;">Pengesahan Akaun</h3>
        <p style="font-size: 13px; color: #666; margin-bottom: 25px;">Sahkan pendaftaran akaun anggota baru.</p>
        <a href="verify_users.php" class="btn-admin" style="background: #28a745; color: white;">Sahkan Pengguna</a>
    </div>

    <div class="card-admin">
        <i class="fas fa-user-shield" style="font-size:2.5rem; margin-bottom:15px;"></i>
        <h3 style="font-weight: 800;">Daftar Staf</h3>
        <p style="font-size: 13px; color: #666; margin-bottom: 25px;">Tambah akaun Admin dan Pegawai Markas.</p>
        <a href="register_staf.php" class="btn-admin">Daftar Staf Baru</a>
    </div>

    <div class="card-admin" style="border-top-color: #6f42c1;">
        <i class="fas fa-users-cog" style="font-size:2.5rem; color:#6f42c1; margin-bottom:15px;"></i>
        <h3 style="font-weight: 800;">Manage Users</h3>
        <p style="font-size: 13px; color: #666; margin-bottom: 25px;">Urus, edit atau padam maklumat pengguna berdaftar.</p>
        <a href="manage_users.php" class="btn-admin" style="background: #6f42c1; color: white;">Urus Pengguna</a>
    </div>
</div>

<?php include 'footer.php'; ?>