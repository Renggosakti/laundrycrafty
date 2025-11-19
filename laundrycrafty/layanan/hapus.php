<?php
require_once '../config.php';
checkLogin();

// Cek apakah ada ID yang dikirim
if (!isset($_GET['id']) || empty($_GET['id'])) {
    flashMessage('error', 'ID layanan tidak valid!');
    header('Location: list.php');
    exit;
}

$id = (int)$_GET['id'];

// Cek apakah layanan ada
$check = $conn->query("SELECT nama_layanan FROM layanan WHERE id_layanan = $id");

if ($check->num_rows === 0) {
    flashMessage('error', 'Layanan tidak ditemukan!');
    header('Location: list.php');
    exit;
}

$layanan = $check->fetch_assoc();

// Cek apakah layanan masih digunakan di transaksi
$transaksi_check = $conn->query("SELECT COUNT(*) as total FROM transaksi WHERE id_layanan = $id");
$transaksi_count = $transaksi_check->fetch_assoc()['total'];

if ($transaksi_count > 0) {
    flashMessage('error', 'Layanan "' . $layanan['nama_layanan'] . '" tidak dapat dihapus karena masih digunakan di ' . $transaksi_count . ' transaksi!');
    header('Location: list.php');
    exit;
}

// Proses hapus
$sql = "DELETE FROM layanan WHERE id_layanan = $id";

if ($conn->query($sql)) {
    flashMessage('success', 'Layanan "' . $layanan['nama_layanan'] . '" berhasil dihapus!');
} else {
    flashMessage('error', 'Gagal menghapus layanan: ' . $conn->error);
}

header('Location: list.php');
exit;
?>