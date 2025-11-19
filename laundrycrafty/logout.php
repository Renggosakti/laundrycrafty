<?php
// logout.php
require_once 'config.php';

if (isLoggedIn()) {
    // Log activity before logout
    logActivity('logout', 'Logout dari sistem');
    
    // Clear remember me
    clearRememberMe();
}

session_start();
session_unset();
session_destroy();
header('Location: login.php');
exit;
?>