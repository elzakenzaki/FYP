<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php'; 

// AUTO-FILL DATA
$user_name = "";
$user_email = "";

if (isset($_SESSION['user_id'])) {
    $u_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT full_name, email FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $u_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res) {
        $user_name = $res['full_name'];
        $user_email = $res['email'];
    }
}

include 'header.php'; // Memanggil header yang anda mahukan
?>

<div style="max-width: 800px; margin: 50px auto; padding: 0 20px;">
    <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border-top: 6px solid #ffc107;">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="font-weight: 800; color: #333; margin-bottom: 10px;">Pusat Aduan & Helpdesk</h1>
            <p style="color: #666;">Sila nyatakan masalah atau pertanyaan anda.</p>
        </div>

        <form action="proses_aduan.php" method="POST">
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; margin-bottom: 8px;">Nama Penuh:</label>
                <input type="text" name="nama_pengadu" value="<?php echo htmlspecialchars($user_name); ?>" 
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; <?php echo $user_name ? 'background:#f0f0f0;' : ''; ?>" 
                       required <?php echo $user_name ? 'readonly' : ''; ?>>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; margin-bottom: 8px;">Alamat Emel / No. Telefon:</label>
                <input type="text" name="kontak_pengadu" value="<?php echo htmlspecialchars($user_email); ?>" 
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; <?php echo $user_email ? 'background:#f0f0f0;' : ''; ?>" 
                       required <?php echo $user_email ? 'readonly' : ''; ?>>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; margin-bottom: 8px;">Kategori Aduan:</label>
                <select name="kategori_aduan" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;" required>
                    <option value="Masalah Teknikal Sistem">Masalah Teknikal Sistem</option>
                    <option value="Pertanyaan Zakat">Pertanyaan Zakat</option>
                    <option value="Lain-lain">Lain-lain</option>
                </select>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 700; margin-bottom: 8px;">Butiran Aduan:</label>
                <textarea name="mesej_aduan" rows="5" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;" placeholder="Terangkan masalah anda di sini..." required></textarea>
            </div>

            <button type="submit" name="submit_aduan" style="background: #ffc107; color: #333; width: 100%; padding: 15px; border: none; border-radius: 50px; font-weight: 800; cursor: pointer; text-transform: uppercase;">HANTAR ADUAN SEKARANG</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>