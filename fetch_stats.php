<?php
// fetch_stats.php
include 'db_connect.php';

// Menukar output kepada format JSON
header('Content-Type: application/json');

// Kiraan Statistik daripada jadual permohonan
$stats = [
    'total_permohonan' => 0,
    'diluluskan' => 0,
    'ditolak' => 0,
    'dalam_proses' => 0
];

// Query untuk jumlah besar
$res1 = mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_permohonan");
$stats['total_permohonan'] = mysqli_fetch_assoc($res1)['total'];

// Query mengikut status (Contoh status: 'Lulus', 'Tolak', 'Baru')
$res2 = mysqli_query($conn, "SELECT 
    SUM(CASE WHEN status_akhir = 'Lulus' THEN 1 ELSE 0 END) as lulus,
    SUM(CASE WHEN status_akhir = 'Tolak' THEN 1 ELSE 0 END) as tolak,
    SUM(CASE WHEN status_akhir = 'Baru' OR status_akhir = 'Semakan Markas' THEN 1 ELSE 0 END) as proses
    FROM tbl_permohonan");

$data = mysqli_fetch_assoc($res2);
$stats['diluluskan'] = $data['lulus'] ?? 0;
$stats['ditolak'] = $data['tolak'] ?? 0;
$stats['dalam_proses'] = $data['proses'] ?? 0;

echo json_encode($stats);
?>