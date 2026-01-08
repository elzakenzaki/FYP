// Dalam export_report.php, pastikan query menggunakan 'id'
$report_query = "SELECT id, full_name, no_kp_tentera, markas_id, status, amount, total_amount_approved, created_at FROM applications 
                 WHERE created_at >= ? AND created_at <= DATE_ADD(?, INTERVAL 1 DAY)";

// Dalam loop fputcsv
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'], // Gunakan id bukan application_id
        $row['full_name'],
        $row['no_kp_tentera'],
        $row['markas_id'],
        $row['status'],
        number_format($row['amount'], 2, '.', ''),
        date('d/m/Y', strtotime($row['created_at']))
    ]);
}