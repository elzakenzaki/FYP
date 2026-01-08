<?php
// process_login.php
session_start();
include_once 'db_connect.php'; //

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userIdentifier = mysqli_real_escape_string($conn, $_POST['userIdentifier']);
    $password = $_POST['password'];

    // Menghubungkan ke jadual 'users' mengikut skema SQL anda
    $sql = "SELECT user_id, full_name, role, password, initial_setup, markas_id FROM users WHERE no_kp_tentera = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("s", $userIdentifier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['markas_id'] = $user['markas_id'];

                // Logik Hala Tuju
                if ($user['role'] == 'user' && $user['initial_setup'] == 0) {
                    header("Location: Register.php");
                    exit();
                }

                switch($user['role']) {
                    case 'superadmin': header("Location: dashboard_superadmin.php"); break;
                    case 'markas': header("Location: dashboard_markas.php"); break;
                    case 'admin': header("Location: dashboard_kagat.php"); break;
                    default: header("Location: dashboard_user.php");
                }
                exit();
            } else {
                $_SESSION['error'] = "Kata laluan salah.";
            }
        } else {
            $_SESSION['error'] = "ID Pengguna tidak dijumpai.";
        }
        $stmt->close();
    }
    header("Location: Login.php");
    exit();
}