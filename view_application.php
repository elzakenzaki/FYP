<?php
include 'db_connect.php';
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'markas', 'superadmin'])) { header("Location: Login.php"); exit(); }
if (!isset($_GET['id'])) { header("Location: manage_applications.php"); exit(); }

$application_id = $_GET['id'];
$user_role = $_SESSION['role'];

// Query untuk mendapatkan data penuh (Gunakan JOIN)
$sql = "SELECT a.*, u.full_name as applicant_name, m.full_name as markas_reviewer_name, k.full_name as kagat_reviewer_name
        FROM applications a 
        JOIN users u ON a.user_id = u.user_id 
        LEFT JOIN users m ON a.markas_reviewer_id = m.user_id
        LEFT JOIN users k ON a.kagat_reviewer_id = k.user_id
        WHERE a.application_id = ?";
$stmt = $conn->prepare($sql); $stmt->bind_param("i", $application_id); $stmt->execute();
$app_data = $stmt->get_result()->fetch_assoc(); $stmt->close();
if (!$app_data) { die("Permohonan tidak ditemui."); }

// Logik Kebenaran Akses Tindakan
$can_markas_act = ($user_role === 'markas' && $app_data['status'] === 'Dalam Proses');
$can_admin_act = ($user_role === 'admin' && $app_data['status'] === 'Semakan KAGAT');
?>
<!DOCTYPE html>
<html lang="ms">
<head><title>Semak Permohonan #<?php echo $application_id; ?></title><link rel="stylesheet" href="ui_styles.php"></head>
<body>
    <div class="header"></div>
    <div class="content">
        <h1>Butiran Permohonan #<?php echo $application_id; ?></h1>
        <a href="manage_applications.php" class="btn btn-secondary" style="margin-bottom: 20px;">← Kembali ke Senarai</a>

        <div class="card">
            <h2>Butiran Pemohon</h2>
            <h2>Dokumen Sokongan</h2>
            <p><a href="<?php echo $app_data['tandatangan_path']; ?>" target="_blank" class="btn-view">Lihat Tandatangan</a></p>
        </div>

        <div class="card">
            <h2>PROSES KELULUSAN (Peranan Anda: <?php echo strtoupper($user_role); ?>)</h2>
            
            <?php if ($can_markas_act): ?>
                <form action="update_status.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $application_id; ?>">
                    <input type="hidden" name="action_role" value="markas">
                    <div class="form-group"><label>Ulasan Markas (Wajib):</label><textarea name="comment" required></textarea></div>
                    <div class="action-buttons">
                        <button type="submit" name="status" value="Semakan KAGAT" class="btn btn-primary" style="background-color: var(--color-warning); color: var(--color-dark);">Lulus Semakan & Majukan ke KAGAT</button>
                        <button type="submit" name="status" value="Ditolak" class="btn btn-danger">Tolak Permohonan (Awal)</button>
                    </div>
                </form>
            <?php elseif ($can_admin_act): ?>
                 <form action="update_status.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $application_id; ?>">
                    <input type="hidden" name="action_role" value="admin">
                    <div class="form-group"><label>Ulasan KAGAT (Wajib):</label><textarea name="comment" required></textarea></div>
                    <div class="action-buttons">
                        <button type="submit" name="status" value="Diluluskan" class="btn btn-primary">LULUS AKHIR</button>
                        <button type="submit" name="status" value="Ditolak" class="btn btn-danger">TOLAK AKHIR</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="message-box message-info">Status permohonan ini adalah **<?php echo $app_data['status']; ?>**. Tiada tindakan diperlukan pada peringkat ini.</div>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>