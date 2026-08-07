<?php
// Thong_ke_bao_cao/tim_kiem.php – Tìm kiếm nâng cao
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/User/auth.php';
requireRole(['Quản lý', 'Admin']);
$pageTitle = 'Tìm kiếm nâng cao';
$db = getDB();

$searched = false;
$results  = [];

$q_ten       = trim($_GET['ten'] ?? '');
$q_lop       = trim($_GET['lop'] ?? '');
$q_chibo     = trim($_GET['chi_bo'] ?? '');
$q_tt        = $_GET['trang_thai'] ?? '';
$q_dvgd      = trim($_GET['dang_vien_giup_do'] ?? '');
$q_from_ns   = $_GET['ns_from'] ?? '';
$q_to_ns     = $_GET['ns_to'] ?? '';
$q_from_kn   = $_GET['kn_from'] ?? '';
$q_to_kn     = $_GET['kn_to'] ?? '';

if (!empty($_GET)) {
    $searched = true;
    $where = ['1=1']; $params = [];
    if ($q_ten)    { $where[] = "ho_ten LIKE ?"; $params[] = "%$q_ten%"; }
    if ($q_lop)    { $where[] = "lop LIKE ?"; $params[] = "%$q_lop%"; }
    if ($q_chibo)  { $where[] = "chi_bo_cong_nhan LIKE ?"; $params[] = "%$q_chibo%"; }
    if ($q_tt)     { $where[] = "trang_thai = ?"; $params[] = $q_tt; }
    if ($q_dvgd)   { $where[] = "dang_vien_giup_do LIKE ?"; $params[] = "%$q_dvgd%"; }
    if ($q_from_ns){ $where[] = "ngay_sinh >= ?"; $params[] = $q_from_ns; }
    if ($q_to_ns)  { $where[] = "ngay_sinh <= ?"; $params[] = $q_to_ns; }
    if ($q_from_kn){ $where[] = "ngay_ket_nap >= ?"; $params[] = $q_from_kn; }
    if ($q_to_kn)  { $where[] = "ngay_ket_nap <= ?"; $params[] = $q_to_kn; }

    $ws = implode(' AND ', $where);
    $stmt = $db->prepare("SELECT * FROM doi_tuong WHERE $ws ORDER BY ho_ten LIMIT 100");
    $stmt->execute($params);
    $results = $stmt->fetchAll();
}

$lopList  = $db->query("SELECT DISTINCT lop FROM doi_tuong WHERE lop != '' AND lop IS NOT NULL ORDER BY lop")->fetchAll(PDO::FETCH_COLUMN);
$chiBoList = $db->query("SELECT DISTINCT chi_bo_cong_nhan FROM doi_tuong WHERE chi_bo_cong_nhan != '' AND chi_bo_cong_nhan IS NOT NULL ORDER BY chi_bo_cong_nhan")->fetchAll(PDO::FETCH_COLUMN);
$dvList   = $db->query("SELECT DISTINCT dang_vien_giup_do FROM doi_tuong WHERE dang_vien_giup_do != '' AND dang_vien_giup_do IS NOT NULL ORDER BY dang_vien_giup_do")->fetchAll(PDO::FETCH_COLUMN);

require_once dirname(__DIR__) . '/Giao_dien/header.php';
?>

<div class="page-header">
  <div class="page-header-left">
    <div class="breadcrumb"><a href="<?= BASE_URL ?>index.php">Dashboard</a><span class="sep">›</span><span class="current">Tìm kiếm nâng cao</span></div>
    <div class="page-title">🔍 Tìm kiếm <span>Nâng cao</span></div>
  </div>
</div>

<!-- Search Form -->
<div class="card fade-in" style="margin-bottom:20px;">
  <div class="card-header"><div class="card-title"><span class="icon">🔍</span> Bộ lọc tìm kiếm</div></div>
  <div class="card-body">
    <form method="get">
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Họ và tên</label>
          <input type="text" name="ten" class="form-control" value="<?= e($q_ten) ?>" placeholder="Nhập một phần tên...">
        </div>
        <div class="form-group">
          <label class="form-label">Lớp</label>
          <input type="text" name="lop" class="form-control" list="lop-list" value="<?= e($q_lop) ?>" placeholder="VD: K63">
          <datalist id="lop-list"><?php foreach($lopList as $l): ?><option value="<?= e($l) ?>"><?php endforeach; ?></datalist>
        </div>
        <div class="form-group">
          <label class="form-label">Chi bộ</label>
          <input type="text" name="chi_bo" class="form-control" list="chibo-list" value="<?= e($q_chibo) ?>">
          <datalist id="chibo-list"><?php foreach($chiBoList as $cb): ?><option value="<?= e($cb) ?>"><?php endforeach; ?></datalist>
        </div>
        <div class="form-group">
          <label class="form-label">Trạng thái</label>
          <select name="trang_thai" class="form-control">
            <option value="">Tất cả</option>
            <?php foreach(['Đang theo dõi','Đã kết nạp','Đã chuyển','Tạm dừng'] as $tt): ?>
            <option value="<?= $tt ?>" <?= $q_tt===$tt?'selected':'' ?>><?= $tt ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Đảng viên giúp đỡ</label>
          <input type="text" name="dang_vien_giup_do" class="form-control" list="dv-list" value="<?= e($q_dvgd) ?>">
          <datalist id="dv-list"><?php foreach($dvList as $dv): ?><option value="<?= e($dv) ?>"><?php endforeach; ?></datalist>
        </div>
      </div>
      <div class="form-grid" style="margin-top:12px;">
        <div class="form-group">
          <label class="form-label">Ngày sinh – Từ</label>
          <input type="date" name="ns_from" class="form-control" value="<?= e($q_from_ns) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Ngày sinh – Đến</label>
          <input type="date" name="ns_to" class="form-control" value="<?= e($q_to_ns) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Ngày kết nạp – Từ</label>
          <input type="date" name="kn_from" class="form-control" value="<?= e($q_from_kn) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Ngày kết nạp – Đến</label>
          <input type="date" name="kn_to" class="form-control" value="<?= e($q_to_kn) ?>">
        </div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
        <a href="tim_kiem.php" class="btn btn-outline">Reset</a>
        <button type="submit" class="btn btn-primary btn-lg">🔍 Tìm kiếm</button>
      </div>
    </form>
  </div>
</div>

<!-- Results -->
<?php if ($searched): ?>
<div class="card fade-in">
  <div class="card-header">
    <div class="card-title"><span class="icon">📊</span> Kết quả</div>
    <div style="display:flex;align-items:center;gap:10px;">
      <span class="badge badge-gold"><?= count($results) ?> bản ghi</span>
      <?php if (!empty($results)): ?>
      <a href="<?= BASE_URL ?>Thong_ke_bao_cao/xuat_excel.php?<?= http_build_query(array_filter(['search'=>$q_ten,'trang_thai'=>$q_tt])) ?>" class="btn btn-gold btn-sm">📤 Xuất</a>
      <?php endif; ?>
    </div>
  </div>
  <div class="card-body" style="padding:0;">
    <?php if (empty($results)): ?>
    <div class="empty-state"><div class="icon">🔍</div><h3>Không tìm thấy kết quả</h3><p>Thử điều chỉnh bộ lọc tìm kiếm</p></div>
    <?php else: ?>
    <div class="table-wrapper">
      <table class="data-table">
        <thead><tr><th>STT</th><th>Mã</th><th>Họ tên</th><th>Lớp</th><th>Chi bộ</th><th>ĐV giúp đỡ</th><th>Ngày kết nạp</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
        <tbody>
          <?php foreach ($results as $i => $r):
            $s = $r['trang_thai'];
            $cls = $s==='Đã kết nạp'?'green':($s==='Đang theo dõi'?'gold':($s==='Đã chuyển'?'blue':'gray'));
          ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td><code style="color:var(--gold);font-size:11px;"><?= e($r['ma_gvsv']??'—') ?></code></td>
            <td><a href="<?= BASE_URL ?>Quan_ly_doi_tuong/chi_tiet.php?id=<?= $r['id'] ?>" class="name-link"><?= e($r['ho_ten']) ?></a></td>
            <td><?= e($r['lop']??'—') ?></td>
            <td style="font-size:12px;"><?= e(mb_substr($r['chi_bo_cong_nhan']??'',0,25)) ?></td>
            <td style="font-size:12px;"><?= e($r['dang_vien_giup_do']??'—') ?></td>
            <td><?= $r['ngay_ket_nap'] ? '<span style="color:var(--success)">'.formatDate($r['ngay_ket_nap']).'</span>' : '—' ?></td>
            <td><span class="badge badge-<?= $cls ?>"><?= e($s) ?></span></td>
            <td>
              <div style="display:flex;gap:5px;">
                <a href="<?= BASE_URL ?>Quan_ly_doi_tuong/chi_tiet.php?id=<?= $r['id'] ?>" class="btn btn-outline btn-sm">👁️</a>
                <a href="<?= BASE_URL ?>Quan_ly_doi_tuong/sua.php?id=<?= $r['id'] ?>" class="btn btn-outline btn-sm">✏️</a>
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
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/Giao_dien/footer.php'; ?>
