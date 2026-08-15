<?php
// Quan_ly_doi_tuong/them.php – Thêm đối tượng mới
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/User/auth.php';
requireRole(['Quản lý', 'Admin']);
$pageTitle = 'Thêm đối tượng';

$db = getDB();
$errors = [];
$data   = [];

// Load dropdowns
$chiBos   = $db->query("SELECT * FROM chi_bo ORDER BY ten_chi_bo")->fetchAll();
$dangViens = $db->query("SELECT * FROM dang_vien ORDER BY ho_ten")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect & validate
    $fields = [
        'ma_gvsv','ho_ten','sdt','gioi_tinh','ngay_sinh','dan_toc','que_quan',
        'chuc_vu','lop','chi_bo_cong_nhan','so_bc_cam_tinh','ngay_hop_cam_tinh',
        'dang_vien_giup_do','ngay_phan_cong_giup_do','so_qd_mo_lop','ngay_qd_mo_lop',
        'tg_lop_boi_duong','ngay_cap_cc','so_qd_cc','don_vi_cap_cc',
        'ten_dv_congtac_khi_cap_cc','ten_chibo_khi_cap_cc','ten_danguy_khi_cap_cc',
        'ten_tinhuy_khi_cap_cc','ma_so','ket_nap_dang','ngay_quyet_dinh',
        'so_qd_ket_nap','ngay_ket_nap','dang_vien_huong_dan',
        'ngay_chuyen_sinh_hoat','noi_chuyen_toi','ghi_chu','trang_thai'
    ];

    foreach ($fields as $f) $data[$f] = trim($_POST[$f] ?? '');

    if (empty($data['ho_ten'])) $errors[] = 'Họ và tên không được để trống';

    // Handle avatar upload
    $avatarPath = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = dirname(__DIR__) . '/uploads/avatars/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp','bmp','svg','tiff','heic'];
        if ($_FILES['avatar']['size'] <= 20 * 1024 * 1024) {
            $fname = 'av_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $fname)) {
                $avatarPath = 'uploads/avatars/' . $fname;
            }
        } else {
            $errors[] = 'Kích thước ảnh đại diện vượt quá giới hạn cho phép (tối đa 20MB)';
        }
    }

    if (empty($errors)) {
        // Convert dates
        $dateFields = ['ngay_sinh','ngay_hop_cam_tinh','ngay_phan_cong_giup_do',
                       'ngay_qd_mo_lop','ngay_cap_cc','ngay_quyet_dinh',
                       'ngay_ket_nap','ngay_chuyen_sinh_hoat'];
        foreach ($dateFields as $df) {
            $data[$df] = $data[$df] ? toDbDate($data[$df]) : null;
        }
        // Empty strings → null for optional
        foreach ($data as &$v) if ($v === '') $v = null;
        $data['avatar'] = $avatarPath;

        $sql = "INSERT INTO doi_tuong (
            ma_gvsv,ho_ten,sdt,gioi_tinh,ngay_sinh,dan_toc,que_quan,chuc_vu,lop,
            chi_bo_cong_nhan,so_bc_cam_tinh,ngay_hop_cam_tinh,dang_vien_giup_do,
            ngay_phan_cong_giup_do,so_qd_mo_lop,ngay_qd_mo_lop,tg_lop_boi_duong,
            ngay_cap_cc,so_qd_cc,don_vi_cap_cc,ten_dv_congtac_khi_cap_cc,
            ten_chibo_khi_cap_cc,ten_danguy_khi_cap_cc,ten_tinhuy_khi_cap_cc,
            ma_so,ket_nap_dang,ngay_quyet_dinh,so_qd_ket_nap,ngay_ket_nap,
            dang_vien_huong_dan,ngay_chuyen_sinh_hoat,noi_chuyen_toi,ghi_chu,trang_thai,avatar
        ) VALUES (
            :ma_gvsv,:ho_ten,:sdt,:gioi_tinh,:ngay_sinh,:dan_toc,:que_quan,:chuc_vu,:lop,
            :chi_bo_cong_nhan,:so_bc_cam_tinh,:ngay_hop_cam_tinh,:dang_vien_giup_do,
            :ngay_phan_cong_giup_do,:so_qd_mo_lop,:ngay_qd_mo_lop,:tg_lop_boi_duong,
            :ngay_cap_cc,:so_qd_cc,:don_vi_cap_cc,:ten_dv_congtac_khi_cap_cc,
            :ten_chibo_khi_cap_cc,:ten_danguy_khi_cap_cc,:ten_tinhuy_khi_cap_cc,
            :ma_so,:ket_nap_dang,:ngay_quyet_dinh,:so_qd_ket_nap,:ngay_ket_nap,
            :dang_vien_huong_dan,:ngay_chuyen_sinh_hoat,:noi_chuyen_toi,:ghi_chu,:trang_thai,:avatar
        )";
        $stmt = $db->prepare($sql);
        $stmt->execute($data);
        $newId = $db->lastInsertId();
        logHistory($newId, 'Thêm mới', 'Thêm đối tượng: ' . ($data['ho_ten'] ?? ''));
        setFlash('success', 'Đã thêm đối tượng "' . ($data['ho_ten'] ?? '') . '" thành công!');
        redirect(BASE_URL . 'Quan_ly_doi_tuong/chi_tiet.php?id=' . $newId);
    }
}

require_once dirname(__DIR__) . '/Giao_dien/header.php';
?>

<div class="page-header">
  <div class="page-header-left">
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>index.php">Dashboard</a><span class="sep">›</span>
      <a href="danh_sach.php">Danh sách</a><span class="sep">›</span>
      <span class="current">Thêm mới</span>
    </div>
    <div class="page-title"><i class="bi bi-person-plus-fill" style="margin-right:6px;"></i> Thêm <span>Đối tượng</span> mới</div>
  </div>
  <a href="danh_sach.php" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Quay lại</a>
</div>

<?php if ($errors): ?>
<div class="flash flash-danger"><i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i> <?= implode('<br><i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i> ', array_map('e', $errors)) ?></div>
<?php endif; ?>

<form method="post" id="addForm" enctype="multipart/form-data">

<!-- 🌟 Edge AI Assistant Widget: Quét hồ sơ CCCD / Thẻ SV & Tự động điền form -->
<div class="form-section" style="background:linear-gradient(135deg, rgba(200,16,46,0.08), rgba(255,215,0,0.08));border:1px dashed var(--gold);border-radius:12px;padding:18px;margin-bottom:20px;">
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:12px;">
    <div style="display:flex;align-items:center;gap:10px;">
      <span style="font-size:24px;color:var(--gold);"><i class="bi bi-cpu-fill"></i></span>
      <div>
        <h4 style="margin:0;font-size:15px;color:var(--gold);font-weight:700;">TRỢ LÝ EDGE AI: QUÉT HỒ SƠ CAMERA & TỰ ĐỘNG ĐIỀN THÔNG TIN</h4>
        <p style="margin:2px 0 0 0;font-size:11.5px;color:var(--text2);">Chụp trực tiếp bằng Camera hoặc Tải ảnh CCCD / Thẻ SV / Đơn xin vào Đảng để AI quét OCR tự động điền form bên dưới.</p>
      </div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
    <!-- Box 1: Quét CCCD / Thẻ SV / Đơn xin -->
    <div style="background:var(--bg2);border:1px solid var(--border);padding:14px;border-radius:8px;">
      <label style="font-size:12px;font-weight:700;display:block;margin-bottom:6px;color:var(--text);"><i class="bi bi-person-vcard-fill" style="margin-right:4px;"></i> 1. Tải lên hoặc Chụp Camera CCCD / Thẻ SV / Hồ sơ</label>
      <input type="file" id="aiDocInput" multiple accept="image/*,application/pdf" class="form-control" style="font-size:11px;padding:6px;">
      <div style="display:flex;gap:6px;margin-top:8px;">
        <button type="button" onclick="openLiveCameraForAutoFill()" class="btn btn-primary btn-sm" style="flex:1;justify-content:center;font-weight:700;" title="Quét tài liệu trực tiếp qua Camera WebRTC"><i class="bi bi-camera-video-fill"></i> Chụp Camera Quét Hồ Sơ</button>
        <button type="button" onclick="triggerEdgeAIOCR()" id="btnAiScan" class="btn btn-gold btn-sm" style="flex:1;justify-content:center;font-weight:700;"><i class="bi bi-lightning-charge-fill"></i> Quét OCR Tự Động</button>
      </div>
      <button type="button" id="btnViewXaiAutoFill" onclick="openXaiForAutoFill()" class="btn btn-outline btn-sm" style="display:none;margin-top:8px;width:100%;justify-content:center;color:#38bdf8;border-color:#38bdf8;font-weight:600;"><i class="bi bi-bullseye"></i> Xem Bản Đồ Độ Tin Cậy XAI (Explainable AI)</button>
      <div id="aiOcrStatus" style="font-size:11.5px;margin-top:6px;color:var(--gold);font-weight:600;"></div>
    </div>

    <!-- Box 2: Smart Crop & Chụp Live Ảnh Chân Dung 3x4 -->
    <div style="background:var(--bg2);border:1px solid var(--border);padding:14px;border-radius:8px;">
      <label style="font-size:12px;font-weight:700;display:block;margin-bottom:6px;color:var(--text);"><i class="bi bi-camera-fill" style="margin-right:4px;"></i> 2. Chụp Live Hoặc Cắt Ảnh Chân Dung 3x4</label>
      <div style="display:flex;gap:6px;">
        <button type="button" onclick="openLiveCameraForAvatar()" class="btn btn-outline btn-sm" style="flex:1;justify-content:center;font-weight:700;"><i class="bi bi-person-bounding-box" style="margin-right:4px;"></i> Chụp Ảnh Thẻ 3x4 Live</button>
        <button type="button" onclick="document.getElementById('avatarInput').click()" class="btn btn-outline btn-sm" style="flex:1;justify-content:center;"><i class="bi bi-folder2-open" style="margin-right:4px;"></i> Chọn Từ Máy</button>
      </div>
      <div id="avatarAiStatus" style="font-size:11.5px;margin-top:8px;color:var(--success);font-weight:600;"></div>
    </div>
  </div>
</div>

<!-- ⓪ Avatar -->
<div class="form-section">
  <div class="form-section-title"><i class="bi bi-image" style="margin-right:6px;"></i> Ảnh đại diện</div>
  <div class="avatar-upload-wrap">
    <div id="avatarPreviewWrap" class="avatar-preview-large"><i class="bi bi-camera" style="font-size:28px;color:var(--text2);"></i></div>
    <div class="avatar-upload-btn">
      <button type="button" class="btn btn-outline" onclick="document.getElementById('avatarInput').click()">
        <i class="bi bi-folder2-open"></i> Chọn ảnh...
      </button>
      <button type="button" class="btn btn-primary btn-sm" onclick="openLiveCameraForAvatar()" style="display:inline-flex;align-items:center;gap:4px;">
        <i class="bi bi-camera-fill"></i> Chụp Live & Cắt 3x4
      </button>
      <button type="button" class="btn btn-danger btn-sm" id="removeAvatarBtn" style="display:none;" onclick="removeAvatar()">
        <i class="bi bi-trash"></i> Xóa ảnh
      </button>
      <div class="avatar-hint">Hỗ trợ tất cả định dạng ảnh · Tối đa: 20MB</div>
    </div>
    <input type="file" name="avatar" id="avatarInput" class="avatar-input-hidden" accept="image/*" onchange="previewAvatar(this)">
  </div>
</div>

<!-- ①  Thông tin cá nhân -->
<div class="form-section">
  <div class="form-section-title"><i class="bi bi-person-badge" style="margin-right:6px;"></i> 1. Thông tin cá nhân</div>
  <div class="form-grid">
    <div class="form-group">
      <label class="form-label">Mã GV/SV</label>
      <input type="text" name="ma_gvsv" class="form-control" value="<?= e($data['ma_gvsv'] ?? '') ?>" placeholder="VD: GV001">
    </div>
    <div class="form-group">
      <label class="form-label">Họ và tên <span class="required">*</span></label>
      <input type="text" name="ho_ten" class="form-control" value="<?= e($data['ho_ten'] ?? '') ?>" placeholder="Nhập đầy đủ họ tên" required>
    </div>
    <div class="form-group">
      <label class="form-label">Số điện thoại</label>
      <input type="tel" name="sdt" class="form-control" value="<?= e($data['sdt'] ?? '') ?>" placeholder="0xxx xxx xxx">
    </div>
    <div class="form-group">
      <label class="form-label">Giới tính</label>
      <select name="gioi_tinh" class="form-control">
        <option value="">-- Chọn --</option>
        <option value="Nam"  <?= ($data['gioi_tinh']??'')==='Nam'?'selected':'' ?>>Nam</option>
        <option value="Nữ"   <?= ($data['gioi_tinh']??'')==='Nữ'?'selected':'' ?>>Nữ</option>
        <option value="Khác" <?= ($data['gioi_tinh']??'')==='Khác'?'selected':'' ?>>Khác</option>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">Ngày sinh</label>
      <input type="date" name="ngay_sinh" class="form-control" value="<?= e($data['ngay_sinh'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Dân tộc</label>
      <input type="text" name="dan_toc" class="form-control" value="<?= e($data['dan_toc'] ?? '') ?>" placeholder="VD: Kinh, Thái...">
    </div>
    <div class="form-group form-full">
      <label class="form-label">Quê quán</label>
      <input type="text" name="que_quan" class="form-control" value="<?= e($data['que_quan'] ?? '') ?>" placeholder="Xã/Phường, Huyện/TP, Tỉnh">
    </div>
    <div class="form-group">
      <label class="form-label">Chức vụ</label>
      <input type="text" name="chuc_vu" class="form-control" value="<?= e($data['chuc_vu'] ?? '') ?>" placeholder="LP ĐS, Cán bộ lớp...">
    </div>
    <div class="form-group">
      <label class="form-label">Lớp</label>
      <input type="text" name="lop" class="form-control" value="<?= e($data['lop'] ?? '') ?>" placeholder="VD: K63 ĐHSP Toán">
    </div>
    <div class="form-group">
      <label class="form-label">Trạng thái</label>
      <select name="trang_thai" class="form-control">
        <option value="Đang theo dõi" <?= ($data['trang_thai']??'Đang theo dõi')==='Đang theo dõi'?'selected':'' ?>>Đang theo dõi</option>
        <option value="Đã kết nạp" <?= ($data['trang_thai']??'')==='Đã kết nạp'?'selected':'' ?>>Đã kết nạp</option>
        <option value="Đã chuyển" <?= ($data['trang_thai']??'')==='Đã chuyển'?'selected':'' ?>>Đã chuyển</option>
        <option value="Tạm dừng" <?= ($data['trang_thai']??'')==='Tạm dừng'?'selected':'' ?>>Tạm dừng</option>
      </select>
    </div>
  </div>
</div>

<!-- ② Cảm tình Đảng -->
<div class="form-section">
  <div class="form-section-title"><i class="bi bi-building" style="margin-right:6px;"></i> 2. Chi bộ & Cảm tình Đảng</div>
  <div class="form-grid">
    <div class="form-group form-full">
      <label class="form-label">Tên chi bộ công nhận</label>
      <input type="text" name="chi_bo_cong_nhan" class="form-control" list="chibo-list"
             value="<?= e($data['chi_bo_cong_nhan'] ?? '') ?>" placeholder="Nhập hoặc chọn chi bộ">
      <datalist id="chibo-list">
        <?php foreach ($chiBos as $cb): ?>
        <option value="<?= e($cb['ten_chi_bo']) ?>">
        <?php endforeach; ?>
      </datalist>
    </div>
    <div class="form-group">
      <label class="form-label">Số Báo cáo CB công nhận CT Đảng</label>
      <input type="text" name="so_bc_cam_tinh" class="form-control" value="<?= e($data['so_bc_cam_tinh'] ?? '') ?>" placeholder="VD: 07-BC/CB-KHTN-CN">
    </div>
    <div class="form-group">
      <label class="form-label">Ngày họp CB công nhận</label>
      <input type="date" name="ngay_hop_cam_tinh" class="form-control" value="<?= e($data['ngay_hop_cam_tinh'] ?? '') ?>">
    </div>
  </div>
</div>

<!-- ③ Đảng viên giúp đỡ -->
<div class="form-section">
  <div class="form-section-title"><i class="bi bi-people" style="margin-right:6px;"></i> 3. Đảng viên được giúp đỡ</div>
  <div class="form-grid">
    <div class="form-group">
      <label class="form-label">Tên Đảng viên giúp đỡ</label>
      <input type="text" name="dang_vien_giup_do" class="form-control" list="dv-list"
             value="<?= e($data['dang_vien_giup_do'] ?? '') ?>" placeholder="Nhập hoặc chọn đảng viên">
      <datalist id="dv-list">
        <?php foreach ($dangViens as $dv): ?>
        <option value="<?= e($dv['ho_ten']) ?>">
        <?php endforeach; ?>
      </datalist>
    </div>
    <div class="form-group">
      <label class="form-label">Ngày được phân công giúp đỡ</label>
      <input type="date" name="ngay_phan_cong_giup_do" class="form-control" value="<?= e($data['ngay_phan_cong_giup_do'] ?? '') ?>">
    </div>
  </div>
</div>

<!-- ④ Lớp bồi dưỡng -->
<div class="form-section">
  <div class="form-section-title"><i class="bi bi-book" style="margin-right:6px;"></i> 4. Lớp Bồi dưỡng nhận thức về Đảng</div>
  <div class="form-grid">
    <div class="form-group">
      <label class="form-label">Số QĐ mở lớp bồi dưỡng</label>
      <input type="text" name="so_qd_mo_lop" class="form-control" value="<?= e($data['so_qd_mo_lop'] ?? '') ?>" placeholder="VD: 319-QĐ/ĐU">
    </div>
    <div class="form-group">
      <label class="form-label">Ngày QĐ mở lớp</label>
      <input type="date" name="ngay_qd_mo_lop" class="form-control" value="<?= e($data['ngay_qd_mo_lop'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Thời gian lớp bồi dưỡng</label>
      <input type="text" name="tg_lop_boi_duong" class="form-control" value="<?= e($data['tg_lop_boi_duong'] ?? '') ?>" placeholder="VD: 09/9/2024 - 18/9/2024">
    </div>
    <div class="form-group">
      <label class="form-label">Ngày cấp CC bồi dưỡng</label>
      <input type="date" name="ngay_cap_cc" class="form-control" value="<?= e($data['ngay_cap_cc'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Số QĐ của CC BD</label>
      <input type="text" name="so_qd_cc" class="form-control" value="<?= e($data['so_qd_cc'] ?? '') ?>" placeholder="VD: 280/CN">
    </div>
    <div class="form-group">
      <label class="form-label">Đơn vị cấp CC</label>
      <input type="text" name="don_vi_cap_cc" class="form-control" value="<?= e($data['don_vi_cap_cc'] ?? '') ?>">
    </div>
    <div class="form-group form-full">
      <label class="form-label">Tên đơn vị công tác/học khi cấp chứng chỉ</label>
      <input type="text" name="ten_dv_congtac_khi_cap_cc" class="form-control" value="<?= e($data['ten_dv_congtac_khi_cap_cc'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Tên chi bộ sinh hoạt khi cấp CC</label>
      <input type="text" name="ten_chibo_khi_cap_cc" class="form-control" value="<?= e($data['ten_chibo_khi_cap_cc'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Tên Đảng uỷ sinh hoạt khi cấp CC</label>
      <input type="text" name="ten_danguy_khi_cap_cc" class="form-control" value="<?= e($data['ten_danguy_khi_cap_cc'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Tên tỉnh uỷ sinh hoạt khi cấp CC</label>
      <input type="text" name="ten_tinhuy_khi_cap_cc" class="form-control" value="<?= e($data['ten_tinhuy_khi_cap_cc'] ?? '') ?>">
    </div>
  </div>
</div>

<!-- ⑤ Kết nạp Đảng -->
<div class="form-section">
  <div class="form-section-title"><i class="bi bi-star-fill" style="margin-right:6px;color:var(--gold);"></i> 5. Kết nạp Đảng</div>
  <div class="form-grid">
    <div class="form-group">
      <label class="form-label">Mã số</label>
      <input type="text" name="ma_so" class="form-control" value="<?= e($data['ma_so'] ?? '') ?>" placeholder="VD: ĐU05">
    </div>
    <div class="form-group">
      <label class="form-label">Kết nạp Đảng (ghi chú)</label>
      <input type="text" name="ket_nap_dang" class="form-control" value="<?= e($data['ket_nap_dang'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Ngày quyết định kết nạp</label>
      <input type="date" name="ngay_quyet_dinh" class="form-control" value="<?= e($data['ngay_quyet_dinh'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Số QĐ Kết nạp đảng viên</label>
      <input type="text" name="so_qd_ket_nap" class="form-control" value="<?= e($data['so_qd_ket_nap'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Ngày kết nạp Đảng</label>
      <input type="date" name="ngay_ket_nap" class="form-control" value="<?= e($data['ngay_ket_nap'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Đảng viên hướng dẫn</label>
      <input type="text" name="dang_vien_huong_dan" class="form-control" list="dv-list" value="<?= e($data['dang_vien_huong_dan'] ?? '') ?>">
    </div>
  </div>
</div>

<!-- ⑥ Chuyển sinh hoạt -->
<div class="form-section">
  <div class="form-section-title"><i class="bi bi-box-arrow-up-right" style="margin-right:6px;"></i> 6. Chuyển sinh hoạt</div>
  <div class="form-grid">
    <div class="form-group">
      <label class="form-label">Ngày chuyển sinh hoạt</label>
      <input type="date" name="ngay_chuyen_sinh_hoat" class="form-control" value="<?= e($data['ngay_chuyen_sinh_hoat'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Nơi chuyển tới</label>
      <input type="text" name="noi_chuyen_toi" class="form-control" value="<?= e($data['noi_chuyen_toi'] ?? '') ?>">
    </div>
    <div class="form-group form-full">
      <label class="form-label">Ghi chú</label>
      <textarea name="ghi_chu" class="form-control"><?= e($data['ghi_chu'] ?? '') ?></textarea>
    </div>
  </div>
</div>


<!-- Submit -->
<div style="display:flex;gap:12px;justify-content:flex-end;margin-top:8px;">
  <a href="danh_sach.php" class="btn btn-outline btn-lg">Hủy</a>
  <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-floppy-fill" style="margin-right:6px;"></i> Lưu đối tượng</button>
</div>

</form>

<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script src="<?= BASE_URL ?>AI_Module/edge_image_processor.js"></script>
<script src="<?= BASE_URL ?>AI_Module/live_camera_scanner.js"></script>
<script src="<?= BASE_URL ?>AI_Module/xai_confidence_overlay.js"></script>
<script src="<?= BASE_URL ?>AI_Module/edge_ai_autofill.js"></script>

<script>
let lastAutoFillOCR = null;
const xaiVisualizer = (typeof XAIConfidenceOverlay !== 'undefined') ? new XAIConfidenceOverlay() : null;

function triggerEdgeAIOCR(filesOverride = null) {
  const files = filesOverride || document.getElementById('aiDocInput').files;
  const statusDiv = document.getElementById('aiOcrStatus');
  const btn = document.getElementById('btnAiScan');
  const btnXai = document.getElementById('btnViewXaiAutoFill');

  if (!files || files.length === 0) {
    alert("Vui lòng chọn hoặc chụp ảnh CCCD (Mặt trước/Mặt sau), Thẻ Sinh Viên hoặc Đơn xin vào Đảng!");
    return;
  }

  if (btn) btn.disabled = true;

  processEdgeAIAutoFill(
    files,
    function(msg) {
      if (statusDiv) statusDiv.innerHTML = `<span style="color:var(--gold);"><i class="bi bi-hourglass-split"></i> ${msg}</span>`;
    },
    function(data, combinedText, ocrMeta) {
      if (btn) btn.disabled = false;
      if (statusDiv) statusDiv.innerHTML = `<span style="color:var(--success);"><i class="bi bi-check-circle-fill"></i> Đã trích xuất & tự động điền các trường thành công!</span>`;

      lastAutoFillOCR = ocrMeta;
      if (btnXai && ocrMeta && ocrMeta.words && ocrMeta.words.length > 0) {
        btnXai.style.display = 'flex';
      }

      // Fill in fields if extracted
      if (data.ho_ten && document.querySelector('input[name="ho_ten"]')) {
        document.querySelector('input[name="ho_ten"]').value = data.ho_ten;
      }
      if (data.ma_gvsv && document.querySelector('input[name="ma_gvsv"]')) {
        document.querySelector('input[name="ma_gvsv"]').value = data.ma_gvsv;
      }
      if (data.ngay_sinh && document.querySelector('input[name="ngay_sinh"]')) {
        document.querySelector('input[name="ngay_sinh"]').value = data.ngay_sinh;
      }
      if (data.gioi_tinh && document.querySelector('select[name="gioi_tinh"]')) {
        document.querySelector('select[name="gioi_tinh"]').value = data.gioi_tinh;
      }
      if (data.lop && document.querySelector('input[name="lop"]')) {
        document.querySelector('input[name="lop"]').value = data.lop;
      }
      if (data.que_quan && document.querySelector('input[name="que_quan"]')) {
        document.querySelector('input[name="que_quan"]').value = data.que_quan;
      }
      if (data.dan_toc && document.querySelector('input[name="dan_toc"]')) {
        document.querySelector('input[name="dan_toc"]').value = data.dan_toc;
      }
      if (data.chi_bo_cong_nhan && document.querySelector('input[name="chi_bo_cong_nhan"]')) {
        document.querySelector('input[name="chi_bo_cong_nhan"]').value = data.chi_bo_cong_nhan;
      }
      if (data.don_vi_cap_cc && document.querySelector('input[name="don_vi_cap_cc"]')) {
        document.querySelector('input[name="don_vi_cap_cc"]').value = data.don_vi_cap_cc;
      }
      if (data.so_qd_cc && document.querySelector('input[name="so_qd_cc"]')) {
        document.querySelector('input[name="so_qd_cc"]').value = data.so_qd_cc;
      }
      if (data.ngay_cap_cc && document.querySelector('input[name="ngay_cap_cc"]')) {
        document.querySelector('input[name="ngay_cap_cc"]').value = data.ngay_cap_cc;
      }
    },
    function(err) {
      if (btn) btn.disabled = false;
      if (statusDiv) statusDiv.innerHTML = `<span style="color:var(--danger);"><i class="bi bi-exclamation-triangle-fill"></i> ${err}</span>`;
    }
  );
}

function openLiveCameraForAutoFill() {
  if (typeof LiveCameraScanner === 'undefined') {
    alert("Thư viện LiveCameraScanner chưa sẵn sàng.");
    return;
  }
  const scanner = new LiveCameraScanner({
    targetType: 'card',
    sharpnessThreshold: 60,
    onCapture: (file, dataUrl) => {
      triggerEdgeAIOCR([file]);
    }
  });
  scanner.open();
}

function dataURLtoFile(dataurl, filename) {
  var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
      bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
  while(n--){
      u8arr[n] = bstr.charCodeAt(n);
  }
  return new File([u8arr], filename, {type:mime});
}

function openLiveCameraForAvatar() {
  if (typeof LiveCameraScanner === 'undefined') {
    alert("Thư viện LiveCameraScanner chưa sẵn sàng.");
    return;
  }
  const scanner = new LiveCameraScanner({
    targetType: 'card',
    autoSnapEnabled: false,
    onCapture: (file, dataUrl) => {
      const status = document.getElementById('avatarAiStatus');
      if (status) status.innerHTML = '<i class="bi bi-hourglass-split"></i> AI đang căn chỉnh ảnh thẻ tỉ lệ 3x4...';

      // Create a temporary canvas to auto crop 3x4
      const tempCanvas = document.createElement('canvas');
      processEdgeAIAvatarCrop(file, tempCanvas, function(res) {
        if (res.success) {
          const croppedDataUrl = tempCanvas.toDataURL('image/jpeg', 0.92);
          const croppedFile = dataURLtoFile(croppedDataUrl, 'avatar_3x4.jpg');
          
          const dt = new DataTransfer();
          dt.items.add(croppedFile);
          const avatarInput = document.getElementById('avatarInput');
          avatarInput.files = dt.files;
          previewAvatar(avatarInput);

          if (status) status.innerHTML = `<i class="bi bi-check-circle-fill"></i> Đã chụp & cắt ảnh thẻ 3x4 thành công!`;
        } else {
          // Fallback to original snapped file
          const dt = new DataTransfer();
          dt.items.add(file);
          const avatarInput = document.getElementById('avatarInput');
          avatarInput.files = dt.files;
          previewAvatar(avatarInput);
          if (status) status.innerHTML = `<i class="bi bi-check-circle-fill"></i> Đã nhận diện ảnh chân dung.`;
        }
      });
    }
  });
  scanner.open();
}

function openXaiForAutoFill() {
  if (!lastAutoFillOCR || !lastAutoFillOCR.image) {
    alert("Chưa có dữ liệu quét OCR để hiển thị XAI Heatmap.");
    return;
  }
  if (!xaiVisualizer) {
    alert("Thư viện XAIConfidenceOverlay chưa sẵn sàng.");
    return;
  }
  xaiVisualizer.open(lastAutoFillOCR.image, lastAutoFillOCR.words || []);
}

function previewAvatar(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      var wrap = document.getElementById('avatarPreviewWrap');
      wrap.innerHTML = '';
      var img = document.createElement('img');
      img.src = e.target.result;
      img.style.cssText = 'width:100%;height:100%;object-fit:cover;border-radius:50%;';
      wrap.appendChild(img);
      wrap.style.padding = '0';
      document.getElementById('removeAvatarBtn').style.display = 'inline-flex';
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function removeAvatar() {
  document.getElementById('avatarInput').value = '';
  var wrap = document.getElementById('avatarPreviewWrap');
  wrap.innerHTML = '<i class="bi bi-camera" style="font-size:28px;color:var(--text2);"></i>';
  wrap.style.cssText = '';
  document.getElementById('removeAvatarBtn').style.display = 'none';
  const status = document.getElementById('avatarAiStatus');
  if (status) status.innerHTML = '';
}
</script>

<?php require_once dirname(__DIR__) . '/Giao_dien/footer.php'; ?>

