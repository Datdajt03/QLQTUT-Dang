<?php
// nhap_thong_tin.php - Form đăng ký thông tin quần chúng ưu tú công khai dành cho sinh viên
require_once __DIR__ . '/config.php';

$db = getDB();
$errors = [];
$success = false;

// Tải danh sách chi bộ để sinh viên chọn
$chiBos = $db->query("SELECT ten_chi_bo FROM chi_bo ORDER BY ten_chi_bo")->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ma_gvsv          = trim($_POST['ma_gvsv'] ?? '');
    $ho_ten           = trim($_POST['ho_ten'] ?? '');
    $sdt             = trim($_POST['sdt'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $gioi_tinh        = trim($_POST['gioi_tinh'] ?? '');
    $ngay_sinh        = trim($_POST['ngay_sinh'] ?? '');
    $dan_toc          = trim($_POST['dan_toc'] ?? '');
    $que_quan         = trim($_POST['que_quan'] ?? '');
    $chuc_vu          = trim($_POST['chuc_vu'] ?? '');
    $lop              = trim($_POST['lop'] ?? '');
    $chi_bo_cong_nhan = trim($_POST['chi_bo_cong_nhan'] ?? '');
    $ghi_chu          = trim($_POST['ghi_chu'] ?? '');

    // Kiểm tra các trường bắt buộc
    if (empty($ho_ten)) {
        $errors[] = 'Vui lòng nhập Họ và tên.';
    }
    if (empty($ma_gvsv)) {
        $errors[] = 'Vui lòng nhập Mã sinh viên.';
    }
    if (empty($lop)) {
        $errors[] = 'Vui lòng nhập tên Lớp học.';
    }
    if (empty($email)) {
        $errors[] = 'Vui lòng nhập địa chỉ Email (Gmail) để nhận thông báo.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Địa chỉ Email không đúng định dạng.';
    }
    if (empty($sdt)) {
        $errors[] = 'Vui lòng nhập Số điện thoại liên hệ.';
    }

    if (empty($errors)) {
        try {
            $sql = "INSERT INTO dang_ky_doi_tuong (
                ma_gvsv, ho_ten, sdt, email, gioi_tinh, ngay_sinh, dan_toc, 
                que_quan, chuc_vu, lop, chi_bo_cong_nhan, ghi_chu, trang_thai
            ) VALUES (
                :ma_gvsv, :ho_ten, :sdt, :email, :gioi_tinh, :ngay_sinh, :dan_toc, 
                :que_quan, :chuc_vu, :lop, :chi_bo_cong_nhan, :ghi_chu, 'Chờ duyệt'
            )";

            // Chuẩn hóa ngày sinh
            $dbNgaySinh = !empty($ngay_sinh) ? $ngay_sinh : null;

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':ma_gvsv'          => $ma_gvsv,
                ':ho_ten'           => $ho_ten,
                ':sdt'             => $sdt,
                ':email'           => $email,
                ':gioi_tinh'        => !empty($gioi_tinh) ? $gioi_tinh : null,
                ':ngay_sinh'        => $dbNgaySinh,
                ':dan_toc'          => !empty($dan_toc) ? $dan_toc : null,
                ':que_quan'         => !empty($que_quan) ? $que_quan : null,
                ':chuc_vu'          => !empty($chuc_vu) ? $chuc_vu : null,
                ':lop'              => $lop,
                ':chi_bo_cong_nhan' => !empty($chi_bo_cong_nhan) ? $chi_bo_cong_nhan : null,
                ':ghi_chu'          => !empty($ghi_chu) ? $ghi_chu : null
            ]);

            $success = true;
        } catch (Exception $e) {
            $errors[] = 'Lỗi lưu trữ dữ liệu đăng ký: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng ký thông tin quần chúng ưu tú – <?= SITE_NAME ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/style.css">
  <style>
    body {
      background: var(--bg);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 40px 20px;
    }
    .form-container {
      max-width: 720px;
      width: 100%;
      background: var(--bg2);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow);
      overflow: hidden;
    }
    .form-header {
      background: linear-gradient(135deg, var(--red-dark), var(--bg2));
      padding: 30px;
      text-align: center;
      border-bottom: 1px solid var(--border);
      position: relative;
    }
    .form-header .star {
      font-size: 32px;
      margin-bottom: 8px;
    }
    .form-header h2 {
      font-family: 'Roboto Condensed', sans-serif;
      font-size: 24px;
      font-weight: 700;
      color: var(--text);
    }
    .form-header h2 span {
      color: var(--gold);
    }
    .form-header p {
      font-size: 13px;
      color: var(--text2);
      margin-top: 4px;
    }
    .form-body {
      padding: 30px;
    }
    .success-card {
      text-align: center;
      padding: 40px 20px;
    }
    .success-card .icon {
      font-size: 64px;
      margin-bottom: 16px;
      color: var(--success);
    }
    .success-card h3 {
      font-size: 20px;
      color: var(--text);
      margin-bottom: 12px;
    }
    .success-card p {
      color: var(--text2);
      max-width: 480px;
      margin: 0 auto 24px;
      line-height: 1.7;
    }
  </style>
</head>
<body>

<div class="form-container">
  <div class="form-header">
    <div class="star">⭐</div>
    <h2>ĐĂNG KÝ THÔNG TIN <span>QUẦN CHÚNG ƯU TÚ</span></h2>
    <p>Nhập thông tin cá nhân chính xác để đề xuất kết nạp Đảng</p>
  </div>

  <div class="form-body">
    <?php if ($success): ?>
      <div class="success-card fade-in">
        <div class="icon">✅</div>
        <h3>Gửi thông tin thành công!</h3>
        <p>Hồ sơ đăng ký của bạn đã được gửi tới Ban quản lý Chi bộ trường. Kết quả phê duyệt cùng thông tin phản hồi sẽ được gửi tới hòm thư Gmail <strong><?= e($email) ?></strong> của bạn sớm nhất.</p>
        <a href="nhap_thong_tin.php" class="btn btn-outline">🔄 Nhập hồ sơ khác</a>
      </div>
    <?php else: ?>
      
      <?php if (!empty($errors)): ?>
        <div class="flash flash-danger" style="margin-bottom: 24px;">
          <strong>❌ Có lỗi xảy ra:</strong>
          <ul style="margin-left: 20px; margin-top: 6px;">
            <?php foreach ($errors as $err): ?>
              <li><?= e($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="nhap_thong_tin.php">
        
        <!-- Section: Thông tin cá nhân -->
        <div class="form-section" style="border-left-color: var(--gold);">
          <div class="form-section-title" style="color: var(--gold);">👤 1. Thông tin sinh viên</div>
          <div class="form-grid">
            
            <!-- Họ tên -->
            <div class="form-group">
              <label class="form-label">Họ và tên <span class="required">*</span></label>
              <input type="text" name="ho_ten" class="form-control" placeholder="Nguyễn Văn A" value="<?= e($ho_ten ?? '') ?>" required>
            </div>
            
            <!-- Mã sinh viên -->
            <div class="form-group">
              <label class="form-label">Mã sinh viên <span class="required">*</span></label>
              <input type="text" name="ma_gvsv" class="form-control" placeholder="VD: SV24101" value="<?= e($ma_gvsv ?? '') ?>" required>
            </div>
            
            <!-- Email -->
            <div class="form-group">
              <label class="form-label">Địa chỉ Email / Gmail <span class="required">*</span></label>
              <input type="email" name="email" class="form-control" placeholder="sv_a@gmail.com" value="<?= e($email ?? '') ?>" required>
            </div>
            
            <!-- Số điện thoại -->
            <div class="form-group">
              <label class="form-label">Số điện thoại <span class="required">*</span></label>
              <input type="tel" name="sdt" class="form-control" placeholder="09xxxxxxxx" value="<?= e($sdt ?? '') ?>" required>
            </div>
            
            <!-- Lớp -->
            <div class="form-group">
              <label class="form-label">Lớp sinh hoạt <span class="required">*</span></label>
              <input type="text" name="lop" class="form-control" placeholder="VD: K63 ĐHSP Toán" value="<?= e($lop ?? '') ?>" required>
            </div>
            
            <!-- Chức vụ -->
            <div class="form-group">
              <label class="form-label">Chức vụ lớp / đoàn thể</label>
              <input type="text" name="chuc_vu" class="form-control" placeholder="Lớp trưởng, Bí thư chi đoàn..." value="<?= e($chuc_vu ?? '') ?>">
            </div>

            <!-- Giới tính -->
            <div class="form-group">
              <label class="form-label">Giới tính</label>
              <select name="gioi_tinh" class="form-control">
                <option value="">-- Chọn --</option>
                <option value="Nam" <?= ($gioi_tinh??'')==='Nam'?'selected':'' ?>>Nam</option>
                <option value="Nữ" <?= ($gioi_tinh??'')==='Nữ'?'selected':'' ?>>Nữ</option>
                <option value="Khác" <?= ($gioi_tinh??'')==='Khác'?'selected':'' ?>>Khác</option>
              </select>
            </div>
            
            <!-- Ngày sinh -->
            <div class="form-group">
              <label class="form-label">Ngày sinh</label>
              <input type="date" name="ngay_sinh" class="form-control" value="<?= e($ngay_sinh ?? '') ?>">
            </div>

            <!-- Dân tộc -->
            <div class="form-group">
              <label class="form-label">Dân tộc</label>
              <input type="text" name="dan_toc" class="form-control" placeholder="VD: Kinh, Mường..." value="<?= e($dan_toc ?? '') ?>">
            </div>

            <!-- Quê quán -->
            <div class="form-group form-full">
              <label class="form-label">Quê quán (địa chỉ chi tiết)</label>
              <input type="text" name="que_quan" class="form-control" placeholder="Xã/Phường, Quận/Huyện, Tỉnh/Thành phố" value="<?= e($que_quan ?? '') ?>">
            </div>
          </div>
        </div>

        <!-- Section: Mong muốn chi bộ -->
        <div class="form-section">
          <div class="form-section-title">🏛️ 2. Nguyện vọng Chi bộ đề xuất</div>
          <div class="form-grid">
            <div class="form-group form-full">
              <label class="form-label">Đề xuất Chi bộ công nhận cảm tình Đảng</label>
              <select name="chi_bo_cong_nhan" class="form-control">
                <option value="">-- Chọn Chi bộ (nếu biết) --</option>
                <?php foreach ($chiBos as $cb): ?>
                  <option value="<?= e($cb) ?>" <?= ($chi_bo_cong_nhan??'')===$cb?'selected':'' ?>><?= e($cb) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group form-full">
              <label class="form-label">Ghi chú hoặc thông tin bổ sung</label>
              <textarea name="ghi_chu" class="form-control" placeholder="Ghi nhận thành tích học tập nổi bật, danh hiệu hoặc nguyện vọng đặc biệt..." rows="3"><?= e($ghi_chu ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div style="margin-top: 24px;">
          <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; justify-content: center;">
            💾 Gửi hồ sơ đăng ký
          </button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
