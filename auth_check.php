<?php
/**
 * auth_check.php
 * Include file ini di bagian atas halaman yang hanya boleh diakses admin.
 * Jika bukan admin, user akan diredirect ke halaman utama (index.php).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
?>
