<?php
// 1. Masukkan library Dompdf (Pastikan folder vendor/dompdf sudah ada)
require_once 'vendor/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Kawalan Akses: Hanya Markas atau Superadmin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['markas', 'superadmin'])) {
    header("Location: Login.php");
    exit();
}

$markas_id = $_SESSION['markas_id'];

// 2. PEMBETULAN RALAT: Ganti 'application_id' kepada 'id'
$sql = "SELECT id, full_name, no_kp_tentera, status, amount, created_at 
        FROM applications 
        WHERE markas_id = ? 
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $markas_id);
$stmt->execute();
$result = $stmt->get_result();

// 3. Bina Struktur HTML untuk PDF (UI Profesional)
$html = '
<style>
    body { font-family: Helvetica, Arial, sans-serif; color: #333; }
    .header { text-align: center; border-bottom: 2px solid #28a745; padding-bottom: 10px; margin-bottom: 20px; }
    .title { font-size: 18px; font-weight: bold; text-transform: uppercase; color: #1a5928; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th { background-color: #28a745; color: white; padding: 10px; font-size: 12px; border: 1px solid #ddd; }
    td { padding: 8px; border: 1px solid #ddd; font-size: 11px; text-align: center; }
    .footer { margin-top: 30px; font-size: 10px; text-align: right; font-style: italic; }
</style>

<div class="header">
    <div class="title">LAPORAN STATISTIK PERMOHONAN ZAKAT</div>
    <div style="font-size: 14px;">UNIT: ' . htmlspecialchars($markas_id) . '</div>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>NAMA PEMOHON</th>
            <th>NO. TENTERA</th>
            <th>STATUS</th>
            <th>AMAUN (RM)</th>
            <th>TARIKH</th>
        </tr>
    </thead>
    <tbody>';

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $html .= '<tr>
            <td>#' . $row['id'] . '</td>
            <td style="text-align:left;">' . htmlspecialchars($row['full_name']) . '</td>
            <td>' . htmlspecialchars($row['no_kp_tentera']) . '</td>
            <td>' . strtoupper($row['status']) . '</td>
            <td>' . number_format($row['amount'], 2) . '</td>
            <td>' . date('d/m/Y', strtotime($row['created_at'])) . '</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="6">Tiada rekod ditemui bagi unit ini.</td></tr>';
}

$html .= '</tbody></table>
<div class="footer">Dicetak pada: ' . date('d/m/Y H:i:s') . '</div>';

// 4. Konfigurasi Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// 5. Muat Turun Fail PDF
$dompdf->stream("Laporan_Unit_" . $markas_id . ".pdf", array("Attachment" => 1));
exit();
?>