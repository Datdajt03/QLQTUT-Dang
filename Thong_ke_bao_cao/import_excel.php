<?php
// Thong_ke_bao_cao/import_excel.php – Import dữ liệu từ file Excel/CSV
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/User/auth.php';
requireRole(['Quản lý', 'Admin']);
$pageTitle = 'Import Excel';

$db     = getDB();
$errors = [];
$stats  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Lỗi upload file: ' . $file['error'];
    } else {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['csv', 'xlsx'])) {
            $errors[] = 'Chỉ hỗ trợ file CSV hoặc XLSX';
        } else {
            // Move to temp
            $tmpPath = sys_get_temp_dir() . '/import_' . time() . '.' . $ext;
            move_uploaded_file($file['tmp_name'], $tmpPath);

            $rows = [];

            if ($ext === 'csv') {
                $handle = fopen($tmpPath, 'r');
                // Skip BOM
                $bom = fread($handle, 3);
                if ($bom !== "\xEF\xBB\xBF") rewind($handle);

                $header = fgetcsv($handle);
                while (($line = fgetcsv($handle)) !== false) {
                    if (count(array_filter($line)) < 2) continue;
                    $rows[] = $line;
                }
                fclose($handle);
            } elseif ($ext === 'xlsx') {
                // Priority 1: High-accuracy Client-side JS + AI Agent mapping rows
                if (isset($_POST['json_parsed_rows']) && !empty($_POST['json_parsed_rows'])) {
                    $parsed = json_decode($_POST['json_parsed_rows'], true);
                    if (is_array($parsed)) {
                        $rows = $parsed;
                    } else {
                        $errors[] = 'Lỗi giải mã dữ liệu XLSX từ trình duyệt';
                    }
                } elseif (class_exists('ZipArchive')) {
                    // Priority 2: Hybrid Native PHP ZipArchive fallback parser
                    $zip = new ZipArchive();
                    if ($zip->open($tmpPath) === TRUE) {
                        $ssXml = $zip->getFromName('xl/sharedStrings.xml');
                        $strings = [];
                        if ($ssXml) {
                            $ss = simplexml_load_string($ssXml);
                            foreach ($ss->si as $si) {
                                $txt = '';
                                if (isset($si->t)) $txt = (string)$si->t;
                                else foreach ($si->r as $r) $txt .= (string)$r->t;
                                $strings[] = $txt;
                            }
                        }
                        
                        $wsXml = $zip->getFromName('xl/worksheets/sheet1.xml');
                        if ($wsXml) {
                            $ws = simplexml_load_string($wsXml);
                            $ws->registerXPathNamespace('s','http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                            $xlRows = $ws->xpath('//s:row');
                            $isFirst = true;
                            
                            foreach ($xlRows as $xlRow) {
                                $rowData = array_fill(0, 35, '');
                                foreach ($xlRow->c as $cell) {
                                    $ref = (string)$cell['r']; // E.g. "A1", "C5"
                                    preg_match('/([A-Z]+)(\d+)/', $ref, $m);
                                    $colLetters = $m[1] ?? 'A';
                                    
                                    // Convert Excel column letters to 0-based index (A=0, B=1, C=2...)
                                    $colIdx = 0;
                                    for ($i = 0; $i < strlen($colLetters); $i++) {
                                        $colIdx = $colIdx * 26 + (ord($colLetters[$i]) - ord('A') + 1);
                                    }
                                    $colIdx -= 1;

                                    $t = (string)$cell['t'];
                                    $v = (string)$cell->v;
                                    $val = $t === 's' ? ($strings[(int)$v] ?? '') : $v;
                                    if ($colIdx < 35) {
                                        $rowData[$colIdx] = trim((string)$val);
                                    }
                                }

                                // Auto-detect and skip ID reference header row or display header row
                                $rowStr = implode(' ', $rowData);
                                if (strpos($rowStr, '[ID:') !== false || strpos($rowStr, 'Mã GV/SV') !== false || strpos($rowStr, 'Họ và tên') !== false) {
                                    continue;
                                }
                                if (count(array_filter($rowData)) < 1) continue; // Skip completely blank lines
                                $rows[] = $rowData;
                            }
                        }
                        $zip->close();
                    } else {
                        $errors[] = 'Không thể mở file XLSX bằng ZipArchive';
                    }
                } else {
                    $errors[] = 'Vui lòng chọn file qua giao diện để AI Agent giải mã và nhập dữ liệu.';
                }
            }

            if (empty($errors) && !empty($rows)) {
                $inserted = 0; $skipped = 0;
                $stmt = $db->prepare("
                    INSERT INTO doi_tuong (ma_gvsv,ho_ten,sdt,gioi_tinh,ngay_sinh,dan_toc,que_quan,chuc_vu,lop,
                        chi_bo_cong_nhan,so_bc_cam_tinh,ngay_hop_cam_tinh,dang_vien_giup_do,ngay_phan_cong_giup_do,
                        so_qd_mo_lop,ngay_qd_mo_lop,tg_lop_boi_duong,ngay_cap_cc,so_qd_cc,don_vi_cap_cc,
                        ten_dv_congtac_khi_cap_cc,ten_chibo_khi_cap_cc,ten_danguy_khi_cap_cc,ten_tinhuy_khi_cap_cc,
                        ma_so,ket_nap_dang,ngay_quyet_dinh,so_qd_ket_nap,ngay_ket_nap,dang_vien_huong_dan,
                        ngay_chuyen_sinh_hoat,noi_chuyen_toi,trang_thai)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'Đang theo dõi')
                ");

                foreach ($rows as $r) {
                    // Pad to at least 33 cols
                    while (count($r) < 33) $r[] = '';
                    
                    // Find first non-empty text in first 5 columns to use as hoTen
                    $hoTen = '';
                    for ($cIdx = 0; $cIdx < 5; $cIdx++) {
                        $val = trim((string)($r[$cIdx] ?? ''));
                        if (!empty($val) && !is_numeric($val)) {
                            $hoTen = $val;
                            break;
                        }
                    }

                    // If still empty, check if row has ANY content at all
                    if (empty($hoTen)) {
                        $nonEmptyContent = array_filter($r, function($v) { return trim((string)$v) !== ''; });
                        if (!empty($nonEmptyContent)) {
                            $hoTen = 'Chưa đặt tên (' . reset($nonEmptyContent) . ')';
                        } else {
                            $skipped++;
                            continue;
                        }
                    }

                    // Parse dates (col 5 may be Excel serial or d/m/Y)
                    $ngaySinh = '';
                    if (!empty($r[5])) {
                        if (is_numeric($r[5])) {
                            // Excel date serial
                            $d = (int)$r[5] - 25569;
                            $ngaySinh = date('Y-m-d', $d * 86400);
                        } else {
                            $ngaySinh = toDbDate($r[5]) ?? '';
                        }
                    }

                    // Normalize gioi_tinh ENUM ('Nam','Nữ','Khác')
                    $gioiTinh = trim((string)($r[4] ?? ''));
                    if (preg_match('/nam/i', $gioiTinh)) $gioiTinh = 'Nam';
                    elseif (preg_match('/nữ|nu/i', $gioiTinh)) $gioiTinh = 'Nữ';
                    else $gioiTinh = null;

                    $vals = [
                        $r[1] !== '' ? $r[1] : null,  // ma_gvsv
                        $hoTen,                        // ho_ten
                        $r[3] !== '' ? $r[3] : null,  // sdt
                        $gioiTinh,                     // gioi_tinh
                        $ngaySinh !== '' ? $ngaySinh : null, // ngay_sinh
                        $r[6] !== '' ? $r[6] : null,  // dan_toc
                        $r[7] !== '' ? $r[7] : null,  // que_quan
                        $r[8] !== '' ? $r[8] : null,  // chuc_vu
                        $r[9] !== '' ? $r[9] : null,  // lop
                        $r[10] !== '' ? $r[10] : null, // chi_bo_cong_nhan
                        $r[11] !== '' ? $r[11] : null, // so_bc_cam_tinh
                        toDbDate($r[12] ?? '') ?: null, // ngay_hop_cam_tinh
                        $r[13] !== '' ? $r[13] : null, // dang_vien_giup_do
                        toDbDate($r[14] ?? '') ?: null, // ngay_phan_cong_giup_do
                        $r[15] !== '' ? $r[15] : null, // so_qd_mo_lop
                        toDbDate($r[16] ?? '') ?: null, // ngay_qd_mo_lop
                        $r[17] !== '' ? $r[17] : null, // tg_lop_boi_duong
                        toDbDate($r[18] ?? '') ?: null, // ngay_cap_cc
                        $r[19] !== '' ? $r[19] : null, // so_qd_cc
                        $r[20] !== '' ? $r[20] : null, // don_vi_cap_cc
                        $r[21] !== '' ? $r[21] : null, // ten_dv_congtac_khi_cap_cc
                        $r[22] !== '' ? $r[22] : null, // ten_chibo_khi_cap_cc
                        $r[23] !== '' ? $r[23] : null, // ten_danguy_khi_cap_cc
                        $r[24] !== '' ? $r[24] : null, // ten_tinhuy_khi_cap_cc
                        $r[25] !== '' ? $r[25] : null, // ma_so
                        $r[26] !== '' ? $r[26] : null, // ket_nap_dang
                        toDbDate($r[27] ?? '') ?: null, // ngay_quyet_dinh
                        $r[28] !== '' ? $r[28] : null, // so_qd_ket_nap
                        toDbDate($r[29] ?? '') ?: null, // ngay_ket_nap
                        $r[30] !== '' ? $r[30] : null, // dang_vien_huong_dan
                        toDbDate($r[31] ?? '') ?: null, // ngay_chuyen_sinh_hoat
                        $r[32] !== '' ? $r[32] : null, // noi_chuyen_toi
                    ];

                    try {
                        $stmt->execute($vals);
                        $inserted++;
                    } catch (PDOException $e) {
                        $skipped++;
                        if (count($errors) < 5) {
                            $errors[] = "Lỗi dòng (Họ tên: '$hoTen'): " . $e->getMessage();
                        }
                    }
                }
                $stats = ['inserted' => $inserted, 'skipped' => $skipped];
                if ($inserted > 0) {
                    setFlash('success', "Import thành công $inserted bản ghi" . ($skipped ? ", bỏ qua $skipped" : ''));
                }
            }
            @unlink($tmpPath);
        }
    }
}

require_once dirname(__DIR__) . '/Giao_dien/header.php';
?>

<div class="page-header">
  <div class="page-header-left">
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>index.php">Dashboard</a><span class="sep">›</span>
      <span class="current">Import Excel</span>
    </div>
    <div class="page-title">📥 Import <span>Excel</span></div>
    <div class="page-subtitle">Nhập danh sách đối tượng từ file Excel hoặc CSV</div>
  </div>
  <a href="danh_sach.php" class="btn btn-outline">← Danh sách</a>
</div>

<?php if ($errors): ?>
<div class="flash flash-danger">❌ <?= implode('<br>', array_map('e', $errors)) ?></div>
<?php endif; ?>

<?php if ($stats): ?>
<div class="flash flash-success">
  ✅ Import thành công: <strong><?= $stats['inserted'] ?></strong> bản ghi.
  <?php if ($stats['skipped']): ?> Bỏ qua: <strong><?= $stats['skipped'] ?></strong><?php endif; ?>
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

  <!-- Upload form -->
  <div class="card fade-in">
    <div class="card-header">
      <div class="card-title"><span class="icon">📁</span> Chọn file</div>
    </div>
    <div class="card-body">
      <form id="mainImportForm" method="post" enctype="multipart/form-data">
        <input type="hidden" id="jsonParsedRowsInput" name="json_parsed_rows" value="">
        <div class="form-group" style="margin-bottom:20px;">
          <label class="form-label">File Excel / CSV</label>
          <div id="dropzone" style="border:2px dashed var(--border2);border-radius:12px;padding:40px;text-align:center;cursor:pointer;transition:all 0.3s;"
               onclick="document.getElementById('fileInput').click()"
               ondragover="event.preventDefault();this.style.borderColor='var(--gold)'"
               ondragleave="this.style.borderColor='var(--border2)'"
               ondrop="handleDrop(event)">
            <div style="font-size:40px;margin-bottom:12px;">📂</div>
            <div style="color:var(--text2);font-size:14px;">Kéo thả file vào đây hoặc <span style="color:var(--gold);font-weight:600;">click để chọn</span></div>
            <div style="color:var(--text2);font-size:12px;margin-top:8px;">Hỗ trợ: .xlsx, .csv (tối đa 10MB)</div>
            <div id="fileName" style="color:var(--success);margin-top:12px;font-weight:600;"></div>
          </div>
          <input type="file" id="fileInput" name="excel_file" accept=".csv,.xlsx" style="display:none"
                 onchange="document.getElementById('fileName').textContent = this.files[0]?.name || ''">
        </div>
        <div class="flash flash-warning" style="margin-bottom:20px;">
          ⚠️ Dữ liệu import sẽ được <strong>thêm mới</strong> vào hệ thống (không ghi đè).
          Đảm bảo file đúng định dạng mẫu.
        </div>
        <button type="submit" class="btn btn-primary btn-lg" style="width:100%">📥 Import dữ liệu</button>
      </form>
    </div>
  </div>

  <!-- Hướng dẫn -->
  <div class="card fade-in">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
      <div class="card-title"><span class="icon">📖</span> Hướng dẫn & Mẫu file</div>
      <a href="http://localhost:5000/api/export/template" target="_blank" class="btn btn-success btn-sm" style="font-weight:600;">
        📊 Tải Mẫu Excel Điền Chuẩn (Kèm ID Cột)
      </a>
    </div>
    <div class="card-body">
      <p style="color:var(--text2);margin-bottom:12px;font-size:13px;">Tải file mẫu bên trên gửi cho các lớp điền (file đã có sẵn Tiêu đề & Mã ID trường dữ liệu chuẩn):</p>
      <div class="table-wrapper" style="max-height:320px;overflow-y:auto;">
        <table class="data-table">
          <thead><tr><th>Cột</th><th>Tên trường</th><th>Bắt buộc</th></tr></thead>
          <tbody>
            <?php
            $cols = [
              ['A','STT (bỏ qua)',''],
              ['B','Mã GV/SV',''],
              ['C','Họ và tên','✅'],
              ['D','SĐT',''],
              ['E','Giới tính',''],
              ['F','Ngày sinh',''],
              ['G','Dân tộc',''],
              ['H','Quê quán',''],
              ['I','Chức vụ',''],
              ['J','Lớp',''],
              ['K','Chi bộ công nhận',''],
              ['L','Số BC cảm tình Đảng',''],
              ['M','Ngày họp CB',''],
              ['N','ĐV giúp đỡ',''],
              ['O','Ngày phân công',''],
            ];
            foreach ($cols as $c): ?>
            <tr>
              <td><code style="color:var(--gold)"><?= $c[0] ?></code></td>
              <td><?= $c[1] ?></td>
              <td style="color:var(--success)"><?= $c[2] ?></td>
            </tr>
            <?php endforeach; ?>
            <tr><td colspan="3" style="color:var(--text2);text-align:center;font-size:12px;">... (tiếp theo đến cột AH)</td></tr>
          </tbody>
        </table>
      </div>
      <div style="margin-top:16px;">
        <a href="../Thong_ke_bao_cao/xuat_excel.php" class="btn btn-gold" style="width:100%;justify-content:center;">
          📤 Tải mẫu file (xuất CSV hiện tại)
        </a>
      </div>
    </div>
  </div>

</div>

<div id="aiColumnMapperModal" class="modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:9999;align-items:center;justify-content:center;">
  <div class="modal-content card fade-in" style="width:90%;max-width:850px;background:var(--bg-card,#1e293b);color:var(--text,#f8fafc);border:1px solid var(--border,#334155);border-radius:12px;padding:24px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.5);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;border-bottom:1px solid var(--border,#334155);padding-bottom:12px;">
      <h3 style="margin:0;font-size:18px;color:var(--gold,#f59e0b);display:flex;align-items:center;gap:8px;">
        🤖 AI Agent: Phân Loại Tên Cột Dữ Liệu Excel
      </h3>
      <button type="button" onclick="closeAiModal()" style="background:none;border:none;color:#94a3b8;font-size:20px;cursor:pointer;">✕</button>
    </div>
    <div style="background:rgba(245,158,11,0.1);border-left:4px solid var(--gold,#f59e0b);padding:12px;margin-bottom:16px;border-radius:4px;font-size:13px;color:#cbd5e1;">
      ℹ️ Hệ thống phát hiện các tiêu đề cột từ file Excel (Ví dụ: <code>Qli</code>, <code>QL</code>, <code>Hoten</code>...). Vui lòng xác nhận hoặc chọn trường dữ liệu CSDL ở cột bên trái để AI Agent ánh xạ chính xác trước khi nhập!
    </div>
    
    <div style="max-height:380px;overflow-y:auto;margin-bottom:20px;padding-right:8px;">
      <table class="data-table" style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
          <tr style="background:#0f172a;text-align:left;">
            <th style="padding:10px;border-bottom:1px solid #334155;">Cột trong File Excel</th>
            <th style="padding:10px;border-bottom:1px solid #334155;">Ánh xạ vào Trường CSDL (Chọn cột bên trái)</th>
            <th style="padding:10px;border-bottom:1px solid #334155;text-align:center;">Độ tin cậy AI Agent</th>
          </tr>
        </thead>
        <tbody id="aiMappingTableBody">
          <!-- Populated dynamically via JS -->
        </tbody>
      </table>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:12px;">
      <button type="button" onclick="closeAiModal()" class="btn btn-outline" style="padding:8px 16px;">Hủy bỏ</button>
      <button type="button" onclick="confirmAiMapping()" class="btn btn-primary" style="padding:8px 20px;background:var(--gold,#f59e0b);border:none;color:#000;font-weight:600;">✅ Xác nhận & Tiến hành Import</button>
    </div>
  </div>
</div>

<input type="hidden" id="jsonParsedRowsInput" name="json_parsed_rows" value="">

<script src="<?= BASE_URL ?>AI_Module/excel_column_agent.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
let currentParsedHeaders = [];
let currentParsedRows = [];

function handleDrop(e) {
  e.preventDefault();
  var file = e.dataTransfer.files[0];
  if (file) {
    var dt = new DataTransfer();
    dt.items.add(file);
    document.getElementById('fileInput').files = dt.files;
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('dropzone').style.borderColor = 'var(--success)';
    processExcelFileForAi(file);
  }
}

document.getElementById('fileInput').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    processExcelFileForAi(file);
  }
});

function processExcelFileForAi(file) {
  const reader = new FileReader();
  reader.onload = function(e) {
    const data = new Uint8Array(e.target.result);
    const workbook = XLSX.read(data, {type: 'array'});
    const firstSheetName = workbook.SheetNames[0];
    const worksheet = workbook.Sheets[firstSheetName];
    const json = XLSX.utils.sheet_to_json(worksheet, {header: 1, defval: ''});
    
    if (json && json.length > 0) {
      currentParsedHeaders = json[0];
      currentParsedRows = json.slice(1);
      
      // Auto pre-populate initial rows immediately into hidden input
      document.getElementById('jsonParsedRowsInput').value = JSON.stringify(currentParsedRows);
      
      showAiAgentMappingModal(currentParsedHeaders);
    }
  };
  reader.readAsArrayBuffer(file);
}

function showAiAgentMappingModal(headers) {
  const tbody = document.getElementById('aiMappingTableBody');
  tbody.innerHTML = '';

  // Helper to convert index to Excel column letter (0->A, 1->B, 2->C...)
  function getColumnLetter(colIdx) {
    let temp, letter = '';
    while (colIdx >= 0) {
      temp = (colIdx) % 26;
      letter = String.fromCharCode(temp + 65) + letter;
      colIdx = Math.floor((colIdx - temp) / 26) - 1;
    }
    return letter;
  }

  headers.forEach((header, idx) => {
    const isBlank = !header || header.toString().trim() === '';
    const displayHeaderName = isBlank 
      ? `⚠️ Cột ${getColumnLetter(idx)} (Trống tiêu đề)` 
      : `📄 ${header}`;
    
    const match = window.ExcelColumnAgent.matchColumn(header);
    
    let optionsHtml = `<option value="">-- Bỏ qua cột này --</option>`;
    window.ExcelColumnAgent.dictionary.forEach(dictItem => {
      const selected = (!isBlank && dictItem.field === match.field) ? 'selected' : '';
      optionsHtml += `<option value="${dictItem.field}" ${selected}>${dictItem.label} (${dictItem.field})</option>`;
    });

    const badgeColor = isBlank ? '#ef4444' : (match.confidence >= 0.8 ? '#10b981' : (match.confidence > 0 ? '#f59e0b' : '#ef4444'));
    const confidenceText = isBlank ? 'Cột Trống (Cần chọn)' : (match.confidence >= 0.8 ? 'Chính xác (High)' : (match.confidence > 0 ? 'Gợi ý (Medium)' : 'Chưa rõ (Low)'));

    const tr = document.createElement('tr');
    tr.style.borderBottom = '1px solid #334155';
    if (isBlank) {
      tr.style.background = 'rgba(239, 68, 68, 0.08)';
    }

    tr.innerHTML = `
      <td style="padding:10px;font-weight:600;color:${isBlank ? '#f87171' : 'var(--gold,#f59e0b)'};">
        ${displayHeaderName}
      </td>
      <td style="padding:10px;">
        <select class="form-control" style="width:100%;padding:6px 10px;background:#0f172a;color:#f8fafc;border:1px solid ${isBlank ? '#ef4444' : '#475569'};border-radius:6px;" data-header-index="${idx}">
          ${optionsHtml}
        </select>
      </td>
      <td style="padding:10px;text-align:center;">
        <span style="display:inline-block;padding:3px 8px;border-radius:12px;font-size:11px;font-weight:600;background:${badgeColor}22;color:${badgeColor};border:1px solid ${badgeColor};">
          ${confidenceText}
        </span>
      </td>
    `;
    tbody.appendChild(tr);
  });

  const modal = document.getElementById('aiColumnMapperModal');
  modal.style.display = 'flex';
}

function closeAiModal() {
  document.getElementById('aiColumnMapperModal').style.display = 'none';
}

function confirmAiMapping() {
  closeAiModal();
  
  // Re-map rows based on user column selections
  const selects = document.querySelectorAll('#aiMappingTableBody select');
  const mappingMap = {}; // dbField -> colIndex
  selects.forEach(select => {
    const colIdx = parseInt(select.getAttribute('data-header-index'), 10);
    const dbField = select.value;
    if (dbField) {
      mappingMap[dbField] = colIdx;
    }
  });

  // DB columns order matching SQL insert:
  // 0:STT(skip), 1:ma_gvsv, 2:ho_ten, 3:sdt, 4:gioi_tinh, 5:ngay_sinh, 6:dan_toc, 7:que_quan, 8:chuc_vu, 9:lop,
  // 10:chi_bo_cong_nhan, 11:so_bc_cam_tinh, 12:ngay_hop_cam_tinh, 13:dang_vien_giup_do, 14:ngay_phan_cong_giup_do,
  // 15:so_qd_mo_lop, 16:ngay_qd_mo_lop, 17:tg_lop_boi_duong, 18:ngay_cap_cc, 19:so_qd_cc, 20:don_vi_cap_cc,
  // 21:ten_dv_congtac_khi_cap_cc, 22:ten_chibo_khi_cap_cc, 23:ten_danguy_khi_cap_cc, 24:ten_tinhuy_khi_cap_cc,
  // 25:ma_so, 26:ket_nap_dang, 27:ngay_quyet_dinh, 28:so_qd_ket_nap, 29:ngay_ket_nap, 30:dang_vien_huong_dan,
  // 31:ngay_chuyen_sinh_hoat, 32:noi_chuyen_toi
  const dbFieldsOrder = [
    'stt_dummy', 'ma_gvsv', 'ho_ten', 'sdt', 'gioi_tinh', 'ngay_sinh', 'dan_toc', 'que_quan', 'chuc_vu', 'lop',
    'chi_bo_cong_nhan', 'so_bc_cam_tinh', 'ngay_hop_cam_tinh', 'dang_vien_giup_do', 'ngay_phan_cong_giup_do',
    'so_qd_mo_lop', 'ngay_qd_mo_lop', 'tg_lop_boi_duong', 'ngay_cap_cc', 'so_qd_cc', 'don_vi_cap_cc',
    'ten_dv_congtac_khi_cap_cc', 'ten_chibo_khi_cap_cc', 'ten_danguy_khi_cap_cc', 'ten_tinhuy_khi_cap_cc',
    'ma_so', 'ket_nap_dang', 'ngay_quyet_dinh', 'so_qd_ket_nap', 'ngay_ket_nap', 'dang_vien_huong_dan',
    'ngay_chuyen_sinh_hoat', 'noi_chuyen_toi'
  ];

  if (currentParsedRows && currentParsedRows.length > 0) {
    const remappedRows = currentParsedRows.map(row => {
      const newRow = new Array(33).fill('');
      dbFieldsOrder.forEach((field, targetIdx) => {
        if (field === 'stt_dummy') return;
        const sourceColIdx = mappingMap[field];
        if (sourceColIdx !== undefined && row[sourceColIdx] !== undefined) {
          newRow[targetIdx] = row[sourceColIdx];
        }
      });
      return newRow;
    });

    document.getElementById('jsonParsedRowsInput').value = JSON.stringify(remappedRows);
  }

  // Ensure form submit event proceeds with json parsed data
  const form = document.getElementById('mainImportForm');
  form.submit();
}
</script>

<?php require_once dirname(__DIR__) . '/Giao_dien/footer.php'; ?>
