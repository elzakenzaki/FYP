<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

$message = ''; 
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $no_kp_tentera = mysqli_real_escape_string($conn, $_POST['no_kp_tentera'] ?? '');
    $markas_id = mysqli_real_escape_string($conn, $_POST['markas_id'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $check_sql = "SELECT user_id FROM users WHERE no_kp_tentera = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("s", $no_kp_tentera);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $message = "❌ No. KP/Tentera ini sudah berdaftar.";
        $message_type = 'error';
    } elseif ($password !== $confirm_password) {
        $message = "❌ Kata laluan tidak sepadan.";
        $message_type = 'error';
    } elseif (empty($markas_id)) {
        $message = "❌ Sila pilih Markas/Unit anda.";
        $message_type = 'error';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user';
        $sql = "INSERT INTO users (full_name, email, password, no_kp_tentera, role, markas_id, initial_setup) VALUES (?, ?, ?, ?, ?, ?, 0)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $full_name, $email, $hashed_password, $no_kp_tentera, $role, $markas_id);
        
        if ($stmt->execute()) {
            // Mesej ini akan ditangkap oleh footer.php di laman Login.php
            $_SESSION['message'] = "Pendaftaran berjaya! Tunggu pengesahan Admin.";
            $_SESSION['message_type'] = "success";
            header("Location: Login.php"); 
            exit();
        } else {
            $message = "❌ Ralat sistem. Sila cuba lagi.";
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Daftar Akaun - E-ZAKAT ATM</title>
    <link rel="stylesheet" href="ui_styles.php">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background: #f8f9fa; margin: 0; font-family: 'Inter', sans-serif; }
        .header-nav { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .reg-card { max-width: 500px; margin: 40px auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-top: 6px solid var(--color-primary); }
        .form-group { text-align: left; margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 700; margin-bottom: 5px; color: #444; font-size: 13px; padding-left: 15px; }
        .form-control { width: 100%; padding: 12px 20px; border-radius: 30px; border: 1.5px solid #eee; box-sizing: border-box; background: #f9f9f9; font-family: inherit; }
        .form-control:focus { outline: none; border-color: var(--color-primary); background: white; }
        .btn-pill-green { width: 100%; padding: 15px; border-radius: 50px; background: linear-gradient(135deg, var(--color-primary) 0%, #1e7e34 100%); color: white; border: none; font-weight: bold; font-size: 16px; cursor: pointer; transition: 0.3s; text-transform: uppercase; }
        .btn-pill-green:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3); }
    </style>
</head>
<body>
    <header class="header-nav">
        <div style="display: flex; align-items: center; gap: 12px;">
            <img src="logo_atm.png" alt="Logo" style="height: 50px;">
            <span style="font-weight: 800; color: var(--color-primary); font-size: 22px;">E-ZAKAT ATM</span>
        </div>
        <a href="Homepage.php" style="background-color: var(--color-primary); color: white; padding: 10px 25px; border-radius: 50px; text-decoration: none; font-weight: bold;">LAMAN UTAMA</a>
    </header>

    <div class="reg-card">
        <h2 style="text-align: center; color: var(--color-primary); font-weight: 800; margin-bottom: 25px;">DAFTAR AKAUN</h2>
        
        <?php if ($message && $message_type == 'error'): ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Perhatian',
                    text: '<?php echo $message; ?>',
                    confirmButtonColor: '#d33'
                });
            </script>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group"><label>Nama Penuh</label><input type="text" name="full_name" class="form-control" placeholder="NAMA SEPERTI DALAM KP" required></div>
            <div class="form-group"><label>Alamat Emel</label><input type="email" name="email" class="form-control" placeholder="ALAMAT EMEL AKTIF" required></div>
            <div class="form-group"><label>No. KP / No. Tentera</label><input type="text" name="no_kp_tentera" class="form-control" placeholder="ID UNTUK LOG MASUK" required></div>
            
            <div class="form-group">
                <label>Markas / Unit</label>
                <select name="markas_id" class="form-control" required>
                    <option value="">-- SILA PILIH MARKAS --</option>
                    <option value="MARKAS DARAT">MARKAS DARAT</option>
                    <option value="MARKAS LAUT">MARKAS LAUT</option>
                    <option value="MARKAS UDARA">MARKAS UDARA</option>
                </select>
            </div>

            <div class="form-group"><label>Cipta Kata Laluan</label><input type="password" name="password" class="form-control" placeholder="MINIMUM 6 AKSARA" required></div>
            <div class="form-group"><label>Sahkan Kata Laluan</label><input type="password" name="confirm_password" class="form-control" placeholder="ULANG KATA LALUAN" required></div>
            <button type="submit" class="btn-pill-green">DAFTAR SEKARANG</button>
        </form>
        <p style="text-align: center; margin-top: 20px; font-size: 13px; color: #777;">Sudah mempunyai akaun? <a href="Login.php" style="color: var(--color-primary); font-weight: bold; text-decoration: none;">Log Masuk</a></p>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>