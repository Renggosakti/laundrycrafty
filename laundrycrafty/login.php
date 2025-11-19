<?php
require_once 'config.php';

// Check Remember Me
if (!isLoggedIn()) {
    checkRememberMe();
}

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// ==========================================
// HANDLE LOGIN
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = sanitize($_POST['username']);
    $password = $_POST['password']; // Jangan di-sanitize password!
    $remember = isset($_POST['remember']);
    
    $sql = "SELECT * FROM user WHERE username = '$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Login berhasil
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['last_activity'] = time();
            
            // Update last login
            $conn->query("UPDATE user SET last_login = NOW() WHERE id_user = {$user['id_user']}");
            
            // Remember Me
            if ($remember) {
                setRememberMe($user['id_user']);
            }
            
            // Log activity
            logActivity('login', 'Login berhasil');
            
            header('Location: index.php');
            exit;
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Username tidak ditemukan!';
    }
}

// ==========================================
// HANDLE REGISTER
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $nama = sanitize($_POST['nama_lengkap']);
    $username = sanitize($_POST['reg_username']);
    $password = $_POST['reg_password']; // Jangan di-sanitize!
    $confirm = $_POST['confirm_password'];
    
    // Validasi
    if (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($password !== $confirm) {
        $error = 'Password tidak sama!';
    } else {
        // Check username exists
        $check = $conn->query("SELECT id_user FROM user WHERE username = '$username'");
        if ($check->num_rows > 0) {
            $error = 'Username sudah digunakan!';
        } else {
            // Hash password dengan benar
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO user (username, password, role, nama_lengkap) 
                    VALUES ('$username', '$hashed', 'kasir', '$nama')";
            
            if ($conn->query($sql)) {
                $success = 'Registrasi berhasil! Silakan login dengan username dan password Anda.';
                
                // Auto-login setelah register (optional)
                // $new_user_id = $conn->insert_id;
                // $_SESSION['user_id'] = $new_user_id;
                // $_SESSION['username'] = $username;
                // $_SESSION['role'] = 'kasir';
                // $_SESSION['nama_lengkap'] = $nama;
                // $_SESSION['last_activity'] = time();
                // logActivity('login', 'Login pertama kali setelah registrasi');
                // header('Location: index.php');
                // exit;
            } else {
                $error = 'Gagal registrasi: ' . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LaundryCrafty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        .animated-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        .bubble {
            position: absolute;
            bottom: -100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: rise 10s infinite ease-in;
        }
        .bubble:nth-child(1) { left: 10%; width: 80px; height: 80px; animation-delay: 0s; }
        .bubble:nth-child(2) { left: 20%; width: 60px; height: 60px; animation-delay: 2s; }
        .bubble:nth-child(3) { left: 35%; width: 100px; height: 100px; animation-delay: 4s; }
        .bubble:nth-child(4) { left: 50%; width: 70px; height: 70px; animation-delay: 0s; }
        .bubble:nth-child(5) { left: 65%; width: 90px; height: 90px; animation-delay: 3s; }
        .bubble:nth-child(6) { left: 80%; width: 120px; height: 120px; animation-delay: 1s; }
        .bubble:nth-child(7) { left: 90%; width: 50px; height: 50px; animation-delay: 5s; }
        @keyframes rise {
            0% { bottom: -100px; transform: translateX(0); opacity: 0; }
            50% { opacity: 0.3; }
            100% { bottom: 110vh; transform: translateX(100px); opacity: 0; }
        }
        .login-container {
            position: relative;
            z-index: 1;
            max-width: 450px;
            width: 100%;
            padding: 20px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .logo-icon i {
            font-size: 40px;
            color: white;
        }
        h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .nav-tabs {
            border: none;
            margin-bottom: 25px;
        }
        .nav-tabs .nav-link {
            border: none;
            color: #666;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        .form-label {
            color: #555;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            font-size: 14px;
        }
        .input-group-icon {
            position: relative;
        }
        .input-group-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            z-index: 2;
        }
        .form-control {
            padding: 12px 15px 12px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: white;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            z-index: 2;
        }
        .password-toggle:hover {
            color: #667eea;
        }
        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        .form-check input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .form-check label {
            margin: 0;
            cursor: pointer;
            font-size: 14px;
            color: #666;
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border-radius: 12px;
            padding: 12px 15px;
            margin-bottom: 20px;
            animation: shake 0.5s;
            font-size: 14px;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        .footer-text {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 13px;
        }
        .demo-info {
            background: rgba(102, 126, 234, 0.1);
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
            text-align: center;
        }
        .demo-info small {
            display: block;
            color: #667eea;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .demo-info code {
            background: white;
            padding: 2px 8px;
            border-radius: 4px;
            color: #764ba2;
            font-weight: 600;
        }
        .password-strength {
            font-size: 12px;
            margin-top: 5px;
            color: #666;
        }
        .password-strength.weak { color: #ef4444; }
        .password-strength.medium { color: #f59e0b; }
        .password-strength.strong { color: #10b981; }
    </style>
</head>
<body>
    <div class="animated-bg">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="fas fa-tshirt"></i>
                </div>
                <h2>LaundryCrafty</h2>
                <p class="subtitle">Sistem Manajemen Laundry Modern</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i><?= $success ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['timeout'])): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-clock me-2"></i>Sesi Anda telah berakhir. Silakan login kembali.
                </div>
            <?php endif; ?>

            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#login" type="button">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#register" type="button">
                        <i class="fas fa-user-plus me-2"></i>Register
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- LOGIN TAB -->
                <div class="tab-pane fade show active" id="login">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <div class="input-group-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus autocomplete="username">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <div class="input-group-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" id="loginPassword" class="form-control" placeholder="Masukkan password" required autocomplete="current-password">
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('loginPassword', this)"></i>
                            </div>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="remember" id="remember" class="form-check-input">
                            <label for="remember" class="form-check-label">Ingat Saya (30 hari)</label>
                        </div>

                        <button type="submit" name="login" class="btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>Masuk
                        </button>
                    </form>

                    <!-- Demo info removed for security -->
                </div>

                <!-- REGISTER TAB -->
                <div class="tab-pane fade" id="register">
                    <form method="POST" action="" id="registerForm">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap</label>
                            <div class="input-group-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" name="nama_lengkap" class="form-control" placeholder="Masukkan nama lengkap" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <div class="input-group-icon">
                                <i class="fas fa-user-circle"></i>
                                <input type="text" name="reg_username" class="form-control" placeholder="Pilih username" required autocomplete="off">
                            </div>
                            <small class="text-muted">Huruf kecil, angka, tanpa spasi</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <div class="input-group-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="reg_password" id="regPassword" class="form-control" placeholder="Buat password" required minlength="6" autocomplete="new-password" oninput="checkPasswordStrength(this.value)">
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('regPassword', this)"></i>
                            </div>
                            <div id="passwordStrength" class="password-strength"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password</label>
                            <div class="input-group-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="confirm_password" id="confirmPassword" class="form-control" placeholder="Ketik ulang password" required autocomplete="new-password">
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('confirmPassword', this)"></i>
                            </div>
                        </div>

                        <button type="submit" name="register" class="btn-login">
                            <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                        </button>
                    </form>
                </div>
            </div>

            <div class="footer-text">
                &copy; 2025 LaundryCrafty. All rights reserved.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function checkPasswordStrength(password) {
            const strengthDiv = document.getElementById('passwordStrength');
            const length = password.length;
            
            if (length === 0) {
                strengthDiv.textContent = '';
                strengthDiv.className = 'password-strength';
            } else if (length < 6) {
                strengthDiv.textContent = '❌ Terlalu pendek (minimal 6 karakter)';
                strengthDiv.className = 'password-strength weak';
            } else if (length < 8) {
                strengthDiv.textContent = '⚠️ Lemah - Tambahkan lebih banyak karakter';
                strengthDiv.className = 'password-strength medium';
            } else {
                strengthDiv.textContent = '✅ Kuat - Password aman!';
                strengthDiv.className = 'password-strength strong';
            }
        }

        // Validasi form register
        document.getElementById('registerForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('regPassword').value;
            const confirm = document.getElementById('confirmPassword').value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Password dan konfirmasi password tidak sama!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password minimal 6 karakter!');
                return false;
            }
        });
    </script>
</body>
</html>