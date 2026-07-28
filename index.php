<?php
// index.php – Dashboard chính
require_once __DIR__ . '/config.php';

$pageTitle = 'Dashboard';

try {
    $db = getDB();

    // Thống kê tổng
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

} catch (PDOException $e) {
    $total = $danTheoD = $daKetNap = $daChuyen = 0;
    $recent = $byStatus = $byMonth = $byChibo = [];
    $dbError = $e->getMessage();
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
  <div class="page-header-left">
    <div class="page-title"><span>⭐</span> Dashboard</div>
    <div class="page-subtitle">Tổng quan hệ thống quản lý quần chúng kết nạp Đảng</div>
  </div>
  <div style="display:flex;gap:10px;align-items:center;">
    <a href="Chucnang/them.php" class="btn btn-primary">➕ Thêm đối tượng mới</a>
    <a href="Chucnang/xuat_excel.php" class="btn btn-gold">📤 Xuất Excel</a>
  </div>
</div>

<?php if (isset($dbError)): ?>
<div class="flash flash-danger">
  ❌ Lỗi kết nối DB: <?= e($dbError) ?> –
  <a href="setup.php" style="color:inherit;font-weight:700;">Chạy Setup để tạo database</a>
</div>
<?php endif; ?>

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
    <div class="card-title"><span class="icon">🕒</span> Đối tượng gần đây</div>
    <a href="Chucnang/danh_sach.php" class="btn btn-outline btn-sm">Xem tất cả →</a>
  </div>
  <div class="card-body" style="padding:0;">
    <?php if (empty($recent)): ?>
    <div class="empty-state">
      <div class="icon">📂</div>
      <h3>Chưa có dữ liệu</h3>
      <p>Thêm đối tượng đầu tiên để bắt đầu quản lý</p>
      <a href="Chucnang/them.php" class="btn btn-primary" style="margin-top:16px;">➕ Thêm ngay</a>
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
              <a href="Chucnang/chi_tiet.php?id=<?= $row['id'] ?>" class="name-link">
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
                <a href="Chucnang/chi_tiet.php?id=<?= $row['id'] ?>" class="btn btn-outline btn-sm" title="Xem">👁️</a>
                <a href="Chucnang/sua.php?id=<?= $row['id'] ?>" class="btn btn-outline btn-sm" title="Sửa">✏️</a>
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
