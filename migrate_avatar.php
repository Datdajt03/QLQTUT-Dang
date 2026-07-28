<?php
// migrate_avatar.php – Thêm cột avatar vào bảng doi_tuong và tạo thư mục uploads
require_once __DIR__ . '/config.php';

$db = getDB();
echo "Đang migrate...\n";

// Add avatar column
try {
    $db->exec("ALTER TABLE doi_tuong ADD COLUMN avatar VARCHAR(255) DEFAULT NULL");
    echo "✅ Đã thêm cột avatar vào bảng doi_tuong\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "ℹ️  Cột avatar đã tồn tại\n";
    } else {
        echo "❌ Lỗi: " . $e->getMessage() . "\n";
    }
}

// Create uploads directory
$uploadDir = __DIR__ . '/uploads/avatars/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
    echo "✅ Đã tạo thư mục: uploads/avatars/\n";
} else {
    echo "ℹ️  Thư mục uploads/avatars/ đã tồn tại\n";
}

// Create .htaccess for security (allow only images)
$htaccess = $uploadDir . '.htaccess';
if (!file_exists($htaccess)) {
    file_put_contents($htaccess, "Options -Indexes\n<FilesMatch \"\\.php$\">\n  Deny from all\n</FilesMatch>\n");
    echo "✅ Đã tạo .htaccess bảo vệ thư mục\n";
}

echo "\n=== Migration hoàn tất ===\n";
