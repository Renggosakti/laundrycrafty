<?php
require_once '../config.php';
checkLogin();

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

$where = "WHERE 1=1";
if ($search) $where .= " AND (p.nama LIKE '%$search%' OR t.id_transaksi LIKE '%$search%')";
if ($status_filter) $where .= " AND t.status = '$status_filter'";

// Query dengan JOIN lengkap
$query = "SELECT 
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
$where
ORDER BY t.created_at DESC
LIMIT 100";

$result = $conn->query($query);

// Debug
$total_db = $conn->query("SELECT COUNT(*) as total FROM transaksi")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - LaundryCrafty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #667eea; --secondary: #764ba2; }
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
        .top-bar { background: white; padding: 15px 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .page-title h4 { margin: 0; color: #1f2937; font-weight: 700; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .user-avatar { width: 45px; height: 45px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 18px; }
        .content-card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); animation: fadeInUp 0.6s; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .card-header-action { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #f3f4f6; }
        .btn-add { background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 10px 25px; border-radius: 10px; font-weight: 600; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-add:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(16,185,129,0.3); color: white; }
        .filter-bar { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
        .filter-bar input, .filter-bar select { padding: 10px 15px; border: 2px solid #e5e7eb; border-radius: 10px; transition: all 0.3s; }
        .filter-bar input { flex: 1; min-width: 250px; }
        .filter-bar input:focus, .filter-bar select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(102,126,234,0.1); }
        .filter-bar button { padding: 10px 20px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border: none; border-radius: 10px; color: white; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .table-modern { width: 100%; margin-top: 20px; border-collapse: separate; border-spacing: 0 10px; }
        .table-modern thead tr { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
        .table-modern thead th { padding: 15px; font-weight: 600; text-align: left; border: none; }
        .table-modern thead th:first-child { border-radius: 10px 0 0 10px; }
        .table-modern thead th:last-child { border-radius: 0 10px 10px 0; }
        .table-modern tbody tr { background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: all 0.3s; }
        .table-modern tbody tr:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .table-modern tbody td { padding: 18px 15px; color: #4b5563; border: none; }
        .table-modern tbody tr td:first-child { border-radius: 10px 0 0 10px; }
        .table-modern tbody tr td:last-child { border-radius: 0 10px 10px 0; }
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge.bg-warning { background: linear-gradient(135deg, #f59e0b, #d97706) !important; }
        .badge.bg-success { background: linear-gradient(135deg, #10b981, #059669) !important; }
        .badge.bg-info { background: linear-gradient(135deg, #3b82f6, #2563eb) !important; }
        .btn-action { padding: 8px 15px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; margin-right: 5px; }
        .btn-edit { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
        .btn-edit:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(245,158,11,0.3); color: white; }
        .btn-delete { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
        .btn-delete:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(239,68,68,0.3); color: white; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo"><i class="fas fa-tshirt"></i><h3>LaundryCrafty</h3></div>
        <ul class="sidebar-menu">
            <li><a href="../index.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="../pelanggan/list.php"><i class="fas fa-users"></i> Pelanggan</a></li>
            <li><a href="../layanan/list.php"><i class="fas fa-tags"></i> Layanan</a></li>
            <li><a href="list.php" class="active"><i class="fas fa-exchange-alt"></i> Transaksi</a></li>
            <li><a href="../laporan/pendapatan.php"><i class="fas fa-chart-line"></i> Laporan</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <h4><i class="fas fa-exchange-alt me-2"></i>Transaksi Laundry</h4>
            </div>
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) ?></div>
                <div><strong><?= $_SESSION['nama_lengkap'] ?></strong><br><small class="text-muted"><?= ucfirst($_SESSION['role']) ?></small></div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header-action">
                <h5><i class="fas fa-list me-2"></i>Daftar Transaksi</h5>
                <a href="tambah.php" class="btn-add"><i class="fas fa-plus"></i> Transaksi Baru</a>
            </div>

            <?php showFlashMessage(); ?>

            <form method="GET" class="filter-bar">
                <input type="text" name="search" placeholder="Cari ID atau nama pelanggan..." value="<?= $search ?>">
                <select name="status">
                    <option value="">Semua Status</option>
                    <option value="proses" <?= $status_filter === 'proses' ? 'selected' : '' ?>>Proses</option>
                    <option value="selesai" <?= $status_filter === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    <option value="diambil" <?= $status_filter === 'diambil' ? 'selected' : '' ?>>Diambil</option>
                </select>
                <button type="submit"><i class="fas fa-search me-2"></i>Filter</button>
            </form>

            <?php if($result->num_rows > 0): ?>
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Berat</th>
                        <th>Total</th>
                        <th>Tgl Masuk</th>
                        <th>Status</th>
                        <th>Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><strong>#<?= $row['id_transaksi'] ?></strong></td>
                        <td><strong><?= $row['nama_pelanggan'] ?></strong><br><small><?= $row['no_hp'] ?></small></td>
                        <td><?= $row['nama_layanan'] ?></td>
                        <td><?= $row['berat'] ?> kg</td>
                        <td><strong><?= formatRupiah($row['total_harga']) ?></strong></td>
                        <td><?= formatTanggal($row['tanggal_masuk']) ?></td>
                        <td><?= getStatusBadge($row['status']) ?></td>
                        <td>
                            <small class="text-muted">
                                <i class="fas fa-user"></i> <?= $row['kasir'] ?>
                            </small>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= $row['id_transaksi'] ?>" class="btn-action btn-edit"><i class="fas fa-edit"></i></a>
                            <a href="hapus.php?id=<?= $row['id_transaksi'] ?>" class="btn-action btn-delete" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div style="text-align: center; padding: 60px; color: #9ca3af;">
                <i class="fas fa-inbox" style="font-size: 80px; opacity: 0.5; margin-bottom: 20px;"></i>
                <h5>Belum Ada Transaksi</h5>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>