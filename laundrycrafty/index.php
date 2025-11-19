<?php
require_once 'config.php';
checkLogin();

// Statistics
$stats = [
    'pelanggan' => $conn->query("SELECT COUNT(*) as total FROM pelanggan")->fetch_assoc()['total'],
    'transaksi_hari_ini' => $conn->query("SELECT COUNT(*) as total FROM transaksi WHERE DATE(tanggal_masuk) = CURDATE()")->fetch_assoc()['total'],
    'pendapatan_hari_ini' => $conn->query("SELECT COALESCE(SUM(total_harga), 0) as total FROM transaksi WHERE DATE(tanggal_masuk) = CURDATE()")->fetch_assoc()['total'],
    'transaksi_proses' => $conn->query("SELECT COUNT(*) as total FROM transaksi WHERE status = 'proses'")->fetch_assoc()['total']
];

// Recent Transactions dengan info kasir - FIX QUERY
$query_transaksi = "SELECT 
    t.id_transaksi,
    t.tanggal_masuk,
    t.tanggal_selesai,
    t.berat,
    t.total_harga,
    t.status,
    p.nama as nama_pelanggan,
    p.no_hp,
    l.nama_layanan,
    u.nama_lengkap as kasir,
    u.username as username_kasir
FROM transaksi t
LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
LEFT JOIN layanan l ON t.id_layanan = l.id_layanan
LEFT JOIN user u ON t.id_user = u.id_user
ORDER BY t.created_at DESC
LIMIT 10";

$recent_transactions = $conn->query($query_transaksi);

// Debug - cek jumlah transaksi
$total_transaksi = $conn->query("SELECT COUNT(*) as total FROM transaksi")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - LaundryCrafty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1f2937;
            --light: #f3f4f6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 260px;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            padding: 20px 0;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: all 0.3s;
        }

        .sidebar-logo {
            text-align: center;
            padding: 20px;
            color: white;
            margin-bottom: 30px;
        }

        .sidebar-logo i {
            font-size: 50px;
            margin-bottom: 10px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .sidebar-logo h3 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .sidebar-logo small {
            opacity: 0.9;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 15px;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateX(5px);
        }

        .sidebar-menu a i {
            margin-right: 12px;
            font-size: 18px;
            width: 25px;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 20px;
            min-height: 100vh;
        }

        /* Top Bar */
        .top-bar {
            background: white;
            padding: 15px 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .welcome-text h4 {
            margin: 0;
            color: var(--dark);
            font-weight: 700;
        }

        .welcome-text p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 18px;
        }

        .btn-logout {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, var(--primary), var(--secondary));
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 15px;
        }

        .stat-card.primary .stat-icon {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .stat-card.success .stat-icon {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .stat-card.warning .stat-icon {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .stat-card.info .stat-icon {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .stat-label {
            color: #6b7280;
            font-size: 14px;
            font-weight: 600;
        }

        /* Recent Transactions */
        .content-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .card-header-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f3f4f6;
        }

        .card-header-custom h5 {
            margin: 0;
            color: var(--dark);
            font-weight: 700;
        }

        .table-modern {
            width: 100%;
            margin-top: 15px;
        }

        .table-modern thead {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .table-modern th {
            padding: 15px;
            font-weight: 600;
            text-align: left;
            border: none;
        }

        .table-modern tbody tr {
            transition: all 0.3s;
            border-bottom: 1px solid #f3f4f6;
        }

        .table-modern tbody tr:hover {
            background: #f9fafb;
            transform: scale(1.01);
        }

        .table-modern td {
            padding: 15px;
            color: #4b5563;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .animate-card:nth-child(1) { animation-delay: 0.1s; }
        .animate-card:nth-child(2) { animation-delay: 0.2s; }
        .animate-card:nth-child(3) { animation-delay: 0.3s; }
        .animate-card:nth-child(4) { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <i class="fas fa-tshirt"></i>
            <h3>LaundryCrafty</h3>
            <small>Modern Laundry System</small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="pelanggan/list.php"><i class="fas fa-users"></i> Pelanggan</a></li>
            <li><a href="layanan/list.php"><i class="fas fa-tags"></i> Layanan</a></li>
            <li><a href="transaksi/list.php"><i class="fas fa-exchange-alt"></i> Transaksi</a></li>
            <li><a href="laporan/pendapatan.php"><i class="fas fa-chart-line"></i> Laporan</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="welcome-text">
                <h4>Selamat Datang, <?= $_SESSION['nama_lengkap'] ?>!</h4>
                <p><?= date('l, d F Y') ?></p>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    <?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) ?>
                </div>
                <div>
                    <strong><?= $_SESSION['nama_lengkap'] ?></strong>
                    <br><small class="text-muted"><?= ucfirst($_SESSION['role']) ?></small>
                </div>
                <a href="logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card primary animate-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?= $stats['pelanggan'] ?></div>
                <div class="stat-label">Total Pelanggan</div>
            </div>

            <div class="stat-card success animate-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-value"><?= $stats['transaksi_hari_ini'] ?></div>
                <div class="stat-label">Transaksi Hari Ini</div>
            </div>

            <div class="stat-card warning animate-card">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-value"><?= formatRupiah($stats['pendapatan_hari_ini']) ?></div>
                <div class="stat-label">Pendapatan Hari Ini</div>
            </div>

            <div class="stat-card info animate-card">
                <div class="stat-icon">
                    <i class="fas fa-spinner"></i>
                </div>
                <div class="stat-value"><?= $stats['transaksi_proses'] ?></div>
                <div class="stat-label">Dalam Proses</div>
            </div>
        </div>

        <!-- Recent Transactions & Activity -->
        <div class="row">
            <div class="col-md-8">
                <div class="content-card animate__animated animate__fadeIn">
                    <div class="card-header-custom">
                        <h5><i class="fas fa-history me-2"></i>Transaksi Terbaru</h5>
                        <a href="transaksi/list.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Berat</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Oleh</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $recent_transactions->fetch_assoc()): ?>
                            <tr>
                                <td><strong>#<?= $row['id_transaksi'] ?></strong></td>
                                <td><?= $row['nama_pelanggan'] ?></td>
                                <td><?= $row['nama_layanan'] ?></td>
                                <td><?= $row['berat'] ?> kg</td>
                                <td><strong><?= formatRupiah($row['total_harga']) ?></strong></td>
                                <td><?= getStatusBadge($row['status']) ?></td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-user-circle"></i> <?= $row['kasir'] ?>
                                    </small>
                                </td>
                                <td><?= formatTanggal($row['tanggal_masuk']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Real-time Activity Logs -->
            <div class="col-md-4">
                <div class="content-card animate__animated animate__fadeIn">
                    <div class="card-header-custom">
                        <h5><i class="fas fa-bell me-2"></i>Aktivitas Terbaru</h5>
                        <span class="badge bg-success" id="liveIndicator">
                            <i class="fas fa-circle" style="font-size: 8px; animation: pulse 2s infinite;"></i> Live
                        </span>
                    </div>
                    
                    <div class="activity-timeline" id="activityTimeline">
                        <?php 
                        $activities = getRecentActivities(10);
                        while($act = $activities->fetch_assoc()): 
                        ?>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <?php
                                $icons = [
                                    'login' => 'sign-in-alt',
                                    'logout' => 'sign-out-alt',
                                    'add_pelanggan' => 'user-plus',
                                    'add_layanan' => 'tags',
                                    'add_transaksi' => 'shopping-cart',
                                    'edit' => 'edit',
                                    'delete' => 'trash'
                                ];
                                $icon = $icons[$act['activity_type']] ?? 'circle';
                                ?>
                                <i class="fas fa-<?= $icon ?>"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-user"><?= $act['nama_lengkap'] ?></div>
                                <div class="activity-desc"><?= $act['description'] ?></div>
                                <div class="activity-time">
                                    <i class="fas fa-clock"></i>
                                    <?php
                                    $time = strtotime($act['created_at']);
                                    $diff = time() - $time;
                                    if ($diff < 60) echo $diff . ' detik lalu';
                                    elseif ($diff < 3600) echo floor($diff / 60) . ' menit lalu';
                                    elseif ($diff < 86400) echo floor($diff / 3600) . ' jam lalu';
                                    else echo date('d/m/Y H:i', $time);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .activity-timeline {
            max-height: 500px;
            overflow-y: auto;
            padding-right: 10px;
        }
        .activity-timeline::-webkit-scrollbar {
            width: 5px;
        }
        .activity-timeline::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 10px;
        }
        .activity-item {
            display: flex;
            gap: 15px;
            padding: 15px;
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.3s;
        }
        .activity-item:hover {
            background: #f9fafb;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .activity-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }
        .activity-content {
            flex: 1;
        }
        .activity-user {
            font-weight: 600;
            color: #1f2937;
            font-size: 14px;
        }
        .activity-desc {
            color: #6b7280;
            font-size: 13px;
            margin: 3px 0;
        }
        .activity-time {
            color: #9ca3af;
            font-size: 12px;
        }
        .activity-time i {
            font-size: 10px;
            margin-right: 3px;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh activity logs every 10 seconds
        setInterval(function() {
            fetch('api/activities.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateActivityTimeline(data.activities);
                    }
                })
                .catch(error => console.log('Error:', error));
        }, 10000);

        function updateActivityTimeline(activities) {
            const timeline = document.getElementById('activityTimeline');
            if (!timeline) return;
            
            let html = '';
            activities.forEach(act => {
                const icons = {
                    'login': 'sign-in-alt',
                    'logout': 'sign-out-alt',
                    'add_pelanggan': 'user-plus',
                    'add_layanan': 'tags',
                    'add_transaksi': 'shopping-cart',
                    'edit': 'edit',
                    'delete': 'trash'
                };
                const icon = icons[act.activity_type] || 'circle';
                
                html += `
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-${icon}"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-user">${act.nama_lengkap}</div>
                            <div class="activity-desc">${act.description}</div>
                            <div class="activity-time">
                                <i class="fas fa-clock"></i> ${act.time_ago}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            timeline.innerHTML = html;
        }
    </script>
</body>
</html>