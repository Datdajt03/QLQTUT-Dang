<?php
// He_thong/cai_dat.php – Cài đặt hệ thống
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/User/auth.php';
requireRole('Admin');
$pageTitle = 'Cài đặt';
$db = getDB();

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keys = ['ten_truong','ten_dang_uy','nam_hoc','admin_email'];
    foreach ($keys as $k) {
        $val = trim($_POST[$k] ?? '');
        $db->prepare("UPDATE cai_dat SET gia_tri=? WHERE khoa=?")->execute([$val, $k]);
    }
    // Change password
    if (!empty($_POST['new_pass']) && !empty($_POST['confirm_pass'])) {
        if ($_POST['new_pass'] === $_POST['confirm_pass']) {
            $hash = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
            $db->prepare("UPDATE cai_dat SET gia_tri=? WHERE khoa='admin_pass'")->execute([$hash]);
            setFlash('success', 'Đã cập nhật cài đặt và mật khẩu!');
        } else {
            setFlash('danger', 'Mật khẩu mới không khớp!');
        }
    } else {
        setFlash('success', 'Đã lưu cài đặt hệ thống!');
    }
    redirect(BASE_URL . 'He_thong/cai_dat.php');
}

// Load settings
$settings = [];
$rows = $db->query("SELECT khoa, gia_tri FROM cai_dat")->fetchAll();
foreach ($rows as $r) $settings[$r['khoa']] = $r['gia_tri'];

// DB stats
$totalDT = $db->query("SELECT COUNT(*) FROM doi_tuong")->fetchColumn();
$totalCB = $db->query("SELECT COUNT(*) FROM chi_bo")->fetchColumn();
$totalDV = $db->query("SELECT COUNT(*) FROM dang_vien")->fetchColumn();
$totalLS = $db->query("SELECT COUNT(*) FROM lich_su")->fetchColumn();

require_once dirname(__DIR__) . '/Giao_dien/header.php';
?>

<div class="page-header">
  <div class="page-header-left">
    <div class="breadcrumb"><a href="<?= BASE_URL ?>index.php">Dashboard</a><span class="sep">›</span><span class="current">Cài đặt</span></div>
    <div class="page-title">Cài đặt <span>Hệ thống</span></div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1.5fr 1fr;gap:20px;">

  <!-- Settings Form -->
  <div class="card fade-in">
    <div class="card-header"><div class="card-title">Thông tin hệ thống</div></div>
    <div class="card-body">
      <form method="post">
        <div class="form-group" style="margin-bottom:16px;">
          <label class="form-label">Tên trường / Đơn vị</label>
          <input type="text" name="ten_truong" class="form-control" value="<?= e($settings['ten_truong'] ?? '') ?>">
        </div>
        <div class="form-group" style="margin-bottom:16px;">
          <label class="form-label">Tên Đảng uỷ</label>
          <input type="text" name="ten_dang_uy" class="form-control" value="<?= e($settings['ten_dang_uy'] ?? '') ?>">
        </div>
        <div class="form-group" style="margin-bottom:16px;">
          <label class="form-label">Năm học</label>
          <input type="text" name="nam_hoc" class="form-control" value="<?= e($settings['nam_hoc'] ?? '') ?>" placeholder="VD: 2024-2025">
        </div>
        <div class="form-group" style="margin-bottom:20px;">
          <label class="form-label">Email quản trị</label>
          <input type="email" name="admin_email" class="form-control" value="<?= e($settings['admin_email'] ?? '') ?>">
        </div>

        <div class="divider"></div>
        <div class="form-section-title" style="margin:16px 0 12px;">Đổi mật khẩu (để trống nếu không đổi)</div>
        <div class="form-group" style="margin-bottom:14px;">
          <label class="form-label">Mật khẩu mới</label>
          <input type="password" name="new_pass" class="form-control" placeholder="Nhập mật khẩu mới...">
        </div>
        <div class="form-group" style="margin-bottom:20px;">
          <label class="form-label">Xác nhận mật khẩu</label>
          <input type="password" name="confirm_pass" class="form-control" placeholder="Nhập lại mật khẩu...">
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;">Lưu cài đặt</button>
      </form>
    </div>
  </div>

  <!-- DB Stats & Info -->
  <div style="display:flex;flex-direction:column;gap:20px;">

    <div class="card fade-in">
      <div class="card-header"><div class="card-title">Thống kê Database</div></div>
      <div class="card-body">
        <?php
        $dbStats = [
          ['Đối tượng',  $totalDT, 'red'],
          ['Chi bộ',     $totalCB, 'blue'],
          ['Đảng viên',  $totalDV, 'gold'],
          ['Lịch sử',    $totalLS, 'green'],
        ];
        foreach ($dbStats as $s): ?>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border);">
          <span style="font-size:13px;"><?= $s[0] ?></span>
          <span class="badge badge-<?= $s[2] ?>"><?= number_format($s[1]) ?></span>
        </div>
        <?php endforeach; ?>
        <div style="margin-top:16px;">
          <div style="font-size:11px;color:var(--text2);margin-bottom:6px;">DATABASE</div>
          <code style="color:var(--gold);font-size:12px;"><?= DB_NAME ?></code>
          <div style="font-size:11px;color:var(--text2);margin-top:4px;"><?= DB_HOST ?> · <?= DB_USER ?></div>
        </div>
      </div>
    </div>

    <div class="card fade-in">
      <div class="card-header"><div class="card-title">Thông tin hệ thống</div></div>
      <div class="card-body">
        <?php
        $sysInfo = [
          'PHP Version'   => PHP_VERSION,
          'Server'        => $_SERVER['SERVER_SOFTWARE'] ?? 'Apache/XAMPP',
          'Charset'       => 'UTF-8',
          'Timezone'      => date_default_timezone_get(),
          'Ngày hệ thống' => date('d/m/Y H:i'),
        ];
        foreach ($sysInfo as $k => $v): ?>
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);font-size:12px;">
          <span style="color:var(--text2);"><?= $k ?></span>
          <span style="color:var(--gold);font-weight:600;"><?= e($v) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="card fade-in">
      <div class="card-header"><div class="card-title">Công cụ</div></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
        <a href="<?= BASE_URL ?>Thong_ke_bao_cao/xuat_excel.php" class="btn btn-gold" style="justify-content:center;">Xuất toàn bộ dữ liệu</a>
        <a href="<?= BASE_URL ?>Thong_ke_bao_cao/import_excel.php" class="btn btn-outline" style="justify-content:center;">Import Excel</a>
        <a href="<?= BASE_URL ?>setup.php" class="btn btn-outline" style="justify-content:center;">Xem Setup DB</a>
      </div>
    </div>
  </div>
</div>

<?php require_once dirname(__DIR__) . '/Giao_dien/footer.php'; ?>
