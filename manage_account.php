<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php'; 

if (!isset($_SESSION['user_id'])) { header("Location: Login.php"); exit(); }
$u_id = $_SESSION['user_id'];
$message = "";

// PROSES KEMASKINI (Dalam fail yang sama untuk elak ralat Not Found)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $pangkat = mysqli_real_escape_string($conn, $_POST['pangkat']);
    $no_tel = mysqli_real_escape_string($conn, $_POST['no_tel']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat_rumah']);
    $t_lahir = mysqli_real_escape_string($conn, $_POST['tarikh_lahir']);
    $thn_masuk = mysqli_real_escape_string($conn, $_POST['tahun_masuk']);
    $taraf = mysqli_real_escape_string($conn, $_POST['taraf_kahwin']);
    $n_pasangan = mysqli_real_escape_string($conn, $_POST['nama_pasangan']);

    $sql = "UPDATE users SET 
            full_name='$full_name', pangkat='$pangkat', no_tel='$no_tel', 
            alamat_rumah='$alamat', tarikh_lahir='$t_lahir', 
            tahun_masuk='$thn_masuk', taraf_kahwin='$taraf',
            nama_pasangan='$n_pasangan'
            WHERE user_id='$u_id'";
    
    if (mysqli_query($conn, $sql)) {
        $message = "<div class='alert-success'>✓ Profil Berjaya Dikemaskini!</div>";
    } else {
        $message = "<div class='alert-danger'>✗ Ralat: " . mysqli_error($conn) . "</div>";
    }
}

// AMBIL DATA USER TERKINI
$res_user = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$u_id'");
$user = mysqli_fetch_assoc($res_user);

include 'header.php'; 
?>

<style>
    /* RESET UNTUK MEMASTIKAN FOOTER DI BAWAH */
    html, body {
        height: 100%;
        margin: 0;
    }

    .main-body-wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh; /* Paksa ambil tinggi penuh skrin */
        background-color: #f4f7f6;
    }

    .content-area {
        flex: 1 0 auto; /* Menolak footer ke bawah skrin secara automatik */
        display: flex;
        justify-content: center;
        padding-top: 40px;
        padding-bottom: 100px; /* Jarak selamat sebelum footer */
    }

    .kad-borang {
        background: #ffffff;
        width: 95%;
        max-width: 1150px; /* Melebarkan borang */
        padding: 50px;
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        border-top: 10px solid #28a745;
    }

    /* TIPOGRAFI & INPUT JELAS */
    .label-custom {
        font-weight: 800;
        color: #1a1a1a;
        font-size: 15px;
        display: block;
        margin-bottom: 10px;
    }

    .input-custom {
        width: 100%;
        padding: 15px;
        border: 2px solid #ced4da;
        border-radius: 12px;
        box-sizing: border-box;
        font-size: 15px;
        margin-bottom: 25px;
        background-color: #fff;
    }

    .seksyen-badge {
        background: #28a745;
        color: white;
        padding: 12px 25px;
        border-radius: 50px;
        font-weight: 800;
        display: inline-block;
        margin-bottom: 30px;
    }

    .grid-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
    }

    .btn-simpan-profil {
        background: linear-gradient(135deg, #28a745, #1a5928);
        color: white;
        width: 100%;
        padding: 22px;
        border: none;
        border-radius: 50px;
        font-weight: 900;
        font-size: 18px;
        cursor: pointer;
        transition: 0.3s;
        text-transform: uppercase;
        margin-top: 30px;
    }

    .btn-simpan-profil:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }

    .alert-success { background: #d4edda; color: #155724; padding: 20px; border-radius: 12px; margin-bottom: 30px; text-align: center; font-weight: bold; }

    @media (max-width: 768px) { .grid-row { grid-template-columns: 1fr; } }
</style>

<div class="main-body-wrapper">
    <div class="content-area">
        <div class="kad-borang">
            <h2 style="text-align: center; font-weight: 900; color: #1a5928; margin-bottom: 40px; text-transform: uppercase;">Kemaskini Maklumat Akaun</h2>
            
            <?php echo $message; ?>

            <form action="" method="POST">
                <div class="seksyen-badge">1. BUTIRAN PERKHIDMATAN & PEMOHON</div>
                
                <label class="label-custom">NAMA PENUH (HURUF BESAR):</label>
                <input type="text" name="full_name" class="input-custom" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" style="text-transform: uppercase;" required>

                <div class="grid-row">
                    <div>
                        <label class="label-custom">PANGKAT:</label>
                        <input type="text" name="pangkat" class="input-custom" value="<?php echo htmlspecialchars($user['pangkat'] ?? ''); ?>">
                    </div>
                    <div>
                        <label class="label-custom">NO. TELEFON (BIMBIT):</label>
                        <input type="text" name="no_tel" class="input-custom" value="<?php echo htmlspecialchars($user['no_tel'] ?? ''); ?>">
                    </div>
                </div>

                <label class="label-custom">ALAMAT RUMAH (TETAP):</label>
                <textarea name="alamat_rumah" class="input-custom" rows="3"><?php echo htmlspecialchars($user['alamat_rumah'] ?? ''); ?></textarea>

                <div class="grid-row">
                    <div>
                        <label class="label-custom">TARIKH LAHIR:</label>
                        <input type="date" name="tarikh_lahir" class="input-custom" value="<?php echo $user['tarikh_lahir'] ?? ''; ?>">
                    </div>
                    <div>
                        <label class="label-custom">TAHUN MASUK TENTERA:</label>
                        <input type="number" name="tahun_masuk" class="input-custom" value="<?php echo $user['tahun_masuk'] ?? ''; ?>">
                    </div>
                </div>

                <div style="max-width: 50%;">
                    <label class="label-custom">TARAF PERKAHWINAN:</label>
                    <select name="taraf_kahwin" class="input-custom">
                        <option value="Berkahwin" <?php echo ($user['taraf_kahwin'] ?? '') == 'Berkahwin' ? 'selected' : ''; ?>>Berkahwin</option>
                        <option value="Bujang" <?php echo ($user['taraf_kahwin'] ?? '') == 'Bujang' ? 'selected' : ''; ?>>Bujang</option>
                        <option value="Duda/Janda" <?php echo ($user['taraf_kahwin'] ?? '') == 'Duda/Janda' ? 'selected' : ''; ?>>Duda / Janda</option>
                    </select>
                </div>

                <div class="seksyen-badge" style="margin-top: 30px;">2. MAKLUMAT PASANGAN</div>
                
                <label class="label-custom">NAMA ISTERI / PASANGAN:</label>
                <input type="text" name="nama_pasangan" class="input-custom" value="<?php echo htmlspecialchars($user['nama_pasangan'] ?? ''); ?>">

                <button type="submit" name="update_profile" class="btn-simpan-profil">SIMPAN PERUBAHAN AKAUN</button>
            </form>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
</div>