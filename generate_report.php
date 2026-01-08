<?php
// Masukkan Dompdf (Pastikan laluan fail adalah betul mengikut folder anda)
require_once 'vendor/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include 'db_connect.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// --- PEMBETULAN 1: BENARKAN ROLE 'markas' UNTUK MENGELAKKAN AUTO LOG OUT ---
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin', 'markas'])) {
    header("Location: Login.php");
    exit();
}

// --- PEMBETULAN 2: SEMAK NAMA KOLUM SQL ---
// Jika anda masih dapat ralat 'no_kp_tentera', pastikan anda sudah jalankan:
// ALTER TABLE applications ADD COLUMN no_kp_tentera VARCHAR(20) AFTER full_name;
$sql = "SELECT id, full_name, no_kp_tentera, markas_id, status, amount, total_amount_approved, created_at 
        FROM applications 
        ORDER BY created_at DESC";

$result = $conn->query($sql);

// Bina Kandungan HTML untuk PDF
$html = '
<style>
    body { font-family: sans-serif; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 11px; }
    th { background-color: #1e7e34; color: white; }
    h2 { text-align: center; color: #1e3a8a; }
    .header-info { text-align: right; font-size: 10px; color: #666; }
</style>

<h2>LAPORAN PENGURUSAN ZAKAT DIGITAL ATM</h2>
<div class="header-info">Tarikh Jana: ' . date('d/m/Y H:i') . '</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Pemohon</th>
            <th>No. Tentera</th>
            <th>Unit/Markas</th>
            <th>Status</th>
            <th>Amaun Dipohon</th>
            <th>Amaun Lulus</th>
        </tr>
    </thead>
    <tbody>';

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $html .= '<tr>
            <td>#' . $row['id'] . '</td>
            <td>' . htmlspecialchars($row['full_name'] ?? 'N/A') . '</td>
            <td>' . htmlspecialchars($row['no_kp_tentera'] ?? '-') . '</td>
            <td>' . htmlspecialchars($row['markas_id'] ?? '-') . '</td>
            <td>' . $row['status'] . '</td>
            <td>RM ' . number_format($row['amount'], 2) . '</td>
            <td>RM ' . number_format($row['total_amount_approved'], 2) . '</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="7" style="text-align:center;">Tiada rekod ditemukan dalam pangkalan data.</td></tr>';
}

$html .= '</tbody></table>';

// Konfigurasi dan Jana PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Nama fail laporan
$filename = "Laporan_Zakat_ATM_" . date('Ymd_His') . ".pdf";
$dompdf->stream($filename, array("Attachment" => 1));
exit();
?>