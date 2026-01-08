<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php';
include 'header.php';

// 1. Kawalan Akses: Benarkan Admin, Markas, dan Superadmin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'markas', 'superadmin'])) {
    header("Location: Login.php");
    exit();
}

$current_role = $_SESSION['role'];
$current_markas = $_SESSION['markas_id'] ?? '';

// --- 2. PROSES PENGAKTIFAN AKAUN ---
if (isset($_GET['activate_id'])) {
    $uid = mysqli_real_escape_string($conn, $_GET['activate_id']);
    
    // Set initial_setup = 1 bermaksud akaun telah disahkan dan boleh login
    $update = "UPDATE users SET initial_setup = 1 WHERE user_id = '$uid'";
    
    if ($conn->query($update)) {
        echo "<script>alert('Akaun anggota berjaya diaktifkan!'); window.location='verify_users.php';</script>";
    } else {
        echo "<script>alert('Ralat Pangkalan Data: " . $conn->error . "');</script>";
    }
}

// --- 3. LOGIK QUERY MENGIKUT PERANAN (ROLE) ---
// Jika Markas, hanya paparkan user dari unit yang sama yang belum diaktifkan (initial_setup = 0)
if ($current_role === 'markas') {
    // Memastikan markas_id dipadankan secara tepat
    $sql = "SELECT * FROM users 
            WHERE role = 'user' 
            AND initial_setup = 0 
            AND markas_id = '" . mysqli_real_escape_string($conn, $current_markas) . "' 
            ORDER BY full_name ASC";
} else {
    // Admin & Superadmin boleh melihat semua permohonan dari semua markas
    $sql = "SELECT * FROM users WHERE initial_setup = 0 AND role = 'user' ORDER BY full_name ASC";
}
$result = $conn->query($sql);
?>

<div style="max-width: 1000px; margin: 50px auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); border-top: 5px solid #28a745;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #28a745; font-weight: 800; margin: 0;">PENGESAHAN AKAUN ANGGOTA</h2>
        <?php if ($current_role === 'markas'): ?>
            <span style="background: #eafaf1; color: #28a745; padding: 5px 15px; border-radius: 50px; font-size: 12px; font-weight: bold; border: 1px solid #28a745;">
                UNIT: <?php echo htmlspecialchars($current_markas); ?>
            </span>
        <?php endif; ?>
    </div>
    
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; text-align: left;">
                <th style="padding: 15px; border-bottom: 2px solid #eee;">Nama Penuh</th>
                <th style="padding: 15px; border-bottom: 2px solid #eee;">No. Tentera/KP</th>
                <th style="padding: 15px; border-bottom: 2px solid #eee;">Markas</th>
                <th style="padding: 15px; border-bottom: 2px solid #eee; text-align: center;">Tindakan</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px; font-weight: bold;"><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td style="padding: 15px;"><?php echo htmlspecialchars($row['no_kp_tentera']); ?></td>
                    <td style="padding: 15px;"><?php echo htmlspecialchars($row['markas_id']); ?></td>
                    <td style="padding: 15px; text-align: center;">
                        <a href="verify_users.php?activate_id=<?php echo $row['user_id']; ?>" 
                           onclick="return confirm('Sahkan pendaftaran akaun anggota ini?')"
                           style="background: #28a745; color: white; padding: 10px 25px; border-radius: 50px; text-decoration: none; font-size: 11px; font-weight: bold; transition: 0.3s; display: inline-block;">
                           AKTIFKAN
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="padding: 40px; text-align: center; color: #999;">
                        <i class="fas fa-user-slash fa-2x" style="display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                        Tiada permohonan akaun baru untuk disahkan buat masa ini.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>