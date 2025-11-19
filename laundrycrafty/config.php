<?php
// config.php - Konfigurasi Database dan Session
session_start();

// ========================
// Database Configuration
// ========================
define('DB_HOST', 'localhost');
define('DB_USER', 'jvfafdzr_laundryuser');
define('DB_PASS', 'LexmRZ5YW@Zpqm6');
define('DB_NAME', 'jvfafdzr_laundrycraft_db');

// ========================
// Site Configuration
// ========================
// Karena file berada langsung di public_html/
define('SITE_NAME', 'LaundryCrafty');
define('BASE_URL', 'https://laundrycraft.web.id/');

date_default_timezone_set('Asia/Jakarta');

// Database Connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}

// Helper Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function checkLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(htmlspecialchars(strip_tags(trim($data))));
}

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function formatTanggal($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

function getStatusBadge($status) {
    $badges = [
        'proses' => '<span class="badge bg-warning">Proses</span>',
        'selesai' => '<span class="badge bg-success">Selesai</span>',
        'diambil' => '<span class="badge bg-info">Diambil</span>'
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
}

function flashMessage($type, $message) {
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

function showFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'];
        $message = $_SESSION['flash_message'];
        
        $alertClass = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ];
        
        echo '<div class="alert ' . $alertClass[$type] . ' alert-dismissible fade show" role="alert">';
        echo $message;
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}

// Auto Logout after 30 minutes
if (isLoggedIn()) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        logActivity('logout', 'Auto logout karena timeout');
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . 'login.php?timeout=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

// Log Activity Function
function logActivity($type, $description) {
    global $conn;
    if (!isLoggedIn()) return;
    
    $id_user = $_SESSION['user_id'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    $sql = "INSERT INTO activity_logs (id_user, activity_type, description, ip_address, user_agent) 
            VALUES ($id_user, '$type', '$description', '$ip', '$user_agent')";
    $conn->query($sql);
}

// Get Recent Activities
function getRecentActivities($limit = 10) {
    global $conn;
    $result = $conn->query("SELECT * FROM view_recent_activities LIMIT $limit");
    return $result;
}

// Remember Me Functions
function setRememberMe($user_id) {
    global $conn;
    $token = bin2hex(random_bytes(32));
    $conn->query("UPDATE user SET remember_token = '$token' WHERE id_user = $user_id");
    setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days
    return $token;
}

function checkRememberMe() {
    global $conn;
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        $result = $conn->query("SELECT * FROM user WHERE remember_token = '$token'");
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['last_activity'] = time();
            return true;
        }
    }
    return false;
}

function clearRememberMe() {
    global $conn;
    if (isLoggedIn()) {
        $user_id = $_SESSION['user_id'];
        $conn->query("UPDATE user SET remember_token = NULL WHERE id_user = $user_id");
    }
    setcookie('remember_token', '', time() - 3600, '/');
}
?>