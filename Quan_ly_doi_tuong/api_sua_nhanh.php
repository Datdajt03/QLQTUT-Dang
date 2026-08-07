<?php
// Chucnang/api_sua_nhanh.php - API xử lý chỉnh sửa nhanh trực tiếp từ bảng Excel
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/User/auth.php';
requireRole(['Quản lý', 'Admin']);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Phương thức yêu cầu không hợp lệ.']);
    exit;
}

$id    = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$field = trim($_POST['field'] ?? '');
$value = trim($_POST['value'] ?? '');

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'ID đối tượng không hợp lệ.']);
    exit;
}

// Whitelist các trường được phép sửa nhanh
$allowedFields = [
    'ma_gvsv', 'ho_ten', 'sdt', 'gioi_tinh', 'ngay_sinh', 
    'que_quan', 'lop', 'chi_bo_cong_nhan', 'trang_thai', 
    'ngay_hop_cam_tinh', 'ngay_ket_nap'
];

if (!in_array($field, $allowedFields)) {
    echo json_encode(['success' => false, 'error' => 'Trường dữ liệu không được phép chỉnh sửa.']);
    exit;
}

// Validation logic
if ($field === 'ho_ten' && empty($value)) {
    echo json_encode(['success' => false, 'error' => 'Họ và tên không được phép để trống.']);
    exit;
}

if ($field === 'gioi_tinh' && $value !== '' && !in_array($value, ['Nam', 'Nữ', 'Khác'])) {
    echo json_encode(['success' => false, 'error' => 'Giới tính phải là Nam, Nữ hoặc Khác.']);
    exit;
}

if ($field === 'trang_thai' && !in_array($value, ['Đang theo dõi', 'Đã kết nạp', 'Đã chuyển', 'Tạm dừng'])) {
    echo json_encode(['success' => false, 'error' => 'Trạng thái không hợp lệ.']);
    exit;
}

// Xử lý định dạng ngày tháng
$isDate = in_array($field, ['ngay_sinh', 'ngay_hop_cam_tinh', 'ngay_ket_nap']);
if ($isDate) {
    if ($value === '') {
        $value = null;
    } else {
        $dbDate = toDbDate($value);
        if (!$dbDate) {
            echo json_encode(['success' => false, 'error' => 'Định dạng ngày tháng không hợp lệ (hợp lệ: dd/mm/yyyy hoặc yyyy-mm-dd).']);
            exit;
        }
        $value = $dbDate;
    }
} else {
    if ($value === '') {
        $value = null; // Tránh lưu chuỗi rỗng cho các trường tùy chọn
    }
}

try {
    $db = getDB();
    
    // Lấy giá trị cũ để lưu lịch sử chi tiết
    $stmtSelect = $db->prepare("SELECT ho_ten, $field FROM doi_tuong WHERE id = ?");
    $stmtSelect->execute([$id]);
    $oldRow = $stmtSelect->fetch();
    
    if (!$oldRow) {
        echo json_encode(['success' => false, 'error' => 'Không tìm thấy đối tượng cần cập nhật.']);
        exit;
    }

    $oldValue = $oldRow[$field];
    $hoTen = $oldRow['ho_ten'];

    // Nếu giá trị không thay đổi, không cần cập nhật
    if ($oldValue === $value) {
        echo json_encode([
            'success' => true, 
            'message' => 'Dữ liệu không thay đổi.',
            'display_value' => $isDate ? formatDate($value) : ($value ?? '')
        ]);
        exit;
    }

    // Tiến hành cập nhật
    $stmtUpdate = $db->prepare("UPDATE doi_tuong SET $field = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmtUpdate->execute([$value, $id]);

    // Ghi nhận lịch sử chỉnh sửa
    $fieldNameVi = [
        'ma_gvsv' => 'Mã GV/SV',
        'ho_ten' => 'Họ tên',
        'sdt' => 'Số điện thoại',
        'gioi_tinh' => 'Giới tính',
        'ngay_sinh' => 'Ngày sinh',
        'que_quan' => 'Quê quán',
        'lop' => 'Lớp',
        'chi_bo_cong_nhan' => 'Chi bộ công nhận',
        'trang_thai' => 'Trạng thái',
        'ngay_hop_cam_tinh' => 'Ngày họp cảm tình',
        'ngay_ket_nap' => 'Ngày kết nạp'
    ][$field];

    $oldDisplay = $isDate ? formatDate($oldValue) : ($oldValue ?? 'Trống');
    $newDisplay = $isDate ? formatDate($value) : ($value ?? 'Trống');
    
    $moTaLog = "Cập nhật {$fieldNameVi} từ '{$oldDisplay}' thành '{$newDisplay}'";
    logHistory($id, 'Chỉnh sửa nhanh', $moTaLog);

    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật thành công.',
        'display_value' => $isDate ? formatDate($value) : ($value ?? '')
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
