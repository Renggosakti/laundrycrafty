<?php
require_once '../config.php';
checkLogin();

$result = $conn->query("SELECT * FROM layanan ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Layanan - LaundryCrafty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #667eea; --secondary: #764ba2; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 260px; background: linear-gradient(180deg, #667eea 0%, #764ba2 100%); padding: 20px 0; box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1); z-index: 1000; }
        .sidebar-logo { text-align: center; padding: 20px; color: white; margin-bottom: 30px; }
        .sidebar-logo i { font-size: 50px; margin-bottom: 10px; }
        .sidebar-logo h3 { font-weight: 700; margin-bottom: 5px; }
        .sidebar-menu { list-style: none; padding: 0 15px; }
        .sidebar-menu a { display: flex; align-items: center; padding: 12px 20px; color: rgba(255, 255, 255, 0.8); text-decoration: none; border-radius: 10px; transition: all 0.3s; margin-bottom: 5px; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255, 255, 255, 0.2); color: white; transform: translateX(5px); }
        .sidebar-menu a i { margin-right: 12px; width: 25px; }
        .main-content { margin-left: 260px; padding: 20px; min-height: 100vh; }
        .top-bar { background: white; padding: 15px 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .page-title h4 { margin: 0; color: #1f2937; font-weight: 700; }
        .breadcrumb { background: none; padding: 0; margin: 0; font-size: 14px; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .user-avatar { width: 45px; height: 45px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 18px; }
        .content-card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); animation: fadeInUp 0.6s ease-out; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .card-header-action { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #f3f4f6; }
        .card-header-action h5 { margin: 0; color: #1f2937; font-weight: 700; font-size: 22px; }
        .btn-add { background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 10px 25px; border-radius: 10px; font-weight: 600; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-add:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3); color: white; }
        .services-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; margin-top: 25px; }
        .service-card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); transition: all 0.3s; position: relative; overflow: hidden; border: 2px solid transparent; }
        .service-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 5px; background: linear-gradient(90deg, var(--primary), var(--secondary)); }
        .service-card:hover { transform: translateY(-8px); box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15); border-color: var(--primary); }
        .service-icon { width: 70px; height: 70px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
        .service-icon i { font-size: 35px; color: white; }
        .service-name { font-size: 20px; font-weight: 700; color: #1f2937; margin-bottom: 10px; }
        .service-price { font-size: 28px; font-weight: 700; color: var(--primary); margin-bottom: 15px; }
        .service-price small { font-size: 14px; color: #6b7280; font-weight: 500; }
        .service-details { color: #6b7280; font-size: 14px; margin-bottom: 15px; }
        .service-details div { margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
        .service-details i { color: var(--primary); width: 20px; }
        .service-actions { display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #f3f4f6; }
        .btn-action { flex: 1; padding: 10px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 5px; }
        .btn-edit { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
        .btn-edit:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(245, 158, 11, 0.3); color: white; }
        .btn-delete { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
        .btn-delete:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3); color: white; }
        .empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
        .empty-state i { font-size: 80px; margin-bottom: 20px; opacity: 0.5; }
        .empty-state h5 { color: #6b7280; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo"><i class="fas fa-tshirt"></i><h3>LaundryCrafty</h3><small>Modern Laundry System</small></div>
        <ul class="sidebar-menu">
            <li><a href="../index.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="../pelanggan/list.php"><i class="fas fa-users"></i> Pelanggan</a></li>
            <li><a href="list.php" class="active"><i class="fas fa-tags"></i> Layanan</a></li>
            <li><a href="../transaksi/list.php"><i class="fas fa-exchange-alt"></i> Transaksi</a></li>
            <li><a href="../laporan/pendapatan.php"><i class="fas fa-chart-line"></i> Laporan</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <h4><i class="fas fa-tags me-2"></i>Data Layanan</h4>
                <nav class="breadcrumb"><a href="../index.php">Dashboard</a> / Layanan</nav>
            </div>
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) ?></div>
                <div><strong><?= $_SESSION['nama_lengkap'] ?></strong><br><small class="text-muted"><?= ucfirst($_SESSION['role']) ?></small></div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header-action">
                <h5><i class="fas fa-list me-2"></i>Daftar Layanan Laundry</h5>
                <a href="tambah.php" class="btn-add"><i class="fas fa-plus"></i> Tambah Layanan</a>
            </div>

            <?php showFlashMessage(); ?>

            <?php if($result->num_rows > 0): ?>
            <div class="services-grid">
                <?php while($row = $result->fetch_assoc()): ?>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <div class="service-name"><?= $row['nama_layanan'] ?></div>
                    <div class="service-price">
                        <?= formatRupiah($row['harga_per_kg']) ?>
                        <small>/kg</small>
                    </div>
                    <div class="service-details">
                        <div><i class="fas fa-clock"></i> Durasi: <?= $row['durasi_hari'] ?> hari</div>
                        <div><i class="fas fa-info-circle"></i> <?= $row['deskripsi'] ?></div>
                    </div>
                    <div class="service-actions">
                        <a href="edit.php?id=<?= $row['id_layanan'] ?>" class="btn-action btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="hapus.php?id=<?= $row['id_layanan'] ?>" class="btn-action btn-delete" onclick="return confirm('Yakin hapus layanan ini?')">
                            <i class="fas fa-trash"></i> Hapus
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-tags"></i>
                <h5>Belum Ada Layanan</h5>
                <p>Klik tombol "Tambah Layanan" untuk menambahkan layanan baru</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>