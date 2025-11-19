<?php
/**
 * Password Hash Generator
 * Jalankan file ini untuk generate password hash baru
 */

// Password yang ingin di-hash
$passwords = [
    'laundry123'  // Password default untuk admin & kasir
];

echo "=== PASSWORD HASH GENERATOR ===\n\n";

foreach ($passwords as $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "Password: {$password}\n";
    echo "Hash: {$hash}\n";
    echo "SQL Query:\n";
    echo "UPDATE user SET password = '{$hash}' WHERE username = 'admin';\n";
    echo "UPDATE user SET password = '{$hash}' WHERE username = 'kasir1';\n\n";
    echo str_repeat("-", 80) . "\n\n";
}

// Verify password
echo "=== VERIFY PASSWORD ===\n\n";
$test_hash = '$2y$10$pF.xQQvwFoLKhG5xM3K8zeqGRvKJHxJ8xqXO4gX5gvXJ9B5g5rO4C';
$test_password = 'laundry123';

if (password_verify($test_password, $test_hash)) {
    echo "✅ Password '{$test_password}' COCOK dengan hash!\n";
} else {
    echo "❌ Password '{$test_password}' TIDAK COCOK dengan hash!\n";
}

echo "\n=== SELESAI ===\n";
?>