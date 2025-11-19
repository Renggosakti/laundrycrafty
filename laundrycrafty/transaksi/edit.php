<?php
require_once '../config.php';
checkLogin();

$id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = sanitize($_POST['status']);
    $catatan = sanitize($_POST['catatan']);
    
    $sql = "UPDATE transaksi SET status='$status', catatan='$catatan' WHERE id_transaksi=$id";
    
    if ($conn->query($sql)) {
        flashMessage('success', 'Transaksi berhasil diupdate!');
        header('Location: list.php');
        exit;
    }
}

$data = $conn->query("SELECT * FROM view_transaksi_detail WHERE id_transaksi = $id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Transaksi - LaundryCrafty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI'; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; padding: 20px; }
        .form-card { background: white; border-radius: 15px; padding: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 600px; margin: 50px auto; }
        .form-header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #f3f4f6; }
        .form-header i { font-size: 60px; background: linear-gradient(135deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .info-box { background: #f9fafb; padding: 20px; border-radius: 10px; margin-bottom: 25px; }
        .info-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .info-item:last-child { border-bottom: none; }
        .form-control, select { padding: 12px 15px; border: 2px solid #e5e7eb; border-radius: 10px; width: 100%; margin-bottom: 20px; }
        .btn-submit { width: 100%; padding: 14px; background: linear-gradient(135deg, #f59e0b, #d97706); border: none; border-radius: 10px; color: white; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(245,158,11,0.3); }
        .btn-cancel { width: 100%; padding: 14px; background: #e5e7eb; border: none; border-radius: 10px; margin-top: 10px; text-decoration: none; display: block; text-align: center; color: #374151; font-weight: 600; }
    </style>
</head>
<body>
    <div class="form-card">
        <div class="form-header">
            <i class="fas fa-edit"></i>
            <h4>Edit Transaksi #<?= $data['id_transaksi'] ?></h4>
        </div>

        <div class="info-box">
            <div class="info-item"><span>Pelanggan:</span><strong><?= $data['nama_pelanggan'] ?></strong></div>
            <div class="info-item"><span>Layanan:</span><strong><?= $data['nama_layanan'] ?></strong></div>
            <div class="info-item"><span>Berat:</span><strong><?= $data['berat'] ?> kg</strong></div>
            <div class="info-item"><span>Total:</span><strong><?= formatRupiah($data['total_harga']) ?></strong></div>
        </div>

        <form method="POST">
            <label><strong>Status Cucian</strong></label>
            <select name="status" class="form-control" required>
                <option value="proses" <?= $data['status'] === 'proses' ? 'selected' : '' ?>>Proses</option>
                <option value="selesai" <?= $data['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                <option value="diambil" <?= $data['status'] === 'diambil' ? 'selected' : '' ?>>Sudah Diambil</option>
            </select>

            <label><strong>Catatan</strong></label>
            <textarea name="catatan" class="form-control" rows="3"><?= $data['catatan'] ?></textarea>

            <button type="submit" class="btn-submit"><i class="fas fa-save me-2"></i>Update Transaksi</button>
            <a href="list.php" class="btn-cancel"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
        </form>
    </div>
</body>
</html>