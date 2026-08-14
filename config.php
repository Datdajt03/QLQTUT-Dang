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

// Include SVG icon helper engine
require_once __DIR__ . '/Giao_dien/icons.php';

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
            
            // Tự động khởi tạo bảng nguoi_dung nếu chưa có
            try {
                $pdo->query("SELECT 1 FROM nguoi_dung LIMIT 1");
            } catch (PDOException $e) {
                $pdo->exec("CREATE TABLE IF NOT EXISTS nguoi_dung (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(100) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    ho_ten VARCHAR(255),
                    vai_tro ENUM('Người dùng thường', 'Quản lý', 'Admin') DEFAULT 'Người dùng thường',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            }

            // Tự động khởi tạo bảng yeu_cau_cap_nhat nếu chưa có
            try {
                $pdo->query("SELECT 1 FROM yeu_cau_cap_nhat LIMIT 1");
            } catch (PDOException $e) {
                $pdo->exec("CREATE TABLE IF NOT EXISTS yeu_cau_cap_nhat (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    doi_tuong_id INT NOT NULL,
                    ho_ten VARCHAR(255),
                    sdt VARCHAR(20),
                    email VARCHAR(255),
                    gioi_tinh VARCHAR(10),
                    ngay_sinh DATE,
                    dan_toc VARCHAR(50),
                    que_quan TEXT,
                    chuc_vu VARCHAR(100),
                    lop VARCHAR(100),
                    trang_thai ENUM('Chờ duyệt', 'Đã duyệt', 'Đã từ chối') DEFAULT 'Chờ duyệt',
                    ly_do_tu_choi TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (doi_tuong_id) REFERENCES doi_tuong(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            }

            // Tự động kiểm tra và thêm các tài khoản mặc định nếu chưa có
            $defaultUsers = [
                ['username' => 'Admin',   'password' => 'Admin123',   'ho_ten' => 'Quản trị viên', 'vai_tro' => 'Admin'],
                ['username' => 'Testql',  'password' => 'Testql123',  'ho_ten' => 'Quản lý Thử nghiệm', 'vai_tro' => 'Quản lý'],
                ['username' => 'Testngd', 'password' => 'Testngd123', 'ho_ten' => 'Người dùng Thử nghiệm', 'vai_tro' => 'Người dùng thường']
            ];

            foreach ($defaultUsers as $u) {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM nguoi_dung WHERE username = ?");
                $stmt->execute([$u['username']]);
                if ($stmt->fetchColumn() == 0) {
                    $hash = password_hash($u['password'], PASSWORD_DEFAULT);
                    $stmtIns = $pdo->prepare("INSERT INTO nguoi_dung (username, password, ho_ten, vai_tro) VALUES (?, ?, ?, ?)");
                    $stmtIns->execute([$u['username'], $hash, $u['ho_ten'], $u['vai_tro']]);
                }
            }
        } catch (PDOException $e) {
            die('<div style="color:red;padding:20px;font-family:sans-serif;">
                <h2>❌ Lỗi kết nối Database</h2>
                <p>' . htmlspecialchars($e->getMessage()) . '</p>
                <p>Vui lòng kiểm tra: MySQL đang chạy và database <strong>' . DB_NAME . '</strong> đã được tạo.</p>
                <p><a href="' . BASE_URL . 'Cau_hinh/setup.php">Chạy Setup</a></p>
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

// Helper: get flash
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

// Helper: Gửi email thông báo (có ghi log cục bộ làm giả làm ở XAMPP)
function sendMailNotification(string $to, string $subject, string $body): bool {
    // 1. Ghi log cục bộ vào thư mục uploads
    try {
        $logDir = __DIR__ . '/uploads';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $logFile = $logDir . '/email_logs.txt';
        $timestamp = date('Y-m-d H:i:s');
        $divider = str_repeat('=', 60);
        $logContent = "{$divider}\n[THỜI GIAN] {$timestamp}\n[GỬI TỚI]   {$to}\n[TIÊU ĐỀ]   {$subject}\n[NỘI DUNG]\n{$body}\n{$divider}\n\n";
        file_put_contents($logFile, $logContent, FILE_APPEND);
    } catch (Exception $e) {
        // Silent catch
    }

    // 2. Thử gửi bằng mail() thật
    try {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: " . getSetting('admin_email', 'admin@example.com') . "\r\n";
        return @mail($to, $subject, $body, $headers);
    } catch (Exception $e) {
        return false;
    }
}

// Start session
if (session_status() === PHP_SESSION_NONE) session_start();
