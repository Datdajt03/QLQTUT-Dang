<?php
// Quan_ly_danh_muc/chi_bo.php – Quản lý Chi bộ
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/User/auth.php';
requireRole(['Quản lý', 'Admin']);
$pageTitle = 'Quản lý Chi bộ';
$db = getDB();
$errors = [];

// Handle POST (add/edit/delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['action'] ?? '';
    if ($act === 'add' || $act === 'edit') {
        $ten  = trim($_POST['ten_chi_bo'] ?? '');
        $ma   = trim($_POST['ma_chi_bo'] ?? '');
        $dang = trim($_POST['dang_uy'] ?? '');
        $tinh = trim($_POST['tinh_uy'] ?? '');
        $ghi  = trim($_POST['ghi_chu'] ?? '');
        if (!$ten) $errors[] = 'Tên chi bộ không được để trống';
        if (empty($errors)) {
            if ($act === 'add') {
                $db->prepare("INSERT INTO chi_bo (ten_chi_bo,ma_chi_bo,dang_uy,tinh_uy,ghi_chu) VALUES (?,?,?,?,?)")
                   ->execute([$ten,$ma,$dang,$tinh,$ghi]);
                setFlash('success', "Đã thêm chi bộ \"$ten\"");
            } else {
                $id = (int)$_POST['edit_id'];
                $db->prepare("UPDATE chi_bo SET ten_chi_bo=?,ma_chi_bo=?,dang_uy=?,tinh_uy=?,ghi_chu=? WHERE id=?")
                   ->execute([$ten,$ma,$dang,$tinh,$ghi,$id]);
                setFlash('success', "Đã cập nhật chi bộ \"$ten\"");
            }
            redirect(BASE_URL . 'Quan_ly_danh_muc/chi_bo.php');
        }
    } elseif ($act === 'delete') {
        $id = (int)$_POST['del_id'];
        $db->prepare("DELETE FROM chi_bo WHERE id=?")->execute([$id]);
        setFlash('success', 'Đã xóa chi bộ');
        redirect(BASE_URL . 'Quan_ly_danh_muc/chi_bo.php');
    }
}

$list  = $db->query("SELECT cb.*, COUNT(dt.id) as so_doi_tuong FROM chi_bo cb LEFT JOIN doi_tuong dt ON dt.chi_bo_cong_nhan = cb.ten_chi_bo GROUP BY cb.id ORDER BY cb.ten_chi_bo")->fetchAll();
$editId = (int)($_GET['edit'] ?? 0);
$editRow = $editId ? $db->prepare("SELECT * FROM chi_bo WHERE id=?") : null;
if ($editRow) { $editRow->execute([$editId]); $editRow = $editRow->fetch(); }

require_once dirname(__DIR__) . '/Giao_dien/header.php';
?>

<div class="page-header">
  <div class="page-header-left">
    <div class="breadcrumb"><a href="<?= BASE_URL ?>index.php">Dashboard</a><span class="sep">›</span><span class="current">Quản lý Chi bộ</span></div>
    <div class="page-title">Quản lý <span>Chi bộ</span></div>
  </div>
</div>

<?php if ($errors): ?><div class="flash flash-danger"><?= implode('<br>', array_map('e',$errors)) ?></div><?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1.6fr;gap:20px;">

  <!-- Form -->
  <div class="card fade-in" style="align-self:start;">
    <div class="card-header">
      <div class="card-title"><?= $editRow ? 'Sửa chi bộ' : 'Thêm chi bộ mới' ?></div>
    </div>
    <div class="card-body">
      <form method="post">
        <input type="hidden" name="action" value="<?= $editRow ? 'edit' : 'add' ?>">
        <?php if ($editRow): ?><input type="hidden" name="edit_id" value="<?= $editRow['id'] ?>"><?php endif; ?>
        <div class="form-group" style="margin-bottom:16px;">
          <label class="form-label">Tên Chi bộ <span class="required">*</span></label>
          <input type="text" name="ten_chi_bo" class="form-control" value="<?= e($editRow['ten_chi_bo'] ?? '') ?>" placeholder="VD: Chi bộ Khoa CNTT" required>
        </div>
        <div class="form-group" style="margin-bottom:16px;">
          <label class="form-label">Mã Chi bộ</label>
          <input type="text" name="ma_chi_bo" class="form-control" value="<?= e($editRow['ma_chi_bo'] ?? '') ?>" placeholder="VD: CNTT">
        </div>
        <div class="form-group" style="margin-bottom:16px;">
          <label class="form-label">Đảng uỷ</label>
          <input type="text" name="dang_uy" class="form-control" value="<?= e($editRow['dang_uy'] ?? '') ?>">
        </div>
        <div class="form-group" style="margin-bottom:16px;">
          <label class="form-label">Tỉnh uỷ</label>
          <input type="text" name="tinh_uy" class="form-control" value="<?= e($editRow['tinh_uy'] ?? '') ?>">
        </div>
        <div class="form-group" style="margin-bottom:20px;">
          <label class="form-label">Ghi chú</label>
          <textarea name="ghi_chu" class="form-control" style="height:70px;"><?= e($editRow['ghi_chu'] ?? '') ?></textarea>
        </div>
        <div style="display:flex;gap:10px;">
          <?php if ($editRow): ?>
          <a href="chi_bo.php" class="btn btn-outline" style="flex:1;justify-content:center;">Hủy</a>
          <?php endif; ?>
          <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">
            <?= $editRow ? 'Lưu thay đổi' : 'Thêm mới' ?>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- List -->
  <div class="card fade-in">
    <div class="card-header">
      <div class="card-title">Danh sách Chi bộ</div>
      <span class="badge badge-red"><?= count($list) ?></span>
    </div>
    <div class="card-body" style="padding:0;">
      <table class="data-table">
        <thead><tr><th>Tên Chi bộ</th><th>Mã</th><th>Đảng uỷ</th><th>Đối tượng</th><th>Thao tác</th></tr></thead>
        <tbody>
          <?php foreach ($list as $cb): ?>
          <tr>
            <td style="font-weight:600;"><?= e($cb['ten_chi_bo']) ?></td>
            <td><code style="color:var(--gold);"><?= e($cb['ma_chi_bo'] ?: '—') ?></code></td>
            <td style="font-size:12px;color:var(--text2);"><?= e(mb_substr($cb['dang_uy'] ?? '—', 0, 30)) ?></td>
            <td><span class="badge badge-blue"><?= $cb['so_doi_tuong'] ?></span></td>
            <td>
              <div style="display:flex;gap:6px;">
                <a href="?edit=<?= $cb['id'] ?>" class="btn btn-outline btn-sm">Sửa</a>
                <form method="post" style="display:inline" onsubmit="return confirm('Xóa chi bộ này?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="del_id" value="<?= $cb['id'] ?>">
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
