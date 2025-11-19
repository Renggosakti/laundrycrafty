<?php
require_once '../config.php';
checkLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_layanan = sanitize($_POST['nama_layanan']);
    $harga_per_kg = sanitize($_POST['harga_per_kg']);
    $deskripsi = sanitize($_POST['deskripsi']);
    $durasi_hari = sanitize($_POST['durasi_hari']);
    
    $sql = "UPDATE layanan SET 
            nama_layanan='$nama_layanan', 
            harga_per_kg='$harga_per_kg', 
            deskripsi='$deskripsi', 
            durasi_hari='$durasi_hari' 
            WHERE id_layanan=$id";
    
    if ($conn->query($sql)) {
        flashMessage('success', 'Layanan berhasil diupdate!');
        header('Location: list.php');
        exit;
    } else {
        $error = 'Gagal mengupdate layanan!';
    }
}

// Get data layanan
$result = $conn->query("SELECT * FROM layanan WHERE id_layanan = $id");
if ($result->num_rows === 0) {
    header('Location: list.php');
    exit;
}
$data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Layanan - LaundryCrafty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
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
            font-size: 18px;
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
            color: #6b7280;
        }

        .breadcrumb a {
            color: #667eea;
            text-decoration: none;
        }

        .form-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            max-width: 700px;
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
            background: linear-gradient(135deg, #f59e0b, #d97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
            display: block;
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

        .form-control,
        textarea {
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            width: 100%;
        }

        .form-control:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
        }

        .btn-cancel {
            width: 100%;
            padding: 14px;
            background: #e5e7eb;
            border: none;
            border-radius: 10px;
            color: #374151;
            font-weight: 600;
            margin-top: 10px;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: all 0.3s;
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
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <i class="fas fa-tshirt"></i>
            <h3>LaundryCrafty</h3>
            <small style="opacity: 0.8;">Modern Laundry System</small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="../index.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="../pelanggan/list.php"><i class="fas fa-users"></i> Pelanggan</a></li>
            <li><a href="list.php" class="active"><i class="fas fa-tags"></i> Layanan</a></li>
            <li><a href="../transaksi/list.php"><i class="fas fa-exchange-alt"></i> Transaksi</a></li>
            <li><a href="../laporan/pendapatan.php"><i class="fas fa-chart-line"></i> Laporan</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="page-title">
                <h4><i class="fas fa-edit me-2"></i>Edit Layanan</h4>
                <nav class="breadcrumb">
                    <a href="../index.php">Dashboard</a> / <a href="list.php">Layanan</a> / Edit
                </nav>
            </div>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <div class="form-header">
                <i class="fas fa-edit"></i>
                <h4>Form Edit Layanan</h4>
            </div>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-tag me-2"></i>Nama Layanan</label>
                    <input type="text" name="nama_layanan" class="form-control" value="<?= htmlspecialchars($data['nama_layanan']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-money-bill-wave me-2"></i>Harga per Kg (Rp)</label>
                    <input type="number" name="harga_per_kg" class="form-control" value="<?= $data['harga_per_kg'] ?>" min="0" step="100" required>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-clock me-2"></i>Durasi Pengerjaan (Hari)</label>
                    <input type="number" name="durasi_hari" class="form-control" value="<?= $data['durasi_hari'] ?>" min="1" max="30" required>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-align-left me-2"></i>Deskripsi Layanan</label>
                    <textarea name="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($data['deskripsi']) ?></textarea>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-save me-2"></i>Update Layanan
                </button>
                <a href="list.php" class="btn-cancel">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>