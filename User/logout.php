<?php
// User/logout.php - Đăng xuất tài khoản

require_once dirname(__DIR__) . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];
session_destroy();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
setFlash('success', 'Đã đăng xuất tài khoản thành công.');
redirect(BASE_URL . 'User/login.php');
