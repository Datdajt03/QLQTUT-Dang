<?php
// Quan_ly_doi_tuong/thanh_vien_chi_bo.php - Trang hiển thị danh sách thành viên cùng lớp/chi bộ dành cho quần chúng/sinh viên
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/User/auth.php';
requireLogin();

$db = getDB();
$user = getCurrentUser();
$pageTitle = 'Thành viên cùng Lớp/Chi bộ';

// 1. Kiểm tra xem người dùng đã được duyệt chưa
$stmt = $db->prepare("SELECT * FROM doi_tuong WHERE ma_gvsv = ? OR ho_ten = ? LIMIT 1");
$stmt->execute([$user['username'], $user['ho_ten']]);
$myProfile = $stmt->fetch();

if (!$myProfile) {
    setFlash('danger', 'Tài khoản của bạn chưa được duyệt vào danh sách quần chúng chính thức!');
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

// 2. Tìm kiếm và lọc thành viên
$search = trim($_GET['search'] ?? '');
$sql = "SELECT id, ma_gvsv, ho_ten, lop, chi_bo_cong_nhan, chuc_vu, trang_thai, email 
        FROM doi_tuong 
        WHERE ((chi_bo_cong_nhan = :chi_bo AND chi_bo_cong_nhan != '') OR (lop = :lop AND lop != '')) AND id != :my_id";

if ($search !== '') {
    $sql .= " AND (ho_ten LIKE :search OR ma_gvsv LIKE :search OR lop LIKE :search OR chuc_vu LIKE :search)";
}

$sql .= " ORDER BY ho_ten ASC";

$stmtMembers = $db->prepare($sql);
$params = [
    ':chi_bo' => $myProfile['chi_bo_cong_nhan'],
    ':lop'    => $myProfile['lop'],
    ':my_id'  => $myProfile['id']
];

if ($search !== '') {
    $params[':search'] = "%$search%";
}

$stmtMembers->execute($params);
$members = $stmtMembers->fetchAll();

require_once dirname(__DIR__) . '/Giao_dien/header.php';
?>

<div class="page-header">
  <div class="page-header-left">
    <div class="breadcrumbs">
      <a href="<?= BASE_URL ?>index.php">Dashboard</a>
      <span class="sep">›</span>
      <span class="current">Thành viên cùng Lớp/Chi bộ</span>
    </div>
    <div class="page-title">🏛️ Thành viên cùng <span>Lớp hoặc Chi bộ</span></div>
    <div class="page-subtitle">Xem danh sách các quần chúng ưu tú và Đảng viên cùng sinh hoạt trong Chi bộ hoặc lớp học với bạn.</div>
  </div>
</div>

<!-- Lọc tìm kiếm -->
<form method="get" class="filter-bar" style="margin-bottom: 20px;">
  <input type="text" name="search" class="form-control filter-search" 
         placeholder="🔍 Tìm đồng chí theo tên, mã sinh viên, lớp..." value="<?= e($search) ?>">
  <button type="submit" class="btn btn-primary">Tìm kiếm</button>
  <?php if ($search !== ''): ?>
    <a href="thanh_vien_chi_bo.php" class="btn btn-outline">Reset</a>
  <?php endif; ?>
</form>

<div class="card fade-in">
  <div class="card-header">
    <div class="card-title">
      👥 Danh sách thành viên cùng Lớp (<strong><?= e($myProfile['lop'] ?: '—') ?></strong>) hoặc Chi bộ (<strong><?= e($myProfile['chi_bo_cong_nhan'] ?: '—') ?></strong>)
    </div>
  </div>
  <div class="card-body" style="padding:0;">
    <?php if (empty($members)): ?>
      <div class="empty-state" style="padding: 40px 20px;">
        <div class="icon">📂</div>
        <h3>Không tìm thấy thành viên nào</h3>
        <p>Không có kết quả nào phù hợp với tìm kiếm hoặc danh sách hiện tại đang trống.</p>
      </div>
    <?php else: ?>
      <div class="table-wrapper">
        <table class="data-table">
          <thead>
            <tr>
              <th>STT</th>
              <th>Mã SV/GV</th>
              <th>Họ và tên</th>
              <th>Lớp</th>
              <th>Chi bộ sinh hoạt</th>
              <th>Chức vụ</th>
              <th>Email liên hệ</th>
              <th>Trạng thái</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($members as $idx => $m): ?>
            <tr>
              <td><?= $idx + 1 ?></td>
              <td><code style="color:var(--gold);font-size:12px;"><?= e($m['ma_gvsv'] ?: '—') ?></code></td>
              <td><strong><?= e($m['ho_ten']) ?></strong></td>
              <td><?= e($m['lop'] ?: '—') ?></td>
              <td><?= e($m['chi_bo_cong_nhan'] ?: '—') ?></td>
              <td><?= e($m['chuc_vu'] ?: '—') ?></td>
              <td>
                <?php if ($m['email']): ?>
                  <a href="mailto:<?= e($m['email']) ?>" style="color:var(--gold); text-decoration:none;">✉️ <?= e($m['email']) ?></a>
                <?php else: ?>
                  —
                <?php endif; ?>
              </td>
              <td><?php
                $s = $m['trang_thai'];
                $cls = $s === 'Đã kết nạp' ? 'green' : ($s === 'Đang theo dõi' ? 'gold' : ($s === 'Đã chuyển' ? 'blue' : 'gray'));
                echo "<span class='badge badge-$cls'>$s</span>";
              ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php
require_once dirname(__DIR__) . '/Giao_dien/footer.php';
?>
