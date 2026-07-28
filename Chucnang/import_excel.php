<?php
// Chucnang/import_excel.php – Import dữ liệu từ file Excel/CSV
require_once dirname(__DIR__) . '/config.php';
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
                // Parse XLSX
                $zip = new ZipArchive();
                if ($zip->open($tmpPath)) {
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
                            $rowData = [];
                            foreach ($xlRow->c as $cell) {
                                $t = (string)$cell['t'];
                                $v = (string)$cell->v;
                                $rowData[] = $t === 's' ? ($strings[(int)$v] ?? '') : $v;
                            }
                            if ($isFirst) { $isFirst = false; continue; } // skip header
                            if (count(array_filter($rowData)) < 2) continue;
                            $rows[] = $rowData;
                        }
                    }
                    $zip->close();
                } else {
                    $errors[] = 'Không thể mở file XLSX';
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
                    // col index offset: 0=STT, 1=Ma, 2=HoTen, ...
                    $hoTen = trim($r[2] ?? '');
                    if (empty($hoTen)) { $skipped++; continue; }

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

                    $vals = [
                        $r[1] ?: null,  // ma_gvsv
                        $hoTen,         // ho_ten
                        $r[3] ?: null,  // sdt
                        $r[4] ?: null,  // gioi_tinh
                        $ngaySinh ?: null, // ngay_sinh
                        $r[6] ?: null,  // dan_toc
                        $r[7] ?: null,  // que_quan
                        $r[8] ?: null,  // chuc_vu
                        $r[9] ?: null,  // lop
                        $r[10] ?: null, // chi_bo_cong_nhan
                        $r[11] ?: null, // so_bc_cam_tinh
                        toDbDate($r[12] ?? '') ?: null, // ngay_hop_cam_tinh
                        $r[13] ?: null, // dang_vien_giup_do
                        toDbDate($r[14] ?? '') ?: null, // ngay_phan_cong_giup_do
                        $r[15] ?: null, // so_qd_mo_lop
                        toDbDate($r[16] ?? '') ?: null, // ngay_qd_mo_lop
                        $r[17] ?: null, // tg_lop_boi_duong
                        toDbDate($r[18] ?? '') ?: null, // ngay_cap_cc
                        $r[19] ?: null, // so_qd_cc
                        $r[20] ?: null, // don_vi_cap_cc
                        $r[21] ?: null, // ten_dv_congtac_khi_cap_cc
                        $r[22] ?: null, // ten_chibo_khi_cap_cc
                        $r[23] ?: null, // ten_danguy_khi_cap_cc
                        $r[24] ?: null, // ten_tinhuy_khi_cap_cc
                        $r[25] ?: null, // ma_so
                        $r[26] ?: null, // ket_nap_dang
                        toDbDate($r[27] ?? '') ?: null, // ngay_quyet_dinh
                        $r[28] ?: null, // so_qd_ket_nap
                        toDbDate($r[29] ?? '') ?: null, // ngay_ket_nap
                        $r[30] ?: null, // dang_vien_huong_dan
                        toDbDate($r[31] ?? '') ?: null, // ngay_chuyen_sinh_hoat
                        $r[32] ?: null, // noi_chuyen_toi
                    ];

                    try {
                        $stmt->execute($vals);
                        $inserted++;
                    } catch (PDOException $e) {
                        $skipped++;
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

require_once dirname(__DIR__) . '/includes/header.php';
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
      <form method="post" enctype="multipart/form-data">
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
    <div class="card-header">
      <div class="card-title"><span class="icon">📖</span> Hướng dẫn & Mẫu file</div>
    </div>
    <div class="card-body">
      <p style="color:var(--text2);margin-bottom:16px;font-size:13px;">File cần có đúng thứ tự cột như sau:</p>
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
        <a href="../Chucnang/xuat_excel.php" class="btn btn-gold" style="width:100%;justify-content:center;">
          📤 Tải mẫu file (xuất CSV hiện tại)
        </a>
      </div>
    </div>
  </div>

</div>

<script>
function handleDrop(e) {
  e.preventDefault();
  var file = e.dataTransfer.files[0];
  if (file) {
    var dt = new DataTransfer();
    dt.items.add(file);
    document.getElementById('fileInput').files = dt.files;
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('dropzone').style.borderColor = 'var(--success)';
  }
}
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
