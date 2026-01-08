<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'markas') {
    header("Location: Login.php");
    exit();
}

include 'header.php'; // Panggil header standard
?>

<div class="container" style="max-width:1200px; margin: 40px auto; padding: 0 20px;">
    <div style="background: white; border-radius: 15px; padding: 35px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
        <h2 style="color: var(--dark-green); font-weight: 800; margin-bottom: 25px;">
            <i class="fas fa-clipboard-check me-2"></i> Urus Semakan Permohonan
        </h2>
        
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; text-align: left;">
                    <th style="padding: 15px; border-bottom: 2px solid #eee;">ID</th>
                    <th style="padding: 15px; border-bottom: 2px solid #eee;">Nama Pemohon</th>
                    <th style="padding: 15px; border-bottom: 2px solid #eee;">No. Tentera</th>
                    <th style="padding: 15px; border-bottom: 2px solid #eee;">Status</th>
                    <th style="padding: 15px; border-bottom: 2px solid #eee; text-align: center;">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px;">#1023</td>
                    <td style="padding: 15px; font-weight: bold;">AHMAD BIN JALAL</td>
                    <td style="padding: 15px;">T1234567</td>
                    <td style="padding: 15px;"><span style="background:#fff3cd; color:#856404; padding:5px 12px; border-radius:50px; font-size:12px; font-weight:bold;">DALAM PROSES</span></td>
                    <td style="padding: 15px; text-align: center;">
                        <a href="review_application.php?id=1023" class="btn-pill btn-blue" style="display:inline-flex;">SEMAK</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>