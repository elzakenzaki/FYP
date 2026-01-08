<?php
// MESTI BARIS PERTAMA
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fail: report_markas.php - Laporan untuk Pegawai Markas

include 'db_connect.php'; 

// ==========================================================
// KAWALAN AKSES (Hanya untuk Pegawai Markas)
// ==========================================================
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'markas') {
    set_session_message("Akses ditolak. Sila log masuk sebagai Pegawai Markas.", 'error');
    header("Location: Login.php");
    exit();
}

$user_role = $_SESSION['role'];
$full_name = $_SESSION['full_name'];
$markas_id = $_SESSION['markas_id']; // Dapatkan ID Markas pengguna
$dashboard_link = "dashboard_markas.php"; 

$message = ''; $message_type = '';
$report_data = [];
$total_approved_amount = 0.00;


// Nilai lalai untuk tapisan laporan
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// ==========================================================
// LOGIK 1: AMBIL DATA REKOD YANG DITAPIS
// ==========================================================

$sql_report = "
    SELECT 
        application_id, full_name, no_kp_tentera, status, amount, total_amount_approved, created_at, approved_date, markas_notes
    FROM applications 
    WHERE markas_id = ? 
    AND status IN ('Diluluskan', 'Ditolak') 
    AND created_at >= ? AND created_at <= DATE_ADD(?, INTERVAL 1 DAY)
    ORDER BY created_at DESC
";

try {
    $stmt_report = $conn->prepare($sql_report);
    $stmt_report->bind_param("sss", $markas_id, $start_date, $end_date);
    $stmt_report->execute();
    $result = $stmt_report->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $report_data[] = $row;
            if ($row['status'] === 'Diluluskan') {
                $total_approved_amount += $row['total_amount_approved'];
            }
        }
    }
    $stmt_report->close();
    
} catch (Exception $e) {
    $message = "❌ Ralat Pangkalan Data: " . $e->getMessage();
    $message_type = 'error';
}

// Fungsi untuk menjana badge status
function get_status_badge_report($status) {
    switch ($status) {
        case 'Diluluskan': $class = 'status-diluluskan'; break;
        case 'Ditolak': $class = 'status-ditolak'; break;
        default: $class = 'status-semakan-markas'; break;
    }
    return "<span class='status-badge $class'>" . htmlspecialchars($status) . "</span>";
}

// Ambil mesej sesi jika ada (jika datang dari skrip eksport)
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message'], $_SESSION['message_type']);
}

// JANGAN ADA TAG PENUTUP PHP DI SINI
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Markas <?php echo htmlspecialchars($markas_id); ?> - E-Zakat ATM</title>
    <link rel="stylesheet" href="ui_styles.php"> 
    <style>
        .status-badge { padding: 5px 10px; border-radius: 4px; font-size: 0.9em; font-weight: bold; }
        .status-diluluskan { background-color: var(--color-primary); color: white; }
        .status-ditolak { background-color: var(--color-danger); color: white; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <img src="logo_atm.png" alt="Logo ATM" class="logo">
            <div class="title" style="color: var(--color-primary);">E-ZAKAT ATM (MARKAS)</div>
        </div>
        <div class="nav-menu">
            <a href="<?php echo $dashboard_link; ?>" class="dashboard-link">← DASHBOARD UTAMA</a>
        </div>
        <div class="header-buttons">
            <a href="manage_account.php" class="urus-link" style="padding: 8px 16px; margin-right: 10px; background-color: var(--color-light); color: var(--color-dark); border: 1px solid var(--color-secondary); border-radius: 5px;">URUS AKAUN</a>
            <span class="user-info"><?php echo htmlspecialchars($full_name); ?> (<?php echo htmlspecialchars($markas_id); ?>)</span>
            <a href="logout.php" class="btn-keluar">KELUAR</a>
        </div>
    </div>
    
    <div class="content">
        <h1 style="color: var(--color-dark); text-align: center;">Laporan Keputusan Permohonan Markas</h1>
        <p style="text-align: center; color: #6c757d; margin-bottom: 30px;">Rekod permohonan yang telah diselesaikan (Diluluskan/Ditolak) oleh Markas <?php echo htmlspecialchars($markas_id); ?>.</p>
        
        <div style="max-width: 1200px; margin: 0 auto;">
            
            <?php if (!empty($message)): ?>
                <div class="message-box message-<?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="dashboard-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px;">
                <div class="card" style="border-top: 5px solid var(--color-primary);">
                    <p style="font-size: 1.1em; color: #6c757d;">Jumlah Permohonan Diselesaikan (Dalam Julat)</p>
                    <h2 style="color: var(--color-primary); margin-top: 10px;"><?php echo count($report_data); ?></h2>
                </div>
                <div class="card" style="border-top: 5px solid var(--color-secondary);">
                    <p style="font-size: 1.1em; color: #6c757d;">Amaun Diluluskan (Oleh Markas)</p>
                    <h2 style="color: var(--color-secondary); margin-top: 10px;">RM <?php echo number_format($total_approved_amount, 2); ?></h2>
                </div>
            </div>

            <div class="card" style="margin-bottom: 30px;">
                <h3>Tapisan Laporan</h3>
                <form action="report_markas.php" method="GET">
                    <div class="application-form-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                        <div class="form-group"><label>Tarikh Mula (Permohonan):</label><input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" required></div>
                        <div class="form-group"><label>Tarikh Akhir (Permohonan):</label><input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" required></div>
                        <div class="form-group" style="display: flex; align-items: flex-end;">
                             <button type="submit" class="btn btn-secondary" style="padding: 12px 25px; width: 100%;">TAMPILKAN REKOD</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="card">
                <h3>Rekod Permohonan Diselesaikan</h3>
                
                <?php if (count($report_data) > 0): ?>
                    <a href="export_report_markas.php?start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>" class="btn btn-primary" style="margin-bottom: 15px;">⬇️ Muat Turun CSV Laporan</a>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID Permohonan</th>
                                <th>Tarikh Hantar</th>
                                <th>Pemohon</th>
                                <th>Status</th>
                                <th>Dipohon (RM)</th>
                                <th>Diluluskan Markas (RM)</th>
                                <th>Nota Markas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report_data as $row): ?>
                                <tr>
                                    <td><?php echo $row['application_id']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                    <td><?php echo get_status_badge_report($row['status']); ?></td>
                                    <td>RM <?php echo number_format($row['amount'], 2); ?></td>
                                    <td>RM <?php echo number_format($row['total_amount_approved'], 2); ?></td>
                                    <td><?php echo htmlspecialchars(substr($row['markas_notes'], 0, 50)) . (strlen($row['markas_notes']) > 50 ? '...' : ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="message-box message-warning">Tiada rekod permohonan diselesaikan (Diluluskan/Ditolak) dalam julat tarikh yang dipilih.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?> 
</body>
</html>