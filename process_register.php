<?php
// process_register.php
include_once 'db_connect.php';
include_once 'email_functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_staf = mysqli_real_escape_string($conn, $_POST['id_staf']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $markas = mysqli_real_escape_string($conn, $_POST['markas']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Semak jika kata laluan sepadan
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Kata laluan tidak sepadan.";
        header("Location: Register.php");
        exit();
    }

    // 2. Semak jika ID Pengguna sudah wujud
    $check_sql = "SELECT id FROM users WHERE id_staf = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("s", $id_staf);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        $_SESSION['error'] = "ID Pengguna sudah berdaftar.";
        header("Location: Register.php");
        exit();
    }

    // 3. Simpan ke database (is_verified = 0 secara default)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $insert_sql = "INSERT INTO users (id_staf, name, email, markas, password, is_verified, role) VALUES (?, ?, ?, ?, ?, 0, 'user')";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("sssss", $id_staf, $name, $email, $markas, $hashed_password);

    if ($stmt->execute()) {
        // 4. Hantar Emel Notifikasi Pendaftaran
        $email_data = ['name' => $name, 'id_staf' => $id_staf];
        send_ezakat_notification($email, 'register', $email_data);

        $_SESSION['success'] = "Pendaftaran Berjaya! Akaun anda sedang diproses untuk pengesahan.";
        header("Location: Login.php");
    } else {
        $_SESSION['error'] = "Ralat sistem. Sila cuba lagi.";
        header("Location: Register.php");
    }
    exit();
}<?php
// process_register.php
include_once 'db_connect.php';
include_once 'email_functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_staf = mysqli_real_escape_string($conn, $_POST['id_staf']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $markas = mysqli_real_escape_string($conn, $_POST['markas']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Semak jika kata laluan sepadan
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Kata laluan tidak sepadan.";
        header("Location: Register.php");
        exit();
    }

    // 2. Semak jika ID Pengguna sudah wujud
    $check_sql = "SELECT id FROM users WHERE id_staf = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("s", $id_staf);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        $_SESSION['error'] = "ID Pengguna sudah berdaftar.";
        header("Location: Register.php");
        exit();
    }

    // 3. Simpan ke database (is_verified = 0 secara default)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $insert_sql = "INSERT INTO users (id_staf, name, email, markas, password, is_verified, role) VALUES (?, ?, ?, ?, ?, 0, 'user')";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("sssss", $id_staf, $name, $email, $markas, $hashed_password);

    if ($stmt->execute()) {
        // 4. Hantar Emel Notifikasi Pendaftaran
        $email_data = ['name' => $name, 'id_staf' => $id_staf];
        send_ezakat_notification($email, 'register', $email_data);

        $_SESSION['success'] = "Pendaftaran Berjaya! Akaun anda sedang diproses untuk pengesahan.";
        header("Location: Login.php");
    } else {
        $_SESSION['error'] = "Ralat sistem. Sila cuba lagi.";
        header("Location: Register.php");
    }
    exit();
}