<?php
// includes/header.php - Shared header & sidebar (collapsible Tab phụ)
if (!defined('BASE_URL')) {
    require_once dirname(__DIR__) . '/config.php';
}
$flash = getFlash();

$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir  = basename(dirname($_SERVER['PHP_SELF']));

function isActive(string $file, string $dir = ''): string {
    global $currentPage, $currentDir;
    if ($dir && $currentDir !== $dir) return '';
    return ($currentPage === $file) ? 'active' : '';
}

// Auto-open Tab phu if on a Tabphu page
$tabPhuOpen = ($currentDir === 'Tabphu');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' – ' : '' ?><?= SITE_NAME ?></title>
  <meta name="description" content="Hệ thống quản lý quần chúng ưu tú phục vụ kết nạp Đảng">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/style.css">
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
      <span class="icon">🏠</span> Dashboard
    </a>

    <!-- Chức năng chính -->
    <div class="nav-section-title">Chức năng</div>
    <a href="<?= BASE_URL ?>Chucnang/danh_sach.php" class="nav-item <?= isActive('danh_sach.php','Chucnang') ?>">
      <span class="icon">📋</span> Danh sách đối tượng
    </a>
    <a href="<?= BASE_URL ?>Chucnang/them.php" class="nav-item <?= isActive('them.php','Chucnang') ?>">
      <span class="icon">➕</span> Thêm đối tượng
    </a>
    <a href="<?= BASE_URL ?>Chucnang/import_excel.php" class="nav-item <?= isActive('import_excel.php','Chucnang') ?>">
      <span class="icon">📥</span> Import Excel
    </a>
    <a href="<?= BASE_URL ?>Chucnang/xuat_excel.php" class="nav-item <?= isActive('xuat_excel.php','Chucnang') ?>">
      <span class="icon">📤</span> Xuất dữ liệu
    </a>

    <!-- Tab phụ (collapsible accordion) -->
    <div class="nav-section-title nav-accordion-toggle <?= $tabPhuOpen ? 'open' : '' ?>"
         id="tabphuToggle" onclick="toggleTabPhu()">
      <span>Tiện ích & Báo cáo</span>
      <span class="accordion-arrow"><?= $tabPhuOpen ? '▲' : '▼' ?></span>
    </div>
    <div class="nav-accordion-body <?= $tabPhuOpen ? 'open' : '' ?>" id="tabphuBody">
      <a href="<?= BASE_URL ?>Tabphu/thong_ke.php" class="nav-item nav-sub <?= isActive('thong_ke.php','Tabphu') ?>">
        <span class="icon">📊</span> Thống kê & Báo cáo
      </a>
      <a href="<?= BASE_URL ?>Tabphu/chi_bo.php" class="nav-item nav-sub <?= isActive('chi_bo.php','Tabphu') ?>">
        <span class="icon">🏛️</span> Quản lý Chi bộ
      </a>
      <a href="<?= BASE_URL ?>Tabphu/dang_vien.php" class="nav-item nav-sub <?= isActive('dang_vien.php','Tabphu') ?>">
        <span class="icon">👥</span> Quản lý Đảng viên
      </a>
      <a href="<?= BASE_URL ?>Tabphu/tim_kiem.php" class="nav-item nav-sub <?= isActive('tim_kiem.php','Tabphu') ?>">
        <span class="icon">🔍</span> Tìm kiếm nâng cao
      </a>
      <a href="<?= BASE_URL ?>Tabphu/cai_dat.php" class="nav-item nav-sub <?= isActive('cai_dat.php','Tabphu') ?>">
        <span class="icon">⚙️</span> Cài đặt
      </a>
    </div>
  </nav>

  <div class="sidebar-footer">
    <div>⭐ <strong>Đảng bộ</strong></div>
    <div style="margin-top:4px;font-size:10px;">v1.1 · <?= date('Y') ?></div>
  </div>
</aside>

<!-- ===== HEADER ===== -->
<header class="header">
  <button class="btn-icon" id="menuToggle" title="Menu" style="border:none;">☰</button>

  <div class="header-title">
    <span>⭐</span> <?= SITE_NAME ?>
  </div>

  <div class="header-search">
    <span style="color:var(--text2);font-size:16px;">🔍</span>
    <input type="text" placeholder="Tìm kiếm đối tượng..." id="globalSearch">
  </div>

  <div class="header-actions">
    <a href="<?= BASE_URL ?>Chucnang/them.php" class="btn btn-primary btn-sm">➕ Thêm mới</a>
    <a href="<?= BASE_URL ?>Chucnang/xuat_excel.php" class="btn-icon" title="Xuất dữ liệu">📤</a>
    <div class="user-badge">
      <div class="avatar">A</div>
      <span class="name">Admin</span>
    </div>
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
