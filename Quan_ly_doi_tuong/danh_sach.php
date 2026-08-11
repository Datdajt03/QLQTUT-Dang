<?php
// Quan_ly_doi_tuong/danh_sach.php - Danh sách đối tượng
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/User/auth.php';
requireRole(['Quản lý', 'Admin']);
$pageTitle = 'Danh sách đối tượng';

$db = getDB();

// Params
$search     = trim($_GET['search'] ?? '');
$trangThai  = $_GET['trang_thai'] ?? '';
$lop        = $_GET['lop'] ?? '';
$perPage    = 15;
$page       = max(1, (int)($_GET['page'] ?? 1));
$offset     = ($page - 1) * $perPage;

// WHERE conditions
$where = ['1=1'];
$params = [];
if ($search) {
    $where[] = "(ho_ten LIKE ? OR ma_gvsv LIKE ? OR sdt LIKE ? OR lop LIKE ?)";
    $params = array_merge($params, ["%$search%","%$search%","%$search%","%$search%"]);
}
if ($trangThai) { $where[] = "trang_thai = ?"; $params[] = $trangThai; }
if ($lop)       { $where[] = "lop LIKE ?"; $params[] = "%$lop%"; }

$whereStr = implode(' AND ', $where);

$totalCount = $db->prepare("SELECT COUNT(*) FROM doi_tuong WHERE $whereStr");
$totalCount->execute($params);
$total = $totalCount->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));

$stmt = $db->prepare("SELECT * FROM doi_tuong WHERE $whereStr ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Distinct lớp for filter
$lopList = $db->query("SELECT DISTINCT lop FROM doi_tuong WHERE lop IS NOT NULL AND lop != '' ORDER BY lop")->fetchAll(PDO::FETCH_COLUMN);

require_once dirname(__DIR__) . '/Giao_dien/header.php';
?>

<!-- Page Header -->
<div class="page-header">
  <div class="page-header-left">
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>index.php">Dashboard</a>
      <span class="sep">›</span>
      <span class="current">Danh sách đối tượng</span>
    </div>
    <div class="page-title">📋 Danh sách <span>Đối tượng</span></div>
    <div class="page-subtitle">Tổng cộng <?= number_format($total) ?> đối tượng</div>
  </div>
  <div style="display:flex;gap:10px;">
    <button type="button" id="btnBatchDelete" class="btn btn-danger" style="display:none;" onclick="confirmBatchDelete()">
      🗑️ Xóa đối tượng đã chọn (<span id="selectedCount">0</span>)
    </button>
    <a href="<?= BASE_URL ?>Quan_ly_doi_tuong/them.php" class="btn btn-primary">➕ Thêm mới</a>
    <a href="<?= BASE_URL ?>Thong_ke_bao_cao/xuat_excel.php?<?= http_build_query(['search'=>$search,'trang_thai'=>$trangThai,'lop'=>$lop]) ?>" class="btn btn-gold">📤 Xuất Excel</a>
  </div>
</div>

<!-- Filter -->
<form method="get" class="filter-bar">
  <input type="text" name="search" class="form-control filter-search" placeholder="🔍 Tìm theo tên, mã, SĐT, lớp..." value="<?= e($search) ?>">
  <select name="trang_thai" class="form-control">
    <option value="">Tất cả trạng thái</option>
    <option value="Đang theo dõi" <?= $trangThai==='Đang theo dõi'?'selected':'' ?>>Đang theo dõi</option>
    <option value="Đã kết nạp"    <?= $trangThai==='Đã kết nạp'?'selected':'' ?>>Đã kết nạp</option>
    <option value="Đã chuyển"     <?= $trangThai==='Đã chuyển'?'selected':'' ?>>Đã chuyển</option>
    <option value="Tạm dừng"      <?= $trangThai==='Tạm dừng'?'selected':'' ?>>Tạm dừng</option>
  </select>
  <select name="lop" class="form-control">
    <option value="">Tất cả lớp</option>
    <?php foreach ($lopList as $l): ?>
    <option value="<?= e($l) ?>" <?= $lop===$l?'selected':'' ?>><?= e($l) ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit" class="btn btn-primary">Lọc</button>
  <a href="danh_sach.php" class="btn btn-outline">Reset</a>
</form>

<!-- Table -->
<form id="batchForm" action="xoa.php" method="post">
<div class="card fade-in">
  <div class="card-body" style="padding:0;">
    <?php if (empty($rows)): ?>
    <div class="empty-state">
      <div class="icon">📂</div>
      <h3>Không tìm thấy kết quả</h3>
      <p>Thử thay đổi bộ lọc hoặc thêm đối tượng mới</p>
    </div>
    <?php else: ?>
    <div class="table-wrapper">
      <table class="data-table">
        <thead>
          <tr>
            <th style="width:40px;text-align:center;">
              <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)" style="cursor:pointer;transform:scale(1.2);">
            </th>
            <th>STT</th>
            <th>Mã GV/SV</th>
            <th>Họ và tên</th>
            <th>SĐT</th>
            <th>Lớp</th>
            <th>Chi bộ</th>
            <th>CT Đảng</th>
            <th>Ngày kết nạp</th>
            <th>Trạng thái</th>
            <th style="text-align:center;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $i => $row): ?>
          <tr>
            <td style="text-align:center;">
              <input type="checkbox" name="ids[]" value="<?= $row['id'] ?>" class="item-checkbox" onclick="updateBatchButton()" style="cursor:pointer;transform:scale(1.1);">
            </td>
            <td style="color:var(--text2);"><?= $offset + $i + 1 ?></td>
            <td><code style="color:var(--gold);font-size:12px;"><?= e($row['ma_gvsv'] ?: '—') ?></code></td>
            <td>
              <div style="display:flex; align-items:center; gap:10px;">
                <?php if ($row['avatar']): ?>
                  <img src="<?= BASE_URL . e($row['avatar']) ?>" class="avatar-cell" alt="">
                <?php else: ?>
                  <?php 
                    $words = explode(' ', $row['ho_ten']);
                    $initials = (count($words) > 1) ? mb_substr($words[count($words)-2], 0, 1) . mb_substr($words[count($words)-1], 0, 1) : mb_substr($row['ho_ten'], 0, 1);
                    $initials = mb_strtoupper($initials);
                  ?>
                  <div class="avatar-cell-default"><?= e($initials) ?></div>
                <?php endif; ?>
                <div>
                  <a href="chi_tiet.php?id=<?= $row['id'] ?>" class="name-link">
                    <?= e($row['ho_ten']) ?>
                  </a>
                  <?php if ($row['gioi_tinh']): ?>
                  <span style="font-size:11px;color:var(--text2);">(<?= e($row['gioi_tinh']) ?>)</span>
                  <?php endif; ?>
                </div>
              </div>
            </td>
            <td><?= e($row['sdt'] ?: '—') ?></td>
            <td><?= e($row['lop'] ?: '—') ?></td>
            <td style="font-size:12px;color:var(--text2);"><?= e(mb_substr($row['chi_bo_cong_nhan'] ?? '', 0, 30)) ?></td>
            <td style="font-size:12px;"><?= $row['ngay_hop_cam_tinh'] ? '<span style="color:var(--success)">✓</span>' : '<span style="color:var(--text2)">—</span>' ?></td>
            <td><?= $row['ngay_ket_nap'] ? '<span style="color:var(--success)">' . formatDate($row['ngay_ket_nap']) . '</span>' : '<span style="color:var(--text2)">—</span>' ?></td>
            <td><?php
              $s = $row['trang_thai'];
              $cls = $s === 'Đã kết nạp' ? 'green' : ($s === 'Đang theo dõi' ? 'gold' : ($s === 'Đã chuyển' ? 'blue' : 'gray'));
              echo "<span class='badge badge-$cls'>$s</span>";
            ?></td>
            <td>
              <div style="display:flex;gap:5px;justify-content:center;">
                <a href="chi_tiet.php?id=<?= $row['id'] ?>" class="btn btn-outline btn-sm" title="Xem chi tiết">👁️</a>
                <a href="sua.php?id=<?= $row['id'] ?>" class="btn btn-outline btn-sm" title="Sửa">✏️</a>
                <button onclick="confirmDelete(<?= $row['id'] ?>, '<?= addslashes(e($row['ho_ten'])) ?>')" class="btn btn-danger btn-sm" title="Xóa">🗑️</button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div style="padding:16px 24px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);">
      <div style="font-size:12px;color:var(--text2);">
        Hiển thị <?= $offset+1 ?>–<?= min($offset+$perPage, $total) ?> / <?= $total ?> bản ghi
      </div>
      <div class="pagination">
        <?php if ($page > 1): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page'=>$page-1])) ?>" class="page-link">‹</a>
        <?php endif; ?>
        <?php for ($p = max(1,$page-2); $p <= min($totalPages,$page+2); $p++): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page'=>$p])) ?>" class="page-link <?= $p===$page?'active':'' ?>"><?= $p ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page'=>$page+1])) ?>" class="page-link">›</a>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
</form>

<!-- Delete Modal -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal">
    <div class="modal-title">🗑️ Xác nhận xóa</div>
    <div class="modal-body">
      Bạn có chắc muốn xóa <span id="deleteModalContent">đối tượng <strong id="deleteName"></strong></span>?
      <br>Hành động này <strong style="color:var(--danger)">không thể hoàn tác</strong>.
    </div>
    <div class="modal-actions">
      <button type="button" onclick="closeModal()" class="btn btn-outline">Hủy</button>
      <button type="button" id="confirmDeleteSubmitBtn" onclick="executeDelete()" class="btn btn-danger">🗑️ Xác nhận xóa</button>
    </div>
  </div>
</div>

<script>
var isBatchAction = false;
var singleDeleteUrl = '';

function toggleSelectAll(master) {
  var checkboxes = document.querySelectorAll('.item-checkbox');
  checkboxes.forEach(function(cb) {
    cb.checked = master.checked;
  });
  updateBatchButton();
}

function updateBatchButton() {
  var selected = document.querySelectorAll('.item-checkbox:checked');
  var btn = document.getElementById('btnBatchDelete');
  var countSpan = document.getElementById('selectedCount');
  
  if (selected.length > 0) {
    btn.style.display = 'inline-flex';
    countSpan.textContent = selected.length;
  } else {
    btn.style.display = 'none';
  }
}

function confirmDelete(id, name) {
  isBatchAction = false;
  singleDeleteUrl = 'xoa.php?id=' + id + '&ref=danh_sach';
  document.getElementById('deleteModalContent').innerHTML = 'đối tượng <strong>' + name + '</strong>';
  document.getElementById('deleteModal').classList.add('open');
}

function confirmBatchDelete() {
  var selected = document.querySelectorAll('.item-checkbox:checked');
  if (selected.length === 0) return;
  isBatchAction = true;
  document.getElementById('deleteModalContent').innerHTML = '<strong style="color:var(--danger)">' + selected.length + ' đối tượng</strong> đã chọn';
  document.getElementById('deleteModal').classList.add('open');
}

function executeDelete() {
  if (isBatchAction) {
    document.getElementById('batchForm').submit();
  } else {
    window.location.href = singleDeleteUrl;
  }
}

function closeModal() {
  document.getElementById('deleteModal').classList.remove('open');
}

document.getElementById('deleteModal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});
</script>

<?php require_once dirname(__DIR__) . '/Giao_dien/footer.php'; ?>
