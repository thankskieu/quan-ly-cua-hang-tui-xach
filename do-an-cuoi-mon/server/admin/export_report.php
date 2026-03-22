<?php
require_once __DIR__ . "/../models/bill.php";

$billObj = new Bill();
$stats = $billObj->getRevenueStats(); // Use the existing revenue stats function

$filename = "bao_cao_doanh_thu_" . date('Ymd_His') . ".csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// UTF-8 BOM for Excel compatibility with Vietnamese characters
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// CSV Header
fputcsv($output, ['Ngày', 'Doanh thu']);

// CSV Data
foreach ($stats as $row) {
    fputcsv($output, [date('d/m/Y', strtotime($row['date'])), $row['revenue']]);
}

fclose($output);
exit;
?>