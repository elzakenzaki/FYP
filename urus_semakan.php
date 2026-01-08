<?php
// MESTI BARIS PERTAMA
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fail: urus_semakan_markas.php - Untuk Pegawai Markas

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
$markas_id = $_SESSION['markas_id'];
$dashboard_link = "dashboard_markas.php"; 
$message = ''; $message_type = '';

// ==========================================================
// LOGIK POST: KEMASKINI STATUS / SEMAKAN MARKAS
// ==========================================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $app_id = $_POST['application_id'] ?? 0;
    $action_type = $_POST['action']; // Contoh: 'lulus_markas', 'tolak_markas'
    $notes = $_POST['notes'] ?? ''; 
    $amount_approved = $_POST['amount_approved'] ?? 0;

    // Tentukan status dan peringkat seterusnya
    $new_status = '';
    
    // Logik Had Kelulusan Markas (Contoh: RM1500)
    // Nilai ini sepatutnya diambil dari jadual 'settings' (MAX_AMOUNT_MARKAS)
    $MAX_AMOUNT_MARKAS = 1500.00; 

    if ($action_type === 'lulus_markas') {
        if ($amount_approved > $MAX_AMOUNT_MARKAS) {
            // Jika kelulusan melebihi had, hantar ke KAGAT untuk Kelulusan Akhir
            $new_status = 'Semakan KAGAT';
        } else {
            // Jika dalam had, Markas boleh meluluskan terus (Kelulusan Akhir Markas)
            $new_status = 'Diluluskan'; 
        }
    } elseif ($action_type === 'tolak_markas') {
        $new_status = 'Ditolak';
    }

    if ($new_status) {
        $sql_update = "UPDATE applications SET status = ?, markas_notes = ?, total_amount_approved = ? WHERE application_id = ? AND markas_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssdis", $new_status, $notes, $amount_approved, $app_id, $markas_id);

        if ($stmt_update->execute()) {
            if ($new_status === 'Diluluskan') {
                $message = "✅ Permohonan ID {$app_id} telah **DILULUSKAN** (Akhir).";
            } elseif ($new_status === 'Semakan KAGAT') {
                $message = "✅ Permohonan ID {$app_id} dihantar ke **KAGAT** untuk Kelulusan Akhir.";
            } else {
                $message = "❌ Permohonan ID {$app_id} telah **DITOLAK** di peringkat Markas.";
            }
            $message_type = 'success';
        } else {
            $message = "❌ Gagal mengemas kini status permohonan ID {$app_id}. Ralat: " . $stmt_update->error;
            $message_type = 'error';
        }
        $stmt_update->close();
    }
}

// ==========================================================
// LOGIK GET: AMBIL SENARAI PERMOHONAN UNTUK MARKAS INI
// ==========================================================
$filter_status = $_GET['filter'] ?? 'pending'; // Default: Permohonan yang memerlukan tindakan

if ($filter_status === 'pending') {
    // Permohonan yang baru dihantar dan memerlukan Semakan Markas
    $where_clause = "AND status = 'Dalam Proses'"; 
    $view_title = 'Menunggu Semakan Awal';
} elseif ($filter_status === 'all') {
    // Semua permohonan (Tolak, Lulus, Proses)
    $where_clause = "";
    $view_title = 'Semua Permohonan';
} else {
    // Cth: 'reviewed' (sudah dihantar ke KAGAT)
    $where_clause = "AND status != 'Dalam Proses' AND status != 'Ditolak'";
    $view_title = 'Sudah Disemak / Diproses';
}

$sql_applications = "
    SELECT application_id, full_name, no_kp_tentera, amount, status, created_at
    FROM applications 
    WHERE markas_id = ? {$where_clause}
    ORDER BY created_at DESC
";
$stmt_app = $conn->prepare($sql_applications);
$stmt_app->bind_param("s", $markas_id);
$stmt_app->execute();
$applications_result = $stmt_app->get_result();
$stmt_app->close();


// Fungsi untuk menjana badge status (Sama seperti status.php)
function get_status_badge($status) {
    switch ($status) {
        case 'Diluluskan': $class = 'status-diluluskan'; break;
        case 'Ditolak': $class = 'status-ditolak'; break;
        case 'Semakan KAGAT': $class = 'status-semakan-kagat'; break;
        case 'Dalam Proses':
        default: $class = 'status-semakan-markas'; break;
    }
    return "<span class='status-badge $class'>" . htmlspecialchars($status) . "</span>";
}
// JANGAN ADA TAG PENUTUP PHP DI SINI
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urus Semakan Markas - E-Zakat ATM</title>
    <link rel="stylesheet" href="ui_styles.php"> 
    <style>
        .filter-nav a { padding: 8px 15px; margin-right: 10px; border-radius: 5px; text-decoration: none; font-weight: 600; }
        .filter-nav .active { background-color: var(--color-primary); color: white; }
        .filter-nav .inactive { background-color: #e9ecef; color: var(--color-dark); }
        .status-badge { padding: 5px 10px; border-radius: 4px; font-size: 0.9em; font-weight: bold; }
        .status-semakan-markas { background-color: var(--color-secondary); color: white; }
        .status-semakan-kagat { background-color: var(--color-warning); color: var(--color-dark); }
        .status-diluluskan { background-color: var(--color-primary); color: white; }
        .status-ditolak { background-color: var(--color-danger); color: white; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <img src="logo_atm.png" alt="Logo ATM" class="logo">
            <div class="title" style="color: var(--color-warning);">E-ZAKAT ATM (MARKAS)</div>
        </div>
        <div class="nav-menu">
            <a href="<?php echo $dashboard_link; ?>" class="dashboard-link">← DASHBOARD UTAMA</a>
        </div>
        <div class="header-buttons">
            <span class="user-info"><?php echo htmlspecialchars($full_name); ?> (<?php echo htmlspecialchars($markas_id); ?>)</span>
            <a href="logout.php" class="btn-keluar">KELUAR</a>
        </div>
    </div>
    
    <div class="content">
        <h1 style="color: var(--color-dark); text-align: center;">Urus Semakan Permohonan Zakat</h1>
        <p style="text-align: center; color: #6c757d; margin-bottom: 30px;">Senarai Permohonan dari Markas **<?php echo htmlspecialchars($markas_id); ?>** (<?php echo $view_title; ?>).</p>
        
        <div class="statistic-card" style="max-width: 1200px; margin: 0 auto; padding: 30px;">
            
            <?php if (!empty($message)): ?>
                <div class="message-box message-<?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="filter-nav" style="margin-bottom: 20px;">
                <a href="urus_semakan_markas.php?filter=pending" class="<?php echo ($filter_status === 'pending' ? 'active' : 'inactive'); ?>">Menunggu Semakan (<?php echo $applications_result->num_rows; ?>)</a>
                <a href="urus_semakan_markas.php?filter=reviewed" class="<?php echo ($filter_status === 'reviewed' ? 'active' : 'inactive'); ?>">Sudah Diproses</a>
                <a href="urus_semakan_markas.php?filter=all" class="<?php echo ($filter_status === 'all' ? 'active' : 'inactive'); ?>">Semua Permohonan</a>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tarikh Hantar</th>
                        <th>Pemohon</th>
                        <th>ID Tentera</th>
                        <th>Jumlah Dipohon (RM)</th>
                        <th>Status</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($applications_result->num_rows > 0): ?>
                        <?php while ($app = $applications_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $app['application_id']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($app['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($app['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($app['no_kp_tentera']); ?></td>
                                <td>RM <?php echo number_format($app['amount'], 2); ?></td>
                                <td><?php echo get_status_badge($app['status']); ?></td>
                                <td class="action-cell">
                                    <?php if ($app['status'] === 'Dalam Proses'): ?>
                                        <a href="review_application.php?id=<?php echo $app['application_id']; ?>" class="btn btn-primary" style="padding: 5px 10px;">SEMAK / LULUS</a>
                                    <?php else: ?>
                                        <a href="review_application.php?id=<?php echo $app['application_id']; ?>" class="btn-view">LIHAT</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">Tiada permohonan dalam kategori ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
    </div>
    <?php include 'footer.php'; ?> 
</body>
</html>