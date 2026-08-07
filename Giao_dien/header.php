<?php
// Giao_dien/header.php - Shared header & sidebar (collapsible Tab phụ)
if (!defined('BASE_URL')) {
    require_once dirname(__DIR__) . '/config.php';
}
require_once dirname(__DIR__) . '/User/auth.php';

// Kiểm tra và bắt buộc đăng nhập
requireLogin();

$flash = getFlash();
$currentUser = getCurrentUser();
$vaiTro = $currentUser['vai_tro'] ?? 'Người dùng thường';

$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir  = basename(dirname($_SERVER['PHP_SELF']));

function isActive(string $file, string $dir = ''): string {
    global $currentPage, $currentDir;
    if ($dir && $currentDir !== $dir) return '';
    return ($currentPage === $file) ? 'active' : '';
}

// Tự động mở menu báo cáo nếu đang trong thư mục tương ứng HOẶC là Quản lý/Admin (mặc định mở)
$tabPhuOpen = in_array($currentDir, ['Quan_ly_danh_muc', 'Thong_ke_bao_cao', 'He_thong'])
              || ($vaiTro !== 'Người dùng thường');

$dbForHeader = getDB();
$pendingCount = 0;
try {
    if ($vaiTro !== 'Người dùng thường') {
        $pendingCount = (int)$dbForHeader->query("SELECT COUNT(*) FROM dang_ky_doi_tuong WHERE trang_thai='Chờ duyệt'")->fetchColumn();
    }
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' – ' : '' ?><?= SITE_NAME ?></title>
  <meta name="description" content="Hệ thống quản lý quần chúng ưu tú phục vụ kết nạp Đảng">
  <link rel="stylesheet" href="<?= BASE_URL ?>Giao_dien/assets/style.css">
</head>
<body>
<div class="layout" id="mainLayout">
<script>
  try {
    if (localStorage.getItem('sidebarCollapsed') === 'true' && window.innerWidth > 768) {
      document.getElementById('mainLayout').classList.add('collapsed');
    }
  } catch(e){}
</script>

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon">⭐</div>
    <div class="logo-text">
      Kết nạp Đảng
      <span>Quản lý Quần chúng ƯT</span>
    </div>
  </div>

  <nav class="sidebar-nav">
    <!-- Tổng quan -->
    <div class="nav-section-title">Tổng quan</div>
    <a href="<?= BASE_URL ?>index.php" class="nav-item <?= isActive('index.php') ?>">
      <span class="icon">🏠</span> <span>Dashboard</span>
    </a>

    <?php if ($vaiTro === 'Người dùng thường'): ?>
      <?php
      $dbHeader = getDB();
      $stmtH = $dbHeader->prepare("SELECT COUNT(*) FROM doi_tuong WHERE ma_gvsv = ? OR ho_ten = ?");
      $stmtH->execute([$user['username'], $user['ho_ten']]);
      $isApprovedUser = ($stmtH->fetchColumn() > 0);
      ?>
      <!-- Chức năng sinh viên/người dùng thường -->
      <div class="nav-section-title">Hồ sơ cá nhân</div>
      <?php if ($isApprovedUser): ?>
        <a href="<?= BASE_URL ?>Quan_ly_doi_tuong/thanh_vien_chi_bo.php" class="nav-item <?= isActive('thanh_vien_chi_bo.php','Quan_ly_doi_tuong') ?>">
          <span class="icon">🏛️</span> <span>Thành viên cùng Lớp</span>
        </a>
        <a href="<?= BASE_URL ?>Quan_ly_doi_tuong/cap_nhat_thong_tin.php" class="nav-item <?= isActive('cap_nhat_thong_tin.php','Quan_ly_doi_tuong') ?>">
          <span class="icon">✏️</span> <span>Cập nhật thông tin</span>
        </a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>Quan_ly_doi_tuong/nhap_thong_tin.php" class="nav-item <?= isActive('nhap_thong_tin.php','Quan_ly_doi_tuong') ?>">
          <span class="icon">✍️</span> <span>Gửi hồ sơ đăng ký</span>
        </a>
      <?php endif; ?>
    <?php else: ?>
      <!-- Chức năng quản lý/admin -->
      <div class="nav-section-title">Nghiệp vụ</div>
      <a href="<?= BASE_URL ?>Quan_ly_doi_tuong/danh_sach.php" class="nav-item <?= isActive('danh_sach.php','Quan_ly_doi_tuong') ?>">
        <span class="icon">📋</span> <span>Danh sách đối tượng</span>
      </a>
      <a href="<?= BASE_URL ?>Quan_ly_doi_tuong/them.php" class="nav-item <?= isActive('them.php','Quan_ly_doi_tuong') ?>">
        <span class="icon">➕</span> <span>Thêm đối tượng</span>
      </a>
      <a href="<?= BASE_URL ?>Quan_ly_doi_tuong/sua_nhanh.php" class="nav-item <?= isActive('sua_nhanh.php','Quan_ly_doi_tuong') ?>">
        <span class="icon">✏️</span> <span>Sửa Excel trực tiếp</span>
      </a>
      <a href="<?= BASE_URL ?>Quan_ly_doi_tuong/duyet_dang_ky.php" class="nav-item <?= isActive('duyet_dang_ky.php','Quan_ly_doi_tuong') ?>">
        <span class="icon">🔔</span> <span>Duyệt thông tin</span>
        <?php if ($pendingCount > 0): ?>
          <span class="badge"><?= $pendingCount ?></span>
        <?php endif; ?>
      </a>

      <!-- Tiện ích & Báo cáo (Dành cho Quản lý & Admin) -->
      <div class="nav-section-title nav-accordion-toggle <?= $tabPhuOpen ? 'open' : '' ?>"
           id="tabphuToggle" onclick="toggleTabPhu()">
        <span>Tiện ích & Báo cáo</span>
        <span class="accordion-arrow"><?= $tabPhuOpen ? '▲' : '▼' ?></span>
      </div>
      <div class="nav-accordion-body <?= $tabPhuOpen ? 'open' : '' ?>" id="tabphuBody">
        <a href="<?= BASE_URL ?>Thong_ke_bao_cao/thong_ke.php" class="nav-item nav-sub <?= isActive('thong_ke.php','Thong_ke_bao_cao') ?>">
          <span class="icon">📊</span> <span>Thống kê & Báo cáo</span>
        </a>
        <a href="<?= BASE_URL ?>Quan_ly_danh_muc/chi_bo.php" class="nav-item nav-sub <?= isActive('chi_bo.php','Quan_ly_danh_muc') ?>">
          <span class="icon">🏛️</span> <span>Quản lý Chi bộ</span>
        </a>
        <a href="<?= BASE_URL ?>Quan_ly_danh_muc/dang_vien.php" class="nav-item nav-sub <?= isActive('dang_vien.php','Quan_ly_danh_muc') ?>">
          <span class="icon">👥</span> <span>Quản lý Đảng viên</span>
        </a>
        <a href="<?= BASE_URL ?>Thong_ke_bao_cao/tim_kiem.php" class="nav-item nav-sub <?= isActive('tim_kiem.php','Thong_ke_bao_cao') ?>">
          <span class="icon">🔍</span> <span>Tìm kiếm nâng cao</span>
        </a>
        <a href="<?= BASE_URL ?>Thong_ke_bao_cao/import_excel.php" class="nav-item nav-sub <?= isActive('import_excel.php','Thong_ke_bao_cao') ?>">
          <span class="icon">📥</span> <span>Import Excel</span>
        </a>
        <a href="<?= BASE_URL ?>Thong_ke_bao_cao/xuat_excel.php" class="nav-item nav-sub <?= isActive('xuat_excel.php','Thong_ke_bao_cao') ?>">
          <span class="icon">📤</span> <span>Xuất Excel</span>
        </a>
        <?php if ($vaiTro === 'Admin'): ?>
        <a href="<?= BASE_URL ?>He_thong/cai_dat.php" class="nav-item nav-sub <?= isActive('cai_dat.php','He_thong') ?>">
          <span class="icon">⚙️</span> <span>Cài đặt hệ thống</span>
        </a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </nav>

  <div class="sidebar-footer">
    <div>⭐ <strong>Đảng bộ</strong></div>
    <div style="margin-top:4px;font-size:10px;">v1.2 · <?= date('Y') ?></div>
  </div>
</aside>

<!-- ===== HEADER ===== -->
<header class="header">
  <button class="btn-icon" id="menuToggle" title="Menu" style="border:none;">☰</button>

  <div class="header-title">
    <span>⭐</span> <?= SITE_NAME ?>
  </div>

  <div class="header-search">
    <?php if ($vaiTro !== 'Người dùng thường'): ?>
    <span style="color:var(--text2);font-size:16px;">🔍</span>
    <input type="text" placeholder="Tìm kiếm đối tượng..." id="globalSearch">
    <?php endif; ?>
  </div>

  <div class="header-actions">
    <?php if ($vaiTro !== 'Người dùng thường'): ?>
      <a href="<?= BASE_URL ?>Quan_ly_doi_tuong/them.php" class="btn btn-primary btn-sm">➕ Thêm mới</a>
      <a href="<?= BASE_URL ?>Thong_ke_bao_cao/xuat_excel.php" class="btn-icon" title="Xuất dữ liệu">📤</a>
    <?php endif; ?>
    <a href="<?= BASE_URL ?>User/logout.php" class="user-badge" title="Nhấp vào để Đăng xuất" style="text-decoration:none; display:flex; align-items:center;">
      <div class="avatar" style="background:var(--red); color:#fff; font-weight:bold;"><?= mb_substr($currentUser['ho_ten'] ?: $currentUser['username'], 0, 1) ?></div>
      <span class="name" style="margin-right:4px;"><?= e($currentUser['ho_ten'] ?: $currentUser['username']) ?></span>
      <span style="font-size:11px;color:var(--text2); opacity:0.8;">(Thoát)</span>
    </a>
  </div>
</header>

<!-- ===== MAIN ===== -->
<main class="main">

<?php if ($flash): ?>
<div class="flash flash-<?= e($flash['type']) ?> fade-in">
  <?= $flash['type'] === 'success' ? '✅' : ($flash['type'] === 'danger' ? '❌' : 'ℹ️') ?>
  <?= e($flash['msg']) ?>
</div>
<?php endif; ?>
