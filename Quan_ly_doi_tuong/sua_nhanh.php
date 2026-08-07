<?php
// Quan_ly_doi_tuong/sua_nhanh.php - Giao diện chỉnh sửa nhanh kiểu Excel trực tiếp (Hiện ô nhập trực tiếp + Lọc + Tìm kiếm)
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/User/auth.php';
requireRole(['Quản lý', 'Admin']);
$pageTitle = 'Sửa Excel trực tiếp';

$db = getDB();

// Lấy danh sách lớp và chi bộ để điền vào dropdown lọc
$lopList = $db->query("SELECT DISTINCT lop FROM doi_tuong WHERE lop IS NOT NULL AND lop != '' ORDER BY lop")->fetchAll(PDO::FETCH_COLUMN);
$chiboList = $db->query("SELECT DISTINCT chi_bo_cong_nhan FROM doi_tuong WHERE chi_bo_cong_nhan IS NOT NULL AND chi_bo_cong_nhan != '' ORDER BY chi_bo_cong_nhan")->fetchAll(PDO::FETCH_COLUMN);

// Nhận tham số lọc và tìm kiếm
$lopFilter       = $_GET['lop'] ?? '';
$chiboFilter     = $_GET['chi_bo'] ?? '';
$trangThaiFilter = $_GET['trang_thai'] ?? '';
$searchFilter    = trim($_GET['search'] ?? '');

// Xây dựng câu truy vấn lọc
$where = ['1=1'];
$params = [];

if ($lopFilter !== '') {
    $where[] = "lop = ?";
    $params[] = $lopFilter;
}
if ($chiboFilter !== '') {
    $where[] = "chi_bo_cong_nhan = ?";
    $params[] = $chiboFilter;
}
if ($trangThaiFilter !== '') {
    $where[] = "trang_thai = ?";
    $params[] = $trangThaiFilter;
}
if ($searchFilter !== '') {
    $where[] = "(ho_ten LIKE ? OR ma_gvsv LIKE ? OR sdt LIKE ? OR lop LIKE ?)";
    $params = array_merge($params, ["%$searchFilter%", "%$searchFilter%", "%$searchFilter%", "%$searchFilter%"]);
}

$whereStr = implode(' AND ', $where);
$stmt = $db->prepare("SELECT * FROM doi_tuong WHERE $whereStr ORDER BY ho_ten ASC");
$stmt->execute($params);
$rows = $stmt->fetchAll();

require_once dirname(__DIR__) . '/Giao_dien/header.php';
?>

<!-- Page Header -->
<div class="page-header">
  <div class="page-header-left">
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>index.php">Dashboard</a>
      <span class="sep">›</span>
      <span class="current">Sửa Excel trực tiếp</span>
    </div>
    <div class="page-title">✏️ Bảng Excel <span>Chỉnh sửa trực tiếp</span></div>
    <div class="page-subtitle">Nhấp chọn ô để nhập dữ liệu trực tiếp. Hệ thống tự động lưu khi nhập xong.</div>
  </div>
  <a href="<?= BASE_URL ?>Quan_ly_doi_tuong/danh_sach.php" class="btn btn-outline">📋 Về danh sách thường</a>
</div>

<!-- Filter Bar -->
<form method="get" class="filter-bar">
  <!-- Ô tìm kiếm văn bản -->
  <input type="text" name="search" class="form-control filter-search" 
         placeholder="🔍 Nhập tên, mã, SĐT hoặc lớp..." value="<?= e($searchFilter) ?>">

  <!-- Chọn Lớp -->
  <select name="lop" class="form-control">
    <option value="">Tất cả Lớp</option>
    <?php foreach ($lopList as $l): ?>
    <option value="<?= e($l) ?>" <?= $lopFilter===$l?'selected':'' ?>><?= e($l) ?></option>
    <?php endforeach; ?>
  </select>

  <!-- Chọn Chi bộ -->
  <select name="chi_bo" class="form-control" style="min-width:200px;">
    <option value="">Tất cả Chi bộ công nhận</option>
    <?php foreach ($chiboList as $cb): ?>
    <option value="<?= e($cb) ?>" <?= $chiboFilter===$cb?'selected':'' ?>><?= e($cb) ?></option>
    <?php endforeach; ?>
  </select>
  
  <!-- Chọn Trạng thái -->
  <select name="trang_thai" class="form-control">
    <option value="">Tất cả Trạng thái</option>
    <option value="Đang theo dõi" <?= $trangThaiFilter==='Đang theo dõi'?'selected':'' ?>>Đang theo dõi</option>
    <option value="Đã kết nạp"    <?= $trangThaiFilter==='Đã kết nạp'?'selected':'' ?>>Đã kết nạp</option>
    <option value="Đã chuyển"     <?= $trangThaiFilter==='Đã chuyển'?'selected':'' ?>>Đã chuyển</option>
    <option value="Tạm dừng"      <?= $trangThaiFilter==='Tạm dừng'?'selected':'' ?>>Tạm dừng</option>
  </select>

  <button type="submit" class="btn btn-primary">🔍 Tìm & Lọc</button>
  <a href="sua_nhanh.php" class="btn btn-outline">Reset</a>
</form>

<!-- Toast notification for feedback -->
<div id="toastMessage" style="position:fixed; bottom:20px; right:20px; z-index:9999; padding:12px 24px; border-radius:8px; display:none; font-weight:600; color:#fff; box-shadow:0 4px 12px rgba(0,0,0,0.5);"></div>

<!-- Excel Sheet Table Area -->
<div class="card fade-in">
  <div class="card-header">
    <div class="card-title">
      <span class="icon">📊</span> Danh sách quần chúng (Lưới Excel)
      <span style="font-size:12px; color:var(--text2); font-weight:normal; margin-left:8px;">
        (Tìm thấy: <?= count($rows) ?> người)
      </span>
    </div>
    <div style="font-size:12px; color:var(--text2);">
      💡 Phím tắt di chuyển: <kbd>Enter</kbd> / <kbd>↓</kbd> xuống ô dưới | <kbd>↑</kbd> lên ô trên | <kbd>→</kbd> qua phải | <kbd>←</kbd> qua trái | <kbd>Tab</kbd> di chuyển ngang
    </div>
  </div>
  <div class="card-body" style="padding:0;">
    <?php if (empty($rows)): ?>
    <div class="empty-state">
      <div class="icon">📂</div>
      <h3>Không tìm thấy dữ liệu phù hợp</h3>
      <p>Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm của bạn.</p>
    </div>
    <?php else: ?>
    <div class="excel-table-wrapper">
      <table class="excel-table" id="excelSheet">
        <thead>
          <tr>
            <th class="col-stt" style="text-align:center;">STT</th>
            <th class="col-ma">Mã GV/SV</th>
            <th class="col-ten">Họ và tên</th>
            <th class="col-lop">Lớp</th>
            <th class="col-chibo">Chi bộ công nhận</th>
            <th class="col-sdt">Số điện thoại</th>
            <th class="col-gioitinh">Giới tính</th>
            <th class="col-ngaysinh">Ngày sinh</th>
            <th class="col-trangthai">Trạng thái</th>
            <th class="col-ngaycamtinh">Ngày cảm tình</th>
            <th class="col-ngayketnap">Ngày kết nạp</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $i => $row): ?>
          <tr data-row-id="<?= $row['id'] ?>">
            <!-- STT -->
            <td style="text-align:center; color:var(--text2); font-weight:600; background:var(--bg3); border-right:1px solid var(--border); user-select:none;">
              <?= $i + 1 ?>
            </td>
            
            <!-- Mã GV/SV -->
            <td data-field="ma_gvsv">
              <input type="text" class="excel-input" value="<?= e($row['ma_gvsv'] ?: '') ?>" 
                     data-original="<?= e($row['ma_gvsv'] ?: '') ?>" placeholder="Trống">
            </td>
            
            <!-- Họ và tên -->
            <td data-field="ho_ten">
              <input type="text" class="excel-input" value="<?= e($row['ho_ten']) ?>" 
                     data-original="<?= e($row['ho_ten']) ?>" style="font-weight:600;" required>
            </td>
            
            <!-- Lớp -->
            <td data-field="lop">
              <input type="text" class="excel-input" value="<?= e($row['lop'] ?: '') ?>" 
                     data-original="<?= e($row['lop'] ?: '') ?>" placeholder="Trống">
            </td>
            
            <!-- Chi bộ công nhận -->
            <td data-field="chi_bo_cong_nhan">
              <input type="text" class="excel-input" value="<?= e($row['chi_bo_cong_nhan'] ?: '') ?>" 
                     data-original="<?= e($row['chi_bo_cong_nhan'] ?: '') ?>" placeholder="Trống">
            </td>
            
            <!-- SĐT -->
            <td data-field="sdt">
              <input type="text" class="excel-input" value="<?= e($row['sdt'] ?: '') ?>" 
                     data-original="<?= e($row['sdt'] ?: '') ?>" placeholder="Trống">
            </td>
            
            <!-- Giới tính -->
            <td data-field="gioi_tinh">
              <select class="excel-select" data-original="<?= e($row['gioi_tinh'] ?: '') ?>">
                <option value="" <?= ($row['gioi_tinh']??'')===''?'selected':'' ?>>--</option>
                <option value="Nam" <?= ($row['gioi_tinh']??'')==='Nam'?'selected':'' ?>>Nam</option>
                <option value="Nữ" <?= ($row['gioi_tinh']??'')==='Nữ'?'selected':'' ?>>Nữ</option>
                <option value="Khác" <?= ($row['gioi_tinh']??'')==='Khác'?'selected':'' ?>>Khác</option>
              </select>
            </td>
            
            <!-- Ngày sinh -->
            <td data-field="ngay_sinh">
              <input type="date" class="excel-input" value="<?= e($row['ngay_sinh'] ?: '') ?>" 
                     data-original="<?= e($row['ngay_sinh'] ?: '') ?>">
            </td>
            
            <!-- Trạng thái -->
            <td data-field="trang_thai">
              <select class="excel-select" data-original="<?= e($row['trang_thai']) ?>">
                <option value="Đang theo dõi" <?= $row['trang_thai']==='Đang theo dõi'?'selected':'' ?>>Đang theo dõi</option>
                <option value="Đã kết nạp" <?= $row['trang_thai']==='Đã kết nạp'?'selected':'' ?>>Đã kết nạp</option>
                <option value="Đã chuyển" <?= $row['trang_thai']==='Đã chuyển'?'selected':'' ?>>Đã chuyển</option>
                <option value="Tạm dừng" <?= $row['trang_thai']==='Tạm dừng'?'selected':'' ?>>Tạm dừng</option>
              </select>
            </td>
            
            <!-- Ngày cảm tình -->
            <td data-field="ngay_hop_cam_tinh">
              <input type="date" class="excel-input" value="<?= e($row['ngay_hop_cam_tinh'] ?: '') ?>" 
                     data-original="<?= e($row['ngay_hop_cam_tinh'] ?: '') ?>">
            </td>
            
            <!-- Ngày kết nạp -->
            <td data-field="ngay_ket_nap">
              <input type="date" class="excel-input" value="<?= e($row['ngay_ket_nap'] ?: '') ?>" 
                     data-original="<?= e($row['ngay_ket_nap'] ?: '') ?>">
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const table = document.getElementById('excelSheet');
  if (!table) return;

  // Ghi nhận giá trị ban đầu khi focus
  table.addEventListener('focusin', function (e) {
    const input = e.target;
    if (input.matches('.excel-input, .excel-select')) {
      if (input.dataset.original === undefined) {
        input.dataset.original = input.value.trim();
      }
    }
  });

  // Tự động lưu khi blur ra ngoài ô
  table.addEventListener('focusout', function (e) {
    const input = e.target;
    if (input.matches('.excel-input, .excel-select')) {
      saveField(input);
    }
  });

  // Tự động lưu khi chọn đối với select dropdown và date picker
  table.addEventListener('change', function (e) {
    const input = e.target;
    if (input.matches('.excel-select, input[type="date"]')) {
      saveField(input);
    }
  });

  // Hàm lưu dữ liệu trường qua API AJAX
  function saveField(input) {
    const td = input.closest('td');
    const field = td.dataset.field;
    const tr = td.closest('tr');
    const rowId = tr.dataset.rowId;
    const newValue = input.value.trim();
    
    const oldValue = input.dataset.original !== undefined ? input.dataset.original : input.getAttribute('value').trim();

    // Nếu không đổi gì thì bỏ qua
    if (newValue === oldValue) return;

    // Hiển thị trạng thái đang lưu
    td.className = 'saving';

    const formData = new FormData();
    formData.append('id', rowId);
    formData.append('field', field);
    formData.append('value', newValue);

    fetch('api_sua_nhanh.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      td.classList.remove('saving');
      if (data.success) {
        // Cập nhật giá trị baseline mới
        input.dataset.original = newValue;
        input.value = newValue;
        
        // Hiệu ứng nháy xanh lá cây thành công
        td.classList.add('save-success');
        setTimeout(() => td.classList.remove('save-success'), 1500);
        
        showToast('✓ Đã tự động lưu thay đổi.', 'var(--success)');
      } else {
        // Lỗi kiểm tra đầu vào hoặc DB
        restoreField(td, input, oldValue, data.error);
      }
    })
    .catch(err => {
      td.classList.remove('saving');
      restoreField(td, input, oldValue, 'Không thể kết nối đến máy chủ.');
    });
  }

  // Khôi phục giá trị cũ nếu lỗi xảy ra
  function restoreField(td, input, oldValue, errorMsg) {
    input.value = oldValue;
    input.dataset.original = oldValue;
    
    // Hiệu ứng nháy đỏ báo lỗi
    td.classList.add('save-error');
    setTimeout(() => td.classList.remove('save-error'), 1500);
    
    showToast('❌ Lỗi: ' + errorMsg, 'var(--danger)');
  }

  // Lắng nghe sự kiện bàn phím để di chuyển giống Excel
  table.addEventListener('keydown', function (e) {
    const input = e.target;
    if (!input.matches('.excel-input, .excel-select')) return;

    const td = input.closest('td');
    const tr = td.closest('tr');
    const tdIndex = Array.from(tr.children).indexOf(td);

    let targetInput = null;

    if (e.key === 'ArrowDown' || e.key === 'Enter') {
      // Enter hoặc Down arrow -> chuyển xuống ô dưới
      e.preventDefault();
      saveField(input); // lưu ô hiện tại trước
      
      const nextTr = tr.nextElementSibling;
      if (nextTr) {
        targetInput = nextTr.children[tdIndex]?.querySelector('.excel-input, .excel-select');
      }
    } else if (e.key === 'ArrowUp') {
      // Up arrow -> chuyển lên ô trên
      e.preventDefault();
      saveField(input);
      
      const prevTr = tr.previousElementSibling;
      if (prevTr) {
        targetInput = prevTr.children[tdIndex]?.querySelector('.excel-input, .excel-select');
      }
    } else if (e.key === 'ArrowRight' && (input.type !== 'text' || input.selectionStart === input.value.length)) {
      // Right arrow -> sang ô bên phải (khi con trỏ ở cuối văn bản hoặc không phải trường text)
      let nextTd = td.nextElementSibling;
      while (nextTd && !nextTd.querySelector('.excel-input, .excel-select')) {
        nextTd = nextTd.nextElementSibling;
      }
      if (nextTd) {
        targetInput = nextTd.querySelector('.excel-input, .excel-select');
      }
    } else if (e.key === 'ArrowLeft' && (input.type !== 'text' || input.selectionStart === 0)) {
      // Left arrow -> sang ô bên trái (khi con trỏ ở đầu văn bản hoặc không phải trường text)
      let prevTd = td.previousElementSibling;
      while (prevTd && !prevTd.querySelector('.excel-input, .excel-select')) {
        prevTd = prevTd.previousElementSibling;
      }
      if (prevTd) {
        targetInput = prevTd.querySelector('.excel-input, .excel-select');
      }
    } else if (e.key === 'Escape') {
      // Esc -> hủy thay đổi trên ô hiện tại và mờ ô
      e.preventDefault();
      const oldValue = input.dataset.original !== undefined ? input.dataset.original : input.getAttribute('value').trim();
      input.value = oldValue;
      input.blur();
    }

    if (targetInput) {
      targetInput.focus();
      if (targetInput.type === 'text') {
        targetInput.select(); // bôi đen văn bản để gõ đè
      }
    }
  });

  // Hiển thị thông báo Toast ở góc màn hình
  let toastTimeout;
  function showToast(msg, bgColor) {
    const toast = document.getElementById('toastMessage');
    toast.textContent = msg;
    toast.style.backgroundColor = bgColor;
    toast.style.display = 'block';
    
    clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => {
      toast.style.display = 'none';
    }, 3000);
  }
});
</script>

<?php require_once dirname(__DIR__) . '/Giao_dien/footer.php'; ?>
