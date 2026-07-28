<?php
// Chucnang/xuat_excel.php – Xuất dữ liệu Excel (3 loại) qua Python API
require_once dirname(__DIR__) . '/config.php';
$pageTitle = 'Xuất Excel';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="page-header">
  <div class="page-header-left">
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>index.php">Dashboard</a><span class="sep">›</span>
      <span class="current">Xuất Excel</span>
    </div>
    <div class="page-title">📤 Xuất dữ liệu <span>Excel</span></div>
    <div class="page-subtitle">Chọn phạm vi và loại xuất để tải file Excel</div>
  </div>
</div>

<!-- API Status Banner -->
<div id="apiBanner" class="flash flash-warning" style="display:none;">
  ⚠️ Python API chưa chạy. <strong>Hãy mở <code>python_api/start_api.bat</code></strong> để khởi động server xuất Excel.
  <button onclick="checkApi()" style="margin-left:12px;" class="btn btn-warning btn-sm">🔄 Thử lại</button>
</div>
<div id="apiOk" class="flash flash-success" style="display:none;">
  ✅ Python API đang chạy – sẵn sàng xuất Excel!
</div>

<!-- Wizard -->
<div style="display:grid;grid-template-columns:320px 1fr;gap:20px;align-items:start;">

  <!-- LEFT: Controls -->
  <div style="display:flex;flex-direction:column;gap:16px;">

    <!-- Step 1: Scope -->
    <div class="card fade-in">
      <div class="card-header">
        <div class="card-title">
          <span style="background:var(--red);color:#fff;width:24px;height:24px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">1</span>
          Chọn phạm vi
        </div>
      </div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
        <label class="radio-card" onclick="setScope('all')">
          <input type="radio" name="scope" value="all" checked> 🏫 Toàn trường
          <span id="badge-all" class="badge badge-red" style="margin-left:auto;">…</span>
        </label>
        <label class="radio-card" onclick="setScope('lop')">
          <input type="radio" name="scope" value="lop"> 📚 Theo lớp
        </label>
        <div id="lop-select-wrap" style="display:none;padding-left:16px;">
          <select id="lopSelect" class="form-control" onchange="loadList()">
            <option value="">-- Chọn lớp --</option>
          </select>
        </div>
        <label class="radio-card" onclick="setScope('chibo')">
          <input type="radio" name="scope" value="chibo"> 🏛️ Theo chi bộ
        </label>
        <div id="chibo-select-wrap" style="display:none;padding-left:16px;">
          <select id="chiboSelect" class="form-control" onchange="loadList()">
            <option value="">-- Chọn chi bộ --</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Step 2: Export type -->
    <div class="card fade-in">
      <div class="card-header">
        <div class="card-title">
          <span style="background:var(--red);color:#fff;width:24px;height:24px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">2</span>
          Loại xuất
        </div>
      </div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
        <label class="radio-card" onclick="setType(1)">
          <input type="radio" name="etype" value="1" checked>
          <div>
            <div style="font-weight:700;font-size:13px;">📋 Loại 1: Toàn bộ danh sách</div>
            <div style="font-size:11px;color:var(--text2);margin-top:2px;">Xuất tất cả người trong phạm vi đã chọn</div>
          </div>
        </label>
        <label class="radio-card" onclick="setType(2)">
          <input type="radio" name="etype" value="2">
          <div>
            <div style="font-weight:700;font-size:13px;">👤 Loại 2: Hồ sơ 1 người</div>
            <div style="font-size:11px;color:var(--text2);margin-top:2px;">Xuất hồ sơ chi tiết một đối tượng</div>
          </div>
        </label>
        <label class="radio-card" onclick="setType(3)">
          <input type="radio" name="etype" value="3">
          <div>
            <div style="font-weight:700;font-size:13px;">☑️ Loại 3: Chọn nhiều người</div>
            <div style="font-size:11px;color:var(--text2);margin-top:2px;">Đánh dấu checkbox rồi xuất</div>
          </div>
        </label>
      </div>
    </div>

    <!-- Step 3: Action -->
    <div class="card fade-in">
      <div class="card-header">
        <div class="card-title">
          <span style="background:var(--gold);color:#111;width:24px;height:24px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">3</span>
          Xuất file
        </div>
      </div>
      <div class="card-body">
        <div id="selCount" style="font-size:12px;color:var(--text2);margin-bottom:12px;"></div>
        <button id="btnExport" onclick="doExport()" class="btn btn-gold btn-lg" style="width:100%;justify-content:center;" disabled>
          📥 Xuất Excel
        </button>
        <div id="exportSpinner" style="display:none;text-align:center;margin-top:12px;">
          <span style="color:var(--text2);font-size:12px;">⏳ Đang tạo file Excel…</span>
        </div>
      </div>
    </div>

  </div>

  <!-- RIGHT: Person list -->
  <div class="card fade-in" id="listCard">
    <div class="card-header">
      <div class="card-title"><span class="icon">👥</span> Danh sách <span id="listCount" style="color:var(--text2);font-size:13px;font-weight:400;"></span></div>
      <div style="display:flex;gap:8px;align-items:center;">
        <input type="text" id="filterInput" class="form-control" style="max-width:200px;padding:6px 12px;"
               placeholder="🔍 Lọc nhanh..." oninput="filterTable()">
        <button onclick="selectAll()" class="btn btn-outline btn-sm" id="selAllBtn" style="display:none;">Chọn tất cả</button>
        <button onclick="deselectAll()" class="btn btn-outline btn-sm" id="deselAllBtn" style="display:none;">Bỏ chọn</button>
      </div>
    </div>
    <div class="card-body" style="padding:0;">
      <div id="listBody">
        <div class="empty-state" style="padding:60px 20px;">
          <div class="icon">📂</div>
          <h3>Chọn phạm vi để xem danh sách</h3>
          <p>Chọn phạm vi ở bảng bên trái để tải danh sách đối tượng</p>
        </div>
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

/* Person list rows */
.person-row { display:flex; align-items:center; gap:12px; padding:10px 18px; border-bottom:1px solid var(--border); transition:background 0.15s; }
.person-row:hover { background:rgba(200,16,46,0.04); }
.person-row.hidden { display:none; }
.person-avatar-sm { width:36px; height:36px; border-radius:50%; object-fit:cover; border:2px solid var(--border); flex-shrink:0; }
.person-avatar-sm.default { background:linear-gradient(135deg,var(--red),var(--gold)); display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; color:#fff; }
.person-name { font-weight:600; font-size:13.5px; color:var(--text); }
.person-meta { font-size:11px; color:var(--text2); margin-top:2px; }
</style>

<script>
var currentList = [];
var currentScope = 'all';
var currentType  = 1;

// ── API check ────────────────────────────────────────────────────────────────
function checkApi() {
  fetch('api_proxy.php?path=health')
    .then(r => r.json())
    .then(d => {
      if (d.status === 'ok') {
        document.getElementById('apiBanner').style.display = 'none';
        document.getElementById('apiOk').style.display = 'flex';
        loadFilters();
        updateExportBtn();
      } else throw new Error();
    })
    .catch(() => {
      document.getElementById('apiBanner').style.display = 'flex';
      document.getElementById('apiOk').style.display = 'none';
    });
}

// ── Load filter options ───────────────────────────────────────────────────────
function loadFilters() {
  fetch('api_proxy.php?path=api/filters')
    .then(r => r.json())
    .then(d => {
      if (d.error) return;
      document.getElementById('badge-all').textContent = d.total + ' người';

      var lopSel = document.getElementById('lopSelect');
      lopSel.innerHTML = '<option value="">-- Chọn lớp --</option>';
      d.lops.forEach(l => lopSel.innerHTML += `<option value="${esc(l)}">${esc(l)}</option>`);

      var cbSel = document.getElementById('chiboSelect');
      cbSel.innerHTML = '<option value="">-- Chọn chi bộ --</option>';
      d.chibos.forEach(c => cbSel.innerHTML += `<option value="${esc(c)}">${esc(c)}</option>`);

      // Auto-load for "all"
      if (currentScope === 'all') loadList();
    });
}

// ── Scope & type setters ──────────────────────────────────────────────────────
function setScope(s) {
  currentScope = s;
  document.getElementById('lop-select-wrap').style.display  = s === 'lop'   ? 'block' : 'none';
  document.getElementById('chibo-select-wrap').style.display = s === 'chibo' ? 'block' : 'none';
  document.querySelectorAll('input[name=scope]').forEach(r => r.checked = r.value === s);
  if (s === 'all') loadList();
  else renderList([]);
}

function setType(t) {
  currentType = t;
  document.querySelectorAll('input[name=etype]').forEach(r => r.checked = parseInt(r.value) === t);
  updateCheckboxes();
  updateExportBtn();
}

// ── Load list via AJAX ────────────────────────────────────────────────────────
function loadList() {
  var ft = currentScope;
  var fv = '';
  if (ft === 'lop')   fv = document.getElementById('lopSelect').value;
  if (ft === 'chibo') fv = document.getElementById('chiboSelect').value;
  if ((ft === 'lop' || ft === 'chibo') && !fv) return renderList([]);

  document.getElementById('listBody').innerHTML = '<div style="padding:40px;text-align:center;color:var(--text2)">⏳ Đang tải...</div>';
  var url = `api_proxy.php?path=api/list&filter_type=${ft}&filter_value=${encodeURIComponent(fv)}`;
  fetch(url).then(r => r.json()).then(d => {
    if (d.error) {
      document.getElementById('listBody').innerHTML = `<div class="empty-state" style="padding:40px;"><div class="icon">⚠️</div><p>${d.message || 'Lỗi tải danh sách'}</p></div>`;
      return;
    }
    currentList = d;
    renderList(d);
    updateExportBtn();
  }).catch(() => renderList([]));
}

// ── Render person list ────────────────────────────────────────────────────────
function renderList(list) {
  var type = currentType;
  document.getElementById('listCount').textContent = list.length ? `(${list.length} người)` : '';
  document.getElementById('selAllBtn').style.display  = (type >= 2 && list.length) ? 'inline-flex' : 'none';
  document.getElementById('deselAllBtn').style.display = (type >= 2 && list.length) ? 'inline-flex' : 'none';

  if (!list.length) {
    document.getElementById('listBody').innerHTML = '<div class="empty-state" style="padding:60px 20px;"><div class="icon">📂</div><h3>Không có dữ liệu</h3><p>Thay đổi phạm vi để xem danh sách</p></div>';
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
      control = `<input type="radio" name="pid" value="${p.id}" style="accent-color:var(--red);" onchange="updateExportBtn()">`;
    } else if (type === 3) {
      control = `<input type="checkbox" class="pcheck" value="${p.id}" style="accent-color:var(--red);width:16px;height:16px;" onchange="updateSelCount()">`;
    }

    html += `<div class="person-row" data-search="${(p.ho_ten+' '+p.lop+' '+p.ma_gvsv).toLowerCase()}">
      ${control}
      ${avatar_html}
      <div style="flex:1;min-width:0;">
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

function updateCheckboxes() {
  renderList(currentList);
}

// ── Filter table ──────────────────────────────────────────────────────────────
function filterTable() {
  var q = document.getElementById('filterInput').value.toLowerCase();
  document.querySelectorAll('.person-row').forEach(row => {
    row.classList.toggle('hidden', q && !row.dataset.search.includes(q));
  });
}

// ── Select all / deselect ─────────────────────────────────────────────────────
function selectAll() {
  document.querySelectorAll('.pcheck').forEach(c => c.checked = true);
  updateSelCount();
}
function deselectAll() {
  document.querySelectorAll('.pcheck:not([disabled])').forEach(c => c.checked = false);
  updateSelCount();
}

// ── Update export button ──────────────────────────────────────────────────────
function updateSelCount() {
  var checked = document.querySelectorAll('.pcheck:checked').length;
  var msg = '';
  if (currentType === 3) msg = checked ? `Đã chọn: <strong>${checked}</strong> người` : 'Chưa chọn ai';
  else if (currentType === 2) msg = document.querySelector('input[name=pid]:checked') ? '✅ Đã chọn 1 người' : 'Chưa chọn ai';
  else if (currentType === 1) msg = currentList.length ? `Sẽ xuất: <strong>${currentList.length}</strong> người` : '';
  document.getElementById('selCount').innerHTML = msg;
  updateExportBtn();
}

function updateExportBtn() {
  var ok = false;
  if (currentType === 1) ok = currentList.length > 0;
  else if (currentType === 2) ok = !!document.querySelector('input[name=pid]:checked');
  else if (currentType === 3) ok = document.querySelectorAll('.pcheck:checked').length > 0;
  document.getElementById('btnExport').disabled = !ok;
}

// ── Do export ─────────────────────────────────────────────────────────────────
function doExport() {
  var btn = document.getElementById('btnExport');
  var spin = document.getElementById('exportSpinner');
  btn.disabled = true;
  spin.style.display = 'block';

  var ft = currentScope;
  var fv = ft === 'lop' ? document.getElementById('lopSelect').value
         : ft === 'chibo' ? document.getElementById('chiboSelect').value : '';

  if (currentType === 1) {
    // POST → /api/export/all
    fetchDownload('api_proxy.php?path=api/export/all', {filter_type: ft, filter_value: fv});
  } else if (currentType === 2) {
    var pid = document.querySelector('input[name=pid]:checked')?.value;
    if (!pid) return done();
    // GET → /api/export/single/{id}
    window.location = `api_proxy.php?path=api/export/single/${pid}`;
    done();
  } else if (currentType === 3) {
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
      var cd = r.headers.get('Content-Disposition') || '';
      var fname = 'export.xlsx';
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
    .catch(e => { alert('Lỗi xuất file: ' + e.message); done(); });
  }
}

// ── Escape HTML ───────────────────────────────────────────────────────────────
function esc(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Init ──────────────────────────────────────────────────────────────────────
checkApi();
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
