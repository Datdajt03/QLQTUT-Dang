<?php
// Quan_ly_doi_tuong/api_save_ai_check.php
header('Content-Type: application/json; charset=utf-8');
require_once('../config.php');
require_once('../User/auth.php');

$currentUser = getCurrentUser();
$userId = $currentUser['id'] ?? 0;

$dataJson = $_POST['analysisData'] ?? '';
$data = json_decode($dataJson, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu phân tích không hợp lệ']);
    exit;
}

$uploadDir = dirname(__DIR__) . '/uploads/ho_so_minh_chung/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$savedFiles = [];
$errors = [];
$maxBytes = 10 * 1024 * 1024; // 10MB

if (!empty($_FILES['files']['name'][0])) {
    foreach ($_FILES['files']['name'] as $i => $name) {
        $tmpName = $_FILES['files']['tmp_name'][$i];
        $size    = $_FILES['files']['size'][$i];
        $err     = $_FILES['files']['error'][$i];

        if ($err === UPLOAD_ERR_OK) {
            if ($size > $maxBytes) {
                $errors[] = "File '$name' vượt quá dung lượng 10MB";
                continue;
            }

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($ext, $allowed)) {
                $errors[] = "File '$name' không đúng định dạng (chỉ chấp nhận PDF, JPG, PNG, WebP)";
                continue;
            }

            $newName = 'minhchung_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $destPath = $uploadDir . $newName;

            if (move_uploaded_file($tmpName, $destPath)) {
                $savedFiles[] = 'uploads/ho_so_minh_chung/' . $newName;
            }
        }
    }
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    $db = getDB();
    // Tạo bảng lưu vết kiểm tra nếu chưa có
    $db->exec("CREATE TABLE IF NOT EXISTS edge_ai_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        trang_thai VARCHAR(100),
        raw_summary TEXT,
        files_json TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $stmt = $db->prepare("INSERT INTO edge_ai_logs (user_id, trang_thai, raw_summary, files_json) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $userId,
        $data['isComplete'] ? 'Đầy đủ hợp lệ' : 'Cần bổ sung',
        $data['rawSummary'] ?? '',
        json_encode($savedFiles, JSON_UNESCAPED_UNICODE)
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Đã lưu thành công ' . count($savedFiles) . ' file minh chứng và nhật ký AI vào hệ thống!',
        'saved_files' => $savedFiles
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
}

