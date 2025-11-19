<?php
require_once '../config.php';
checkLogin();

// Search functionality
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$where = $search ? "WHERE nama LIKE '%$search%' OR no_hp LIKE '%$search%'" : '';

$result = $conn->query("SELECT * FROM pelanggan $where ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggan - LaundryCrafty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
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
        }

        .sidebar-logo h3 {
            font-weight: 700;
            margin-bottom: 5px;
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

        .main-content {
            margin-left: 260px;
            padding: 20px;
            min-height: 100vh;
        }

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

        .page-title h4 {
            margin: 0;
            color: #1f2937;
            font-weight: 700;
        }

        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
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

        .content-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            animation: fadeInUp 0.6s ease-out;
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

        .card-header-action {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f3f4f6;
        }

        .card-header-action h5 {
            margin: 0;
            color: #1f2937;
            font-weight: 700;
            font-size: 22px;
        }

        .btn-add {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .search-box {
            position: relative;
            margin-bottom: 20px;
        }

        .search-box input {
            width: 100%;
            padding: 12px 45px 12px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .search-box button {
            position: absolute;
            right: 5px;
            top: 5px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .search-box button:hover {
            transform: scale(1.05);
        }

        .table-modern {
            width: 100%;
            margin-top: 20px;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .table-modern thead tr {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .table-modern thead th {
            padding: 15px;
            font-weight: 600;
            text-align: left;
            border: none;
        }

        .table-modern thead th:first-child {
            border-radius: 10px 0 0 10px;
        }

        .table-modern thead th:last-child {
            border-radius: 0 10px 10px 0;
        }

        .table-modern tbody tr {
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .table-modern tbody tr:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .table-modern tbody td {
            padding: 18px 15px;
            color: #4b5563;
            border: none;
        }

        .table-modern tbody tr td:first-child {
            border-radius: 10px 0 0 10px;
        }

        .table-modern tbody tr td:last-child {
            border-radius: 0 10px 10px 0;
        }

        .btn-action {
            padding: 8px 15px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-right: 5px;
        }

        .btn-edit {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(245, 158, 11, 0.3);
            color: white;
        }

        .btn-delete {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h5 {
            color: #6b7280;
            margin-bottom: 10px;
        }
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
            <li><a href="../index.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="list.php" class="active"><i class="fas fa-users"></i> Pelanggan</a></li>
            <li><a href="../layanan/list.php"><i class="fas fa-tags"></i> Layanan</a></li>
            <li><a href="../transaksi/list.php"><i class="fas fa-exchange-alt"></i> Transaksi</a></li>
            <li><a href="../laporan/pendapatan.php"><i class="fas fa-chart-line"></i> Laporan</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="page-title">
                <h4><i class="fas fa-users me-2"></i>Data Pelanggan</h4>
                <nav class="breadcrumb">
                    <a href="../index.php">Dashboard</a> / Pelanggan
                </nav>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    <?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) ?>
                </div>
                <div>
                    <strong><?= $_SESSION['nama_lengkap'] ?></strong>
                    <br><small class="text-muted"><?= ucfirst($_SESSION['role']) ?></small>
                </div>
            </div>
        </div>

        <!-- Content Card -->
        <div class="content-card">
            <div class="card-header-action">
                <h5><i class="fas fa-list me-2"></i>Daftar Pelanggan</h5>
                <a href="tambah.php" class="btn-add">
                    <i class="fas fa-plus"></i> Tambah Pelanggan
                </a>
            </div>

            <?php showFlashMessage(); ?>

            <!-- Search Box -->
            <form method="GET" class="search-box">
                <input type="text" name="search" placeholder="Cari nama atau nomor HP pelanggan..." value="<?= $search ?>">
                <button type="submit"><i class="fas fa-search me-2"></i>Cari</button>
            </form>

            <!-- Table -->
            <?php if($result->num_rows > 0): ?>
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Pelanggan</th>
                        <th>Alamat</th>
                        <th>No. HP</th>
                        <th>Email</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><strong>#<?= $row['id_pelanggan'] ?></strong></td>
                        <td><strong><?= $row['nama'] ?></strong></td>
                        <td><?= $row['alamat'] ?: '-' ?></td>
                        <td><i class="fas fa-phone me-1"></i><?= $row['no_hp'] ?></td>
                        <td><?= $row['email'] ?: '-' ?></td>
                        <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id_pelanggan'] ?>" class="btn-action btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="hapus.php?id=<?= $row['id_pelanggan'] ?>" class="btn-action btn-delete" onclick="return confirm('Yakin hapus pelanggan ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h5>Belum Ada Data Pelanggan</h5>
                <p>Klik tombol "Tambah Pelanggan" untuk menambahkan data baru</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>