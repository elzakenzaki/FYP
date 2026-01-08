<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php'; 

// 1. KAWALAN AKSES
if (!isset($_SESSION['user_id'])) { 
    header("Location: Login.php"); 
    exit(); 
}

$u_id = $_SESSION['user_id'];
include 'header.php'; 
?>

<style>
    .main-wrapper {
        min-height: calc(100vh - 450px);
        display: flex;
        flex-direction: column;
        background-color: #f9f9f9;
        padding-bottom: 50px;
    }

    .status-container { 
        max-width: 1100px; 
        margin: 50px auto; 
        padding: 0 20px; 
    }

    .status-card { 
        background: white; 
        border-radius: 15px; 
        padding: 40px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.08); 
        border-top: 6px solid #28a745; 
    }

    .table-custom {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .table-custom th {
        background: #f8f9fa;
        padding: 15px;
        font-size: 13px;
        text-transform: uppercase;
        color: #666;
        border-bottom: 2px solid #eee;
    }

    .table-custom td {
        padding: 20px 15px;
        background: white;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }

    .badge-status {
        padding: 6px 15px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 800;
        display: inline-block;
    }

    .badge-proses { background: #fff3cd; color: #856404; }
    .badge-lulus { background: #d4edda; color: #155724; }
    .badge-gagal { background: #f8d7da; color: #721c24; }
</style>

<div class="main-wrapper">
    <div class="status-container">
        <div class="status-card">
            <h2 class="mb-4" style="font-weight: 800; color: #333;">
                <i class="fas fa-history me-2" style="color: #28a745;"></i> STATUS PERMOHONAN SAYA
            </h2>
            
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Tarikh</th>
                            <th>Kategori Asnaf</th>
                            <th>Amaun Dipohon</th>
                            <th>Amaun Diluluskan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // PEMBETULAN SQL: Gunakan alias 'amount_approved' untuk kolum 'total_amount_approved'
                        $query = "SELECT asnaf, amount, total_amount_approved AS amount_approved, status, created_at 
                                  FROM applications 
                                  WHERE user_id = ? 
                                  ORDER BY created_at DESC";
                        
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("i", $u_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $status_db = $row['status'];
                                $badge_class = 'badge-proses';
                                
                                // Logik paparan label status
                                if ($status_db == 'Diluluskan') {
                                    $badge_class = 'badge-lulus';
                                } elseif ($status_db == 'Ditolak') {
                                    $badge_class = 'badge-gagal';
                                }

                                echo "<tr>
                                        <td style='color: #666; font-size: 14px;'>" . date('d/m/Y', strtotime($row['created_at'])) . "</td>
                                        <td><span class='fw-bold' style='color: #1a5928;'>" . htmlspecialchars($row['asnaf']) . "</span></td>
                                        <td class='fw-bold'>RM " . number_format($row['amount'], 2) . "</td>
                                        <td class='text-success fw-bold'>RM " . number_format($row['amount_approved'], 2) . "</td>
                                        <td><span class='badge-status $badge_class'>" . strtoupper($status_db) . "</span></td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'>
                                    <i class='fas fa-folder-open fa-3x mb-3 d-block' style='opacity: 0.3;'></i>
                                    Tiada rekod permohonan ditemui.
                                  </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>