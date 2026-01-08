<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php';

// Skrip ini hanya boleh dijalankan oleh Superadmin untuk tujuan baiki data
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    die("Akses disekat. Sila login sebagai Superadmin untuk jalankan skrip ini.");
}

echo "<h2>🔧 Skrip Pemulihan Data Markas Latihan</h2>";

// 1. KEMASKINI SEMUA PERMOHONAN (ID 1-6)
// Kita tukar semua markas_id kepada satu format yang tepat: "Markas Latihan"
$target = "Markas Latihan";
$sql1 = "UPDATE applications SET markas_id = '$target'";

if ($conn->query($sql1)) {
    echo "✅ <b>Jadual Applications:</b> " . $conn->affected_rows . " permohonan telah ditukar kepada '$target'.<br>";
}

// 2. KEMASKINI AKAUN PEGAWAI (Jadual Users)
// Pastikan akaun pegawai markas juga mempunyai ejaan yang tepat sama
$sql2 = "UPDATE users SET markas_id = '$target' WHERE role = 'markas' AND full_name LIKE '%JAAFAR%'"; 

if ($conn->query($sql2)) {
    echo "✅ <b>Jadual Users:</b> Akaun Pegawai Markas telah dikemaskini kepada '$target'.<br>";
}

echo "<p style='color:blue;'><b>ARAHAN:</b> Sila LOGOUT dari akaun Markas dan LOGIN SEMULA untuk kesan perubahan.</p>";
echo "<a href='dashboard_superadmin.php'>Kembali ke Dashboard</a>";
?>