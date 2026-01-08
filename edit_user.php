<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php';

// 1. KAWALAN AKSES KETAT: Hanya Superadmin dibenarkan
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: dashboard_markas.php?error=unauthorized");
    exit();
}

// Ambil ID dari URL (Pastikan URL anda adalah edit_user.php?id=...)
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';

// 2. PROSES KEMASKINI DATA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $markas_id = mysqli_real_escape_string($conn, $_POST['markas_id']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Ralat dibaiki di sini: Menggunakan user_id sebagai ganti id
    $sql_update = "UPDATE users SET 
                   full_name = '$full_name', 
                   email = '$email', 
                   markas_id = '$markas_id', 
                   role = '$role' 
                   WHERE user_id = $id";

    if ($conn->query($sql_update)) {
        $message = "<div style='padding:15px; background:#d4edda; color:#155724; border-radius:10px; margin-bottom:20px; text-align:center; font-weight:bold;'>✅ Profil pengguna berjaya dikemaskini.</div>";
    } else {
        $message = "<div style='padding:15px; background:#f8d7da; color:#721c24; border-radius:10px; margin-bottom:20px; text-align:center;'>❌ Ralat SQL: " . $conn->error . "</div>";
    }
}

// 3. TARIK DATA PENGGUNA (Ralat Unknown Column id dibaiki di sini)
$sql = "SELECT * FROM users WHERE user_id = $id";
$result = $conn->query($sql);
$user_data = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;

if (!$user_data) {
    die("<div style='text-align:center; margin-top:50px; font-family:sans-serif;'><h2>❌ Ralat: Pengguna ID $id tidak ditemui.</h2><p>Sila pastikan nama kolum dalam database adalah <b>user_id</b>.</p><a href='dashboard_superadmin.php'>Kembali</a></div>");
}

include 'header.php';
?>

<div style="max-width: 700px; margin: 50px auto; padding: 0 20px; font-family: 'Inter', sans-serif;">
    <?php echo $message; ?>

    <div style="background: white; border-radius: 25px; padding: 40px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); border-top: 8px solid #000;">
        <h2 style="font-weight: 900; color: #1a1a1a; margin-bottom: 30px; text-align: center; text-transform: uppercase;">
            <i class="fas fa-user-edit"></i> Edit Pengguna #<?php echo $id; ?>
        </h2>

        <form method="POST">
            <div style="margin-bottom: 20px;">
                <label style="font-weight: 800; display: block; margin-bottom: 8px;">Nama Penuh:</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user_data['full_name']); ?>" style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 10px;" required>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-weight: 800; display: block; margin-bottom: 8px;">Emel:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 10px;" required>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-weight: 800; display: block; margin-bottom: 8px;">Unit / Markas:</label>
                <input type="text" name="markas_id" value="<?php echo htmlspecialchars($user_data['markas_id']); ?>" style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 10px;" required>
            </div>

            <div style="margin-bottom: 30px;">
                <label style="font-weight: 800; display: block; margin-bottom: 8px;">Peranan (Role):</label>
                <select name="role" style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 10px;" required>
                    <option value="user" <?php if($user_data['role'] == 'user') echo 'selected'; ?>>Anggota (User)</option>
                    <option value="markas" <?php if($user_data['role'] == 'markas') echo 'selected'; ?>>Pegawai Markas</option>
                    <option value="admin" <?php if($user_data['role'] == 'admin') echo 'selected'; ?>>Admin / KAGAT</option>
                    <option value="superadmin" <?php if($user_data['role'] == 'superadmin') echo 'selected'; ?>>Superadmin</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <button type="submit" name="update_user" style="background: #000; color: white; padding: 15px; border-radius: 50px; border: none; font-weight: 800; cursor: pointer; text-transform: uppercase;">Kemaskini</button>
                <a href="dashboard_superadmin.php" style="background: #eee; color: #333; padding: 15px; border-radius: 50px; text-decoration: none; text-align: center; font-weight: 800; text-transform: uppercase; font-size: 13px; line-height: 20px;">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>