<?php
require_once '../config.php';
header('Content-Type: application/json');

$activities = getRecentActivities(10);
$data = [];

while($act = $activities->fetch_assoc()) {
    $time = strtotime($act['created_at']);
    $diff = time() - $time;
    
    if ($diff < 60) {
        $time_ago = $diff . ' detik lalu';
    } elseif ($diff < 3600) {
        $time_ago = floor($diff / 60) . ' menit lalu';
    } elseif ($diff < 86400) {
        $time_ago = floor($diff / 3600) . ' jam lalu';
    } else {
        $time_ago = date('d/m/Y H:i', $time);
    }
    
    $data[] = [
        'id_log' => $act['id_log'],
        'activity_type' => $act['activity_type'],
        'description' => $act['description'],
        'nama_lengkap' => $act['nama_lengkap'],
        'username' => $act['username'],
        'role' => $act['role'],
        'created_at' => $act['created_at'],
        'time_ago' => $time_ago
    ];
}

echo json_encode([
    'success' => true,
    'activities' => $data,
    'count' => count($data)
]);
?>