<?php
// process_decision.php
include_once 'db_connect.php';
include_once 'email_functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $app_id = $_POST['app_id'];
    $status = $_POST['status']; // 'Lulus' atau 'Gagal'
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);

    // 1. Update status permohonan
    $sql = "UPDATE tbl_permohonan SET status = ?, catatan_admin = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status, $catatan, $app_id);

    if ($stmt->execute()) {
        // 2. Ambil info pemohon untuk emel
        $query_user = "SELECT p.id_staf, u.email, u.name 
                       FROM tbl_permohonan p 
                       JOIN tbl_pengguna u ON p.user_id = u.id 
                       WHERE p.id = ?";
        $stmt_user = $conn->prepare($query_user);
        $stmt_user->bind_param("i", $app_id);
        $stmt_user->execute();
        $user_data = $stmt_user->get_result()->fetch_assoc();

        // 3. Hantar emel keputusan
        $email_data = [
            'status' => $status,
            'ref_no' => 'APP-'.str_pad($app_id, 5, '0', STR_PAD_LEFT)
        ];
        send_ezakat_notification($user_data['email'], 'decision', $email_data);

        set_session_message("Keputusan telah disimpan dan emel dihantar.", "success");
    }
    header("Location: manage_applications.php");
    exit();
}