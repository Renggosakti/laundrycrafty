<?php
require_once '../config.php';
checkLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pelanggan = sanitize($_POST['id_pelanggan']);
    $id_layanan = sanitize($_POST['id_layanan']);
    $berat = sanitize($_POST['berat']);
    $tanggal_masuk = sanitize($_POST['tanggal_masuk']);
    $catatan = sanitize($_POST['catatan']);
    
    // Get harga and durasi
    $layanan = $conn->query("SELECT harga_per_kg, durasi_hari FROM layanan WHERE id_layanan = $id_layanan")->fetch_assoc();
    $total_harga = $berat * $layanan['harga_per_kg'];
    $tanggal_selesai = date('Y-m-d', strtotime($tanggal_masuk . ' +' . $layanan['durasi_hari'] . ' days'));
    
    $sql = "INSERT INTO transaksi (id_pelanggan, id_layanan, id_user, tanggal_masuk, tanggal_selesai, berat, total_harga, catatan) 
            VALUES ($id_pelanggan, $id_layanan, {$_SESSION['user_id']}, '$tanggal_masuk', '$tanggal_selesai', $berat, $total_harga, '$catatan')";
    
    if ($conn->query($sql)) {
        $id_transaksi = $conn->insert_id;
        
        // Log activity
        $pelanggan_info = $conn->query("SELECT nama FROM pelanggan WHERE id_pelanggan = $id_pelanggan")->fetch_assoc();
        $layanan_info = $conn->query("SELECT nama_layanan FROM layanan WHERE id_layanan = $id_layanan")->fetch_assoc();
        logActivity('add_transaksi', "Menambahkan transaksi #{$id_transaksi} untuk {$pelanggan_info['nama']} - {$layanan_info['nama_layanan']}");
        
        flashMessage('success', 'Transaksi berhasil dibuat!');
        header('Location: list.php');
        exit;
    }
}

$pelanggan = $conn->query("SELECT * FROM pelanggan ORDER BY nama");
$layanan = $conn->query("SELECT * FROM layanan ORDER BY nama_layanan");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Baru - LaundryCrafty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .form-card { background: white; border-radius: 15px; padding: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 900px; margin: 0 auto; animation: fadeInUp 0.6s; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .form-header { text-align: center; margin-bottom: 35px; padding-bottom: 20px; border-bottom: 2px solid #f3f4f6; }
        .form-header i { font-size: 60px; background: linear-gradient(135deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 15px; }
        .form-group { margin-bottom: 25px; }
        .form-label { color: #374151; font-weight: 600; margin-bottom: 8px; display: block; }
        .form-control, select { padding: 12px 15px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 15px; transition: all 0.3s; width: 100%; }
        .form-control:focus, select:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 4px rgba(102,126,234,0.1); }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .summary-card { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 25px; border-radius: 15px; margin: 30px 0; }
        .summary-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.2); }
        .summary-item:last-child { border-bottom: none; font-size: 20px; font-weight: 700; }
        .btn-submit { width: 100%; padding: 14px; background: linear-gradient(135deg, #10b981, #059669); border: none; border-radius: 10px; color: white; font-weight: 600; font-size: 16px; cursor: pointer; transition: all 0.3s; margin-top: 20px; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(16,185,129,0.3); }
        .btn-cancel { width: 100%; padding: 14px; background: #e5e7eb; border: none; border-radius: 10px; color: #374151; font-weight: 600; margin-top: 10px; text-decoration: none; display: block; text-align: center; }
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
        <div class="form-card">
            <div class="form-header">
                <i class="fas fa-cash-register"></i>
                <h4>Transaksi Laundry Baru</h4>
            </div>

            <form method="POST" id="transaksiForm">
                <div class="row">
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-user me-2"></i>Pelanggan</label>
                        <select name="id_pelanggan" class="form-control" required>
                            <option value="">Pilih Pelanggan</option>
                            <?php while($p = $pelanggan->fetch_assoc()): ?>
                            <option value="<?= $p['id_pelanggan'] ?>"><?= $p['nama'] ?> - <?= $p['no_hp'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-tags me-2"></i>Layanan</label>
                        <select name="id_layanan" id="layanan" class="form-control" required onchange="hitungTotal()">
                            <option value="">Pilih Layanan</option>
                            <?php while($l = $layanan->fetch_assoc()): ?>
                            <option value="<?= $l['id_layanan'] ?>" data-harga="<?= $l['harga_per_kg'] ?>" data-durasi="<?= $l['durasi_hari'] ?>">
                                <?= $l['nama_layanan'] ?> - <?= formatRupiah($l['harga_per_kg']) ?>/kg
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-weight me-2"></i>Berat (Kg)</label>
                        <input type="number" name="berat" id="berat" class="form-control" step="0.1" min="0.1" placeholder="0.0" required oninput="hitungTotal()">
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-calendar me-2"></i>Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-sticky-note me-2"></i>Catatan (Opsional)</label>
                    <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
                </div>

                <div class="summary-card">
                    <div class="summary-item">
                        <span>Harga per Kg:</span>
                        <strong id="hargaPerKg">Rp 0</strong>
                    </div>
                    <div class="summary-item">
                        <span>Berat:</span>
                        <strong id="beratDisplay">0 kg</strong>
                    </div>
                    <div class="summary-item">
                        <span>Estimasi Selesai:</span>
                        <strong id="estimasiSelesai">-</strong>
                    </div>
                    <div class="summary-item">
                        <span>TOTAL BAYAR:</span>
                        <strong id="totalBayar">Rp 0</strong>
                    </div>
                </div>

                <button type="submit" class="btn-submit"><i class="fas fa-save me-2"></i>Simpan Transaksi</button>
                <a href="list.php" class="btn-cancel"><i class="fas fa-times me-2"></i>Batal</a>
            </form>
        </div>
    </div>

    <script>
        function formatRupiah(angka) {
            return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function hitungTotal() {
            const layanan = document.getElementById('layanan');
            const berat = parseFloat(document.getElementById('berat').value) || 0;
            
            if (layanan.selectedIndex > 0) {
                const option = layanan.options[layanan.selectedIndex];
                const harga = parseInt(option.dataset.harga);
                const durasi = parseInt(option.dataset.durasi);
                const total = harga * berat;
                
                document.getElementById('hargaPerKg').textContent = formatRupiah(harga);
                document.getElementById('beratDisplay').textContent = berat + ' kg';
                document.getElementById('totalBayar').textContent = formatRupiah(total);
                
                const today = new Date();
                today.setDate(today.getDate() + durasi);
                document.getElementById('estimasiSelesai').textContent = today.toLocaleDateString('id-ID');
            }
        }
    </script>
</body>
</html>