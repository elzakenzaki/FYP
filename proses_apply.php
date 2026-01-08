<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

if (isset($_POST['submit_apply'])) {
    $u_id = $_SESSION['user_id'];
    $markas_id = $_SESSION['markas_id'] ?? 'Markas Latihan';

    // Ambil maklumat pemohon dari database supaya data tidak NULL
    $stmt_user = $conn->prepare("SELECT full_name FROM users WHERE user_id = ?");
    $stmt_user->bind_param("i", $u_id);
    $stmt_user->execute();
    $res_user = $stmt_user->get_result()->fetch_assoc();
    $full_name = $res_user['full_name'] ?? 'Pemohon';

    // Ambil data borang
    $p_diri = $_POST['pendapatan_diri'] ?? 0;
    $p_pasangan = $_POST['pendapatan_isteri_suami'] ?? 0;
    $p_asasi = $_POST['perbelanjaan_asasi'] ?? 0;
    $p_sewa = $_POST['perbelanjaan_sewa'] ?? 0;
    $bil_anak = $_POST['jumlah_tanggungan_anak'] ?? 0;
    $bil_lain = $_POST['jumlah_tanggungan_lain'] ?? 0;
    $kediaman = $_POST['tempat_kediaman'] ?? '';
    $asnaf = $_POST['asnaf'] ?? '';
    $tujuan = $_POST['bantuan_tujuan'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    
    // KEMASKINI: Ambil data Maklumat Akaun Bank
    $n_bank = $_POST['nama_bank'] ?? '';
    $no_acc = $_POST['no_akaun'] ?? '';
    $n_pemegang = $_POST['nama_pemegang'] ?? '';
    
    $status = 'Semakan KAGAT'; // Status wajib untuk dipaparkan di urus_kelulusan

    // Proses Muat Naik Fail
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    $file_ic = time() . "_ic_" . basename($_FILES["doc_ic"]["name"]);
    $file_gaji = time() . "_gaji_" . basename($_FILES["doc_gaji"]["name"]);

    move_uploaded_file($_FILES["doc_ic"]["tmp_name"], $target_dir . $file_ic);
    move_uploaded_file($_FILES["doc_gaji"]["tmp_name"], $target_dir . $file_gaji);

    // KEMASKINI: Simpan ke Database (Tambah kolum maklumat akaun bank)
    $sql = "INSERT INTO applications (
                user_id, full_name, markas_id, pendapatan_diri, pendapatan_isteri_suami, 
                perbelanjaan_asasi, perbelanjaan_sewa, jumlah_tanggungan_anak, 
                jumlah_tanggungan_lain, tempat_kediaman, asnaf, bantuan_tujuan, 
                amount, nama_bank, no_akaun, nama_pemegang, status, doc_ic, doc_gaji
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    
    // KEMASKINI: Bind_param disesuaikan dengan 19 parameter (tambah sss untuk maklumat bank)
    $stmt->bind_param("isssdddiisssdssssss", 
        $u_id, $full_name, $markas_id, $p_diri, $p_pasangan, 
        $p_asasi, $p_sewa, $bil_anak, $bil_lain, $kediaman, 
        $asnaf, $tujuan, $amount, $n_bank, $no_acc, $n_pemegang, $status, $file_ic, $file_gaji
    );

    if ($stmt->execute()) {
        $_SESSION['message'] = "✅ Permohonan Berjaya Dihantar!";
        header("Location: status.php");
        exit();
    } else {
        echo "Ralat: " . $stmt->error;
    }
}
?>