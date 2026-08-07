<?php
// User/auth.php - Helper quản lý đăng nhập và phân quyền

if (!defined('BASE_URL')) {
    require_once dirname(__DIR__) . '/config.php';
}

// Kiểm tra người dùng đã đăng nhập chưa
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

// Lấy thông tin người dùng hiện tại
function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    static $currentUser = null;
    if ($currentUser === null) {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM nguoi_dung WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $currentUser = $stmt->fetch() ?: null;
        } catch (Exception $e) {
            return null;
        }
    }
    return $currentUser;
}

// Bắt buộc đăng nhập
function requireLogin(): void {
    if (!isLoggedIn()) {
        setFlash('danger', 'Vui lòng đăng nhập để tiếp tục.');
        redirect(BASE_URL . 'User/login.php');
    }
}

// Yêu cầu vai trò cụ thể (ví dụ: 'Admin', 'Quản lý', 'Người dùng thường')
// $allowedRoles có thể là chuỗi hoặc mảng các chuỗi
function requireRole($allowedRoles): void {
    requireLogin();
    $user = getCurrentUser();
    if (!$user) {
        setFlash('danger', 'Tài khoản không hợp lệ.');
        redirect(BASE_URL . 'User/login.php');
    }
    
    $roles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];
    if (!in_array($user['vai_tro'], $roles)) {
        setFlash('danger', 'Bạn không có quyền truy cập chức năng này.');
        redirect(BASE_URL . 'index.php');
    }
}

// Kiểm tra quyền hạn (trả về boolean để ẩn/hiện menu)
function hasRole($allowedRoles): bool {
    if (!isLoggedIn()) return false;
    $user = getCurrentUser();
    if (!$user) return false;
    $roles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];
    return in_array($user['vai_tro'], $roles);
}
