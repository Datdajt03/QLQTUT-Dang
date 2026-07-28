<?php
// setup.php – Tạo database và các bảng
$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'quan_ly_ket_nap_dang';

$status = [];

try {
    // Connect without DB first
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Create DB
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $status[] = ['ok', "✅ Database <strong>$dbName</strong> đã được tạo (hoặc đã tồn tại)"];

    // Use DB
    $pdo->exec("USE `$dbName`");
    $pdo->exec("SET NAMES utf8mb4");

    // Read and execute SQL file
    $sqlFile = __DIR__ . '/db.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        // Split by semicolon (basic)
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        $cnt = 0;
        foreach ($statements as $stmt) {
            if (empty($stmt) || str_starts_with($stmt, '--') || str_starts_with($stmt, 'CREATE DATABASE') || str_starts_with($stmt, 'USE ')) continue;
            try { $pdo->exec($stmt); $cnt++; } catch (PDOException $e) {
                // Table already exists – ignore
                if (strpos($e->getMessage(), 'already exists') === false && strpos($e->getMessage(), '1060') === false) {
                    $status[] = ['warn', '⚠️ ' . htmlspecialchars($e->getMessage())];
                }
            }
        }
        $status[] = ['ok', "✅ Đã chạy $cnt câu SQL từ db.sql"];
    } else {
        $status[] = ['warn', '⚠️ Không tìm thấy file db.sql'];
    }

    // Test connection
    $test = $pdo->query("SELECT COUNT(*) FROM doi_tuong")->fetchColumn();
    $status[] = ['ok', "✅ Bảng <strong>doi_tuong</strong> sẵn sàng – hiện có <strong>$test</strong> bản ghi"];

    $success = true;
} catch (PDOException $e) {
    $status[] = ['err', '❌ Lỗi: ' . htmlspecialchars($e->getMessage())];
    $success  = false;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Setup Database – Kết nạp Đảng</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Roboto', sans-serif; background: #0f0f14; color: #e8e8f0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
    .box { background: #16161f; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; max-width: 560px; width: 100%; overflow: hidden; }
    .box-header { background: linear-gradient(135deg, #C8102E, #9e0b22); padding: 28px; text-align: center; }
    .box-header h1 { font-size: 24px; font-weight: 700; margin-bottom: 6px; }
    .box-header p { font-size: 13px; opacity: 0.8; }
    .box-body { padding: 28px; }
    .status-item { display: flex; align-items: flex-start; gap: 10px; padding: 10px 14px; border-radius: 8px; margin-bottom: 10px; font-size: 13px; }
    .status-item.ok   { background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.25); color: #22c55e; }
    .status-item.warn { background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.25); color: #f59e0b; }
    .status-item.err  { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); color: #ef4444; }
    .btn { display: block; text-align: center; background: linear-gradient(135deg,#C8102E,#9e0b22); color: #fff; padding: 14px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 15px; margin-top: 20px; transition: all 0.2s; }
    .btn:hover { opacity: 0.85; transform: translateY(-1px); }
    .btn-gold { background: linear-gradient(135deg,#FFD700,#e6c200); color: #1a1a00; }
    .note { font-size: 12px; color: #6060780; margin-top: 16px; text-align: center; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.06); }
    code { background: rgba(255,255,255,0.08); padding: 2px 8px; border-radius: 4px; font-size: 12px; color: #FFD700; }
  </style>
</head>
<body>
<div class="box">
  <div class="box-header">
    <div style="font-size:48px;margin-bottom:12px;">⭐</div>
    <h1>Setup Hệ thống</h1>
    <p>Thiết kế Website quản lý quần chúng ưu tú phục vụ kết nạp Đảng</p>
  </div>
  <div class="box-body">
    <?php foreach ($status as $s): ?>
    <div class="status-item <?= $s[0] ?>"><?= $s[1] ?></div>
    <?php endforeach; ?>

    <?php if ($success ?? false): ?>
    <a href="index.php" class="btn">🏠 Vào trang Dashboard</a>
    <?php else: ?>
    <div class="status-item warn">
      <div>
        <strong>Kiểm tra lại:</strong><br>
        • XAMPP đã bật MySQL chưa?<br>
        • Thông tin kết nối: Host <code>localhost</code>, User <code>root</code>, Pass <code>(trống)</code>
      </div>
    </div>
    <a href="setup.php" class="btn btn-gold" style="margin-top:10px;">🔄 Thử lại</a>
    <?php endif; ?>

    <div class="note">
      File cấu hình: <code>config.php</code> · Database: <code><?= $dbName ?></code>
    </div>
  </div>
</div>
</body>
</html>
