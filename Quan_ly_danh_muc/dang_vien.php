<?php
// Quan_ly_danh_muc/dang_vien.php – Quản lý Đảng viên giúp đỡ
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/User/auth.php';
requireRole(['Quản lý', 'Admin']);
$pageTitle = 'Quản lý Đảng viên';
$db = getDB();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['action'] ?? '';
    if ($act === 'add' || $act === 'edit') {
        $ht   = trim($_POST['ho_ten'] ?? '');
        $ma   = trim($_POST['ma_dang_vien'] ?? '');
        $cv   = trim($_POST['chuc_vu'] ?? '');
        $cb   = (int)$_POST['chi_bo_id'];
        $sdt  = trim($_POST['sdt'] ?? '');
        $em   = trim($_POST['email'] ?? '');
        $ghi  = trim($_POST['ghi_chu'] ?? '');
        if (!$ht) $errors[] = 'Họ tên không được để trống';
        if (empty($errors)) {
            if ($act === 'add') {
                $db->prepare("INSERT INTO dang_vien (ho_ten,ma_dang_vien,chuc_vu,chi_bo_id,sdt,email,ghi_chu) VALUES (?,?,?,?,?,?,?)")
                   ->execute([$ht,$ma,$cv,$cb ?: null,$sdt,$em,$ghi]);
                setFlash('success', "Đã thêm đảng viên \"$ht\"");
            } else {
                $id = (int)$_POST['edit_id'];
                $db->prepare("UPDATE dang_vien SET ho_ten=?,ma_dang_vien=?,chuc_vu=?,chi_bo_id=?,sdt=?,email=?,ghi_chu=? WHERE id=?")
                   ->execute([$ht,$ma,$cv,$cb ?: null,$sdt,$em,$ghi,$id]);
                setFlash('success', "Đã cập nhật đảng viên \"$ht\"");
            }
            redirect(BASE_URL . 'Quan_ly_danh_muc/dang_vien.php');
        }
    } elseif ($act === 'delete') {
        $id = (int)$_POST['del_id'];
        $db->prepare("DELETE FROM dang_vien WHERE id=?")->execute([$id]);
        setFlash('success', 'Đã xóa đảng viên');
        redirect(BASE_URL . 'Quan_ly_danh_muc/dang_vien.php');
    }
}

$list = $db->query("
    SELECT dv.*, cb.ten_chi_bo,
           COUNT(dt.id) as so_giup_do
    FROM dang_vien dv
    LEFT JOIN chi_bo cb ON cb.id = dv.chi_bo_id
    LEFT JOIN doi_tuong dt ON dt.dang_vien_giup_do = dv.ho_ten
    GROUP BY dv.id ORDER BY dv.ho_ten
")->fetchAll();

$chiBos  = $db->query("SELECT * FROM chi_bo ORDER BY ten_chi_bo")->fetchAll();
$editId  = (int)($_GET['edit'] ?? 0);
$editRow = null;
if ($editId) {
    $q = $db->prepare("SELECT * FROM dang_vien WHERE id=?");
    $q->execute([$editId]);
    $editRow = $q->fetch();
}

require_once dirname(__DIR__) . '/Giao_dien/header.php';
?>

<div class="page-header">
  <div class="page-header-left">
    <div class="breadcrumb"><a href="<?= BASE_URL ?>index.php">Dashboard</a><span class="sep">›</span><span class="current">Quản lý Đảng viên</span></div>
    <div class="page-title">Quản lý <span>Đảng viên</span></div>
    <div class="page-subtitle">Danh sách đảng viên giúp đỡ & hướng dẫn quần chúng</div>
  </div>
</div>

<?php if ($errors): ?><div class="flash flash-danger"><?= implode('<br>', array_map('e',$errors)) ?></div><?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1.6fr;gap:20px;">

  <!-- Form -->
  <div class="card fade-in" style="align-self:start;">
    <div class="card-header">
      <div class="card-title"><?= $editRow ? 'Sửa thông tin' : 'Thêm đảng viên' ?></div>
    </div>
    <div class="card-body">
      <form method="post">
        <input type="hidden" name="action" value="<?= $editRow ? 'edit' : 'add' ?>">
        <?php if ($editRow): ?><input type="hidden" name="edit_id" value="<?= $editRow['id'] ?>"><?php endif; ?>
        <div class="form-group" style="margin-bottom:14px;">
          <label class="form-label">Họ và tên <span class="required">*</span></label>
          <input type="text" name="ho_ten" class="form-control" value="<?= e($editRow['ho_ten']??'') ?>" required>
        </div>
        <div class="form-group" style="margin-bottom:14px;">
          <label class="form-label">Mã Đảng viên</label>
          <input type="text" name="ma_dang_vien" class="form-control" value="<?= e($editRow['ma_dang_vien']??'') ?>">
        </div>
        <div class="form-group" style="margin-bottom:14px;">
          <label class="form-label">Chức vụ</label>
          <input type="text" name="chuc_vu" class="form-control" value="<?= e($editRow['chuc_vu']??'') ?>" placeholder="Bí thư, Đảng viên...">
        </div>
        <div class="form-group" style="margin-bottom:14px;">
          <label class="form-label">Chi bộ</label>
          <select name="chi_bo_id" class="form-control">
            <option value="">-- Chọn --</option>
            <?php foreach ($chiBos as $cb): ?>
            <option value="<?= $cb['id'] ?>" <?= ($editRow['chi_bo_id']??'')==$cb['id']?'selected':'' ?>><?= e($cb['ten_chi_bo']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group" style="margin-bottom:14px;">
          <label class="form-label">SĐT</label>
          <input type="tel" name="sdt" class="form-control" value="<?= e($editRow['sdt']??'') ?>">
        </div>
        <div class="form-group" style="margin-bottom:14px;">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?= e($editRow['email']??'') ?>">
        </div>
        <div class="form-group" style="margin-bottom:20px;">
          <label class="form-label">Ghi chú</label>
          <textarea name="ghi_chu" class="form-control" style="height:60px;"><?= e($editRow['ghi_chu']??'') ?></textarea>
        </div>
        <div style="display:flex;gap:10px;">
          <?php if ($editRow): ?><a href="dang_vien.php" class="btn btn-outline" style="flex:1;justify-content:center;">Hủy</a><?php endif; ?>
          <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">
            <?= $editRow ? 'Lưu' : 'Thêm' ?>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- List -->
  <div class="card fade-in">
    <div class="card-header">
      <div class="card-title">Danh sách Đảng viên</div>
      <span class="badge badge-red"><?= count($list) ?></span>
    </div>
    <div class="card-body" style="padding:0;">
      <table class="data-table">
        <thead><tr><th>Họ tên</th><th>Chức vụ</th><th>Chi bộ</th><th>SĐT</th><th>Giúp đỡ</th><th>Thao tác</th></tr></thead>
        <tbody>
          <?php foreach ($list as $dv): ?>
          <tr>
            <td><span style="font-weight:600;"><?= e($dv['ho_ten']) ?></span>
              <?php if ($dv['ma_dang_vien']): ?><br><code style="color:var(--gold);font-size:11px;"><?= e($dv['ma_dang_vien']) ?></code><?php endif; ?>
            </td>
            <td style="font-size:12px;"><?= e($dv['chuc_vu'] ?: '—') ?></td>
            <td style="font-size:12px;color:var(--text2);"><?= e(mb_substr($dv['ten_chi_bo'] ?? '—', 0, 25)) ?></td>
            <td style="font-size:12px;"><?= e($dv['sdt'] ?: '—') ?></td>
            <td><span class="badge badge-blue"><?= $dv['so_giup_do'] ?></span></td>
            <td>
              <div style="display:flex;gap:6px;">
                <a href="?edit=<?= $dv['id'] ?>" class="btn btn-outline btn-sm">Sửa</a>
                <form method="post" style="display:inline" onsubmit="return confirm('Xóa đảng viên này?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="del_id" value="<?= $dv['id'] ?>">
                  <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once dirname(__DIR__) . '/Giao_dien/footer.php'; ?>
