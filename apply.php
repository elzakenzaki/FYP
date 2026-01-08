<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php'; 

// KAWALAN AKSES: Pastikan pengguna log masuk
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

// AMBIL DATA PROFIL SECARA AUTOMATIK
$full_name = "";
$u_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT full_name FROM users WHERE user_id = ?");
$stmt->bind_param("i", $u_id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
if ($res) {
    $full_name = $res['full_name'];
}

$markas_id = $_SESSION['markas_id'] ?? 'Markas Latihan'; 

include 'header.php'; 
?>

<style>
    .form-section-title { 
        margin-top: 25px; 
        border-bottom: 2px solid #28a745; 
        padding-bottom: 5px; 
        color: #28a745; 
        font-size: 1.1em; 
        font-weight: 800; 
        text-transform: uppercase; 
        margin-bottom: 15px;
    }
    .input-field {
        width: 100%; 
        padding: 12px; 
        border: 1px solid #ddd; 
        border-radius: 8px; 
        margin-bottom: 15px;
        box-sizing: border-box;
    }
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .radio-group {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }
</style>

<div style="max-width: 900px; margin: 40px auto; padding: 0 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border-top: 6px solid #28a745;">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="font-weight: 800; color: #333; margin-bottom: 10px;">Borang Permohonan Zakat</h1>
            <p style="color: #666;">Sila lengkapkan butiran permohonan anda di bawah.</p>
        </div>

        <form action="proses_apply.php" method="POST" enctype="multipart/form-data">
            
            <div class="form-section-title">Maklumat Pemohon</div>
            <div class="grid-2">
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px;">Nama Penuh:</label>
                    <input type="text" class="input-field" value="<?php echo htmlspecialchars($full_name); ?>" style="background:#f0f0f0;" readonly>
                </div>
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 8px;">Markas / Unit:</label>
                    <input type="text" class="input-field" value="<?php echo htmlspecialchars($markas_id); ?>" style="background:#f0f0f0;" readonly>
                </div>
            </div>

            <div class="form-section-title">1. Maklumat Kewangan (RM Sebulan)</div>
            <div class="grid-2">
                <div>
                    <label style="font-weight: 600;">Pendapatan Diri:</label>
                    <input type="number" name="pendapatan_diri" step="0.01" class="input-field" value="0.00" required>
                </div>
                <div>
                    <label style="font-weight: 600;">Pendapatan Pasangan:</label>
                    <input type="number" name="pendapatan_isteri_suami" step="0.01" class="input-field" value="0.00">
                </div>
                <div>
                    <label style="font-weight: 600;">Perbelanjaan Asasi (Makan):</label>
                    <input type="number" name="perbelanjaan_asasi" step="0.01" class="input-field" value="0.00" required>
                </div>
                <div>
                    <label style="font-weight: 600;">Sewa Rumah / Utiliti:</label>
                    <input type="number" name="perbelanjaan_sewa" step="0.01" class="input-field" value="0.00">
                </div>
            </div>

            <div class="form-section-title">2. Tanggungan & Kediaman</div>
            <div class="grid-2">
                <div>
                    <label style="font-weight: 600;">Bilangan Anak:</label>
                    <input type="number" name="jumlah_tanggungan_anak" class="input-field" value="0" required>
                </div>
                <div>
                    <label style="font-weight: 600;">Tanggungan Lain:</label>
                    <input type="number" name="jumlah_tanggungan_lain" class="input-field" value="0" required>
                </div>
            </div>
            
            <label style="font-weight: 600;">Tempat Kediaman:</label>
            <div class="radio-group">
                <label><input type="radio" name="tempat_kediaman" value="Rumah Sendiri" required> Rumah Sendiri</label>
                <label><input type="radio" name="tempat_kediaman" value="Rumah Sewa"> Rumah Sewa</label>
                <label><input type="radio" name="tempat_kediaman" value="Kuarters"> Kuarters</label>
            </div>

            <div class="form-section-title">3. Maklumat Bantuan</div>
            <label style="font-weight: 700;">Kategori Asnaf:</label>
            <select name="asnaf" class="input-field" required>
                <option value="Miskin">Miskin</option>
                <option value="Fakir">Fakir</option>
                <option value="Muallaf">Muallaf</option>
                <option value="Gharimin">Gharimin</option>
            </select>

            <label style="font-weight: 700;">Tujuan & Sebab Permohonan:</label>
            <textarea name="bantuan_tujuan" rows="4" class="input-field" placeholder="Sila nyatakan sebab permohonan..." required></textarea>

            <label style="font-weight: 700;">Jumlah Dipohon (RM):</label>
            <input type="number" name="amount" step="0.01" class="input-field" placeholder="0.00" required>

            <div class="form-section-title">3.5 Maklumat Akaun Bank</div>
            <div class="grid-2">
                <div>
                    <label style="font-weight: 600;">Nama Bank:</label>
                    <select name="nama_bank" class="input-field" required>
                        <option value="">-- Pilih Bank --</option>
                        <option value="Affin Bank">Affin Bank</option>
                        <option value="Bank Islam">Bank Islam</option>
                        <option value="BSN">Bank Simpanan Nasional (BSN)</option>
                        <option value="CIMB">CIMB Bank</option>
                        <option value="Maybank">Maybank</option>
                        <option value="RHB">RHB Bank</option>
                    </select>
                </div>
                <div>
                    <label style="font-weight: 600;">No. Akaun Bank:</label>
                    <input type="text" name="no_akaun" class="input-field" placeholder="Contoh: 164123456789" required pattern="\d+" title="Sila masukkan nombor sahaja">
                </div>
            </div>
            <label style="font-weight: 600;">Nama Pemegang Akaun:</label>
            <input type="text" name="nama_pemegang" class="input-field" placeholder="NAMA PENUH SEPERTI DALAM KAD PENGENALAN" required>

            <div class="form-section-title">4. Dokumen Sokongan</div>
            <div style="background: #eef9f0; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9em; color: #155724;">
                Sila muat naik salinan IC dan Slip Gaji (Format PDF/JPG/PNG).
            </div>
            <div class="grid-2">
                <div>
                    <label style="font-weight: 600;">Salinan IC:</label>
                    <input type="file" name="doc_ic" class="input-field">
                </div>
                <div>
                    <label style="font-weight: 600;">Slip Gaji:</label>
                    <input type="file" name="doc_gaji" class="input-field">
                </div>
            </div>

            <div style="margin: 30px 0; padding: 20px; background: #fff3cd; border-radius: 10px; border: 1px solid #ffeeba;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="pengakuan" value="1" required style="width: 20px; height: 20px;">
                    <span style="font-weight: 700; color: #856404;">Saya mengaku bahawa segala maklumat yang diberikan adalah benar dan sahih.</span>
                </label>
            </div>

            <button type="submit" name="submit_apply" style="background: #28a745; color: white; width: 100%; padding: 16px; border: none; border-radius: 50px; font-weight: 800; cursor: pointer; text-transform: uppercase; font-size: 1.1em; transition: 0.3s;">Hantar Permohonan Zakat</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>