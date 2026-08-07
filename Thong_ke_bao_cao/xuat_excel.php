<?php
// Thong_ke_bao_cao/xuat_excel.php – Xuất dữ liệu Excel/PDF qua Python API
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/User/auth.php';
requireRole(['Quản lý', 'Admin']);
$pageTitle = 'Xuất dữ liệu';

$db = getDB();

// Fetch filter options directly via PHP so page works even if Python API is offline
$lopList = $db->query("SELECT DISTINCT lop FROM doi_tuong WHERE lop IS NOT NULL AND lop!='' ORDER BY lop")->fetchAll(PDO::FETCH_COLUMN);
$chiBoList = $db->query("SELECT DISTINCT chi_bo_cong_nhan FROM doi_tuong WHERE chi_bo_cong_nhan IS NOT NULL AND chi_bo_cong_nhan!='' ORDER BY chi_bo_cong_nhan")->fetchAll(PDO::FETCH_COLUMN);
$totalObjects = $db->query("SELECT COUNT(*) FROM doi_tuong")->fetchColumn();

// Fetch initial list of all objects to display on load
$initialObjects = $db->query("SELECT id, ma_gvsv, ho_ten, gioi_tinh, lop, chi_bo_cong_nhan, trang_thai, avatar FROM doi_tuong ORDER BY ho_ten")->fetchAll(PDO::FETCH_ASSOC);

require_once dirname(__DIR__) . '/Giao_dien/header.php';
?>

<div class="page-header">
  <div class="page-header-left">
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>index.php">Dashboard</a><span class="sep">›</span>
      <span class="current">Xuất dữ liệu</span>
    </div>
    <div class="page-title">📤 Xuất <span>dữ liệu báo cáo</span></div>
    <div class="page-subtitle">Chọn phạm vi và loại xuất để tải tệp Excel hoặc tài liệu PDF</div>
  </div>
</div>

<!-- API Status Banner -->
<div id="apiBanner" class="flash flash-warning" style="display:none;">
  ⚠️ Python API chưa chạy. <strong>Hãy mở <code>python_api/start_api.bat</code></strong> để có thể xuất dữ liệu.
  <button onclick="checkApi()" style="margin-left:12px;" class="btn btn-warning btn-sm">🔄 Thử lại</button>
</div>
<div id="apiOk" class="flash flash-success" style="display:none;">
  ✅ Python API đang chạy – Sẵn sàng xuất dữ liệu!
</div>

<!-- Wizard Layout -->
<div style="display:grid;grid-template-columns:340px 1fr;gap:20px;align-items:start;">

  <!-- LEFT: Controls -->
  <div style="display:flex;flex-direction:column;gap:16px;">

    <!-- Step 1: Scope -->
    <div class="card fade-in">
      <div class="card-header">
        <div class="card-title">
          <span style="background:var(--red);color:#fff;width:24px;height:24px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">1</span>
          Chọn phạm vi lọc
        </div>
      </div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
        <label class="radio-card" onclick="setScope('all')">
          <input type="radio" name="scope" value="all" checked> 🏫 Toàn trường
          <span class="badge badge-red" style="margin-left:auto;"><?= $totalObjects ?> người</span>
        </label>
        <label class="radio-card" onclick="setScope('lop')">
          <input type="radio" name="scope" value="lop"> 📚 Theo lớp
        </label>
        <div id="lop-select-wrap" style="display:none;padding-left:16px;">
          <select id="lopSelect" class="form-control" onchange="loadListPHP()">
            <option value="">-- Chọn lớp --</option>
            <?php foreach ($lopList as $l): ?>
            <option value="<?= e($l) ?>"><?= e($l) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <label class="radio-card" onclick="setScope('chibo')">
          <input type="radio" name="scope" value="chibo"> 🏛️ Theo chi bộ
        </label>
        <div id="chibo-select-wrap" style="display:none;padding-left:16px;">
          <select id="chiboSelect" class="form-control" onchange="loadListPHP()">
            <option value="">-- Chọn chi bộ --</option>
            <?php foreach ($chiBoList as $c): ?>
            <option value="<?= e($c) ?>"><?= e($c) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>

    <!-- Step 2: Export type -->
    <div class="card fade-in">
      <div class="card-header">
        <div class="card-title">
          <span style="background:var(--red);color:#fff;width:24px;height:24px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">2</span>
          Chọn định dạng xuất
        </div>
      </div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
        <label class="radio-card" onclick="setType(1)">
          <input type="radio" name="etype" value="1" checked>
          <div>
            <div style="font-weight:700;font-size:13px;">📊 Loại 1: Xuất Excel toàn bộ</div>
            <div style="font-size:11px;color:var(--text2);margin-top:2px;">Xuất danh sách cấp ủy, thông tin tất cả cột.</div>
          </div>
        </label>
        <label class="radio-card" onclick="setType(2)">
          <input type="radio" name="etype" value="2">
          <div>
            <div style="font-weight:700;font-size:13px;">📄 Loại 2: Xuất PDF hồ sơ chi tiết</div>
            <div style="font-size:11px;color:var(--text2);margin-top:2px;">Xuất file PDF hồ sơ cá nhân của 1 người.</div>
          </div>
        </label>
        <label class="radio-card" onclick="setType(3)">
          <input type="radio" name="etype" value="3">
          <div>
            <div style="font-weight:700;font-size:13px;">📋 Loại 3: Xuất PDF danh sách chọn</div>
            <div style="font-size:11px;color:var(--text2);margin-top:2px;">Chọn nhiều người để xuất danh sách PDF.</div>
          </div>
        </label>
      </div>
    </div>

    <!-- Step 3: Action -->
    <div class="card fade-in">
      <div class="card-header">
        <div class="card-title">
          <span style="background:var(--gold);color:#111;width:24px;height:24px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">3</span>
          Tải tài liệu
        </div>
      </div>
      <div class="card-body">
        <div id="selCount" style="font-size:12px;color:var(--text2);margin-bottom:12px;"></div>
        <button id="btnExport" onclick="doExport()" class="btn btn-gold btn-lg" style="width:100%;justify-content:center;" disabled>
          📥 Xuất tài liệu
        </button>
        <div id="exportSpinner" style="display:none;text-align:center;margin-top:12px;">
          <span style="color:var(--text2);font-size:12px;">⏳ Đang tạo file tải về…</span>
        </div>
      </div>
    </div>

  </div>

  <!-- RIGHT: Person list -->
  <div class="card fade-in" id="listCard">
    <div class="card-header">
      <div class="card-title"><span class="icon">👥</span> Danh sách quần chúng <span id="listCount" style="color:var(--text2);font-size:13px;font-weight:400;"></span></div>
      <div style="display:flex;gap:8px;align-items:center;">
        <input type="text" id="filterInput" class="form-control" style="max-width:200px;padding:6px 12px;"
               placeholder="🔍 Lọc nhanh..." oninput="filterTable()">
        <button onclick="selectAll()" class="btn btn-outline btn-sm" id="selAllBtn" style="display:none;">Chọn tất cả</button>
        <button onclick="deselectAll()" class="btn btn-outline btn-sm" id="deselAllBtn" style="display:none;">Bỏ chọn</button>
      </div>
    </div>
    <div class="card-body" style="padding:0;">
      <div id="listBody">
        <!-- Rendered by JS -->
      </div>
    </div>
  </div>
</div>

<style>
.radio-card {
  display:flex; align-items:flex-start; gap:10px;
  padding:12px 14px; border-radius:10px;
  border:1px solid var(--border);
  cursor:pointer; transition:all 0.2s;
  user-select:none;
}
.radio-card:hover { border-color:var(--red); background:rgba(200,16,46,0.05); }
.radio-card input[type=radio] { margin-top:2px; accent-color:var(--red); }
.radio-card input[type=radio]:checked + div,
.radio-card input[type=radio]:checked + span { color:var(--text); }
.radio-card:has(input:checked) { border-color:var(--gold); background:rgba(255,215,0,0.06); }

.person-row { display:flex; align-items:center; gap:12px; padding:10px 18px; border-bottom:1px solid var(--border); transition:background 0.15s; }
.person-row:hover { background:rgba(200,16,46,0.04); }
.person-row.hidden { display:none; }
.person-avatar-sm { width:36px; height:36px; border-radius:50%; object-fit:cover; border:2px solid var(--border); flex-shrink:0; }
.person-avatar-sm.default { background:linear-gradient(135deg,var(--red),var(--gold)); display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; color:#fff; }
.person-name { font-weight:600; font-size:13.5px; color:var(--text); }
.person-meta { font-size:11px; color:var(--text2); margin-top:2px; }
</style>

<script>
// Load initial data printed by PHP
var allObjects = <?= json_encode($initialObjects) ?>;
var currentList = [...allObjects];
var currentScope = 'all';
var currentType  = 1;
var apiOnline = false;

// ── API health check ──────────────────────────────────────────────────────────
function checkApi() {
  fetch('api_proxy.php?path=health')
    .then(r => r.json())
    .then(d => {
      if (d.status === 'ok') {
        document.getElementById('apiBanner').style.display = 'none';
        document.getElementById('apiOk').style.display = 'flex';
        apiOnline = true;
      } else {
        throw new Error();
      }
      updateExportBtn();
    })
    .catch(() => {
      document.getElementById('apiBanner').style.display = 'flex';
      document.getElementById('apiOk').style.display = 'none';
      apiOnline = false;
      updateExportBtn();
    });
}

// ── Scope selection ──────────────────────────────────────────────────────────
function setScope(s) {
  currentScope = s;
  document.getElementById('lop-select-wrap').style.display  = s === 'lop'   ? 'block' : 'none';
  document.getElementById('chibo-select-wrap').style.display = s === 'chibo' ? 'block' : 'none';
  document.querySelectorAll('input[name=scope]').forEach(r => r.checked = r.value === s);
  loadListPHP();
}

function setType(t) {
  currentType = t;
  document.querySelectorAll('input[name=etype]').forEach(r => r.checked = parseInt(r.value) === t);
  renderList();
  updateExportBtn();
}

// ── Load list locally using PHP values ──────────────────────────────────────
function loadListPHP() {
  var ft = currentScope;
  if (ft === 'all') {
    currentList = [...allObjects];
  } else if (ft === 'lop') {
    var val = document.getElementById('lopSelect').value;
    if (!val) currentList = [];
    else currentList = allObjects.filter(p => p.lop === val);
  } else if (ft === 'chibo') {
    var val = document.getElementById('chiboSelect').value;
    if (!val) currentList = [];
    else currentList = allObjects.filter(p => p.chi_bo_cong_nhan === val);
  }
  renderList();
  updateExportBtn();
}

// ── Render list ──────────────────────────────────────────────────────────────
function renderList() {
  var list = currentList;
  var type = currentType;
  
  document.getElementById('listCount').textContent = list.length ? `(${list.length} người)` : '';
  document.getElementById('selAllBtn').style.display  = (type === 3 && list.length) ? 'inline-flex' : 'none';
  document.getElementById('deselAllBtn').style.display = (type === 3 && list.length) ? 'inline-flex' : 'none';

  if (!list.length) {
    document.getElementById('listBody').innerHTML = '<div class="empty-state" style="padding:60px 20px;"><div class="icon">📂</div><h3>Không có dữ liệu</h3><p>Chọn phạm vi/bộ lọc để hiển thị danh sách</p></div>';
    updateSelCount();
    return;
  }

  var html = '';
  list.forEach((p, i) => {
    var initials = (p.ho_ten || '?').split(' ').slice(-2).map(w=>w[0]).join('').toUpperCase();
    var badge_class = p.trang_thai === 'Đã kết nạp' ? 'badge-green' : (p.trang_thai === 'Đang theo dõi' ? 'badge-gold' : 'badge-gray');
    var avatar_html = p.avatar
      ? `<img src="<?= BASE_URL ?>${esc(p.avatar)}" class="person-avatar-sm" alt="">`
      : `<div class="person-avatar-sm default">${initials}</div>`;

    var control = '';
    if (type === 2) {
      control = `<input type="radio" name="pid" value="${p.id}" style="accent-color:var(--red);" onchange="updateSelCount()">`;
    } else if (type === 3) {
      control = `<input type="checkbox" class="pcheck" value="${p.id}" style="accent-color:var(--red);width:16px;height:16px;" onchange="updateSelCount()">`;
    }

    html += `<div class="person-row" data-search="${(p.ho_ten+' '+p.lop+' '+(p.ma_gvsv||'')).toLowerCase()}" onclick="rowClick(event, ${p.id})">
      ${control}
      ${avatar_html}
      <div style="flex:1;min-width:0;margin-left:8px;">
        <div class="person-name">${esc(p.ho_ten)}</div>
        <div class="person-meta">${esc(p.ma_gvsv||'—')} · ${esc(p.lop||'—')}</div>
      </div>
      <div style="text-align:right;">
        <span class="badge ${badge_class}" style="font-size:10px;">${esc(p.trang_thai)}</span>
      </div>
    </div>`;
  });
  document.getElementById('listBody').innerHTML = html;
  updateSelCount();
}

function rowClick(e, pid) {
  // If user clicked input, do nothing
  if (e.target.tagName === 'INPUT') return;
  var row = e.currentTarget;
  if (currentType === 2) {
    var rad = row.querySelector('input[type=radio]');
    if (rad) { rad.checked = true; updateSelCount(); }
  } else if (currentType === 3) {
    var chk = row.querySelector('input[type=checkbox]');
    if (chk) { chk.checked = !chk.checked; updateSelCount(); }
  }
}

// ── Search Table ──────────────────────────────────────────────────────────────
function filterTable() {
  var q = document.getElementById('filterInput').value.toLowerCase();
  document.querySelectorAll('.person-row').forEach(row => {
    row.classList.toggle('hidden', q && !row.dataset.search.includes(q));
  });
}

// ── Selection helper ──────────────────────────────────────────────────────────
function selectAll() {
  document.querySelectorAll('.pcheck').forEach(c => c.checked = true);
  updateSelCount();
}
function deselectAll() {
  document.querySelectorAll('.pcheck').forEach(c => c.checked = false);
  updateSelCount();
}

// ── Selection Counter ─────────────────────────────────────────────────────────
function updateSelCount() {
  var msg = '';
  if (currentType === 3) {
    var checked = document.querySelectorAll('.pcheck:checked').length;
    msg = checked ? `Đã chọn: <strong>${checked}</strong> người` : 'Chưa chọn ai';
  } else if (currentType === 2) {
    var radioChecked = !!document.querySelector('input[name=pid]:checked');
    msg = radioChecked ? '✅ Đã chọn 1 người' : 'Chưa chọn ai';
  } else if (currentType === 1) {
    msg = currentList.length ? `Sẽ xuất: <strong>${currentList.length}</strong> người` : '';
  }
  document.getElementById('selCount').innerHTML = msg;
  updateExportBtn();
}

function updateExportBtn() {
  if (!apiOnline) {
    document.getElementById('btnExport').disabled = true;
    return;
  }
  var ok = false;
  if (currentType === 1) ok = currentList.length > 0;
  else if (currentType === 2) ok = !!document.querySelector('input[name=pid]:checked');
  else if (currentType === 3) ok = document.querySelectorAll('.pcheck:checked').length > 0;
  document.getElementById('btnExport').disabled = !ok;
}

// ── Do Export (Fetch File) ────────────────────────────────────────────────────
function doExport() {
  var btn = document.getElementById('btnExport');
  var spin = document.getElementById('exportSpinner');
  btn.disabled = true;
  spin.style.display = 'block';

  var ft = currentScope;
  var fv = ft === 'lop' ? document.getElementById('lopSelect').value
         : ft === 'chibo' ? document.getElementById('chiboSelect').value : '';

  if (currentType === 1) {
    // Excel - POST → /api/export/all
    fetchDownload('api_proxy.php?path=api/export/all', {filter_type: ft, filter_value: fv});
  } else if (currentType === 2) {
    // PDF (Single Profile) - GET → /api/export/single/{id}
    var pid = document.querySelector('input[name=pid]:checked')?.value;
    if (!pid) return done();
    window.location = `api_proxy.php?path=api/export/single/${pid}`;
    setTimeout(done, 1500);
  } else if (currentType === 3) {
    // PDF (Selected List) - POST → /api/export/selected
    var ids = [...document.querySelectorAll('.pcheck:checked')].map(c => parseInt(c.value));
    fetchDownload('api_proxy.php?path=api/export/selected', {ids});
  }

  function done() { btn.disabled = false; spin.style.display = 'none'; updateExportBtn(); }

  function fetchDownload(url, body) {
    fetch(url, {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(body)
    })
    .then(r => {
      if (!r.ok) throw new Error("Mã trạng thái: " + r.status);
      var cd = r.headers.get('Content-Disposition') || '';
      var fname = currentType === 1 ? 'DanhSach_Excel.xlsx' : 'TaiLieu.pdf';
      var m = cd.match(/filename="?([^";]+)/);
      if (m) fname = m[1];
      return r.blob().then(b => ({blob: b, fname}));
    })
    .then(({blob, fname}) => {
      var a = document.createElement('a');
      a.href = URL.createObjectURL(blob);
      a.download = fname;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      done();
    })
    .catch(e => { 
      alert('Lỗi xuất dữ liệu: ' + e.message + '\nKiểm tra lại xem Python Flask đã được khởi chạy chưa.'); 
      done(); 
    });
  }
}

function esc(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Startup ──────────────────────────────────────────────────────────────────
renderList();
checkApi();
</script>

<?php require_once dirname(__DIR__) . '/Giao_dien/footer.php'; ?>
