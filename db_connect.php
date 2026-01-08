<?php
// elzakenzaki/psm/PSM-0865452e0a988abcce29ee1c389601e5150ae441/db_connect.php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "zakat_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Sambungan gagal: " . mysqli_connect_error());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('set_session_message')) {
    function set_session_message($message, $type = 'success') {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $type;
    }
}
?>