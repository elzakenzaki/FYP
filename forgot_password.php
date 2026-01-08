<?php
// forgot_password.php
session_start();
include_once 'db_connect.php';

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_identifier = mysqli_real_escape_string($conn, $_POST['user_identifier']);

    // 1. Semak sama ada No. KP/Tentera atau E-mel wujud dalam jadual 'users'
    $sql = "SELECT user_id, full_name, email FROM users WHERE no_kp_tentera = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_identifier, $user_identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Dalam sistem sebenar, anda akan menghantar emel pautan reset di sini.
        // Untuk tujuan pembangunan (FYP), kita akan simpan ID dalam sesi dan bawa ke halaman reset.
        $_SESSION['reset_user_id'] = $user['user_id'];
        
        // Simulasi Berjaya
        $message = "Akaun dijumpai untuk " . htmlspecialchars($user['full_name']) . ". Sila tetapkan kata laluan baru.";
        $message_type = "success";
        
        // Alihkan ke halaman reset_password.php selepas 2 saat
        header("refresh:2;url=reset_password.php");
    } else {
        $message = "Ralat: No. KP/Tentera atau E-mel tidak dijumpai dalam sistem.";
        $message_type = "error";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Laluan - E-ZAKAT ATM</title>
    <link rel="stylesheet" href="ui_styles.php">
    <style>
        body { background-color: #f0f2f5; font-family: 'Inter', 'Segoe UI', sans-serif; }
        
        .forgot-container {
            max-width: 450px;
            margin: 80px auto;
            padding: 40px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border-bottom: 6px solid #dc3545; /* Warna amaran merah */
        }

        .forgot-header { text-align: center; margin-bottom: 30px; }
        .forgot-header h2 { color: #333; font-weight: 800; margin-bottom: 10px; }
        .forgot-header p { color: #666; font-size: 14px; }

        .form-group { margin-bottom: 20px; }
        .form-group label { font-weight: 700; display: block; margin-bottom: 8px; color: #444; }
        
        .form-control {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: 1.5px solid #ddd;
            box-sizing: border-box;
            transition: 0.3s;
            font-size: 15px;
        }

        .form-control:focus {
            border-color: #dc3545;
            outline: none;
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.1);
        }

        /* Butang Submit Premium (Gaya Pill) */
        .btn-reset {
            width: 100%;
            padding: 15px;
            border-radius: 35px;
            background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);
            color: white;
            border: none;
            font-weight: 700;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 15px rgba(220, 53, 69, 0.3);
            margin-top: 10px;
        }

        .btn-reset:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.5);
            filter: brightness(1.1);
        }

        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
        }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="forgot-container">
        <div class="forgot-header">
            <img src="logo_atm.png" alt="Logo ATM" style="height: 60px; margin-bottom: 15px;">
            <h2>Lupa Kata Laluan?</h2>
            <p>Masukkan No. KP/Tentera atau E-mel anda untuk mencari akaun anda.</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="forgot_password.php" method="POST">
            <div class="form-group">
                <label>No. KP / No. Tentera / E-mel:</label>
                <input type="text" name="user_identifier" class="form-control" placeholder="Cth: 900101015522 atau staf@mod.gov.my" required>
            </div>
            
            <button type="submit" class="btn-reset">CARI AKAUN</button>
        </form>

        <a href="Login.php" class="back-link">← Kembali ke Log Masuk</a>
    </div>

</body>
</html>