<?php
// nhap_thong_tin.php - Form đăng ký thông tin quần chúng ưu tú công khai dành cho sinh viên
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/User/auth.php';
requireLogin();

$db = getDB();
$errors = [];
$success = false;

$user = getCurrentUser();
$ho_ten = $user['ho_ten'] ?? '';
$ma_gvsv = $user['username'] ?? '';
$email = '';
$sdt = '';
$gioi_tinh = '';
$ngay_sinh = '';
$dan_toc = '';
$que_quan = '';
$chuc_vu = '';
$lop = '';
$chi_bo_cong_nhan = '';

// Tải danh sách chi bộ để sinh viên chọn
$chiBos = $db->query("SELECT ten_chi_bo FROM chi_bo ORDER BY ten_chi_bo")->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $ma_gvsv = trim($_POST['ma_gvsv'] ?? '');
  $ho_ten = trim($_POST['ho_ten'] ?? '');
  $sdt = trim($_POST['sdt'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $gioi_tinh = trim($_POST['gioi_tinh'] ?? '');
  $ngay_sinh = trim($_POST['ngay_sinh'] ?? '');
  $dan_toc = trim($_POST['dan_toc'] ?? '');
  $que_quan = trim($_POST['que_quan'] ?? '');
  $chuc_vu = trim($_POST['chuc_vu'] ?? '');
  $lop = trim($_POST['lop'] ?? '');
  $chi_bo_cong_nhan = trim($_POST['chi_bo_cong_nhan'] ?? '');
  $ghi_chu = trim($_POST['ghi_chu'] ?? '');

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
        ':ma_gvsv' => $ma_gvsv,
        ':ho_ten' => $ho_ten,
        ':sdt' => $sdt,
        ':email' => $email,
        ':gioi_tinh' => !empty($gioi_tinh) ? $gioi_tinh : null,
        ':ngay_sinh' => $dbNgaySinh,
        ':dan_toc' => !empty($dan_toc) ? $dan_toc : null,
        ':que_quan' => !empty($que_quan) ? $que_quan : null,
        ':chuc_vu' => !empty($chuc_vu) ? $chuc_vu : null,
        ':lop' => $lop,
        ':chi_bo_cong_nhan' => !empty($chi_bo_cong_nhan) ? $chi_bo_cong_nhan : null,
        ':ghi_chu' => !empty($ghi_chu) ? $ghi_chu : null
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
      <div class="star"><i class="bi bi-star-fill" style="color:var(--gold);"></i></div>
      <h2>ĐĂNG KÝ THÔNG TIN <span>QUẦN CHÚNG ƯU TÚ</span></h2>
      <p>Nhập thông tin cá nhân chính xác để đề xuất kết nạp Đảng</p>
    </div>

    <div class="form-body">
      <?php if ($success): ?>
        <div class="success-card fade-in">
          <div class="icon"><i class="bi bi-check-circle-fill" style="color:var(--success);font-size:64px;"></i></div>
          <h3>Gửi thông tin thành công!</h3>
          <p>Hồ sơ đăng ký của bạn đã được gửi tới Ban quản lý Chi bộ trường. Kết quả phê duyệt cùng thông tin phản hồi sẽ
            được gửi tới hòm thư Gmail <strong><?= e($email) ?></strong> của bạn sớm nhất.</p>
          <a href="nhap_thong_tin.php" class="btn btn-outline"><i class="bi bi-arrow-clockwise"></i> Nhập hồ sơ khác</a>
        </div>
      <?php else: ?>

        <?php if (!empty($errors)): ?>
          <div class="flash flash-danger" style="margin-bottom: 24px;">
            <strong><i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i> Có lỗi xảy ra:</strong>
            <ul style="margin-left: 20px; margin-top: 6px;">
              <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <!-- Edge AI Widget: AI_Module/edge_ai_autofill.js -->
        <div style="background:linear-gradient(135deg, rgba(200,16,46,0.1), rgba(255,215,0,0.1));border:1px dashed var(--gold);border-radius:12px;padding:20px;margin-bottom:24px;">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
            <span style="font-size:24px;color:var(--gold);"><i class="bi bi-cpu-fill"></i></span>
            <div>
              <h4 style="margin:0;font-size:15px;color:var(--gold);">TRỢ LÝ EDGE AI (`AI_Module`): TỰ ĐỘNG ĐIỀN FORM & CẮT ẢNH THẺ 3x4</h4>
              <p style="margin:2px 0 0 0;font-size:11.5px;color:var(--text2);">Tải <strong>CCCD (mặt trước & sau) + Thẻ sinh viên</strong> để AI tự động điền form, hoặc tải <strong>Ảnh thẻ</strong> để AI căn chỉnh 3x4.</p>
            </div>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-top:14px;">
            <!-- Card 1: CCCD 2 mặt + Thẻ Sinh Viên -->
            <div style="background:var(--bg2);border:1px solid var(--border);padding:14px;border-radius:8px;">
              <label style="font-size:12px;font-weight:700;display:block;margin-bottom:6px;color:var(--text);"><i class="bi bi-person-vcard-fill" style="margin-right:4px;"></i> Upload CCCD (2 Mặt) + Thẻ Sinh Viên</label>
              <input type="file" id="aiDocInput" multiple accept="image/*,application/pdf" class="form-control" style="font-size:11px;padding:6px;">
              <div style="display:flex;gap:6px;margin-top:8px;">
                <button type="button" onclick="triggerEdgeAIOCR()" id="btnAiScan" class="btn btn-gold btn-sm" style="flex:1;justify-content:center;"><i class="bi bi-lightning-charge-fill" style="margin-right:4px;"></i> Quét OCR Tự Động</button>
                <button type="button" onclick="openLiveCameraForAutoFill()" class="btn btn-primary btn-sm" style="justify-content:center;" title="Quét trực tiếp qua Camera WebRTC"><i class="bi bi-camera-video-fill"></i> Camera</button>
              </div>
              <button type="button" id="btnViewXaiAutoFill" onclick="openXaiForAutoFill()" class="btn btn-outline btn-sm" style="display:none;margin-top:8px;width:100%;justify-content:center;color:#38bdf8;border-color:#38bdf8;"><i class="bi bi-bullseye"></i> Bản Đồ Độ Tin Cậy XAI</button>
              <div id="aiOcrStatus" style="font-size:11px;margin-top:6px;color:var(--gold);font-weight:600;"></div>
            </div>

            <!-- Card 2: Smart Avatar Crop 3x4 -->
            <div style="background:var(--bg2);border:1px solid var(--border);padding:14px;border-radius:8px;">
              <label style="font-size:12px;font-weight:700;display:block;margin-bottom:6px;color:var(--text);"><i class="bi bi-camera-fill" style="margin-right:4px;"></i> Upload Ảnh Chân Dung (Smart Crop 3x4)</label>
              <input type="file" id="aiAvatarInput" accept="image/*" onchange="triggerEdgeAIAvatar()" class="form-control" style="font-size:11px;padding:6px;">
              <button type="button" onclick="openLiveCameraForAvatar()" class="btn btn-outline btn-sm" style="margin-top:8px;width:100%;justify-content:center;"><i class="bi bi-person-bounding-box" style="margin-right:4px;"></i> Chụp Ảnh Chân Dung Live</button>
              <div id="avatarPreviewWrap" style="display:none;margin-top:8px;align-items:center;gap:10px;">
                <canvas id="avatarCanvas" style="width:50px;height:67px;border-radius:4px;border:2px solid var(--gold);object-fit:cover;"></canvas>
                <span id="avatarAiStatus" style="font-size:11px;color:var(--success);font-weight:600;"></span>
              </div>
            </div>
          </div>
        </div>

        <form method="post" action="nhap_thong_tin.php">

          <!-- Section: Thông tin cá nhân -->
          <div class="form-section" style="border-left-color: var(--gold);">
            <div class="form-section-title" style="color: var(--gold);"><i class="bi bi-person-fill" style="margin-right:6px;"></i> 1. Thông tin sinh viên</div>
            <div class="form-grid">

              <!-- Họ tên -->
              <div class="form-group">
                <label class="form-label">Họ và tên <span class="required">*</span></label>
                <input type="text" name="ho_ten" class="form-control"
                  style="background-color: var(--bg3); cursor: not-allowed;" value="<?= e($ho_ten) ?>" readonly required>
              </div>

              <!-- Mã sinh viên -->
              <div class="form-group">
                <label class="form-label">Mã sinh viên <span class="required">*</span></label>
                <input type="text" name="ma_gvsv" class="form-control"
                  style="background-color: var(--bg3); cursor: not-allowed;" value="<?= e($ma_gvsv) ?>" readonly required>
              </div>

              <!-- Email -->
              <div class="form-group">
                <label class="form-label">Địa chỉ Email / Gmail <span class="required">*</span></label>
                <input type="email" name="email" class="form-control" placeholder="sv_a@gmail.com"
                  value="<?= e($email ?? '') ?>" required>
              </div>

              <!-- Số điện thoại -->
              <div class="form-group">
                <label class="form-label">Số điện thoại <span class="required">*</span></label>
                <input type="tel" name="sdt" class="form-control" placeholder="09xxxxxxxx" value="<?= e($sdt ?? '') ?>"
                  required>
              </div>

              <!-- Lớp -->
              <div class="form-group">
                <label class="form-label">Lớp sinh hoạt <span class="required">*</span></label>
                <input type="text" name="lop" class="form-control" placeholder="VD: K63 ĐHSP Toán"
                  value="<?= e($lop ?? '') ?>" required>
              </div>

              <!-- Chức vụ -->
              <div class="form-group">
                <label class="form-label">Chức vụ lớp / đoàn thể</label>
                <input type="text" name="chuc_vu" class="form-control" placeholder="Lớp trưởng, Bí thư chi đoàn..."
                  value="<?= e($chuc_vu ?? '') ?>">
              </div>

              <!-- Giới tính -->
              <div class="form-group">
                <label class="form-label">Giới tính</label>
                <select name="gioi_tinh" class="form-control">
                  <option value="">-- Chọn --</option>
                  <option value="Nam" <?= ($gioi_tinh ?? '') === 'Nam' ? 'selected' : '' ?>>Nam</option>
                  <option value="Nữ" <?= ($gioi_tinh ?? '') === 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                  <option value="Khác" <?= ($gioi_tinh ?? '') === 'Khác' ? 'selected' : '' ?>>Khác</option>
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
                <input type="text" name="dan_toc" class="form-control" placeholder="VD: Kinh, Mường..."
                  value="<?= e($dan_toc ?? '') ?>">
              </div>

              <!-- Quê quán -->
              <div class="form-group form-full">
                <label class="form-label">Quê quán (địa chỉ chi tiết)</label>
                <input type="text" name="que_quan" class="form-control"
                  placeholder="Xã/Phường, Quận/Huyện, Tỉnh/Thành phố" value="<?= e($que_quan ?? '') ?>">
              </div>
            </div>
          </div>

          <!-- Section: Mong muốn chi bộ -->
          <div class="form-section">
            <div class="form-section-title"><i class="bi bi-building" style="margin-right:6px;"></i> 2. Nguyện vọng Chi bộ đề xuất</div>
            <div class="form-grid">
              <div class="form-group form-full">
                <label class="form-label">Đề xuất Chi bộ công nhận cảm tình Đảng</label>
                <select name="chi_bo_cong_nhan" class="form-control">
                  <option value="">-- Chọn Chi bộ (nếu biết) --</option>
                  <?php foreach ($chiBos as $cb): ?>
                    <option value="<?= e($cb) ?>" <?= ($chi_bo_cong_nhan ?? '') === $cb ? 'selected' : '' ?>><?= e($cb) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group form-full">
                <label class="form-label">Ghi chú hoặc thông tin bổ sung</label>
                <textarea name="ghi_chu" class="form-control"
                  placeholder="Ghi nhận thành tích học tập nổi bật, danh hiệu hoặc nguyện vọng đặc biệt..."
                  rows="3"><?= e($ghi_chu ?? '') ?></textarea>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div style="margin-top: 24px;">
            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; justify-content: center;">
              <i class="bi bi-floppy-fill" style="margin-right:6px;"></i> Gửi hồ sơ đăng ký
            </button>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script src="<?= BASE_URL ?>AI_Module/edge_image_processor.js"></script>
<script src="<?= BASE_URL ?>AI_Module/live_camera_scanner.js"></script>
<script src="<?= BASE_URL ?>AI_Module/xai_confidence_overlay.js"></script>
<script src="<?= BASE_URL ?>AI_Module/edge_ai_autofill.js"></script>
<script>
let lastAutoFillOCR = null;
const xaiVisualizer = new XAIConfidenceOverlay();

function triggerEdgeAIOCR(filesOverride = null) {
  const files = filesOverride || document.getElementById('aiDocInput').files;
  const statusDiv = document.getElementById('aiOcrStatus');
  const btn = document.getElementById('btnAiScan');
  const btnXai = document.getElementById('btnViewXaiAutoFill');

  if (!files || files.length === 0) {
    alert("Vui lòng chọn ít nhất 1 ảnh CCCD (Mặt trước/Mặt sau) hoặc Thẻ Sinh Viên!");
    return;
  }

  btn.disabled = true;

  processEdgeAIAutoFill(
    files,
    function(msg) {
      statusDiv.innerHTML = `<span style="color:var(--gold);"><i class="bi bi-hourglass-split"></i> ${msg}</span>`;
    },
    function(data, combinedText, ocrMeta) {
      btn.disabled = false;
      statusDiv.innerHTML = `<span style="color:var(--success);"><i class="bi bi-check-circle-fill"></i> Đã trích xuất & điền form tự động thành công!</span>`;

      lastAutoFillOCR = ocrMeta;
      if (btnXai && ocrMeta && ocrMeta.words && ocrMeta.words.length > 0) {
        btnXai.style.display = 'flex';
      }

      // Fill in fields if extracted
      if (data.ho_ten && document.querySelector('input[name="ho_ten"]')) {
        document.querySelector('input[name="ho_ten"]').value = data.ho_ten;
      }
      if (data.ma_gvsv && document.querySelector('input[name="ma_gvsv"]')) {
        document.querySelector('input[name="ma_gvsv"]').value = data.ma_gvsv;
      }
      if (data.ngay_sinh && document.querySelector('input[name="ngay_sinh"]')) {
        document.querySelector('input[name="ngay_sinh"]').value = data.ngay_sinh;
      }
      if (data.gioi_tinh && document.querySelector('select[name="gioi_tinh"]')) {
        document.querySelector('select[name="gioi_tinh"]').value = data.gioi_tinh;
      }
      if (data.lop && document.querySelector('input[name="lop"]')) {
        document.querySelector('input[name="lop"]').value = data.lop;
      }
      if (data.que_quan && document.querySelector('input[name="que_quan"]')) {
        document.querySelector('input[name="que_quan"]').value = data.que_quan;
      }
      if (data.dan_toc && document.querySelector('input[name="dan_toc"]')) {
        document.querySelector('input[name="dan_toc"]').value = data.dan_toc;
      }
    },
    function(err) {
      btn.disabled = false;
      statusDiv.innerHTML = `<span style="color:var(--danger);"><i class="bi bi-exclamation-triangle-fill"></i> ${err}</span>`;
    }
  );
}

function openLiveCameraForAutoFill() {
  const scanner = new LiveCameraScanner({
    targetType: 'card',
    sharpnessThreshold: 60,
    onCapture: (file, dataUrl) => {
      triggerEdgeAIOCR([file]);
    }
  });
  scanner.open();
}

function openLiveCameraForAvatar() {
  const scanner = new LiveCameraScanner({
    targetType: 'card',
    autoSnapEnabled: false,
    onCapture: (file, dataUrl) => {
      const wrap = document.getElementById('avatarPreviewWrap');
      const canvas = document.getElementById('avatarCanvas');
      const status = document.getElementById('avatarAiStatus');

      wrap.style.display = 'flex';
      status.innerHTML = '⏳ AI đang căn chỉnh tỉ lệ 3x4...';

      processEdgeAIAvatarCrop(file, canvas, function(res) {
        status.innerHTML = res.message;
      });
    }
  });
  scanner.open();
}

function openXaiForAutoFill() {
  if (!lastAutoFillOCR || !lastAutoFillOCR.image) {
    alert("Chưa có dữ liệu quét OCR để hiển thị XAI Heatmap.");
    return;
  }
  xaiVisualizer.open(lastAutoFillOCR.image, lastAutoFillOCR.words);
}

function triggerEdgeAIAvatar() {
  const file = document.getElementById('aiAvatarInput').files[0];
  const wrap = document.getElementById('avatarPreviewWrap');
  const canvas = document.getElementById('avatarCanvas');
  const status = document.getElementById('avatarAiStatus');

  if (!file) return;
  wrap.style.display = 'flex';
  status.innerHTML = '⏳ AI đang kiểm tra khuôn mặt...';

  processEdgeAIAvatarCrop(file, canvas, function(res) {
    status.innerHTML = res.message;
  });
}
</script>

</body>

</html>