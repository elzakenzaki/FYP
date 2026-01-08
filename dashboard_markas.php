<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php'; 

// 1. Kawalan Akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'markas') {
    header("Location: Login.php");
    exit();
}

// Ambil nama markas dan buang ruang kosong yang tidak kelihatan
$current_markas = trim($_SESSION['markas_id'] ?? '');

// 2. LOGIK AJAX REAL-TIME (Dibaiki untuk kepastian data muncul)
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    
    // Gunakan LIKE %...% supaya 'Markas Latihan' tetap dikesan walaupun sesi anda 'Latihan' sahaja
    $search_term = "%" . mysqli_real_escape_string($conn, $current_markas) . "%";
    
    $sql_stats = "SELECT 
        COUNT(*) as jumlah,
        SUM(CASE WHEN status = 'Diluluskan' THEN 1 ELSE 0 END) as lulus,
        SUM(CASE WHEN status = 'Ditolak' THEN 1 ELSE 0 END) as tolak,
        SUM(CASE WHEN status IN ('Baru', 'Dalam Proses', 'Semakan KAGAT', 'Menunggu Pengesahan') THEN 1 ELSE 0 END) as proses
    FROM applications 
    WHERE markas_id LIKE '$search_term'"; // Logik LIKE untuk fleksibiliti
    
    $res = $conn->query($sql_stats);
    $stats = ($res) ? $res->fetch_assoc() : ['jumlah'=>0, 'lulus'=>0, 'tolak'=>0, 'proses'=>0];
    
    // Pastikan nilai NULL ditukar ke 0 sebelum dihantar ke JavaScript
    foreach($stats as $key => $val) { if($val === null) $stats[$key] = 0; }
    
    echo json_encode($stats);
    exit();
}

include 'header.php'; 
?>

<style>
    /* Styling "Sebiji-sebiji" seperti Dashboard KAGAT */
    .hero-banner { background: white; padding: 40px 0; border-bottom: 1px solid #eee; margin-bottom: 70px; text-align: center; }
    .stats-row { max-width: 1200px; margin: -100px auto 50px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; padding: 0 20px; }
    .stat-kagat { background: white; padding: 20px; border-radius: 15px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-bottom: 4px solid #eee; }
    .stat-value { font-size: 2rem; font-weight: 900; color: #333; display: block; }
    .stat-label { font-size: 11px; font-weight: 800; color: #888; text-transform: uppercase; letter-spacing: 1px; }
    .card-grid-kagat { max-width: 1200px; margin: 0 auto 100px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; padding: 0 20px; }
    .card-kagat { background: white; border-radius: 30px; padding: 50px 30px; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.03); border-top: 10px solid #007bff; transition: 0.3s; display: flex; flex-direction: column; height: 100%; }
    .card-kagat:hover { transform: translateY(-10px); }
    .btn-kagat-main { margin-top: auto; display: inline-block; padding: 15px 40px; background: #007bff; color: white; text-decoration: none; border-radius: 50px; font-weight: 800; font-size: 11px; text-transform: uppercase; }
</style>

<div class="hero-banner">
    <h1 style="font-weight: 900; color: #1a1a1a; text-transform: uppercase;">
        DASHBOARD PEGAWAI MARKAS <span style="color: #007bff;"><?php echo htmlspecialchars($current_markas); ?></span>
    </h1>
    <p style="color: #666;">Selamat Datang, Pegawai Unit. Pantau permohonan secara masa nyata.</p>
</div>

<div class="stats-row">
    <div class="stat-kagat" style="border-bottom-color: #007bff;">
        <span id="stat_jumlah" class="stat-value">0</span>
        <span class="stat-label">Jumlah Kes</span>
    </div>
    <div class="stat-kagat" style="border-bottom-color: #28a745;">
        <span id="stat_lulus" class="stat-value" style="color: #28a745;">0</span>
        <span class="stat-label">Diluluskan</span>
    </div>
    <div class="stat-kagat" style="border-bottom-color: #dc3545;">
        <span id="stat_tolak" class="stat-value" style="color: #dc3545;">0</span>
        <span class="stat-label">Ditolak</span>
    </div>
    <div class="stat-kagat" style="border-bottom-color: #ffc107;">
        <span id="stat_proses" class="stat-value" style="color: #ffc107;">0</span>
        <span class="stat-label">Dalam Semakan</span>
    </div>
</div>

<div class="card-grid-kagat">
    <div class="card-kagat" style="border-top-color: #28a745;">
        <div style="font-size: 3rem; color: #28a745; margin-bottom: 20px;"><i class="fas fa-user-check"></i></div>
        <h3 style="font-weight: 800;">Pengesahan Akaun</h3>
        <p style="color: #888; font-size: 14px; margin-bottom: 30px;">Sahkan pendaftaran akaun anggota ATM baru sebelum mereka dibenarkan login.</p>
        <a href="verify_users.php" class="btn-kagat-main" style="background: #28a745;">Mula Pengesahan</a>
    </div>

    <div class="card-kagat">
        <div style="font-size: 3rem; color: #007bff; margin-bottom: 20px;"><i class="fas fa-check-double"></i></div>
        <h3 style="font-weight: 800;">Kelulusan Akhir</h3>
        <p style="color: #888; font-size: 14px; margin-bottom: 30px;">Semak permohonan zakat yang dihantar oleh anggota unit di bawah seliaan anda.</p>
        <a href="urus_kelulusan.php" class="btn-kagat-main">Akses Kelulusan</a>
    </div>

    <div class="card-kagat" style="border-top-color: #dc3545;">
        <div style="font-size: 3rem; color: #dc3545; margin-bottom: 20px;"><i class="fas fa-file-pdf"></i></div>
        <h3 style="font-weight: 800;">Laporan Unit</h3>
        <p style="color: #888; font-size: 14px; margin-bottom: 30px;">Jana dan muat turun laporan permohonan unit dalam format PDF.</p>
        <a href="generate_report.php" class="btn-kagat-main" style="background: #dc3545;">Jana Laporan</a>
    </div>
</div>

<script>
// Fungsi Refresh Masa Nyata
function refreshStats() {
    fetch('dashboard_markas.php?ajax=1')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            // Update angka secara dinamik
            document.getElementById('stat_jumlah').innerText = data.jumlah;
            document.getElementById('stat_lulus').innerText = data.lulus;
            document.getElementById('stat_tolak').innerText = data.tolak;
            document.getElementById('stat_proses').innerText = data.proses;
        })
        .catch(err => console.error("Ralat Refresh:", err));
}

// Jalankan setiap 3 saat untuk kesan real-time yang pantas
setInterval(refreshStats, 3000);
refreshStats();
</script>

<?php include 'footer.php'; ?>