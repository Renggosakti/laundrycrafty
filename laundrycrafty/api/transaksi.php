<?php
require_once '../config.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        if(isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $result = $conn->query("SELECT * FROM view_transaksi_detail WHERE id_transaksi = $id");
            $data = $result->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            $result = $conn->query("SELECT * FROM view_transaksi_detail ORDER BY tanggal_masuk DESC LIMIT 50");
            $data = [];
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $data]);
        }
        break;
        
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $id_pelanggan = (int)$input['id_pelanggan'];
        $id_layanan = (int)$input['id_layanan'];
        $berat = (float)$input['berat'];
        $tanggal_masuk = $conn->real_escape_string($input['tanggal_masuk']);
        
        // Get harga and calculate
        $layanan = $conn->query("SELECT harga_per_kg, durasi_hari FROM layanan WHERE id_layanan = $id_layanan")->fetch_assoc();
        $total_harga = $berat * $layanan['harga_per_kg'];
        $tanggal_selesai = date('Y-m-d', strtotime($tanggal_masuk . ' +' . $layanan['durasi_hari'] . ' days'));
        
        $sql = "INSERT INTO transaksi (id_pelanggan, id_layanan, id_user, tanggal_masuk, tanggal_selesai, berat, total_harga) 
                VALUES ($id_pelanggan, $id_layanan, 1, '$tanggal_masuk', '$tanggal_selesai', $berat, $total_harga)";
        
        if($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Transaksi berhasil dibuat', 'id' => $conn->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal membuat transaksi']);
        }
        break;
        
    case 'PUT':
        parse_str(file_get_contents('php://input'), $input);
        $id = (int)$input['id'];
        $status = $conn->real_escape_string($input['status']);
        
        $sql = "UPDATE transaksi SET status='$status' WHERE id_transaksi=$id";
        
        if($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Status berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal update status']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}