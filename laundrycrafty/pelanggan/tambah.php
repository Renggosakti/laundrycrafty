<?php
require_once '../config.php';
checkLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($_POST['nama']);
    $alamat = sanitize($_POST['alamat']);
    $no_hp = sanitize($_POST['no_hp']);
    $email = sanitize($_POST['email']);
    
    $sql = "INSERT INTO pelanggan (nama, alamat, no_hp, email) VALUES ('$nama', '$alamat', '$no_hp', '$email')";
    
    if ($conn->query($sql)) {
        // Log activity
        logActivity('add_pelanggan', "Menambahkan pelanggan baru: {$nama}");
        
        flashMessage('success', 'Pelanggan berhasil ditambahkan!');
        header('Location: list.php');
        exit;
    } else {
        $error = 'Gagal menambahkan pelanggan: ' . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pelanggan - LaundryCrafty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
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
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 15px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s;
            margin-bottom: 5px;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateX(5px);
        }

        .sidebar-menu a i {
            margin-right: 12px;
            width: 25px;
        }

        .main-content {
            margin-left: 260px;
            padding: 20px;
        }

        .top-bar {
            background: white;
            padding: 15px 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
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

        .form-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            max-width: 800px;
            margin: 0 auto;
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

        .form-header {
            text-align: center;
            margin-bottom: 35px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f3f4f6;
        }

        .form-header i {
            font-size: 60px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
        }

        .form-header h4 {
            color: #1f2937;
            font-weight: 700;
            margin: 0;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            color: #374151;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }

        .form-label .required {
            color: #ef4444;
        }

        .form-control {
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            width: 100%;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .btn-group-action {
            display: flex;
            gap: 15px;
            margin-top: 35px;
        }

        .btn-submit {
            flex: 1;
            padding: 14px;
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }

        .btn-cancel {
            flex: 1;
            padding: 14px;
            background: #e5e7eb;
            border: none;
            border-radius: 10px;
            color: #374151;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-cancel:hover {
            background: #d1d5db;
            color: #1f2937;
        }

        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .input-icon .form-control {
            padding-left: 45px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <i class="fas fa-tshirt"></i>
            <h3>LaundryCrafty</h3>
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
        <div class="top-bar">
            <div class="page-title">
                <h4><i class="fas fa-user-plus me-2"></i>Tambah Pelanggan</h4>
                <nav class="breadcrumb">
                    <a href="../index.php">Dashboard</a> / <a href="list.php">Pelanggan</a> / Tambah
                </nav>
            </div>
        </div>

        <div class="form-card">
            <div class="form-header">
                <i class="fas fa-user-plus"></i>
                <h4>Form Tambah Pelanggan Baru</h4>
            </div>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <div class="input-icon">
                        <i class="fas fa-map-marker-alt"></i>
                        <textarea name="alamat" class="form-control" placeholder="Masukkan alamat lengkap"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor HP <span class="required">*</span></label>
                    <div class="input-icon">
                        <i class="fas fa-phone"></i>
                        <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="email@example.com">
                    </div>
                </div>

                <div class="btn-group-action">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Simpan Pelanggan
                    </button>
                    <a href="list.php" class="btn-cancel">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>