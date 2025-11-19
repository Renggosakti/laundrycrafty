<?php
require_once '../config.php';
checkLogin();

$id = (int)$_GET['id'];

if($conn->query("DELETE FROM transaksi WHERE id_transaksi = $id")) {
    flashMessage('success', 'Transaksi berhasil dihapus!');
} else {
    flashMessage('error', 'Gagal menghapus transaksi!');
}

header('Location: list.php');
exit;
?>