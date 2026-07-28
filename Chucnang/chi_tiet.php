<?php
// Chucnang/chi_tiet.php – Xem chi tiết hồ sơ
require_once dirname(__DIR__) . '/config.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { setFlash('danger','ID không hợp lệ'); redirect(BASE_URL.'Chucnang/danh_sach.php'); }

$db = getDB();
$row = $db->prepare("SELECT * FROM doi_tuong WHERE id = ?");
$row->execute([$id]);
$dt = $row->fetch();
if (!$dt) { setFlash('danger','Không tìm thấy đối tượng'); redirect(BASE_URL.'Chucnang/danh_sach.php'); }

// History
$hist = $db->prepare("SELECT * FROM lich_su WHERE doi_tuong_id = ? ORDER BY thoi_gian DESC LIMIT 20");
$hist->execute([$id]);
$history = $hist->fetchAll();

$pageTitle = 'Chi tiết: ' . $dt['ho_ten'];

// Determine timeline steps status
function stepDone($val): bool { return !empty($val); }

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="page-header">
  <div class="page-header-left">
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>index.php">Dashboard</a><span class="sep">›</span>
      <a href="danh_sach.php">Danh sách</a><span class="sep">›</span>
      <span class="current">Chi tiết</span>
    </div>
    <div class="page-title">👤 <?= e($dt['ho_ten']) ?></div>
    <div style="display:flex;align-items:center;gap:10px;margin-top:6px;">
      <?php
        $s = $dt['trang_thai'];
        $cls = $s==='Đã kết nạp'?'green':($s==='Đang theo dõi'?'gold':($s==='Đã chuyển'?'blue':'gray'));
      ?>
      <span class="badge badge-<?= $cls ?>"><?= e($s) ?></span>
      <?php if ($dt['lop']): ?><span style="color:var(--text2);font-size:13px;">📚 <?= e($dt['lop']) ?></span><?php endif; ?>
      <?php if ($dt['ma_gvsv']): ?><code style="color:var(--gold);font-size:12px;"><?= e($dt['ma_gvsv']) ?></code><?php endif; ?>
    </div>
  </div>
  <div style="display:flex;gap:10px;">
    <a href="sua.php?id=<?= $id ?>" class="btn btn-primary">✏️ Sửa</a>
    <button onclick="confirmDelete()" class="btn btn-danger">🗑️ Xóa</button>
    <a href="danh_sach.php" class="btn btn-outline">← Danh sách</a>
  </div>
</div>

<!-- Progress Timeline -->
<div class="card fade-in" style="margin-bottom:20px;">
  <div class="card-header">
    <div class="card-title"><span class="icon">🗺️</span> Tiến trình kết nạp</div>
  </div>
  <div class="card-body">
    <div class="steps">
      <div class="step-item <?= stepDone($dt['ngay_hop_cam_tinh'])?'done':($s==='Đang theo dõi'?'active':'') ?>">
        <div class="step-circle">1</div>
        <div class="step-label">Cảm tình Đảng</div>
        <div class="step-line"></div>
      </div>
      <div class="step-item <?= stepDone($dt['ngay_phan_cong_giup_do'])?'done':'' ?>">
        <div class="step-circle">2</div>
        <div class="step-label">Phân công giúp đỡ</div>
        <div class="step-line"></div>
      </div>
      <div class="step-item <?= stepDone($dt['ngay_cap_cc'])?'done':'' ?>">
        <div class="step-circle">3</div>
        <div class="step-label">Cấp chứng chỉ BD</div>
        <div class="step-line"></div>
      </div>
      <div class="step-item <?= stepDone($dt['ngay_ket_nap'])?'done':($s==='Đã kết nạp'?'active':'') ?>">
        <div class="step-circle">⭐</div>
        <div class="step-label">Kết nạp Đảng</div>
        <div class="step-line"></div>
      </div>
      <div class="step-item <?= stepDone($dt['ngay_chuyen_sinh_hoat'])?'done':'' ?>">
        <div class="step-circle">5</div>
        <div class="step-label">Chuyển sinh hoạt</div>
      </div>
    </div>
  </div>
</div>

<!-- Tabs -->
<div class="tabs">
  <button class="tab-btn active" onclick="showTab('thongtin',this)">📋 Thông tin</button>
  <button class="tab-btn" onclick="showTab('quytrinh',this)">📅 Quy trình</button>
  <button class="tab-btn" onclick="showTab('lichsu',this)">🕒 Lịch sử</button>
</div>

<!-- Tab: Thông tin -->
<div class="tab-content active" id="tab-thongtin">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    <div class="card">
      <div class="card-header"><div class="card-title"><span class="icon">👤</span> Thông tin cá nhân</div></div>
      <div class="card-body">
        <div class="info-grid">
          <?php
          $personalFields = [
            'Mã GV/SV'   => $dt['ma_gvsv'],
            'Họ và tên'   => $dt['ho_ten'],
            'Số ĐT'       => $dt['sdt'],
            'Giới tính'   => $dt['gioi_tinh'],
            'Ngày sinh'   => formatDate($dt['ngay_sinh']),
            'Dân tộc'     => $dt['dan_toc'],
            'Quê quán'    => $dt['que_quan'],
            'Chức vụ'     => $dt['chuc_vu'],
            'Lớp'         => $dt['lop'],
          ];
          foreach ($personalFields as $label => $value):
          ?>
          <div class="info-item">
            <div class="info-label"><?= $label ?></div>
            <div class="info-value <?= empty($value)?'empty':'' ?>"><?= e($value ?: '—') ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><div class="card-title"><span class="icon">🏛️</span> Chi bộ & Tổ chức</div></div>
      <div class="card-body">
        <div class="info-grid">
          <?php
          $orgFields = [
            'Chi bộ công nhận'     => $dt['chi_bo_cong_nhan'],
            'Số BC cảm tình Đảng'  => $dt['so_bc_cam_tinh'],
            'Ngày họp CB'          => formatDate($dt['ngay_hop_cam_tinh']),
            'ĐV giúp đỡ'           => $dt['dang_vien_giup_do'],
            'Ngày phân công'       => formatDate($dt['ngay_phan_cong_giup_do']),
            'Mã số'                => $dt['ma_so'],
          ];
          foreach ($orgFields as $label => $value):
          ?>
          <div class="info-item">
            <div class="info-label"><?= $label ?></div>
            <div class="info-value <?= empty($value)?'empty':'' ?>"><?= e($value ?: '—') ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php if ($dt['ghi_chu']): ?>
        <div class="divider"></div>
        <div class="info-item">
          <div class="info-label">Ghi chú</div>
          <div class="info-value"><?= nl2br(e($dt['ghi_chu'])) ?></div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Tab: Quy trình -->
<div class="tab-content" id="tab-quytrinh">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    <div class="card">
      <div class="card-header"><div class="card-title"><span class="icon">📚</span> Lớp Bồi dưỡng</div></div>
      <div class="card-body">
        <div class="info-grid">
          <?php
          $bdFields = [
            'Số QĐ mở lớp'         => $dt['so_qd_mo_lop'],
            'Ngày QĐ mở lớp'       => formatDate($dt['ngay_qd_mo_lop']),
            'Thời gian lớp BD'     => $dt['tg_lop_boi_duong'],
            'Ngày cấp CC'          => formatDate($dt['ngay_cap_cc']),
            'Số QĐ CC BD'          => $dt['so_qd_cc'],
            'Đơn vị cấp CC'        => $dt['don_vi_cap_cc'],
            'ĐV công tác khi cấp CC' => $dt['ten_dv_congtac_khi_cap_cc'],
            'CB sinh hoạt khi cấp CC' => $dt['ten_chibo_khi_cap_cc'],
            'Đảng uỷ khi cấp CC'  => $dt['ten_danguy_khi_cap_cc'],
            'Tỉnh uỷ khi cấp CC'  => $dt['ten_tinhuy_khi_cap_cc'],
          ];
          foreach ($bdFields as $label => $value):
          ?>
          <div class="info-item">
            <div class="info-label"><?= $label ?></div>
            <div class="info-value <?= empty($value)?'empty':'' ?>"><?= e($value ?: '—') ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><div class="card-title"><span class="icon">⭐</span> Kết nạp & Chuyển SH</div></div>
      <div class="card-body">
        <div class="info-grid">
          <?php
          $knFields = [
            'Kết nạp Đảng'         => $dt['ket_nap_dang'],
            'Ngày quyết định'      => formatDate($dt['ngay_quyet_dinh']),
            'Số QĐ kết nạp'        => $dt['so_qd_ket_nap'],
            'Ngày kết nạp'         => formatDate($dt['ngay_ket_nap']),
            'ĐV hướng dẫn'         => $dt['dang_vien_huong_dan'],
            'Ngày chuyển SH'       => formatDate($dt['ngay_chuyen_sinh_hoat']),
            'Nơi chuyển tới'       => $dt['noi_chuyen_toi'],
          ];
          foreach ($knFields as $label => $value):
          ?>
          <div class="info-item">
            <div class="info-label"><?= $label ?></div>
            <div class="info-value <?= empty($value)?'empty':'' ?>"><?= e($value ?: '—') ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Tab: Lịch sử -->
<div class="tab-content" id="tab-lichsu">
  <div class="card">
    <div class="card-header"><div class="card-title"><span class="icon">🕒</span> Lịch sử thao tác</div></div>
    <div class="card-body">
      <?php if (empty($history)): ?>
      <div class="empty-state" style="padding:30px;">
        <div class="icon">📄</div>
        <p>Chưa có lịch sử</p>
      </div>
      <?php else: ?>
      <div class="timeline">
        <?php foreach ($history as $h): ?>
        <div class="timeline-item done">
          <div class="timeline-date"><?= e($h['thoi_gian']) ?></div>
          <div class="timeline-title"><?= e($h['hanh_dong']) ?></div>
          <div class="timeline-content"><?= e($h['mo_ta']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal">
    <div class="modal-title">🗑️ Xác nhận xóa</div>
    <div class="modal-body">Bạn có chắc muốn xóa hồ sơ của <strong><?= e($dt['ho_ten']) ?></strong>? Hành động này <strong style="color:var(--danger)">không thể hoàn tác</strong>.</div>
    <div class="modal-actions">
      <button onclick="document.getElementById('deleteModal').classList.remove('open')" class="btn btn-outline">Hủy</button>
      <a href="xoa.php?id=<?= $id ?>" class="btn btn-danger">🗑️ Xóa</a>
    </div>
  </div>
</div>

<script>
function confirmDelete() { document.getElementById('deleteModal').classList.add('open'); }

function showTab(name, btn) {
  document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + name).classList.add('active');
  btn.classList.add('active');
}
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
