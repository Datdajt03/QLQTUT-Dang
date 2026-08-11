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
    <div class="page-title">➕ Thêm <span>Đối tượng</span> mới</div>
  </div>
  <a href="danh_sach.php" class="btn btn-outline">← Quay lại</a>
</div>

<?php if ($errors): ?>
<div class="flash flash-danger">❌ <?= implode('<br>❌ ', array_map('e', $errors)) ?></div>
<?php endif; ?>

<form method="post" id="addForm" enctype="multipart/form-data">

<!-- ⓪ Avatar -->
<div class="form-section">
  <div class="form-section-title">🖼️ Ảnh đại diện</div>
  <div class="avatar-upload-wrap">
    <div id="avatarPreviewWrap" class="avatar-preview-large">📷</div>
    <div class="avatar-upload-btn">
      <button type="button" class="btn btn-outline" onclick="document.getElementById('avatarInput').click()">
        📁 Chọn ảnh...
      </button>
      <button type="button" class="btn btn-danger btn-sm" id="removeAvatarBtn" style="display:none;" onclick="removeAvatar()">
        🗑️ Xóa ảnh
      </button>
      <div class="avatar-hint">Hỗ trợ tất cả định dạng ảnh · Tối đa: 20MB</div>
    </div>
    <input type="file" name="avatar" id="avatarInput" class="avatar-input-hidden" accept="image/*" onchange="previewAvatar(this)">
  </div>
</div>

<!-- ①  Thông tin cá nhân -->
<div class="form-section">
  <div class="form-section-title">👤 1. Thông tin cá nhân</div>
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
  <div class="form-section-title">🏛️ 2. Chi bộ & Cảm tình Đảng</div>
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
  <div class="form-section-title">🤝 3. Đảng viên được giúp đỡ</div>
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
  <div class="form-section-title">📚 4. Lớp Bồi dưỡng nhận thức về Đảng</div>
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
  <div class="form-section-title">⭐ 5. Kết nạp Đảng</div>
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
  <div class="form-section-title">↗️ 6. Chuyển sinh hoạt</div>
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
  <button type="submit" class="btn btn-primary btn-lg">💾 Lưu đối tượng</button>
</div>

</form>

<script>
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
  wrap.innerHTML = '📷';
  wrap.style.cssText = '';
  document.getElementById('removeAvatarBtn').style.display = 'none';
}
</script>

<?php require_once dirname(__DIR__) . '/Giao_dien/footer.php'; ?>

