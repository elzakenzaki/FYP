</div> 
<style>
    .footer-standard { 
        background-color: #1a1a1a; color: white; padding: 50px 20px; 
        text-align: center; border-top: 5px solid #28a745; width: 100%;
    }
    .footer-logos { display: flex; justify-content: center; align-items: center; gap: 30px; flex-wrap: wrap; margin-bottom: 35px; }
    .footer-logos img { height: 60px; width: auto; transition: 0.3s; }
    .footer-logos img:hover { transform: scale(1.1); }
    .footer-links-row { margin-bottom: 25px; }
    .footer-links-row a { color: #ffffff; text-decoration: none; font-size: 13px; margin: 0 12px; transition: 0.3s; }
    .footer-links-row a:hover { color: #28a745; text-decoration: underline; }
    .btn-helpdesk { color: #ffc107 !important; font-weight: 800; }
    .copyright-text { font-size: 11px; color: #666; border-top: 1px solid #333; padding-top: 25px; margin-top: 25px; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<footer class="footer-standard">
    <div style="font-weight: 800; margin-bottom: 30px; color: #28a745; letter-spacing: 1.5px; text-transform: uppercase; font-size: 14px;">
        Sokongan Institusi Kerajaan & Ketenteraan
    </div>
    <div class="footer-logos">
        <img src="logo_mindef.png" alt="MINDEF">
        <img src="logo_darat.png" alt="Tentera Darat">
        <img src="logo_navy.png" alt="TLDM">
        <img src="logo_efos.png" alt="TUDM">
        <img src="logo_kagat.png" alt="KAGAT">
        <img src="logo_atm.png" alt="ATM">
    </div>
    <div class="footer-links-row">
        <a href="aduan.php" class="btn-helpdesk"> PUSAT ADUAN & HELPDESK</a> | 
        <a href="#">Dasar Privasi</a> | 
        <a href="#">Dasar Keselamatan</a> | 
        <a href="#">Penafian</a>
    </div>
    <div class="copyright-text">
        © 2026 E-Zakat ATM. Hak Cipta Terpelihara.
    </div>
</footer>

<?php if (isset($_SESSION['message'])): ?>
<script>
    Swal.fire({
        icon: '<?php echo $_SESSION['message_type'] ?? 'success'; ?>',
        title: '<?php echo ($_SESSION['message_type'] ?? 'success') === 'success' ? 'Berjaya!' : 'Perhatian'; ?>',
        text: '<?php echo $_SESSION['message']; ?>',
        confirmButtonColor: '#1e7e34'
    });
</script>
<?php 
    unset($_SESSION['message']); 
    unset($_SESSION['message_type']); 
endif; ?>
</body>
</html>