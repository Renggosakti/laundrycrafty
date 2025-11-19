<?php
require_once '../config.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $result = $conn->query("SELECT * FROM pelanggan ORDER BY created_at DESC");
        $data = [];
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $data]);
        break;
        
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $nama = $conn->real_escape_string($input['nama']);
        $alamat = $conn->real_escape_string($input['alamat']);
        $no_hp = $conn->real_escape_string($input['no_hp']);
        $email = $conn->real_escape_string($input['email']);
        
        $sql = "INSERT INTO pelanggan (nama, alamat, no_hp, email) VALUES ('$nama', '$alamat', '$no_hp', '$email')";
        
        if($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Pelanggan berhasil ditambahkan', 'id' => $conn->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan pelanggan']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}