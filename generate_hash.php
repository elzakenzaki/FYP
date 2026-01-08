<?php
// generate_hash.php
// GANTIKAN DENGAN KATA LALUAN YANG ANDA AKAN GUNAKAN UNTUK LOGIN
$password_teks_biasa = 'Markas2026'; 
$hashed_password = password_hash($password_teks_biasa, PASSWORD_DEFAULT);

echo "Hash yang Perlu Disimpan di DB: <strong>" . $hashed_password . "</strong>";
// Salin rentetan penuh bermula $2y$10$...
?>