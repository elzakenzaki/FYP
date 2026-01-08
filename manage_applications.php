<?php
include 'db_connect.php';
// Akses: Hanya Markas, KAGAT, atau Super Admin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'markas', 'superadmin'])) { header("Location: Login.php"); exit(); }

$user_role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$filter_status = $_GET['status'] ?? 'all';
$search_term = $_GET['search'] ?? '';

// ... (LOGIK PENAPISAN DAN QUERY BERDASARKAN PERANAN) ...

// Ini hanya templat, sila salin logik penapisan penuh dari jawapan sebelumnya dan terapkan UI styles.
?>
<!DOCTYPE html>
<html lang="ms">
<head><title>Urus Permohonan - E-Zakat ATM</title><link rel="stylesheet" href="ui_styles.php"></head>
<body>
    <div class="header">
        </div>
    <div class="content">
        <h1>Urus Permohonan Zakat (<?php echo strtoupper($user_role); ?>)</h1>
        
        <div class="filter-section">
            </div>

        <div class="card card-full-width">
            <h3>Senarai Permohonan</h3>
            <?php if (isset($_SESSION['message'])): ?>
                 <div class="message-box message-<?php echo $_SESSION['message_type']; ?>"><?php echo $_SESSION['message']; ?></div>
                 <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
            <?php endif; ?>
             
             <a href="view_application.php?id=..." class="btn-view">Lihat & Urus</a>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>