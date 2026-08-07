<?php
// Quan_ly_doi_tuong/cap_nhat_thong_tin.php - Form đề xuất cập nhật thông tin chính thức dành cho quần chúng/sinh viên
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/User/auth.php';
requireLogin();

$db = getDB();
$user = getCurrentUser();
$errors = [];
$success = false;
$pageTitle = 'Đề xuất cập nhật thông tin';

// 1. Lấy thông tin đối tượng chính thức tương ứng với tài khoản đăng nhập
$stmt = $db->prepare("SELECT * FROM doi_tuong WHERE ma_gvsv = ? OR ho_ten = ? LIMIT 1");
$stmt->execute([$user['username'], $user['ho_ten']]);
$profile = $stmt->fetch();

if (!$profile) {
    setFlash('danger', 'Tài khoản của bạn chưa được phê duyệt vào danh sách quần chúng chính thức. Không thể gửi yêu cầu cập nhật!');
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$doiTuongId = $profile['id'];

// 2. Kiểm tra xem có yêu cầu cập nhật nào đang ở trạng thái 'Chờ duyệt' hay không
$stmtPending = $db->prepare("SELECT id FROM yeu_cau_cap_nhat WHERE doi_tuong_id = ? AND trang_thai = 'Chờ duyệt' LIMIT 1");
$stmtPending->execute([$doiTuongId]);
$hasPending = $stmtPending->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$hasPending) {
    $ho_ten    = $profile['ho_ten']; // Không cho phép đổi tên để tránh sai lệch tài khoản
    $sdt       = trim($_POST['sdt'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $gioi_tinh = trim($_POST['gioi_tinh'] ?? '');
    $ngay_sinh = trim($_POST['ngay_sinh'] ?? '');
    $dan_toc   = trim($_POST['dan_toc'] ?? '');
    $que_quan  = trim($_POST['que_quan'] ?? '');
    $chuc_vu   = trim($_POST['chuc_vu'] ?? '');
    $lop       = trim($_POST['lop'] ?? '');

    // Kiểm tra hợp lệ
    if (empty($sdt)) $errors[] = 'Vui lòng nhập Số điện thoại.';
    if (empty($email)) $errors[] = 'Vui lòng nhập Email.';
    if (empty($lop)) $errors[] = 'Vui lòng nhập Lớp học.';

    if (empty($errors)) {
        try {
            $sql = "INSERT INTO yeu_cau_cap_nhat (
                doi_tuong_id, ho_ten, sdt, email, gioi_tinh, ngay_sinh, dan_toc, que_quan, chuc_vu, lop, trang_thai
            ) VALUES (
                :doi_tuong_id, :ho_ten, :sdt, :email, :gioi_tinh, :ngay_sinh, :dan_toc, :que_quan, :chuc_vu, :lop, 'Chờ duyệt'
            )";
            $stmtInsert = $db->prepare($sql);
            $stmtInsert->execute([
                ':doi_tuong_id' => $doiTuongId,
                ':ho_ten'       => $ho_ten,
                ':sdt'          => $sdt,
                ':email'        => $email,
                ':gioi_tinh'    => $gioi_tinh,
                ':ngay_sinh'    => !empty($ngay_sinh) ? $ngay_sinh : null,
                ':dan_toc'      => $dan_toc,
                ':que_quan'     => $que_quan,
                ':chuc_vu'      => $chuc_vu,
                ':lop'          => $lop,
            ]);

            setFlash('success', 'Gửi yêu cầu cập nhật thông tin thành công! Vui lòng đợi quản lý phê duyệt.');
            header('Location: ' . BASE_URL . 'index.php');
            exit;
        } catch (Exception $e) {
            $errors[] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
    }
}

require_once dirname(__DIR__) . '/Giao_dien/header.php';
?>

<div class="page-header">
  <div class="page-header-left">
    <div class="breadcrumbs">
      <a href="<?= BASE_URL ?>index.php">Dashboard</a>
      <span class="sep">›</span>
      <span class="current">Cập nhật thông tin</span>
    </div>
    <div class="page-title">✏️ Đề xuất <span>cập nhật thông tin cá nhân</span></div>
    <div class="page-subtitle">Sửa đổi các thông tin liên hệ, lớp, chức vụ của bạn và gửi Ban quản lý Chi bộ xét duyệt.</div>
  </div>
</div>

<div class="container-narrow" style="max-width:800px; margin: 0 auto 40px;">
  
  <?php if ($hasPending): ?>
    <div class="flash flash-warning" style="margin-bottom: 24px;">
      ⏳ <strong>Thông báo:</strong> Bạn đã gửi một đề xuất cập nhật trước đó và đang ở trạng thái <strong>Chờ duyệt</strong>. Vui lòng chờ Ban quản lý chi bộ duyệt xong yêu cầu này trước khi gửi đề xuất tiếp theo.
      <br><a href="<?= BASE_URL ?>index.php" style="color:inherit; font-weight:bold; text-decoration:underline;">Quay lại Dashboard</a>
    </div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <div class="flash flash-danger">
      <ul>
        <?php foreach ($errors as $err): ?>
          <li><?= e($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="card fade-in">
    <div class="card-header">
      <div class="card-title">📝 Form đề xuất cập nhật thông tin</div>
    </div>
    <div class="card-body">
      <form method="post" action="cap_nhat_thong_tin.php">
        <div class="form-grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
          
          <!-- Họ tên (Readonly) -->
          <div class="form-group" style="grid-column: span 2;">
            <label class="form-label" style="font-weight:600;">Họ và tên (Chính thức)</label>
            <input type="text" class="form-control" style="background-color: var(--bg3); cursor: not-allowed;" value="<?= e($profile['ho_ten']) ?>" readonly>
            <small style="color:var(--text2); font-size:11px;">Họ tên chính thức không thể tự sửa đổi trực tuyến. Vui lòng liên hệ trực tiếp Quản lý nếu có sai sót chính tả.</small>
          </div>

          <!-- Số điện thoại -->
          <div class="form-group">
            <label class="form-label" style="font-weight:600;">Số điện thoại <span class="required" style="color:var(--red);">*</span></label>
            <input type="tel" name="sdt" class="form-control" value="<?= e($_POST['sdt'] ?? $profile['sdt']) ?>" required <?= $hasPending ? 'disabled' : '' ?>>
          </div>

          <!-- Email -->
          <div class="form-group">
            <label class="form-label" style="font-weight:600;">Email <span class="required" style="color:var(--red);">*</span></label>
            <input type="email" name="email" class="form-control" value="<?= e($_POST['email'] ?? $profile['email']) ?>" required <?= $hasPending ? 'disabled' : '' ?>>
          </div>

          <!-- Lớp -->
          <div class="form-group">
            <label class="form-label" style="font-weight:600;">Lớp <span class="required" style="color:var(--red);">*</span></label>
            <input type="text" name="lop" class="form-control" value="<?= e($_POST['lop'] ?? $profile['lop']) ?>" required <?= $hasPending ? 'disabled' : '' ?>>
          </div>

          <!-- Chức vụ -->
          <div class="form-group">
            <label class="form-label" style="font-weight:600;">Chức vụ</label>
            <input type="text" name="chuc_vu" class="form-control" value="<?= e($_POST['chuc_vu'] ?? $profile['chuc_vu']) ?>" <?= $hasPending ? 'disabled' : '' ?>>
          </div>

          <!-- Giới tính -->
          <div class="form-group">
            <label class="form-label" style="font-weight:600;">Giới tính</label>
            <select name="gioi_tinh" class="form-control" <?= $hasPending ? 'disabled' : '' ?>>
              <option value="Nam" <?= ($_POST['gioi_tinh'] ?? $profile['gioi_tinh']) === 'Nam' ? 'selected' : '' ?>>Nam</option>
              <option value="Nữ" <?= ($_POST['gioi_tinh'] ?? $profile['gioi_tinh']) === 'Nữ' ? 'selected' : '' ?>>Nữ</option>
            </select>
          </div>

          <!-- Ngày sinh -->
          <div class="form-group">
            <label class="form-label" style="font-weight:600;">Ngày sinh</label>
            <input type="date" name="ngay_sinh" class="form-control" value="<?= e($_POST['ngay_sinh'] ?? $profile['ngay_sinh']) ?>" <?= $hasPending ? 'disabled' : '' ?>>
          </div>

          <!-- Dân tộc -->
          <div class="form-group" style="grid-column: span 2;">
            <label class="form-label" style="font-weight:600;">Dân tộc</label>
            <input type="text" name="dan_toc" class="form-control" value="<?= e($_POST['dan_toc'] ?? $profile['dan_toc']) ?>" <?= $hasPending ? 'disabled' : '' ?>>
          </div>

          <!-- Quê quán -->
          <div class="form-group" style="grid-column: span 2;">
            <label class="form-label" style="font-weight:600;">Quê quán</label>
            <textarea name="que_quan" class="form-control" rows="3" <?= $hasPending ? 'disabled' : '' ?>><?= e($_POST['que_quan'] ?? $profile['que_quan']) ?></textarea>
          </div>

        </div>

        <div style="margin-top:24px; display:flex; gap:12px; justify-content:flex-end;">
          <a href="<?= BASE_URL ?>index.php" class="btn btn-outline">Hủy bỏ</a>
          <?php if (!$hasPending): ?>
            <button type="submit" class="btn btn-primary">🚀 Gửi đề xuất cập nhật</button>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
require_once dirname(__DIR__) . '/Giao_dien/header.php';
?>
