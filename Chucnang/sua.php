<?php
// Chucnang/sua.php – Sửa thông tin đối tượng
require_once dirname(__DIR__) . '/config.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { setFlash('danger','ID không hợp lệ'); redirect(BASE_URL.'Chucnang/danh_sach.php'); }

$db = getDB();
$q  = $db->prepare("SELECT * FROM doi_tuong WHERE id = ?");
$q->execute([$id]);
$dt = $q->fetch();
if (!$dt) { setFlash('danger','Không tìm thấy đối tượng'); redirect(BASE_URL.'Chucnang/danh_sach.php'); }

$chiBos    = $db->query("SELECT * FROM chi_bo ORDER BY ten_chi_bo")->fetchAll();
$dangViens = $db->query("SELECT * FROM dang_vien ORDER BY ho_ten")->fetchAll();
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    $data = [];
    foreach ($fields as $f) $data[$f] = trim($_POST[$f] ?? '');
    if (empty($data['ho_ten'])) $errors[] = 'Họ và tên không được để trống';

    // Handle avatar
    $avatarPath = $dt['avatar']; // Keep old avatar by default
    if (isset($_POST['remove_avatar_flag']) && $_POST['remove_avatar_flag'] === '1') {
        if ($dt['avatar'] && file_exists(dirname(__DIR__) . '/' . $dt['avatar'])) {
            @unlink(dirname(__DIR__) . '/' . $dt['avatar']);
        }
        $avatarPath = null;
    }

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = dirname(__DIR__) . '/uploads/avatars/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (in_array($ext, $allowed) && $_FILES['avatar']['size'] <= 5 * 1024 * 1024) {
            // Delete old file if exists
            if ($dt['avatar'] && file_exists(dirname(__DIR__) . '/' . $dt['avatar'])) {
                @unlink(dirname(__DIR__) . '/' . $dt['avatar']);
            }
            $fname = 'av_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $fname)) {
                $avatarPath = 'uploads/avatars/' . $fname;
            }
        } else {
            $errors[] = 'Ảnh phải là JPG/PNG/GIF/WebP và nhỏ hơn 5MB';
        }
    }

    if (empty($errors)) {
        $dateFields = ['ngay_sinh','ngay_hop_cam_tinh','ngay_phan_cong_giup_do',
                       'ngay_qd_mo_lop','ngay_cap_cc','ngay_quyet_dinh',
                       'ngay_ket_nap','ngay_chuyen_sinh_hoat'];
        foreach ($dateFields as $df) $data[$df] = $data[$df] ? toDbDate($data[$df]) : null;
        foreach ($data as &$v) if ($v === '') $v = null;

        $fields[] = 'avatar';
        $data['avatar'] = $avatarPath;

        $sets = implode(', ', array_map(fn($f) => "$f = :$f", $fields));
        $data['id'] = $id;
        $db->prepare("UPDATE doi_tuong SET $sets WHERE id = :id")->execute($data);
        logHistory($id, 'Cập nhật', 'Cập nhật thông tin đối tượng');
        setFlash('success', 'Đã cập nhật thông tin "' . ($data['ho_ten'] ?? '') . '"');
        redirect(BASE_URL . 'Chucnang/chi_tiet.php?id=' . $id);
    }
    // merge back for redisplay
    $dt = array_merge($dt, $data);
}

$pageTitle = 'Sửa: ' . $dt['ho_ten'];
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="page-header">
  <div class="page-header-left">
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>index.php">Dashboard</a><span class="sep">›</span>
      <a href="danh_sach.php">Danh sách</a><span class="sep">›</span>
      <a href="chi_tiet.php?id=<?= $id ?>">Chi tiết</a><span class="sep">›</span>
      <span class="current">Sửa</span>
    </div>
    <div class="page-title">✏️ Sửa thông tin <span><?= e($dt['ho_ten']) ?></span></div>
  </div>
  <div style="display:flex;gap:10px;">
    <a href="chi_tiet.php?id=<?= $id ?>" class="btn btn-outline">← Quay lại</a>
  </div>
</div>

<?php if ($errors): ?>
<div class="flash flash-danger">❌ <?= implode('<br>❌ ', array_map('e', $errors)) ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
<input type="hidden" name="remove_avatar_flag" id="removeAvatarFlag" value="0">

<!-- ⓪ Avatar -->
<div class="form-section">
  <div class="form-section-title">🖼️ Ảnh đại diện</div>
  <div class="avatar-upload-wrap">
    <div id="avatarPreviewWrap" class="avatar-preview-large">
      <?php if ($dt['avatar']): ?>
        <img src="<?= BASE_URL . e($dt['avatar']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
      <?php else: ?>
        📷
      <?php endif; ?>
    </div>
    <div class="avatar-upload-btn">
      <button type="button" class="btn btn-outline" onclick="document.getElementById('avatarInput').click()">
        📁 Chọn ảnh...
      </button>
      <button type="button" class="btn btn-danger btn-sm" id="removeAvatarBtn" style="<?= $dt['avatar'] ? 'display:inline-flex;' : 'display:none;' ?>" onclick="removeAvatar()">
        🗑️ Xóa ảnh
      </button>
      <div class="avatar-hint">Định dạng: JPG, PNG, WebP · Tối đa: 5MB</div>
    </div>
    <input type="file" name="avatar" id="avatarInput" class="avatar-input-hidden" accept="image/*" onchange="previewAvatar(this)">
  </div>
</div>

<!-- ① Thông tin cá nhân -->
<div class="form-section">
  <div class="form-section-title">👤 1. Thông tin cá nhân</div>
  <div class="form-grid">
    <div class="form-group">
      <label class="form-label">Mã GV/SV</label>
      <input type="text" name="ma_gvsv" class="form-control" value="<?= e($dt['ma_gvsv'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Họ và tên <span class="required">*</span></label>
      <input type="text" name="ho_ten" class="form-control" value="<?= e($dt['ho_ten'] ?? '') ?>" required>
    </div>
    <div class="form-group">
      <label class="form-label">SĐT</label>
      <input type="tel" name="sdt" class="form-control" value="<?= e($dt['sdt'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Giới tính</label>
      <select name="gioi_tinh" class="form-control">
        <option value="">-- Chọn --</option>
        <option value="Nam"  <?= ($dt['gioi_tinh']??'')==='Nam'?'selected':'' ?>>Nam</option>
        <option value="Nữ"   <?= ($dt['gioi_tinh']??'')==='Nữ'?'selected':'' ?>>Nữ</option>
        <option value="Khác" <?= ($dt['gioi_tinh']??'')==='Khác'?'selected':'' ?>>Khác</option>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">Ngày sinh</label>
      <input type="date" name="ngay_sinh" class="form-control" value="<?= e($dt['ngay_sinh'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Dân tộc</label>
      <input type="text" name="dan_toc" class="form-control" value="<?= e($dt['dan_toc'] ?? '') ?>">
    </div>
    <div class="form-group form-full">
      <label class="form-label">Quê quán</label>
      <input type="text" name="que_quan" class="form-control" value="<?= e($dt['que_quan'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Chức vụ</label>
      <input type="text" name="chuc_vu" class="form-control" value="<?= e($dt['chuc_vu'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Lớp</label>
      <input type="text" name="lop" class="form-control" value="<?= e($dt['lop'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Trạng thái</label>
      <select name="trang_thai" class="form-control">
        <?php foreach(['Đang theo dõi','Đã kết nạp','Đã chuyển','Tạm dừng'] as $ts): ?>
        <option value="<?= $ts ?>" <?= ($dt['trang_thai']??'')===$ts?'selected':'' ?>><?= $ts ?></option>
        <?php endforeach; ?>
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
      <input type="text" name="chi_bo_cong_nhan" class="form-control" list="chibo-list" value="<?= e($dt['chi_bo_cong_nhan'] ?? '') ?>">
      <datalist id="chibo-list">
        <?php foreach ($chiBos as $cb): ?><option value="<?= e($cb['ten_chi_bo']) ?>"><?php endforeach; ?>
      </datalist>
    </div>
    <div class="form-group">
      <label class="form-label">Số Báo cáo CT Đảng</label>
      <input type="text" name="so_bc_cam_tinh" class="form-control" value="<?= e($dt['so_bc_cam_tinh'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Ngày họp CB công nhận</label>
      <input type="date" name="ngay_hop_cam_tinh" class="form-control" value="<?= e($dt['ngay_hop_cam_tinh'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">ĐV được giúp đỡ</label>
      <input type="text" name="dang_vien_giup_do" class="form-control" list="dv-list" value="<?= e($dt['dang_vien_giup_do'] ?? '') ?>">
      <datalist id="dv-list">
        <?php foreach ($dangViens as $dv): ?><option value="<?= e($dv['ho_ten']) ?>"><?php endforeach; ?>
      </datalist>
    </div>
    <div class="form-group">
      <label class="form-label">Ngày phân công giúp đỡ</label>
      <input type="date" name="ngay_phan_cong_giup_do" class="form-control" value="<?= e($dt['ngay_phan_cong_giup_do'] ?? '') ?>">
    </div>
  </div>
</div>

<!-- ③ Lớp bồi dưỡng -->
<div class="form-section">
  <div class="form-section-title">📚 3. Lớp Bồi dưỡng nhận thức về Đảng</div>
  <div class="form-grid">
    <div class="form-group"><label class="form-label">Số QĐ mở lớp</label><input type="text" name="so_qd_mo_lop" class="form-control" value="<?= e($dt['so_qd_mo_lop']??'') ?>"></div>
    <div class="form-group"><label class="form-label">Ngày QĐ mở lớp</label><input type="date" name="ngay_qd_mo_lop" class="form-control" value="<?= e($dt['ngay_qd_mo_lop']??'') ?>"></div>
    <div class="form-group"><label class="form-label">Thời gian lớp BD</label><input type="text" name="tg_lop_boi_duong" class="form-control" value="<?= e($dt['tg_lop_boi_duong']??'') ?>"></div>
    <div class="form-group"><label class="form-label">Ngày cấp CC</label><input type="date" name="ngay_cap_cc" class="form-control" value="<?= e($dt['ngay_cap_cc']??'') ?>"></div>
    <div class="form-group"><label class="form-label">Số QĐ CC BD</label><input type="text" name="so_qd_cc" class="form-control" value="<?= e($dt['so_qd_cc']??'') ?>"></div>
    <div class="form-group"><label class="form-label">Đơn vị cấp CC</label><input type="text" name="don_vi_cap_cc" class="form-control" value="<?= e($dt['don_vi_cap_cc']??'') ?>"></div>
    <div class="form-group form-full"><label class="form-label">ĐV công tác khi cấp CC</label><input type="text" name="ten_dv_congtac_khi_cap_cc" class="form-control" value="<?= e($dt['ten_dv_congtac_khi_cap_cc']??'') ?>"></div>
    <div class="form-group"><label class="form-label">CB sinh hoạt khi cấp CC</label><input type="text" name="ten_chibo_khi_cap_cc" class="form-control" value="<?= e($dt['ten_chibo_khi_cap_cc']??'') ?>"></div>
    <div class="form-group"><label class="form-label">Đảng uỷ khi cấp CC</label><input type="text" name="ten_danguy_khi_cap_cc" class="form-control" value="<?= e($dt['ten_danguy_khi_cap_cc']??'') ?>"></div>
    <div class="form-group"><label class="form-label">Tỉnh uỷ khi cấp CC</label><input type="text" name="ten_tinhuy_khi_cap_cc" class="form-control" value="<?= e($dt['ten_tinhuy_khi_cap_cc']??'') ?>"></div>
  </div>
</div>

<!-- ④ Kết nạp -->
<div class="form-section">
  <div class="form-section-title">⭐ 4. Kết nạp Đảng</div>
  <div class="form-grid">
    <div class="form-group"><label class="form-label">Mã số</label><input type="text" name="ma_so" class="form-control" value="<?= e($dt['ma_so']??'') ?>"></div>
    <div class="form-group"><label class="form-label">Kết nạp Đảng</label><input type="text" name="ket_nap_dang" class="form-control" value="<?= e($dt['ket_nap_dang']??'') ?>"></div>
    <div class="form-group"><label class="form-label">Ngày quyết định</label><input type="date" name="ngay_quyet_dinh" class="form-control" value="<?= e($dt['ngay_quyet_dinh']??'') ?>"></div>
    <div class="form-group"><label class="form-label">Số QĐ kết nạp</label><input type="text" name="so_qd_ket_nap" class="form-control" value="<?= e($dt['so_qd_ket_nap']??'') ?>"></div>
    <div class="form-group"><label class="form-label">Ngày kết nạp</label><input type="date" name="ngay_ket_nap" class="form-control" value="<?= e($dt['ngay_ket_nap']??'') ?>"></div>
    <div class="form-group"><label class="form-label">ĐV hướng dẫn</label><input type="text" name="dang_vien_huong_dan" class="form-control" list="dv-list" value="<?= e($dt['dang_vien_huong_dan']??'') ?>"></div>
    <div class="form-group"><label class="form-label">Ngày chuyển SH</label><input type="date" name="ngay_chuyen_sinh_hoat" class="form-control" value="<?= e($dt['ngay_chuyen_sinh_hoat']??'') ?>"></div>
    <div class="form-group"><label class="form-label">Nơi chuyển tới</label><input type="text" name="noi_chuyen_toi" class="form-control" value="<?= e($dt['noi_chuyen_toi']??'') ?>"></div>
    <div class="form-group form-full">
      <label class="form-label">Ghi chú</label>
      <textarea name="ghi_chu" class="form-control"><?= e($dt['ghi_chu']??'') ?></textarea>
    </div>
  </div>
</div>

<div style="display:flex;gap:12px;justify-content:flex-end;margin-top:8px;">
  <a href="chi_tiet.php?id=<?= $id ?>" class="btn btn-outline btn-lg">Hủy</a>
  <button type="submit" class="btn btn-primary btn-lg">💾 Lưu thay đổi</button>
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
      document.getElementById('removeAvatarFlag').value = '0';
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
  document.getElementById('removeAvatarFlag').value = '1';
  document.getElementById('removeAvatarBtn').style.display = 'none';
}
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>

