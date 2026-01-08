<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php'; 

// 1. Kawalan Akses
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin', 'markas'])) {
    header("Location: Login.php");
    exit();
}

$current_markas = trim($_SESSION['markas_id'] ?? '');

// 2. LOGIK AJAX UNTUK MASA NYATA (REAL-TIME)
if (isset($_GET['ajax'])) {
    $search_term = "%" . mysqli_real_escape_string($conn, $current_markas) . "%";
    
    $where = "WHERE markas_id LIKE '$search_term' 
              AND status IN ('Baru', 'Dalam Proses', 'Semakan KAGAT', 'Menunggu Pengesahan')";
    
    if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superadmin') {
        $where = "WHERE status IN ('Baru', 'Dalam Proses', 'Semakan KAGAT', 'Menunggu Pengesahan')";
    }

    // KEMASKINI: Tambah kolum nama_bank dalam SELECT
    $sql = "SELECT id, full_name, markas_id, status, nama_bank FROM applications $where ORDER BY id DESC";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<tr style="border-bottom: 1px solid #f0f0f0;">
                <td style="padding: 15px; font-weight: bold; color: #007bff;">#' . $row['id'] . '</td>
                <td style="padding: 15px; font-weight: 700;">' . htmlspecialchars($row['full_name']) . '</td>
                <td style="padding: 15px;">' . htmlspecialchars($row['markas_id']) . '</td>
                <td style="padding: 15px; font-weight: 600; color: #555;">' . htmlspecialchars($row['nama_bank'] ?? '-') . '</td>
                <td style="padding: 15px;">
                    <span style="padding: 5px 12px; border-radius: 20px; font-size: 10px; background: #fff3cd; color: #856404; font-weight: bold; text-transform: uppercase;">
                        ' . htmlspecialchars($row['status']) . '
                    </span>
                </td>
                <td style="padding: 15px; text-align: center;">
                    <a href="review_application.php?id=' . $row['id'] . '" 
                       style="background: #007bff; color: white; padding: 8px 20px; border-radius: 50px; text-decoration: none; font-size: 11px; font-weight: bold; transition: 0.3s;">
                        SEMAK DETAIL
                    </a>
                </td>
            </tr>';
        }
    } else {
        echo '<tr><td colspan="6" style="padding: 50px; text-align: center; color: #999;">Tiada permohonan baru untuk unit anda buat masa ini.</td></tr>';
    }
    exit();
}

include 'header.php';
?>

<div style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
    <div style="background: white; border-radius: 25px; padding: 40px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); border-top: 8px solid #007bff;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-weight: 900; color: #1a1a1a; margin: 0; text-transform: uppercase;">
                URUS KELULUSAN: <span style="color: #007bff;"><?php echo htmlspecialchars($current_markas); ?></span>
            </h2>
            <p style="font-size: 12px; color: #888; margin: 0; font-weight: bold;">
                Masa Terkini: <span id="current_time"><?php echo date('h:i:s A'); ?></span> (Auto-refresh 5s)
            </p>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; text-align: left; border-radius: 10px;">
                    <th style="padding: 18px; border-bottom: 2px solid #eee; font-size: 13px; letter-spacing: 1px;">ID</th>
                    <th style="padding: 18px; border-bottom: 2px solid #eee; font-size: 13px; letter-spacing: 1px;">NAMA PEMOHON</th>
                    <th style="padding: 18px; border-bottom: 2px solid #eee; font-size: 13px; letter-spacing: 1px;">UNIT</th>
                    <th style="padding: 18px; border-bottom: 2px solid #eee; font-size: 13px; letter-spacing: 1px;">BANK</th>
                    <th style="padding: 18px; border-bottom: 2px solid #eee; font-size: 13px; letter-spacing: 1px;">STATUS</th>
                    <th style="padding: 18px; border-bottom: 2px solid #eee; text-align: center; font-size: 13px; letter-spacing: 1px;">TINDAKAN</th>
                </tr>
            </thead>
            <tbody id="table_content">
                </tbody>
        </table>
    </div>
</div>

<script>
function updateTable() {
    fetch('urus_kelulusan.php?ajax=1')
        .then(res => {
            if (!res.ok) throw new Error('Ralat rangkaian');
            return res.text();
        })
        .then(data => {
            document.getElementById('table_content').innerHTML = data;
            document.getElementById('current_time').innerText = new Date().toLocaleTimeString();
        })
        .catch(err => console.error('Ralat Update Jadual:', err));
}

setInterval(updateTable, 5000);
updateTable(); 
</script>

<?php include 'footer.php'; ?>