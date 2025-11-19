<?php
require_once '../config.php';
checkLogin();

$periode = isset($_GET['periode']) ? $_GET['periode'] : 'bulan';
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m');

// Query berdasarkan periode
if ($periode === 'hari') {
    $where = "DATE(tanggal_masuk) = '$tanggal'";
    $group = "DATE(tanggal_masuk)";
} elseif ($periode === 'minggu') {
    $where = "YEARWEEK(tanggal_masuk) = YEARWEEK('$tanggal')";
    $group = "DATE(tanggal_masuk)";
} else {
    $where = "DATE_FORMAT(tanggal_masuk, '%Y-%m') = '$tanggal'";
    $group = "DATE(tanggal_masuk)";
}

$result = $conn->query("SELECT DATE(tanggal_masuk) as tgl, COUNT(*) as jumlah, SUM(total_harga) as pendapatan 
                        FROM transaksi WHERE $where GROUP BY $group ORDER BY tgl");

$total_transaksi = 0;
$total_pendapatan = 0;
$chart_labels = [];
$chart_data = [];

while($row = $result->fetch_assoc()) {
    $total_transaksi += $row['jumlah'];
    $total_pendapatan += $row['pendapatan'];
    $chart_labels[] = date('d M', strtotime($row['tgl']));
    $chart_data[] = $row['pendapatan'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pendapatan - LaundryCrafty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <style>
        body { font-family: 'Segoe UI'; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 260px; background: linear-gradient(180deg, #667eea 0%, #764ba2 100%); padding: 20px 0; box-shadow: 4px 0 20px rgba(0,0,0,0.1); z-index: 1000; }
        .sidebar-logo { text-align: center; padding: 20px; color: white; margin-bottom: 30px; }
        .sidebar-logo i { font-size: 50px; margin-bottom: 10px; }
        .sidebar-logo h3 { font-weight: 700; }
        .sidebar-menu { list-style: none; padding: 0 15px; }
        .sidebar-menu a { display: flex; align-items: center; padding: 12px 20px; color: rgba(255,255,255,0.8); text-decoration: none; border-radius: 10px; transition: all 0.3s; margin-bottom: 5px; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255,255,255,0.2); color: white; transform: translateX(5px); }
        .sidebar-menu a i { margin-right: 12px; width: 25px; }
        .main-content { margin-left: 260px; padding: 20px; }
        .top-bar { background: white; padding: 15px 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .content-card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 20px; animation: fadeInUp 0.6s; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .filter-bar { display: flex; gap: 15px; margin-bottom: 30px; }
        .filter-bar select, .filter-bar input { padding: 10px 15px; border: 2px solid #e5e7eb; border-radius: 10px; }
        .filter-bar button { padding: 10px 20px; background: linear-gradient(135deg, #667eea, #764ba2); border: none; border-radius: 10px; color: white; font-weight: 600; cursor: pointer; }
        .stats-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-box { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 30px; border-radius: 15px; text-align: center; }
        .stat-box .value { font-size: 36px; font-weight: 700; margin-bottom: 10px; }
        .stat-box .label { font-size: 16px; opacity: 0.9; }
        .chart-container { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo"><i class="fas fa-tshirt"></i><h3>LaundryCrafty</h3></div>
        <ul class="sidebar-menu">
            <li><a href="../index.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="../pelanggan/list.php"><i class="fas fa-users"></i> Pelanggan</a></li>
            <li><a href="../layanan/list.php"><i class="fas fa-tags"></i> Layanan</a></li>
            <li><a href="../transaksi/list.php"><i class="fas fa-exchange-alt"></i> Transaksi</a></li>
            <li><a href="pendapatan.php" class="active"><i class="fas fa-chart-line"></i> Laporan</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h4><i class="fas fa-chart-line me-2"></i>Laporan Pendapatan</h4>
        </div>

        <div class="content-card">
            <form method="GET" class="filter-bar">
                <select name="periode">
                    <option value="hari" <?= $periode === 'hari' ? 'selected' : '' ?>>Harian</option>
                    <option value="minggu" <?= $periode === 'minggu' ? 'selected' : '' ?>>Mingguan</option>
                    <option value="bulan" <?= $periode === 'bulan' ? 'selected' : '' ?>>Bulanan</option>
                </select>
                <input type="<?= $periode === 'hari' ? 'date' : 'month' ?>" name="tanggal" value="<?= $tanggal ?>">
                <button type="submit"><i class="fas fa-filter me-2"></i>Filter</button>
            </form>

            <div class="stats-row">
                <div class="stat-box">
                    <div class="value"><?= $total_transaksi ?></div>
                    <div class="label"><i class="fas fa-shopping-cart me-2"></i>Total Transaksi</div>
                </div>
                <div class="stat-box">
                    <div class="value"><?= formatRupiah($total_pendapatan) ?></div>
                    <div class="label"><i class="fas fa-money-bill-wave me-2"></i>Total Pendapatan</div>
                </div>
            </div>

            <div class="chart-container">
                <canvas id="pendapatanChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('pendapatanChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($chart_labels) ?>,
                datasets: [{
                    label: 'Pendapatan',
                    data: <?= json_encode($chart_data) ?>,
                    backgroundColor: 'rgba(102, 126, 234, 0.2)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, position: 'top' },
                    title: { display: true, text: 'Grafik Pendapatan', font: { size: 18, weight: 'bold' } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: value => 'Rp ' + value.toLocaleString('id-ID') } }
                }
            }
        });
    </script>
</body>
</html>