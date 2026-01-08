<?php
include 'email_function.php';

$data = [
    'name' => 'Ujian Sistem',
    'ref_no' => '#TEST123',
    'status' => 'Lulus'
];

// Gantikan dengan emel peribadi anda untuk testing
$result = send_ezakat_notification('emel_anda@gmail.com', 'decision', $data);

if ($result) {
    echo "✅ Berjaya! Sila semak Inbox atau Spam.";
} else {
    echo "❌ Gagal. Sila semak log ralat.";
}
?>