<?php
require_once '../config.php';
header('Content-Type: application/json');

$periode = isset($_GET['periode']) ? $_GET['periode'] : 'bulan';
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m');

if($periode === 'hari') {
    $where = "DATE(tanggal_masuk) = '$tanggal'";
} elseif($periode === 'minggu') {
    $where = "YEARWEEK(tanggal_masuk) = YEARWEEK('$tanggal')";
} else {
    $where = "DATE_FORMAT(tanggal_masuk, '%Y-%m') = '$tanggal'";
}

$result = $conn->query("SELECT DATE(tanggal_masuk) as tanggal, COUNT(*) as jumlah_transaksi, SUM(total_harga) as total_pendapatan 
                        FROM transaksi WHERE $where GROUP BY DATE(tanggal_masuk) ORDER BY tanggal");

$data = [];
$total_transaksi = 0;
$total_pendapatan = 0;

while($row = $result->fetch_assoc()) {
    $data[] = $row;
    $total_transaksi += $row['jumlah_transaksi'];
    $total_pendapatan += $row['total_pendapatan'];
}

echo json_encode([
    'success' => true,
    'periode' => $periode,
    'tanggal' => $tanggal,
    'summary' => [
        'total_transaksi' => $total_transaksi,
        'total_pendapatan' => $total_pendapatan
    ],
    'detail' => $data
]);
?>