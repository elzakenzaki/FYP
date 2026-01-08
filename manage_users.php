<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php';

// KAWALAN AKSES: Hanya Admin KAGAT (admin) dan Superadmin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    header("Location: Login.php");
    exit();
}

// PROSES PADAM USER (Jika butang delete ditekan)
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    // Elakkan daripada memadam diri sendiri
    if ($delete_id != $_SESSION['user_id']) {
        $sql_delete = "DELETE FROM users WHERE user_id = ?";
        $stmt_del = $conn->prepare($sql_delete);
        $stmt_del->bind_param("i", $delete_id);
        $stmt_del->execute();
        header("Location: manage_users.php?view=" . ($_GET['view'] ?? 'pemohon'));
        exit();
    }
}

$view = $_GET['view'] ?? 'pemohon';
include 'header.php';
?>

<div style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="color: #28a745; font-weight: 800; margin: 0; text-transform: uppercase;">
            <i class="fas fa-users-cog"></i> Pengurusan Pengguna
        </h2>
        
        <a href="dashboard_superadmin.php" class="btn-pill btn-green" style="background:#28a745; color:white; text-decoration:none; padding:10px 20px; border-radius:50px; font-weight:bold; font-size:12px; display:flex; align-items:center; gap:8px; box-shadow: 0 4px 10px rgba(40,167,69,0.3);">
            <i class="fas fa-arrow-left"></i> KEMBALI KE DASHBOARD
        </a>
    </div>

    <div style="background: white; border-radius: 15px; padding: 35px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border-top: 6px solid #28a745;">
        
        <div style="display: flex; gap: 10px; margin-bottom: 30px;">
            <a href="manage_users.php?view=pemohon" style="text-decoration:none; padding:10px 25px; border-radius:50px; font-weight:bold; font-size:13px; background:<?php echo $view=='pemohon'?'#28a745':'#eee'; ?>; color:<?php echo $view=='pemohon'?'white':'#666'; ?>;">Senarai Pemohon</a>
            <a href="manage_users.php?view=markas" style="text-decoration:none; padding:10px 25px; border-radius:50px; font-weight:bold; font-size:13px; background:<?php echo $view=='markas'?'#28a745':'#eee'; ?>; color:<?php echo $view=='markas'?'white':'#666'; ?>;">Pegawai Markas</a>
            <a href="manage_users.php?view=admin" style="text-decoration:none; padding:10px 25px; border-radius:50px; font-weight:bold; font-size:13px; background:<?php echo $view=='admin'?'#28a745':'#eee'; ?>; color:<?php echo $view=='admin'?'white':'#666'; ?>;">Pegawai KAGAT (Admin)</a>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; text-align: left;">
                        <th style="padding: 15px; border-bottom: 2px solid #eee; font-size: 13px;">NAMA PENUH</th>
                        <th style="padding: 15px; border-bottom: 2px solid #eee; font-size: 13px;">NO. TENTERA / KP</th>
                        <th style="padding: 15px; border-bottom: 2px solid #eee; font-size: 13px;">E-MEL</th>
                        <th style="padding: 15px; border-bottom: 2px solid #eee; font-size: 13px; text-align: center;">STATUS</th>
                        <th style="padding: 15px; border-bottom: 2px solid #eee; font-size: 13px; text-align: center;">TINDAKAN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query mengikut tab yang dipilih
                    $role_filter = $view; 
                    if ($view === 'pemohon') { $role_filter = 'user'; }

                    $sql_users = "SELECT * FROM users WHERE role = ? ORDER BY full_name ASC";
                    $stmt = $conn->prepare($sql_users);
                    $stmt->bind_param("s", $role_filter);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr style='border-bottom: 1px solid #eee;'>";
                            echo "<td style='padding:15px; font-weight:bold;'>" . htmlspecialchars($row['full_name'] ?? 'N/A') . "</td>";
                            echo "<td style='padding:15px;'>" . htmlspecialchars($row['no_kp_tentera'] ?? '-') . "</td>";
                            echo "<td style='padding:15px;'>" . htmlspecialchars($row['email'] ?? '-') . "</td>";
                            echo "<td style='padding:15px; text-align:center;'><span style='background:#d4edda; color:#155724; padding:4px 12px; border-radius:50px; font-size:11px; font-weight:bold;'>AKTIF</span></td>";
                            echo "<td style='padding:15px; text-align:center; display:flex; justify-content:center; gap:15px;'>
                                    <a href='edit_user.php?id=".$row['user_id']."' style='color:#007bff; text-decoration:none; font-size:18px;' title='Edit'><i class='fas fa-edit'></i></a>
                                    <a href='manage_users.php?view=$view&delete_id=".$row['user_id']."' onclick='return confirm(\"Adakah anda pasti mahu memadam pengguna ini?\")' style='color:#dc3545; text-decoration:none; font-size:18px;' title='Padam'><i class='fas fa-trash-alt'></i></a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='padding:30px; text-align:center; color:#999;'>Tiada rekod ditemui.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>