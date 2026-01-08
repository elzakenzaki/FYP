<?php
// 1. Aktifkan paparan ralat untuk mengesan punca skrin putih (Sangat Penting)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_identifier = trim($_POST['userIdentifier'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($user_identifier) && !empty($password)) {
        // 2. Query SQL yang diselaraskan untuk mengelakkan Fatal Error
        $sql = "SELECT user_id, full_name, role, password, initial_setup, markas_id FROM users WHERE no_kp_tentera = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $user_identifier);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                
                // 3. Verifikasi Kata Laluan
                if (password_verify($password, $user['password'])) {
                    
                    // 4. Logik Pengaktifan/Verify User
                    if ($user['initial_setup'] == 0) {
                        $error = "Akaun anda belum disahkan oleh Admin.";
                    } else {
                        // Set data sesi
                        $_SESSION['user_id'] = $user['user_id']; 
                        $_SESSION['role'] = $user['role']; 
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['markas_id'] = $user['markas_id']; 

                        // 5. Redirect Dinamik mengikut role yang betul
                        if ($user['role'] == 'superadmin') { 
                            header("Location: dashboard_superadmin.php"); 
                        } elseif ($user['role'] == 'markas') { 
                            header("Location: dashboard_markas.php"); 
                        } elseif ($user['role'] == 'admin') { 
                            header("Location: dashboard_kagat.php"); 
                        } else { 
                            header("Location: dashboard_user.php"); 
                        }
                        exit();
                    }
                } else { 
                    $error = "Kata Laluan Salah."; 
                }
            } else { 
                $error = "ID Pengguna tidak ditemui."; 
            }
            $stmt->close();
        } else {
            $error = "Ralat sistem: Gagal menyediakan capaian data.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Masuk - E-ZAKAT ATM</title>
    <link rel="stylesheet" href="ui_styles.php">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background: #f8f9fa; margin: 0; font-family: 'Inter', sans-serif; }
        .header-nav { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .login-card { max-width: 450px; margin: 80px auto; background: white; padding: 50px 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; border-top: 6px solid var(--color-primary, #28a745); }
        .form-group { text-align: left; margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 700; margin-bottom: 8px; color: #444; font-size: 14px; padding-left: 15px; }
        .form-control { width: 100%; padding: 14px 20px; border-radius: 30px; border: 1.5px solid #eee; box-sizing: border-box; transition: 0.3s; background: #f9f9f9; }
        .form-control:focus { border-color: var(--color-primary, #28a745); background: white; outline: none; }
        .btn-pill { width: 100%; padding: 16px; border-radius: 50px; background: linear-gradient(135deg, var(--color-primary, #28a745) 0%, #1e7e34 100%); color: white; border: none; font-weight: 800; font-size: 16px; cursor: pointer; transition: 0.3s; text-transform: uppercase; margin-top: 10px; }
        .btn-pill:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3); }
    </style>
</head>
<body>

    <?php if ($error): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal Log Masuk',
                text: '<?php echo $error; ?>',
                confirmButtonColor: '#d33'
            });
        </script>
    <?php endif; ?>

    <header class="header-nav">
        <div style="display: flex; align-items: center; gap: 12px;">
            <img src="logo_atm.png" alt="Logo" style="height: 50px;">
            <span style="font-weight: 800; color: var(--color-primary, #28a745); font-size: 22px;">E-ZAKAT ATM</span>
        </div>
        <a href="Homepage.php" style="background-color: var(--color-primary, #28a745); color: white; padding: 10px 25px; border-radius: 50px; text-decoration: none; font-weight: bold;">LAMAN UTAMA</a>
    </header>

    <div class="login-card">
        <h2 style="color: var(--color-primary, #28a745); font-weight: 800; margin-bottom: 30px;">LOG MASUK</h2>
        
        <form method="POST">
            <div class="form-group">
                <label>ID Pengguna (No. KP / Tentera)</label>
                <input type="text" name="userIdentifier" class="form-control" placeholder="Contoh: 900101145566" required>
            </div>
            <div class="form-group">
                <label>Kata Laluan</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan kata laluan" required>
            </div>
            <button type="submit" class="btn-pill">MASUK SISTEM</button>
        </form>
        
        <p style="margin-top: 25px; font-size: 14px; color: #666;">
            Belum mempunyai akaun? <a href="Register.php" style="color: var(--color-primary, #28a745); font-weight: bold; text-decoration: none;">Daftar Sekarang</a>
        </p>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>