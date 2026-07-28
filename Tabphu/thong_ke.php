<?php
// Tabphu/thong_ke.php – Thống kê & Báo cáo
require_once dirname(__DIR__) . '/config.php';
$pageTitle = 'Thống kê & Báo cáo';
$db = getDB();

// Tổng quan
$stats = $db->query("
    SELECT 
        COUNT(*) as tong,
        SUM(trang_thai='Đang theo dõi') as dang_theo_doi,
        SUM(trang_thai='Đã kết nạp') as da_ket_nap,
        SUM(trang_thai='Đã chuyển') as da_chuyen,
        SUM(trang_thai='Tạm dừng') as tam_dung
    FROM doi_tuong
")->fetch();

// Theo tháng (24 tháng)
$byMonth = $db->query("
    SELECT DATE_FORMAT(created_at,'%Y-%m') as thang, COUNT(*) as so_luong
    FROM doi_tuong WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 MONTH)
    GROUP BY thang ORDER BY thang
")->fetchAll();

// Kết nạp theo tháng
$ketNapByMonth = $db->query("
    SELECT DATE_FORMAT(ngay_ket_nap,'%Y-%m') as thang, COUNT(*) as so_luong
    FROM doi_tuong WHERE ngay_ket_nap IS NOT NULL
    GROUP BY thang ORDER BY thang
")->fetchAll();

// Theo chi bộ
$byChibo = $db->query("
    SELECT chi_bo_cong_nhan, COUNT(*) as so_luong,
           SUM(trang_thai='Đã kết nạp') as da_ket_nap
    FROM doi_tuong WHERE chi_bo_cong_nhan IS NOT NULL AND chi_bo_cong_nhan != ''
    GROUP BY chi_bo_cong_nhan ORDER BY so_luong DESC
")->fetchAll();

// Theo lớp
$byLop = $db->query("
    SELECT lop, COUNT(*) as so_luong
    FROM doi_tuong WHERE lop IS NOT NULL AND lop != ''
    GROUP BY lop ORDER BY so_luong DESC LIMIT 10
")->fetchAll();

// Theo giới tính
$byGioiTinh = $db->query("
    SELECT IFNULL(gioi_tinh,'Chưa xác định') as gioi_tinh, COUNT(*) as so_luong
    FROM doi_tuong GROUP BY gioi_tinh
")->fetchAll();

// Đảng viên giúp đỡ nhiều nhất
$topDV = $db->query("
    SELECT dang_vien_giup_do, COUNT(*) as so_luong
    FROM doi_tuong WHERE dang_vien_giup_do IS NOT NULL AND dang_vien_giup_do != ''
    GROUP BY dang_vien_giup_do ORDER BY so_luong DESC LIMIT 5
")->fetchAll();

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="page-header">
  <div class="page-header-left">
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>index.php">Dashboard</a><span class="sep">›</span>
      <span class="current">Thống kê</span>
    </div>
    <div class="page-title">📊 Thống kê <span>& Báo cáo</span></div>
  </div>
  <a href="<?= BASE_URL ?>Chucnang/xuat_excel.php" class="btn btn-gold">📤 Xuất báo cáo</a>
</div>

<!-- Stat Cards -->
<div class="stat-grid">
  <div class="stat-card red"><div class="stat-icon">👥</div><div class="stat-info"><div class="stat-number"><?= $stats['tong'] ?></div><div class="stat-label">Tổng đối tượng</div></div></div>
  <div class="stat-card gold"><div class="stat-icon">🔄</div><div class="stat-info"><div class="stat-number"><?= $stats['dang_theo_doi'] ?></div><div class="stat-label">Đang theo dõi</div></div></div>
  <div class="stat-card green"><div class="stat-icon">⭐</div><div class="stat-info"><div class="stat-number"><?= $stats['da_ket_nap'] ?></div><div class="stat-label">Đã kết nạp</div></div></div>
  <div class="stat-card blue"><div class="stat-icon">↗️</div><div class="stat-info"><div class="stat-number"><?= $stats['da_chuyen'] ?></div><div class="stat-label">Đã chuyển SH</div></div></div>
</div>

<!-- Charts Row 1 -->
<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:20px;">
  <div class="card fade-in">
    <div class="card-header"><div class="card-title"><span class="icon">📈</span> Đối tượng thêm mới theo tháng</div></div>
    <div class="card-body"><div style="height:250px;"><canvas id="chartThemMoi"></canvas></div></div>
  </div>
  <div class="card fade-in">
    <div class="card-header"><div class="card-title"><span class="icon">🍩</span> Theo giới tính</div></div>
    <div class="card-body"><div style="height:250px;"><canvas id="chartGioiTinh"></canvas></div></div>
  </div>
</div>

<!-- Charts Row 2 -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
  <div class="card fade-in">
    <div class="card-header"><div class="card-title"><span class="icon">⭐</span> Kết nạp theo tháng</div></div>
    <div class="card-body"><div style="height:220px;"><canvas id="chartKetNap"></canvas></div></div>
  </div>
  <div class="card fade-in">
    <div class="card-header"><div class="card-title"><span class="icon">📚</span> Top lớp nhiều nhất</div></div>
    <div class="card-body"><div style="height:220px;"><canvas id="chartLop"></canvas></div></div>
  </div>
</div>

<!-- Tables Row -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

  <!-- Chi bộ -->
  <div class="card fade-in">
    <div class="card-header"><div class="card-title"><span class="icon">🏛️</span> Thống kê theo Chi bộ</div></div>
    <div class="card-body" style="padding:0;">
      <table class="data-table">
        <thead><tr><th>Chi bộ</th><th>Tổng</th><th>Đã kết nạp</th><th>Tỷ lệ</th></tr></thead>
        <tbody>
          <?php foreach ($byChibo as $cb):
            $pct = $cb['so_luong'] > 0 ? round($cb['da_ket_nap'] / $cb['so_luong'] * 100) : 0;
          ?>
          <tr>
            <td style="font-size:12px;"><?= e($cb['chi_bo_cong_nhan']) ?></td>
            <td><span class="badge badge-red"><?= $cb['so_luong'] ?></span></td>
            <td><span class="badge badge-green"><?= $cb['da_ket_nap'] ?></span></td>
            <td>
              <div style="display:flex;align-items:center;gap:8px;">
                <div style="flex:1;background:var(--bg3);border-radius:3px;height:5px;">
                  <div style="width:<?= $pct ?>%;height:100%;background:var(--success);border-radius:3px;"></div>
                </div>
                <span style="font-size:11px;color:var(--gold)"><?= $pct ?>%</span>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Top ĐV giúp đỡ -->
  <div class="card fade-in">
    <div class="card-header"><div class="card-title"><span class="icon">🤝</span> Đảng viên giúp đỡ nhiều nhất</div></div>
    <div class="card-body">
      <?php foreach ($topDV as $i => $dv):
        $max = $topDV[0]['so_luong'];
        $pct = round($dv['so_luong'] / $max * 100);
        $medals = ['🥇','🥈','🥉','4️⃣','5️⃣'];
      ?>
      <div style="margin-bottom:16px;">
        <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
          <span style="font-size:13px;font-weight:600;"><?= $medals[$i] ?> <?= e($dv['dang_vien_giup_do']) ?></span>
          <span style="color:var(--gold);font-weight:700;"><?= $dv['so_luong'] ?></span>
        </div>
        <div style="background:var(--bg3);border-radius:4px;height:6px;">
          <div style="width:<?= $pct ?>%;height:100%;background:linear-gradient(90deg,var(--red),var(--gold));border-radius:4px;"></div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (empty($topDV)): ?>
      <div class="empty-state" style="padding:20px;"><div class="icon">📊</div><p>Chưa có dữ liệu</p></div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.color = '#a0a0b8';
Chart.defaults.font.family = 'Roboto';

// Thêm mới theo tháng
new Chart('chartThemMoi', {
  type:'line',
  data:{
    labels: <?= json_encode(array_column($byMonth,'thang')) ?>,
    datasets:[{
      label:'Thêm mới',
      data: <?= json_encode(array_column($byMonth,'so_luong')) ?>,
      borderColor:'#C8102E',backgroundColor:'rgba(200,16,46,0.1)',
      tension:0.4,fill:true,pointBackgroundColor:'#C8102E',pointRadius:4
    }]
  },
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},
    scales:{x:{grid:{color:'rgba(255,255,255,0.04)'}},y:{beginAtZero:true,grid:{color:'rgba(255,255,255,0.04)'},ticks:{precision:0}}}}
});

// Giới tính
new Chart('chartGioiTinh',{
  type:'doughnut',
  data:{
    labels:<?= json_encode(array_column($byGioiTinh,'gioi_tinh')) ?>,
    datasets:[{data:<?= json_encode(array_column($byGioiTinh,'so_luong')) ?>,
               backgroundColor:['#3b82f6','#ec4899','#6b7280'],borderColor:'#16161f',borderWidth:3}]
  },
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{padding:12,font:{size:11}}}}}
});

// Kết nạp theo tháng
new Chart('chartKetNap',{
  type:'bar',
  data:{
    labels:<?= json_encode(array_column($ketNapByMonth,'thang')) ?>,
    datasets:[{label:'Kết nạp',data:<?= json_encode(array_column($ketNapByMonth,'so_luong')) ?>,
               backgroundColor:'rgba(34,197,94,0.6)',borderColor:'#22c55e',borderWidth:2,borderRadius:6}]
  },
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},
    scales:{x:{grid:{color:'rgba(255,255,255,0.04)'}},y:{beginAtZero:true,grid:{color:'rgba(255,255,255,0.04)'},ticks:{precision:0}}}}
});

// Lớp
new Chart('chartLop',{
  type:'bar',
  data:{
    labels:<?= json_encode(array_column($byLop,'lop')) ?>,
    datasets:[{label:'Số lượng',data:<?= json_encode(array_column($byLop,'so_luong')) ?>,
               backgroundColor:'rgba(255,215,0,0.5)',borderColor:'#FFD700',borderWidth:2,borderRadius:6}]
  },
  options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},
    scales:{x:{beginAtZero:true,grid:{color:'rgba(255,255,255,0.04)'},ticks:{precision:0}},y:{grid:{color:'rgba(255,255,255,0.04)'}}}}
});
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
