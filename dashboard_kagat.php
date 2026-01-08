<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

// STATISTIK KAGAT
$sql_stats = "SELECT 
    COUNT(*) as jumlah,
    SUM(CASE WHEN status = 'Diluluskan' THEN 1 ELSE 0 END) as lulus,
    SUM(CASE WHEN status = 'Ditolak' THEN 1 ELSE 0 END) as tolak,
    SUM(CASE WHEN status = 'Semakan KAGAT' THEN 1 ELSE 0 END) as proses
FROM applications";
$stats = $conn->query($sql_stats)->fetch_assoc();

include 'header.php'; 
?>

<style>
    .hero-banner { background: white; padding: 40px 0; border-bottom: 1px solid #eee; margin-bottom: 70px; text-align: center; }
    /* Grid 4 Kolum untuk Statistik */
    .stats-row { max-width: 1200px; margin: -100px auto 50px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; padding: 0 20px; }
    .stat-kagat { background: white; padding: 20px; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-left: 5px solid #007bff; }
    /* Grid 3 Kolum untuk Menu */
    .menu-grid { max-width: 1200px; margin: 0 auto; padding: 0 20px 100px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
    .card-kagat { background: white; border-radius: 15px; padding: 35px; text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.05); transition: 0.3s; display: flex; flex-direction: column; border-top: 5px solid #007bff; height: 100%; }
    .card-kagat:hover { transform: translateY(-8px); }
    .btn-kagat-main { margin-top: auto; background: #007bff; color: white; padding: 12px; border-radius: 50px; text-decoration: none; font-weight: 800; font-size: 11px; text-transform: uppercase; }
</style>

<div class="hero-banner">
    <h2>DASHBOARD PUSAT <span style="color:#007bff;">KAGAT</span></h2>
    <p>Pengurusan Kelulusan Zakat & Pengesahan Akaun Anggota.</p>
</div>

<div class="stats-row">
    <div class="stat-kagat">
        <h2 style="margin:0; font-weight:900; color:#007bff;"><?php echo $stats['jumlah']; ?></h2>
        <small style="font-weight:700; color:#888;">JUMLAH PERMOHONAN</small>
    </div>
    <div class="stat-kagat" style="border-left-color:#28a745;">
        <h2 style="margin:0; font-weight:900; color:#28a745;"><?php echo $stats['lulus']; ?></h2>
        <small style="font-weight:700; color:#888;">TELAH DILULUSKAN</small>
    </div>
    <div class="stat-kagat" style="border-left-color:#dc3545;">
        <h2 style="margin:0; font-weight:900; color:#dc3545;"><?php echo $stats['tolak']; ?></h2>
        <small style="font-weight:700; color:#888;">DITOLAK</small>
    </div>
    <div class="stat-kagat" style="border-left-color:#ffc107;">
        <h2 style="margin:0; font-weight:900; color:#ffc107;"><?php echo $stats['proses']; ?></h2>
        <small style="font-weight:700; color:#888;">MENUNGGU SEMAKAN</small>
    </div>
</div>

<div class="menu-grid">
    <div class="card-kagat" style="border-top-color: #28a745;">
        <div style="font-size: 3rem; color: #28a745; margin-bottom: 20px;"><i class="fas fa-user-check"></i></div>
        <h3 style="font-weight: 800;">Pengesahan Akaun</h3>
        <p style="color: #888; font-size: 14px; margin-bottom: 30px; line-height: 1.6;">Sahkan pendaftaran akaun anggota ATM baru sebelum mereka dibenarkan login.</p>
        <a href="verify_users.php" class="btn-kagat-main" style="background: #28a745;">Mula Pengesahan</a>
    </div>
    <div class="card-kagat">
        <div style="font-size: 3rem; color: #007bff; margin-bottom: 20px;"><i class="fas fa-check-double"></i></div>
        <h3 style="font-weight: 800;">Kelulusan Akhir</h3>
        <p style="color: #888; font-size: 14px; margin-bottom: 30px; line-height: 1.6;">Semak permohonan zakat yang dihantar oleh anggota unit di bawah seliaan anda.</p>
        <a href="urus_kelulusan.php" class="btn-kagat-main">Akses Kelulusan</a>
    </div>
    <div class="card-kagat" style="border-top-color: #dc3545;">
        <div style="font-size: 3rem; color: #dc3545; margin-bottom: 20px;"><i class="fas fa-file-pdf"></i></div>
        <h3 style="font-weight: 800;">Laporan PDF</h3>
        <p style="color: #888; font-size: 14px; margin-bottom: 30px; line-height: 1.6;">Jana dan muat turun laporan permohonan unit dalam format PDF.</p>
        <a href="generate_report.php" class="btn-kagat-main" style="background: #dc3545;">Jana Laporan</a>
    </div>
</div>

<?php include 'footer.php'; ?>