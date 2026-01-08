<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php';
include 'email_function.php'; 

// 1. KAWALAN AKSES
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin', 'markas'])) {
    header("Location: Login.php"); 
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$current_role = $_SESSION['role'];
$message = '';

// 2. PROSES KEPUTUSAN (KAGAT / ADMIN) & SEMAKAN (MARKAS)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // A. Proses Simpan Ulasan Markas
    if (isset($_POST['simpan_komen_markas'])) {
        $komen_markas = mysqli_real_escape_string($conn, $_POST['markas_comments']);
        $tick_semak = isset($_POST['markas_tick']) ? 1 : 0;
        
        $sql_update = "UPDATE applications SET 
                       markas_comments = '$komen_markas', 
                       markas_review_status = '$tick_semak',
                       status = 'Semakan KAGAT' 
                       WHERE id = $id";

        if ($conn->query($sql_update)) {
            $message = "<div style='padding:15px; background:#d4edda; color:#155724; border-radius:10px; margin-bottom:20px; font-weight:bold; text-align:center;'>✅ Ulasan unit berjaya disimpan dan dihantar ke KAGAT.</div>";
        }
    }

    // B. Proses Kelulusan Akhir KAGAT
    if (isset($_POST['proses_keputusan_akhir'])) {
        $keputusan = mysqli_real_escape_string($conn, $_POST['keputusan']); 
        $nota_kagat = mysqli_real_escape_string($conn, $_POST['kagat_notes']);
        $amaun_lulus = isset($_POST['amount_approved']) ? floatval($_POST['amount_approved']) : 0;

        $sql_lulus = "UPDATE applications SET status = '$keputusan', kagat_notes = '$nota_kagat', total_amount_approved = '$amaun_lulus' WHERE id = $id";

        if ($conn->query($sql_lulus)) {
            $user_query = "SELECT u.email, u.full_name FROM users u JOIN applications a ON u.full_name = a.full_name WHERE a.id = $id";
            $user_res = $conn->query($user_query);
            if ($user_res && $user_res->num_rows > 0) {
                $user = $user_res->fetch_assoc();
                $email_data = ['name' => $user['full_name'], 'ref_no' => "#$id", 'status' => ($keputusan == 'Diluluskan' ? 'Lulus' : 'Gagal')];
                send_ezakat_notification($user['email'], 'decision', $email_data);
            }
            $message = "<div style='padding:15px; background:#d4edda; color:#155724; border-radius:10px; margin-bottom:20px; text-align:center;'>✅ Keputusan $keputusan berjaya disimpan & emel dihantar.</div>";
        }
    }
}

// 3. TARIK DATA PERMOHONAN LENGKAP
$sql = "SELECT * FROM applications WHERE id = $id";
$result = $conn->query($sql);
$data = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;

include 'header.php';
?>

<div style="max-width: 1000px; margin: 40px auto; padding: 0 20px; font-family: 'Inter', sans-serif;">
    <?php echo $message; ?>

    <?php if ($data): ?>
        <div style="background: white; border-radius: 30px; padding: 45px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); border-top: 10px solid #007bff;">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
                <h2 style="font-weight: 900; color: #1a1a1a; margin: 0;">SEMAK PERMOHONAN #<?php echo $data['id']; ?></h2>
                <span style="background: #007bff; color: white; padding: 8px 25px; border-radius: 50px; font-weight: 800; font-size: 11px; text-transform: uppercase;">
                    <?php echo htmlspecialchars($data['status']); ?>
                </span>
            </div>

            <div style="background: #fcfcfc; border: 1px solid #f0f0f0; border-radius: 25px; padding: 35px; margin-bottom: 40px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <div>
                        <label style="font-size: 10px; color: #aaa; font-weight: 900; text-transform: uppercase;">Nama Penuh</label>
                        <p style="font-size: 18px; font-weight: 800; margin: 5px 0 0;"><?php echo htmlspecialchars($data['full_name']); ?></p>
                    </div>
                    <div>
                        <label style="font-size: 10px; color: #aaa; font-weight: 900; text-transform: uppercase;">Unit / Markas</label>
                        <p style="font-size: 18px; font-weight: 800; margin: 5px 0 0;"><?php echo htmlspecialchars($data['markas_id']); ?></p>
                    </div>
                    <div>
                        <label style="font-size: 10px; color: #aaa; font-weight: 900; text-transform: uppercase;">Jumlah Dipohon</label>
                        <p style="font-size: 22px; font-weight: 900; margin: 5px 0 0; color: #28a745;">RM <?php echo number_format($data['amount'], 2); ?></p>
                    </div>
                </div>
            </div>

            <div style="background: #f0fdf4; border: 1px solid #dcfce7; border-radius: 25px; padding: 35px; margin-bottom: 40px;">
                <h4 style="font-weight: 800; color: #166534; margin-top: 0; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    🏦 BUTIRAN PEMBAYARAN (AKAUN BANK)
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <div>
                        <label style="font-size: 10px; color: #166534; font-weight: 900; text-transform: uppercase;">Nama Bank</label>
                        <p style="font-size: 16px; font-weight: 700; margin: 5px 0 0;"><?php echo htmlspecialchars($data['nama_bank'] ?: 'Tidak Dinyatakan'); ?></p>
                    </div>
                    <div>
                        <label style="font-size: 10px; color: #166534; font-weight: 900; text-transform: uppercase;">No. Akaun Bank</label>
                        <p style="font-size: 18px; font-weight: 800; margin: 5px 0 0; color: #1a1a1a; letter-spacing: 1px;"><?php echo htmlspecialchars($data['no_akaun'] ?: 'Tidak Dinyatakan'); ?></p>
                    </div>
                    <div style="grid-column: span 2;">
                        <label style="font-size: 10px; color: #166534; font-weight: 900; text-transform: uppercase;">Nama Pemegang Akaun</label>
                        <p style="font-size: 16px; font-weight: 700; margin: 5px 0 0; text-transform: uppercase;"><?php echo htmlspecialchars($data['nama_pemegang'] ?: 'Tidak Dinyatakan'); ?></p>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 45px;">
                <h4 style="font-weight: 800; margin-bottom: 20px;">📄 DOKUMEN SOKONGAN</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <?php if(!empty($data['doc_ic'])): ?>
                        <a href="uploads/<?php echo $data['doc_ic']; ?>" target="_blank" style="text-decoration: none; border: 2px solid #eee; padding: 20px; border-radius: 15px; text-align: center; color: #444; font-weight: 800;">LIHAT SALINAN IC</a>
                    <?php endif; ?>
                    <?php if(!empty($data['doc_gaji'])): ?>
                        <a href="uploads/<?php echo $data['doc_gaji']; ?>" target="_blank" style="text-decoration: none; border: 2px solid #eee; padding: 20px; border-radius: 15px; text-align: center; color: #444; font-weight: 800;">LIHAT SLIP GAJI</a>
                    <?php endif; ?>
                </div>
            </div>

            <div style="border-top: 1px solid #f0f0f0; padding-top: 30px; margin-top: 30px;">
                <h4 style="font-weight: 800; color: #333;">ULASAN UNIT MARKAS</h4>
                
                <?php if ($current_role === 'markas'): ?>
                    <form method="POST">
                        <div style="margin-bottom: 25px; margin-top: 15px;">
                            <textarea name="markas_comments" rows="4" style="width: 100%; padding: 20px; border: 2px solid #f0f0f0; border-radius: 15px; font-family: inherit; font-size: 15px;" placeholder="Tulis ulasan anda di sini..."><?php echo htmlspecialchars($data['markas_comments'] ?? ''); ?></textarea>
                        </div>
                        <div style="margin-bottom: 35px; display: flex; align-items: center; gap: 12px; background: #f8f9fa; padding: 15px 20px; border-radius: 12px;">
                            <input type="checkbox" name="markas_tick" id="confirm" style="width: 22px; height: 22px; cursor: pointer;" required <?php echo ($data['markas_review_status'] == 1) ? 'checked' : ''; ?>>
                            <label for="confirm" style="font-weight: 700; color: #555; cursor: pointer; font-size: 14px;">Saya mengesahkan maklumat anggota ini adalah benar.</label>
                        </div>
                        <button type="submit" name="simpan_komen_markas" style="width: 100%; background: #007bff; color: white; padding: 20px; border-radius: 50px; border: none; font-weight: 900; cursor: pointer; text-transform: uppercase;">
                            SIMPAN SEMAKAN UNIT
                        </button>
                    </form>
                <?php else: ?>
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 15px; margin-top: 15px; border: 1px solid #eee;">
                        <p style="font-size: 14px; color: #555;"><strong>Status Semakan:</strong> <?php echo ($data['markas_review_status'] == 1) ? '✅ Disahkan oleh Unit' : '⏳ Belum Disahkan oleh Unit'; ?></p>
                        <p style="font-size: 14px; color: #555;"><strong>Ulasan Unit:</strong> <?php echo nl2br(htmlspecialchars($data['markas_comments'] ?? 'Tiada ulasan daripada markas.')); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (in_array($current_role, ['admin', 'superadmin'])): ?>
                <div style="border-top: 5px double #007bff; padding-top: 40px; margin-top: 50px;">
                    <h4 style="font-weight: 900; color: #007bff; text-align: center;">PANEL KELULUSAN KAGAT</h4>
                    <form method="POST">
                        <div style="margin-bottom: 25px; margin-top: 30px;">
                            <label style="font-weight: 800; display: block; margin-bottom: 12px;">Amaun Diluluskan (RM):</label>
                            <input type="number" name="amount_approved" step="0.01" style="width: 100%; padding: 15px; border: 2px solid #007bff; border-radius: 15px; font-size: 18px; font-weight: 800;" value="<?php echo $data['total_amount_approved'] ?: $data['amount']; ?>">
                        </div>
                        <div style="margin-bottom: 25px;">
                            <label style="font-weight: 800; display: block; margin-bottom: 12px;">Nota / Justifikasi Pusat:</label>
                            <textarea name="kagat_notes" rows="4" style="width: 100%; padding: 20px; border: 2px solid #f0f0f0; border-radius: 15px;"><?php echo htmlspecialchars($data['kagat_notes'] ?? ''); ?></textarea>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <button type="submit" name="proses_keputusan_akhir" value="Diluluskan" onclick="setDecision('Diluluskan')" style="background: #28a745; color: white; padding: 20px; border-radius: 50px; border: none; font-weight: 900; cursor: pointer;">LULUSKAN</button>
                            <button type="submit" name="proses_keputusan_akhir" value="Ditolak" onclick="setDecision('Ditolak')" style="background: #dc3545; color: white; padding: 20px; border-radius: 50px; border: none; font-weight: 900; cursor: pointer;">GAGAL / TOLAK</button>
                            <input type="hidden" name="keputusan" id="keputusan_val" value="Diluluskan">
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <div style="margin-top: 30px; text-align: center;">
                <a href="urus_kelulusan.php" style="color: #bbb; text-decoration: none; font-size: 12px; font-weight: 800;">← Kembali ke Senarai</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function setDecision(val) {
    document.getElementById('keputusan_val').value = val;
}
</script>

<?php include 'footer.php'; ?>