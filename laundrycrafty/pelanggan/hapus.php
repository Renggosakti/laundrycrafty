<?php
require_once '../config.php';
checkLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $sql = "DELETE FROM pelanggan WHERE id_pelanggan = $id";
    
    if ($conn->query($sql)) {
        flashMessage('success', 'Pelanggan berhasil dihapus!');
    } else {
        flashMessage('error', 'Gagal menghapus pelanggan!');
    }
}

header('Location: list.php');
exit;
?>