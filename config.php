<?php
// ============================================================
// Cấu hình kết nối Database
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'quan_ly_ket_nap_dang');
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'Quản lý Kết nạp Đảng');
define('SITE_TITLE', 'Thiết kế Website quản lý quần chúng ưu tú phục vụ kết nạp Đảng');
define('BASE_URL', 'http://localhost/web1/');

// Kết nối PDO
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('<div style="color:red;padding:20px;font-family:sans-serif;">
                <h2>❌ Lỗi kết nối Database</h2>
                <p>' . htmlspecialchars($e->getMessage()) . '</p>
                <p>Vui lòng kiểm tra: MySQL đang chạy và database <strong>' . DB_NAME . '</strong> đã được tạo.</p>
                <p><a href="' . BASE_URL . 'setup.php">Chạy Setup</a></p>
            </div>');
        }
    }
    return $pdo;
}

// Helper: log lịch sử
function logHistory(int $doiTuongId, string $hanhDong, string $moTa = ''): void {
    try {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO lich_su (doi_tuong_id, hanh_dong, mo_ta) VALUES (?, ?, ?)");
        $stmt->execute([$doiTuongId, $hanhDong, $moTa]);
    } catch (Exception $e) { /* silent */ }
}

// Helper: get setting
function getSetting(string $key, string $default = ''): string {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT gia_tri FROM cai_dat WHERE khoa = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row ? $row['gia_tri'] : $default;
    } catch (Exception $e) { return $default; }
}

// Helper: flash message
function setFlash(string $type, string $msg): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash(): ?array {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

// Helper: redirect
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

// Sanitize
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Format date
function formatDate(?string $date): string {
    if (!$date || $date === '0000-00-00') return '';
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d ? $d->format('d/m/Y') : $date;
}

// Convert display date to DB
function toDbDate(?string $date): ?string {
    if (!$date) return null;
    // Try d/m/Y
    $d = DateTime::createFromFormat('d/m/Y', $date);
    if ($d) return $d->format('Y-m-d');
    // Try Y-m-d
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if ($d) return $d->format('Y-m-d');
    return null;
}

// Start session
if (session_status() === PHP_SESSION_NONE) session_start();
