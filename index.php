<?php
// index.php – Dashboard chính
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/User/auth.php';

// Yêu cầu đăng nhập
requireLogin();

$user = getCurrentUser();
$vaiTro = $user['vai_tro'] ?? 'Người dùng thường';

$pageTitle = 'Dashboard';
// Khởi tạo các biến thống kê
$total = $danTheoD = $daKetNap = $daChuyen = 0;
$recent = $byStatus = $byMonth = $byChibo = [];
$myRequests = [];
$myProfile = null;
$chiBoMembers = [];

try {
    $db = getDB();

    if ($vaiTro === 'Người dùng thường') {
        // Kiểm tra xem đối tượng đã được duyệt vào danh sách chính thức (doi_tuong) chưa
        $stmt = $db->prepare("SELECT * FROM doi_tuong WHERE ma_gvsv = ? OR ho_ten = ? LIMIT 1");
        $stmt->execute([$user['username'], $user['ho_ten']]);
        $myProfile = $stmt->fetch();

        if ($myProfile) {
            // Lấy danh sách thành viên cùng chi bộ hoặc cùng lớp học
            $stmtMembers = $db->prepare("SELECT id, ma_gvsv, ho_ten, lop, chi_bo_cong_nhan, chuc_vu, trang_thai, email 
                                         FROM doi_tuong 
                                         WHERE ((chi_bo_cong_nhan = ? AND chi_bo_cong_nhan != '') OR (lop = ? AND lop != '')) AND id != ? 
                                         ORDER BY ho_ten ASC");
            $stmtMembers->execute([$myProfile['chi_bo_cong_nhan'], $myProfile['lop'], $myProfile['id']]);
            $chiBoMembers = $stmtMembers->fetchAll();
        } else {
            // Lấy danh sách yêu cầu đăng ký (chờ duyệt/từ chối)
            $stmtReq = $db->prepare("SELECT id, ma_gvsv, ho_ten, lop, trang_thai, created_at, ly_do_tu_choi 
                                     FROM dang_ky_doi_tuong 
                                     WHERE ho_ten = ? OR ma_gvsv = ? 
                                     ORDER BY created_at DESC");
            $stmtReq->execute([$user['ho_ten'], $user['username']]);
            $myRequests = $stmtReq->fetchAll();
        }
    } else {
        // Đối với Quản lý và Admin: Thống kê đầy đủ hệ thống
        $total    = $db->query("SELECT COUNT(*) FROM doi_tuong")->fetchColumn();
        $danTheoD = $db->query("SELECT COUNT(*) FROM doi_tuong WHERE trang_thai='Đang theo dõi'")->fetchColumn();
        $daKetNap = $db->query("SELECT COUNT(*) FROM doi_tuong WHERE trang_thai='Đã kết nạp'")->fetchColumn();
        $daChuyen = $db->query("SELECT COUNT(*) FROM doi_tuong WHERE trang_thai='Đã chuyển'")->fetchColumn();

        // Mới nhất (10 bản ghi)
        $recent = $db->query("SELECT id, ho_ten, lop, trang_thai, created_at, ngay_ket_nap, ma_gvsv
                              FROM doi_tuong ORDER BY created_at DESC LIMIT 10")->fetchAll();

        // Theo trạng thái cho biểu đồ
        $byStatus = $db->query("SELECT trang_thai, COUNT(*) as cnt FROM doi_tuong GROUP BY trang_thai")->fetchAll();

        // Theo tháng (12 tháng gần đây)
        $byMonth = $db->query("
            SELECT DATE_FORMAT(created_at,'%Y-%m') as month, COUNT(*) as cnt
            FROM doi_tuong
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY month ORDER BY month
        ")->fetchAll();

        // Theo chi bộ
        $byChibo = $db->query("
            SELECT chi_bo_cong_nhan, COUNT(*) as cnt
            FROM doi_tuong WHERE chi_bo_cong_nhan IS NOT NULL AND chi_bo_cong_nhan != ''
            GROUP BY chi_bo_cong_nhan ORDER BY cnt DESC LIMIT 6
        ")->fetchAll();
    }

} catch (PDOException $e) {
    $dbError = $e->getMessage();
}

require_once __DIR__ . '/Giao_dien/header.php';
?>

<!-- Page Header -->
<div class="page-header">
  <div class="page-header-left">
    <div class="page-title"><span>⭐</span> Dashboard</div>
    <div class="page-subtitle">Xin chào, <strong><?= e($user['ho_ten'] ?: $user['username']) ?></strong> (Vai trò: <span style="color:var(--gold);"><?= $vaiTro ?></span>)</div>
  </div>
  <?php if ($vaiTro !== 'Người dùng thường'): ?>
  <div style="display:flex;gap:10px;align-items:center;">
    <a href="Quan_ly_doi_tuong/them.php" class="btn btn-primary">➕ Thêm đối tượng mới</a>
    <a href="Thong_ke_bao_cao/xuat_excel.php" class="btn btn-gold">📤 Xuất Excel</a>
  </div>
  <?php else: ?>
  <div style="display:flex;gap:10px;align-items:center;">
    <?php if ($myProfile): ?>
      <a href="Quan_ly_doi_tuong/cap_nhat_thong_tin.php" class="btn btn-gold">✏️ Yêu cầu cập nhật thông tin</a>
    <?php else: ?>
      <a href="Quan_ly_doi_tuong/nhap_thong_tin.php" class="btn btn-primary">✍️ Gửi yêu cầu đăng ký mới</a>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<?php if (isset($dbError)): ?>
<div class="flash flash-danger">
  ❌ Lỗi kết nối DB: <?= e($dbError) ?> –
  <a href="Cau_hinh/setup.php" style="color:inherit;font-weight:700;">Chạy Setup để tạo database</a>
</div>
<?php endif; ?>

<?php if ($vaiTro === 'Người dùng thường'): ?>
  <!-- DASHBOARD CHO NGƯỜI DÙNG THƯỜNG -->
  <?php if ($myProfile): ?>
  <!-- Custom Styles for User Profile & Timeline -->
  <style>
    .profile-grid {
      display: grid;
      grid-template-columns: 1fr 1.2fr;
      gap: 20px;
      margin-bottom: 24px;
    }
    @media (max-width: 992px) {
      .profile-grid {
        grid-template-columns: 1fr;
      }
    }
    .profile-info-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .info-row {
      display: flex;
      justify-content: space-between;
      border-bottom: 1px solid var(--border);
      padding-bottom: 8px;
    }
    .info-label {
      font-weight: 600;
      color: var(--text2);
      font-size: 13px;
    }
    .info-value {
      font-weight: 500;
      color: var(--text);
      font-size: 13px;
      text-align: right;
    }
    
    /* Timeline styles */
    .timeline-container {
      display: flex;
      flex-direction: column;
      position: relative;
      margin-left: 20px;
      padding-left: 20px;
      border-left: 3px solid var(--bg3);
    }
    .timeline-item {
      position: relative;
      margin-bottom: 24px;
    }
    .timeline-item:last-child {
      margin-bottom: 0;
    }
    .timeline-marker {
      position: absolute;
      left: -32px;
      top: 0;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background: var(--bg2);
      border: 3px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s;
    }
    .timeline-item.completed .timeline-marker {
      background: var(--success);
      border-color: var(--success);
    }
    .timeline-item.active .timeline-marker {
      background: var(--gold);
      border-color: var(--gold);
      box-shadow: 0 0 10px var(--gold);
    }
    .timeline-content {
      padding-top: 2px;
    }
    .timeline-title {
      font-size: 14px;
      font-weight: 700;
      color: var(--text2);
    }
    .timeline-item.active .timeline-title {
      color: var(--gold);
    }
    .timeline-item.completed .timeline-title {
      color: var(--text);
    }
    .timeline-date {
      font-size: 12px;
      color: var(--gold);
      font-weight: 600;
      margin-top: 4px;
    }
    .timeline-desc {
      font-size: 12px;
      color: var(--text2);
      margin-top: 4px;
      line-height: 1.5;
    }
  </style>

  <?php
  // Tính toán trạng thái các bước tiến trình
  $step1 = true; // Quần chúng ưu tú (Luôn xong)
  $step2 = !empty($myProfile['ngay_hop_cam_tinh']); // Cảm tình Đảng
  $step3 = !empty($myProfile['ngay_cap_cc']); // Bồi dưỡng nhận thức Đảng
  $step4 = !empty($myProfile['ngay_ket_nap']); // Kết nạp Đảng
  $step5 = ($myProfile['trang_thai'] === 'Đã kết nạp' || $myProfile['trang_thai'] === 'Đã chuyển'); // Đảng viên chính thức
  
  // Xác định bước hoạt động hiện tại (active)
  $activeStep = 1;
  if ($step5) $activeStep = 5;
  elseif ($step4) $activeStep = 5;
  elseif ($step3) $activeStep = 4;
  elseif ($step2) $activeStep = 3;
  else $activeStep = 2;
  ?>

  <div class="profile-grid">
    <!-- Cột 1: Thông tin cá nhân -->
    <div class="card fade-in">
      <div class="card-header">
        <div class="card-title"><span class="icon">👤</span> Hồ sơ Quần chúng chính thức</div>
      </div>
      <div class="card-body">
        <div style="display:flex; gap:16px; align-items:center; margin-bottom:20px; padding-bottom:16px; border-bottom:1px solid var(--border);">
          <div class="avatar" style="width:64px; height:64px; border-radius:50%; background:var(--red); color:#fff; font-size:28px; font-weight:bold; display:flex; align-items:center; justify-content:center;">
            <?= mb_substr($myProfile['ho_ten'], 0, 1) ?>
          </div>
          <div style="text-align:left;">
            <h3 style="font-size:18px; font-weight:700; color:var(--text);"><?= e($myProfile['ho_ten']) ?></h3>
            <span class="badge badge-green" style="margin-top:4px;"><?= e($myProfile['trang_thai']) ?></span>
          </div>
        </div>

        <div class="profile-info-list">
          <div class="info-row">
            <span class="info-label">Mã SV/GV:</span>
            <span class="info-value"><code style="color:var(--gold);"><?= e($myProfile['ma_gvsv'] ?: '—') ?></code></span>
          </div>
          <div class="info-row">
            <span class="info-label">Lớp:</span>
            <span class="info-value"><?= e($myProfile['lop'] ?: '—') ?></span>
          </div>
          <div class="info-row">
            <span class="info-label">Chi bộ sinh hoạt:</span>
            <span class="info-value" style="color:var(--gold); font-weight:bold;"><?= e($myProfile['chi_bo_cong_nhan'] ?: '—') ?></span>
          </div>
          <div class="info-row">
            <span class="info-label">Chức vụ:</span>
            <span class="info-value"><?= e($myProfile['chuc_vu'] ?: '—') ?></span>
          </div>
          <div class="info-row">
            <span class="info-label">Số điện thoại:</span>
            <span class="info-value"><?= e($myProfile['sdt'] ?: '—') ?></span>
          </div>
          <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value"><?= e($myProfile['email'] ?: '—') ?></span>
          </div>
          <div class="info-row">
            <span class="info-label">Ngày sinh:</span>
            <span class="info-value"><?= $myProfile['ngay_sinh'] ? formatDate($myProfile['ngay_sinh']) : '—' ?></span>
          </div>
          <div class="info-row">
            <span class="info-label">Quê quán:</span>
            <span class="info-value" style="max-width:240px; text-align:right; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= e($myProfile['que_quan']) ?>"><?= e($myProfile['que_quan'] ?: '—') ?></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Cột 2: Tiến trình kết nạp Đảng -->
    <div class="card fade-in">
      <div class="card-header">
        <div class="card-title"><span class="icon">📈</span> Tiến trình Kết nạp Đảng</div>
      </div>
      <div class="card-body" style="padding-top:20px;">
        <div class="timeline-container">
          
          <!-- Bước 1 -->
          <div class="timeline-item completed">
            <div class="timeline-marker"></div>
            <div class="timeline-content">
              <div class="timeline-title">1. Quần chúng ưu tú đề xuất</div>
              <div class="timeline-desc">Hồ sơ đã được gửi lên hệ thống và Ban chi bộ phê duyệt chính thức ghi nhận.</div>
            </div>
          </div>

          <!-- Bước 2 -->
          <div class="timeline-item <?= $step2 ? 'completed' : ($activeStep === 2 ? 'active' : '') ?>">
            <div class="timeline-marker"></div>
            <div class="timeline-content">
              <div class="timeline-title">2. Cảm tình Đảng</div>
              <?php if ($step2): ?>
                <div class="timeline-date">✓ Hoàn thành ngày: <?= formatDate($myProfile['ngay_hop_cam_tinh']) ?></div>
                <div class="timeline-desc">Đã được chi bộ họp xét công nhận cảm tình Đảng (Số công văn BC: <?= e($myProfile['so_bc_cam_tinh'] ?: '—') ?>).</div>
              <?php else: ?>
                <div class="timeline-desc">Chi bộ đang làm hồ sơ đề nghị công nhận cảm tình Đảng.</div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Bước 3 -->
          <div class="timeline-item <?= $step3 ? 'completed' : ($activeStep === 3 ? 'active' : '') ?>">
            <div class="timeline-marker"></div>
            <div class="timeline-content">
              <div class="timeline-title">3. Bồi dưỡng nhận thức về Đảng</div>
              <?php if ($step3): ?>
                <div class="timeline-date">✓ Cấp chứng chỉ ngày: <?= formatDate($myProfile['ngay_cap_cc']) ?></div>
                <div class="timeline-desc">Đã hoàn thành khoá bồi dưỡng lý luận nhận thức về Đảng (Quyết định số: <?= e($myProfile['so_qd_cc'] ?: '—') ?>).</div>
              <?php else: ?>
                <div class="timeline-desc">Đang chờ cử đi học lớp bồi dưỡng nhận thức về Đảng hoặc chờ cập nhật chứng chỉ.</div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Bước 4 -->
          <div class="timeline-item <?= $step4 ? 'completed' : ($activeStep === 4 ? 'active' : '') ?>">
            <div class="timeline-marker"></div>
            <div class="timeline-content">
              <div class="timeline-title">4. Hồ sơ kết nạp Đảng</div>
              <?php if ($step4): ?>
                <div class="timeline-date">✓ Quyết định kết nạp ngày: <?= formatDate($myProfile['ngay_quyet_dinh']) ?></div>
                <div class="timeline-desc">Ban thường vụ Đảng uỷ đã ra quyết định kết nạp Đảng viên mới (Số quyết định: <?= e($myProfile['so_qd_ket_nap'] ?: '—') ?>).</div>
              <?php else: ?>
                <div class="timeline-desc">Đang hoàn thiện hồ sơ lý lịch, phân công Đảng viên giúp đỡ (ĐV giúp đỡ: <?= e($myProfile['dang_vien_giup_do'] ?: '—') ?>) để trình Đảng uỷ cấp trên xét duyệt kết nạp.</div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Bước 5 -->
          <div class="timeline-item <?= $step5 ? 'completed' : ($activeStep === 5 ? 'active' : '') ?>">
            <div class="timeline-marker"></div>
            <div class="timeline-content">
              <div class="timeline-title">5. Kết nạp Đảng & Đảng viên dự bị</div>
              <?php if ($step5): ?>
                <div class="timeline-date">✓ Ngày tổ chức lễ kết nạp: <?= formatDate($myProfile['ngay_ket_nap']) ?></div>
                <div class="timeline-desc">Đã tổ chức lễ kết nạp Đảng viên mới chính thức. Đang trong thời gian dự bị 12 tháng (ĐV hướng dẫn: <?= e($myProfile['dang_vien_huong_dan'] ?: '—') ?>).</div>
              <?php else: ?>
                <div class="timeline-desc">Sẽ được tổ chức lễ kết nạp sau khi có quyết định chuẩn y từ Đảng uỷ cấp trên.</div>
              <?php endif; ?>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Bảng thành viên cùng chi bộ hoặc cùng lớp -->
  <div class="card fade-in">
    <div class="card-header">
      <div class="card-title"><span class="icon">🏛️</span> Danh sách thành viên cùng Lớp (<strong><?= e($myProfile['lop'] ?: '—') ?></strong>) hoặc cùng Chi bộ (<strong><?= e($myProfile['chi_bo_cong_nhan'] ?: '—') ?></strong>)</div>
    </div>
    <div class="card-body" style="padding:0;">
      <?php if (empty($chiBoMembers)): ?>
      <div class="empty-state" style="padding: 30px 20px;">
        <div class="icon">📂</div>
        <h3>Không có thành viên nào khác cùng Lớp hoặc Chi bộ</h3>
        <p>Hệ thống chưa ghi nhận thành viên chính thức nào khác trong lớp hoặc chi bộ của bạn.</p>
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
              <th>Chi bộ</th>
              <th>Chức vụ</th>
              <th>Email liên hệ</th>
              <th>Trạng thái kết nạp</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($chiBoMembers as $idx => $m): ?>
            <tr>
              <td><?= $idx + 1 ?></td>
              <td><code style="color:var(--gold);font-size:12px;"><?= e($m['ma_gvsv'] ?: '—') ?></code></td>
              <td><strong><?= e($m['ho_ten']) ?></strong></td>
              <td><?= e($m['lop'] ?: '—') ?></td>
              <td><?= e($m['chi_bo_cong_nhan'] ?: '—') ?></td>
              <td><?= e($m['chuc_vu'] ?: '—') ?></td>
              <td><a href="mailto:<?= e($m['email']) ?>" style="color:var(--gold); text-decoration:none;"><?= e($m['email'] ?: '—') ?></a></td>
              <td><?php
                $ms = $m['trang_thai'];
                $mcls = $ms === 'Đã kết nạp' ? 'green' : ($ms === 'Đang theo dõi' ? 'gold' : ($ms === 'Đã chuyển' ? 'blue' : 'gray'));
                echo "<span class='badge badge-$mcls'>$ms</span>";
              ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <?php else: ?>
  <!-- DASHBOARD CHO NGƯỜI DÙNG THƯỜNG CHƯA ĐƯỢC DUYỆT -->
  <div class="card fade-in" style="margin-bottom: 24px;">
    <div class="card-header">
      <div class="card-title"><span class="icon">📋</span> Danh sách hồ sơ đăng ký của bạn</div>
    </div>
    <div class="card-body" style="padding:0;">
      <?php if (empty($myRequests)): ?>
      <div class="empty-state" style="padding: 40px 20px;">
        <div class="icon" style="font-size:48px;">📂</div>
        <h3>Bạn chưa gửi hồ sơ đăng ký nào</h3>
        <p>Gửi hồ sơ đăng ký của bạn ngay để Ban chi bộ xem xét phê duyệt kết nạp Đảng.</p>
        <a href="Quan_ly_doi_tuong/nhap_thong_tin.php" class="btn btn-primary" style="margin-top:16px;">✍️ Gửi hồ sơ đăng ký ngay</a>
      </div>
      <?php else: ?>
      <div class="table-wrapper">
        <table class="data-table">
          <thead>
            <tr>
              <th>STT</th>
              <th>Mã SV</th>
              <th>Họ và tên</th>
              <th>Lớp</th>
              <th>Ngày gửi</th>
              <th>Trạng thái</th>
              <th>Phản hồi / Lý do</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($myRequests as $i => $row): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><code style="color:var(--gold);font-size:12px;"><?= e($row['ma_gvsv'] ?: '—') ?></code></td>
              <td><strong><?= e($row['ho_ten']) ?></strong></td>
              <td><?= e($row['lop'] ?: '—') ?></td>
              <td><?= formatDate(substr($row['created_at'], 0, 10)) ?></td>
              <td><?php
                $s = $row['trang_thai'];
                $cls = $s === 'Đã duyệt' ? 'green' : ($s === 'Chờ duyệt' ? 'gold' : 'red');
                echo "<span class='badge badge-$cls'>$s</span>";
              ?></td>
              <td>
                <?php if ($row['trang_thai'] === 'Đã từ chối'): ?>
                  <span style="color:var(--red);font-size:13px;">❌ <?= e($row['ly_do_tu_choi'] ?: 'Không có lý do cụ thể') ?></span>
                <?php elseif ($row['trang_thai'] === 'Đã duyệt'): ?>
                  <span style="color:var(--success);font-size:13px;">✅ Hồ sơ đã được duyệt và thêm vào danh sách theo dõi</span>
                <?php else: ?>
                  <span style="color:var(--text2);font-size:13px;">⏳ Đang chờ Ban quản lý Chi bộ kiểm tra hồ sơ</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
<?php else: ?>
  <!-- DASHBOARD CHO QUẢN LÝ / ADMIN -->
  <!-- Stat Cards -->
  <div class="stat-grid">
    <div class="stat-card red fade-in">
      <div class="stat-icon">👥</div>
      <div class="stat-info">
        <div class="stat-number"><?= number_format($total) ?></div>
        <div class="stat-label">Tổng đối tượng</div>
        <div class="stat-sub">Quần chúng trong hệ thống</div>
      </div>
    </div>
    <div class="stat-card gold fade-in" style="animation-delay:0.1s">
      <div class="stat-icon">🔄</div>
      <div class="stat-info">
        <div class="stat-number"><?= number_format($danTheoD) ?></div>
        <div class="stat-label">Đang theo dõi</div>
        <div class="stat-sub">Chờ kết nạp Đảng</div>
      </div>
    </div>
    <div class="stat-card green fade-in" style="animation-delay:0.2s">
      <div class="stat-icon">✅</div>
      <div class="stat-info">
        <div class="stat-number"><?= number_format($daKetNap) ?></div>
        <div class="stat-label">Đã kết nạp</div>
        <div class="stat-sub">Đảng viên mới</div>
      </div>
    </div>
    <div class="stat-card blue fade-in" style="animation-delay:0.3s">
      <div class="stat-icon">↗️</div>
      <div class="stat-info">
        <div class="stat-number"><?= number_format($daChuyen) ?></div>
        <div class="stat-label">Đã chuyển</div>
        <div class="stat-sub">Chuyển sinh hoạt</div>
      </div>
    </div>
  </div>

  <!-- Charts + Recent -->
  <div style="display:grid;grid-template-columns:1fr 1.4fr;gap:20px;margin-bottom:24px;">

    <!-- Biểu đồ tròn -->
    <div class="card fade-in">
      <div class="card-header">
        <div class="card-title"><span class="icon">📊</span> Theo trạng thái</div>
      </div>
      <div class="card-body">
        <div class="chart-container" style="height:220px;">
          <canvas id="chartStatus"></canvas>
        </div>
      </div>
    </div>

    <!-- Biểu đồ tháng -->
    <div class="card fade-in">
      <div class="card-header">
        <div class="card-title"><span class="icon">📈</span> Đối tượng theo tháng</div>
      </div>
      <div class="card-body">
        <div class="chart-container" style="height:220px;">
          <canvas id="chartMonth"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Danh sách gần đây -->
  <div class="card fade-in">
    <div class="card-header">
      <div class="card-title"><span class="icon">🕒</span> Đối tượng mới cập nhật</div>
      <a href="Quan_ly_doi_tuong/danh_sach.php" class="btn btn-outline btn-sm">Xem tất cả →</a>
    </div>
    <div class="card-body" style="padding:0;">
      <?php if (empty($recent)): ?>
      <div class="empty-state">
        <div class="icon">📂</div>
        <h3>Chưa có dữ liệu đối tượng</h3>
        <p>Thêm đối tượng đầu tiên để bắt đầu quản lý</p>
        <a href="Quan_ly_doi_tuong/them.php" class="btn btn-primary" style="margin-top:16px;">➕ Thêm ngay</a>
      </div>
      <?php else: ?>
      <div class="table-wrapper">
        <table class="data-table">
          <thead>
            <tr>
              <th>STT</th>
              <th>Mã GV/SV</th>
              <th>Họ và tên</th>
              <th>Lớp</th>
              <th>Trạng thái</th>
              <th>Ngày kết nạp</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent as $i => $row): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><code style="color:var(--gold);font-size:12px;"><?= e($row['ma_gvsv'] ?: '—') ?></code></td>
              <td>
                <a href="Quan_ly_doi_tuong/chi_tiet.php?id=<?= $row['id'] ?>" class="name-link">
                  <?= e($row['ho_ten']) ?>
                </a>
              </td>
              <td><?= e($row['lop'] ?: '—') ?></td>
              <td><?php
                $s = $row['trang_thai'];
                $cls = $s === 'Đã kết nạp' ? 'green' : ($s === 'Đang theo dõi' ? 'gold' : ($s === 'Đã chuyển' ? 'blue' : 'gray'));
                echo "<span class='badge badge-$cls'>$s</span>";
              ?></td>
              <td><?= $row['ngay_ket_nap'] ? formatDate($row['ngay_ket_nap']) : '<span style="color:var(--text2)">—</span>' ?></td>
              <td>
                <div style="display:flex;gap:6px;">
                  <a href="Quan_ly_doi_tuong/chi_tiet.php?id=<?= $row['id'] ?>" class="btn btn-outline btn-sm" title="Xem">👁️</a>
                  <a href="Quan_ly_doi_tuong/sua.php?id=<?= $row['id'] ?>" class="btn btn-outline btn-sm" title="Sửa">✏️</a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Chi bộ summary -->
  <?php if (!empty($byChibo)): ?>
  <div class="card fade-in" style="margin-top:20px;">
    <div class="card-header">
      <div class="card-title"><span class="icon">🏛️</span> Thống kê theo Chi bộ</div>
    </div>
    <div class="card-body">
      <?php
      $maxCnt = max(array_column($byChibo, 'cnt'));
      foreach ($byChibo as $cb):
        $pct = $maxCnt > 0 ? round($cb['cnt'] / $maxCnt * 100) : 0;
      ?>
      <div style="margin-bottom:14px;">
        <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
          <span style="font-size:13px;font-weight:500;"><?= e($cb['chi_bo_cong_nhan']) ?></span>
          <span style="font-size:12px;color:var(--gold);font-weight:700;"><?= $cb['cnt'] ?></span>
        </div>
        <div style="background:var(--bg3);border-radius:4px;height:6px;overflow:hidden;">
          <div style="width:<?= $pct ?>%;height:100%;background:linear-gradient(90deg,var(--red),var(--gold));border-radius:4px;transition:width 1s ease;"></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script>
  Chart.defaults.color = '#a0a0b8';
  Chart.defaults.font.family = 'Roboto, sans-serif';

  // Status chart
  var statusData = <?= json_encode(array_values(array_map(fn($r) => (int)$r['cnt'], $byStatus))) ?>;
  var statusLabels = <?= json_encode(array_values(array_map(fn($r) => $r['trang_thai'], $byStatus))) ?>;
  new Chart(document.getElementById('chartStatus'), {
    type: 'doughnut',
    data: {
      labels: statusLabels,
      datasets: [{
        data: statusData,
        backgroundColor: ['#FFD700','#22c55e','#3b82f6','#6b7280'],
        borderColor: '#16161f', borderWidth: 3
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom', labels: { padding: 16, font: { size: 12 } } }
      }
    }
  });

  // Month chart
  var monthLabels = <?= json_encode(array_column($byMonth, 'month')) ?>;
  var monthData   = <?= json_encode(array_map(fn($r) => (int)$r['cnt'], $byMonth)) ?>;
  new Chart(document.getElementById('chartMonth'), {
    type: 'bar',
    data: {
      labels: monthLabels,
      datasets: [{
        label: 'Số đối tượng',
        data: monthData,
        backgroundColor: 'rgba(200,16,46,0.6)',
        borderColor: '#C8102E',
        borderWidth: 2,
        borderRadius: 6
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { color: 'rgba(255,255,255,0.05)' } },
        y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { precision: 0 } }
      }
    }
  });
  </script>
<?php endif; ?>

<?php require_once __DIR__ . '/Giao_dien/footer.php'; ?>
