<?php
// Quan_ly_doi_tuong/duyet_dang_ky.php - Giao diện phê duyệt đăng ký thông tin sinh viên
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/User/auth.php';
requireRole(['Quản lý', 'Admin']);
$pageTitle = 'Phê duyệt hồ sơ';

$db = getDB();

// Xử lý phê duyệt hoặc từ chối hành động gửi lên
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($id && in_array($action, ['approve', 'reject'])) {
        // Tìm hồ sơ đăng ký tương ứng
        $stmt = $db->prepare("SELECT * FROM dang_ky_doi_tuong WHERE id = ?");
        $stmt->execute([$id]);
        $reg = $stmt->fetch();

        if ($reg) {
            if ($action === 'approve') {
                try {
                    $db->beginTransaction();

                    // 1. Chèn thông tin vào bảng doi_tuong
                    $sqlInsert = "INSERT INTO doi_tuong (
                        ma_gvsv, ho_ten, sdt, email, gioi_tinh, ngay_sinh, dan_toc,
                        que_quan, chuc_vu, lop, chi_bo_cong_nhan, trang_thai
                    ) VALUES (
                        :ma_gvsv, :ho_ten, :sdt, :email, :gioi_tinh, :ngay_sinh, :dan_toc,
                        :que_quan, :chuc_vu, :lop, :chi_bo_cong_nhan, 'Đang theo dõi'
                    )";

                    $stmtInsert = $db->prepare($sqlInsert);
                    $stmtInsert->execute([
                        ':ma_gvsv'          => $reg['ma_gvsv'],
                        ':ho_ten'           => $reg['ho_ten'],
                        ':sdt'             => $reg['sdt'],
                        ':email'           => $reg['email'],
                        ':gioi_tinh'        => $reg['gioi_tinh'],
                        ':ngay_sinh'        => $reg['ngay_sinh'],
                        ':dan_toc'          => $reg['dan_toc'],
                        ':que_quan'         => $reg['que_quan'],
                        ':chuc_vu'          => $reg['chuc_vu'],
                        ':lop'              => $reg['lop'],
                        ':chi_bo_cong_nhan' => $reg['chi_bo_cong_nhan']
                    ]);

                    $newDoiTuongId = $db->lastInsertId();

                    // 2. Cập nhật trạng thái trong dang_ky_doi_tuong
                    $stmtUpdate = $db->prepare("UPDATE dang_ky_doi_tuong SET trang_thai = 'Đã duyệt' WHERE id = ?");
                    $stmtUpdate->execute([$id]);

                    // Ghi lịch sử
                    logHistory($newDoiTuongId, 'Phê duyệt', 'Phê duyệt hồ sơ đăng ký trực tuyến của sinh viên: ' . $reg['ho_ten']);

                    // 3. Gửi email chúc mừng sinh viên
                    $mailBody = '
                    <div style="font-family:sans-serif;max-width:600px;margin:auto;padding:25px;border:1px solid rgba(200,16,46,0.1);border-radius:12px;background:#16161f;color:#e8e8f0;">
                        <h2 style="color:#FFD700;text-align:center;margin-bottom:20px;font-size:22px;">⭐ THÔNG BÁO DUYỆT HỒ SƠ ⭐</h2>
                        <p>Chào bạn <strong>' . e($reg['ho_ten']) . '</strong>,</p>
                        <p>Ban quản lý chi bộ trường xin chúc mừng! Hồ sơ đăng ký quần chúng ưu tú phục vụ kết nạp Đảng của bạn đã được xem xét và <strong>PHÊ DUYỆT THÀNH CÔNG</strong>.</p>
                        <p style="background:rgba(255,255,255,0.05);padding:14px;border-radius:8px;border-left:4px solid #FFD700;">
                            <strong>Mã sinh viên:</strong> ' . e($reg['ma_gvsv']) . '<br>
                            <strong>Lớp:</strong> ' . e($reg['lop']) . '<br>
                            <strong>Trạng thái:</strong> Đang theo dõi (Quần chúng ưu tú)
                        </p>
                        <p>Thông tin của bạn đã được chính thức đồng bộ vào hệ thống dữ liệu quản lý kết nạp Đảng của Đảng bộ nhà trường.</p>
                        <p>Chúc bạn tiếp tục nỗ lực học tập và rèn luyện tốt để sớm được đứng vào hàng ngũ của Đảng Cộng sản Việt Nam!</p>
                        <hr style="border:none;border-top:1px solid rgba(255,255,255,0.08);margin:20px 0;">
                        <p style="font-size:11px;color:#a0a0b8;text-align:center;">Hệ thống Quản lý Kết nạp Đảng · Đại học Tây Bắc</p>
                    </div>';

                    sendMailNotification($reg['email'], '⭐ [Kết nạp Đảng] Hồ sơ đăng ký của bạn đã được phê duyệt', $mailBody);

                    $db->commit();
                    setFlash('success', 'Đã phê duyệt hồ sơ và thêm "' . $reg['ho_ten'] . '" vào danh sách chính thức!');
                } catch (Exception $e) {
                    $db->rollBack();
                    setFlash('danger', 'Lỗi khi phê duyệt hồ sơ: ' . $e->getMessage());
                }
            } elseif ($action === 'reject') {
                $lyDo = trim($_POST['ly_do_tu_choi'] ?? '');
                if (empty($lyDo)) {
                    setFlash('danger', 'Vui lòng cung cấp lý do từ chối hồ sơ.');
                } else {
                    try {
                        // 1. Cập nhật trạng thái và lý do từ chối
                        $stmtUpdate = $db->prepare("UPDATE dang_ky_doi_tuong SET trang_thai = 'Đã từ chối', ly_do_tu_choi = ? WHERE id = ?");
                        $stmtUpdate->execute([$lyDo, $id]);

                        // 2. Gửi email thông báo lý do bác bỏ
                        $mailBody = '
                        <div style="font-family:sans-serif;max-width:600px;margin:auto;padding:25px;border:1px solid rgba(239,68,68,0.1);border-radius:12px;background:#16161f;color:#e8e8f0;">
                            <h2 style="color:#ef4444;text-align:center;margin-bottom:20px;font-size:22px;">❌ KẾT QUẢ DUYỆT HỒ SƠ ĐĂNG KÝ ❌</h2>
                            <p>Chào bạn <strong>' . e($reg['ho_ten']) . '</strong>,</p>
                            <p>Cảm ơn bạn đã gửi hồ sơ đăng ký quần chúng ưu tú. Sau khi kiểm tra đối chiếu dữ liệu, Ban quản lý rất tiếc phải thông báo hồ sơ của bạn <strong>CHƯA ĐƯỢC PHÊ DUYỆT</strong>.</p>
                            <p style="margin:16px 0;font-weight:600;">Lý do phản hồi từ Ban quản lý:</p>
                            <blockquote style="background:rgba(239,68,68,0.08);border-left:4px solid #ef4444;padding:16px;border-radius:0 8px 8px 0;margin:0 0 20px;font-style:italic;color:#ff8080;">
                                ' . nl2br(e($lyDo)) . '
                            </blockquote>
                            <p>Vui lòng chuẩn bị và chỉnh sửa lại thông tin chính xác, sau đó tiến hành đăng ký lại hồ sơ tại trang web đăng ký trực tuyến.</p>
                            <hr style="border:none;border-top:1px solid rgba(255,255,255,0.08);margin:20px 0;">
                            <p style="font-size:11px;color:#a0a0b8;text-align:center;">Hệ thống Quản lý Kết nạp Đảng · Đại học Tây Bắc</p>
                        </div>';

                        sendMailNotification($reg['email'], '❌ [Kết nạp Đảng] Kết quả hồ sơ đăng ký quần chúng ưu tú', $mailBody);

                        setFlash('warning', 'Đã từ chối hồ sơ của "' . $reg['ho_ten'] . '" và gửi mail lý do thông báo thành công!');
                    } catch (Exception $e) {
                        setFlash('danger', 'Lỗi khi từ chối hồ sơ: ' . $e->getMessage());
                    }
                }
            }
        }
    }
    redirect(BASE_URL . 'Quan_ly_doi_tuong/duyet_dang_ky.php');
}

// Lọc và hiển thị hồ sơ đăng ký
$searchFilter = trim($_GET['search'] ?? '');
$tabFilter    = $_GET['tab'] ?? 'pending'; // pending | approved | rejected

$where = [];
$params = [];

if ($tabFilter === 'approved') {
    $where[] = "trang_thai = 'Đã duyệt'";
} elseif ($tabFilter === 'rejected') {
    $where[] = "trang_thai = 'Đã từ chối'";
} else {
    $where[] = "trang_thai = 'Chờ duyệt'";
}

if ($searchFilter !== '') {
    $where[] = "(ho_ten LIKE ? OR ma_gvsv LIKE ? OR lop LIKE ?)";
    $params = array_merge($params, ["%$searchFilter%", "%$searchFilter%", "%$searchFilter%"]);
}

$whereStr = implode(' AND ', $where);
$stmt = $db->prepare("SELECT * FROM dang_ky_doi_tuong WHERE $whereStr ORDER BY created_at DESC");
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Đếm số lượng chờ duyệt hiện tại
$pendingCount = $db->query("SELECT COUNT(*) FROM dang_ky_doi_tuong WHERE trang_thai = 'Chờ duyệt'")->fetchColumn();
$approvedCount = $db->query("SELECT COUNT(*) FROM dang_ky_doi_tuong WHERE trang_thai = 'Đã duyệt'")->fetchColumn();
$rejectedCount = $db->query("SELECT COUNT(*) FROM dang_ky_doi_tuong WHERE trang_thai = 'Đã từ chối'")->fetchColumn();

require_once dirname(__DIR__) . '/Giao_dien/header.php';
?>

<!-- Page Header -->
<div class="page-header">
  <div class="page-header-left">
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>index.php">Dashboard</a>
      <span class="sep">›</span>
      <span class="current">Phê duyệt hồ sơ</span>
    </div>
    <div class="page-title">🔔 Duyệt Hồ sơ <span>Đăng ký trực tuyến</span></div>
    <div class="page-subtitle">Quản lý, thẩm định thông tin sinh viên tự khai báo và đồng bộ vào hệ thống.</div>
  </div>
  <div style="display:flex;gap:10px;">
    <a href="<?= BASE_URL ?>nhap_thong_tin.php" target="_blank" class="btn btn-gold">🔗 Mở link đăng ký công khai</a>
  </div>
</div>

<!-- Tabs selector -->
<div class="tabs">
  <a href="?tab=pending&search=<?= e($searchFilter) ?>" class="tab-btn <?= $tabFilter==='pending'?'active':'' ?>">
    📥 Chờ duyệt (<?= $pendingCount ?>)
  </a>
  <a href="?tab=approved&search=<?= e($searchFilter) ?>" class="tab-btn <?= $tabFilter==='approved'?'active':'' ?>">
    ✅ Đã duyệt (<?= $approvedCount ?>)
  </a>
  <a href="?tab=rejected&search=<?= e($searchFilter) ?>" class="tab-btn <?= $tabFilter==='rejected'?'active':'' ?>">
    ❌ Đã từ chối (<?= $rejectedCount ?>)
  </a>
</div>

<!-- Filter search -->
<form method="get" class="filter-bar">
  <input type="hidden" name="tab" value="<?= e($tabFilter) ?>">
  <input type="text" name="search" class="form-control filter-search" 
         placeholder="🔍 Tìm theo họ tên, mã sinh viên, lớp..." value="<?= e($searchFilter) ?>">
  <button type="submit" class="btn btn-primary">Tìm kiếm</button>
  <a href="?tab=<?= e($tabFilter) ?>" class="btn btn-outline">Reset</a>
</form>

<!-- Table -->
<div class="card fade-in">
  <div class="card-body" style="padding:0;">
    <?php if (empty($rows)): ?>
      <div class="empty-state">
        <div class="icon">📂</div>
        <h3>Không có hồ sơ nào</h3>
        <p>Danh sách hồ sơ thuộc trạng thái này hiện tại đang trống.</p>
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
              <th>Email (Gmail)</th>
              <th>SĐT</th>
              <th>Chi bộ mong muốn</th>
              <th style="text-align:center;">Thời gian gửi</th>
              <th style="text-align:center;">Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $i => $row): ?>
              <tr>
                <td style="color:var(--text2);"><?= $i + 1 ?></td>
                <td><code style="color:var(--gold);font-size:12px;"><?= e($row['ma_gvsv']) ?></code></td>
                <td>
                  <div style="font-weight:600;color:var(--text);"><?= e($row['ho_ten']) ?></div>
                  <span style="font-size:11px;color:var(--text2);">(<?= e($row['gioi_tinh'] ?: 'Chưa chọn') ?> · <?= $row['ngay_sinh'] ? formatDate($row['ngay_sinh']) : 'Chưa nhập ngày sinh' ?>)</span>
                </td>
                <td><?= e($row['lop']) ?></td>
                <td><?= e($row['email']) ?></td>
                <td><?= e($row['sdt']) ?></td>
                <td><?= e($row['chi_bo_cong_nhan'] ?: '—') ?></td>
                <td style="text-align:center;font-size:12px;color:var(--text2);"><?= date('H:i d/m/Y', strtotime($row['created_at'])) ?></td>
                <td>
                  <div style="display:flex;gap:5px;justify-content:center;">
                    <button class="btn btn-outline btn-sm" onclick="openDetailsModal(<?= htmlspecialchars(json_encode($row)) ?>)">👁️ Xem chi tiết</button>
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

<!-- Details & Approval Modal -->
<div class="modal-overlay" id="detailsModal">
  <div class="modal" style="max-width: 600px; width: 95%;">
    <div class="modal-title">📋 Chi tiết hồ sơ đăng ký</div>
    <div class="modal-body" style="max-height: 450px; overflow-y: auto; padding-right: 5px;">
      
      <div style="display:grid;grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:20px;">
        <div>
          <span style="font-size:11px;color:var(--text2);text-transform:uppercase;">Họ và tên</span>
          <div style="font-weight:600;font-size:16px;color:var(--text);" id="modal_name"></div>
        </div>
        <div>
          <span style="font-size:11px;color:var(--text2);text-transform:uppercase;">Mã sinh viên</span>
          <div style="font-weight:600;font-size:16px;color:var(--gold);" id="modal_ma"></div>
        </div>
        <div>
          <span style="font-size:11px;color:var(--text2);text-transform:uppercase;">Lớp sinh hoạt</span>
          <div style="font-weight:500;" id="modal_lop"></div>
        </div>
        <div>
          <span style="font-size:11px;color:var(--text2);text-transform:uppercase;">Chức vụ</span>
          <div id="modal_chuc_vu"></div>
        </div>
        <div>
          <span style="font-size:11px;color:var(--text2);text-transform:uppercase;">Số điện thoại</span>
          <div id="modal_sdt"></div>
        </div>
        <div>
          <span style="font-size:11px;color:var(--text2);text-transform:uppercase;">Gmail</span>
          <div id="modal_email" style="word-break:break-all;"></div>
        </div>
        <div>
          <span style="font-size:11px;color:var(--text2);text-transform:uppercase;">Giới tính</span>
          <div id="modal_gioi_tinh"></div>
        </div>
        <div>
          <span style="font-size:11px;color:var(--text2);text-transform:uppercase;">Ngày sinh</span>
          <div id="modal_ngay_sinh"></div>
        </div>
        <div>
          <span style="font-size:11px;color:var(--text2);text-transform:uppercase;">Dân tộc</span>
          <div id="modal_dan_toc"></div>
        </div>
        <div>
          <span style="font-size:11px;color:var(--text2);text-transform:uppercase;">Chi bộ mong muốn</span>
          <div id="modal_chibo"></div>
        </div>
      </div>

      <div style="margin-bottom:16px;">
        <span style="font-size:11px;color:var(--text2);text-transform:uppercase;">Quê quán</span>
        <div id="modal_que_quan" style="background:var(--bg3);padding:10px;border-radius:8px;border:1px solid var(--border);margin-top:4px;"></div>
      </div>

      <div style="margin-bottom:16px;">
        <span style="font-size:11px;color:var(--text2);text-transform:uppercase;">Ghi chú bổ sung</span>
        <div id="modal_ghi_chu" style="background:var(--bg3);padding:10px;border-radius:8px;border:1px solid var(--border);margin-top:4px;white-space:pre-wrap;font-style:italic;"></div>
      </div>

      <div id="modal_ly_do_reject_view" style="display:none;margin-bottom:16px;">
        <span style="font-size:11px;color:var(--danger);text-transform:uppercase;font-weight:700;">Lý do đã từ chối</span>
        <div id="modal_ly_do_reject_text" style="background:rgba(239,68,68,0.06);padding:12px;border-radius:8px;border:1px solid rgba(239,68,68,0.2);margin-top:4px;color:#ff8080;"></div>
      </div>

      <!-- Reject Input Box (Toggles on Reject Click) -->
      <div id="rejectReasonBox" style="display:none; border-top:1px solid var(--border); padding-top:16px; margin-top:16px;">
        <form method="post" id="rejectForm">
          <input type="hidden" name="action" value="reject">
          <input type="hidden" name="id" id="reject_id">
          <div class="form-group">
            <label class="form-label" style="color:var(--danger)">Lý do từ chối hồ sơ (Sẽ gửi trực tiếp tới Gmail sinh viên) <span class="required">*</span></label>
            <textarea name="ly_do_tu_choi" class="form-control" placeholder="VD: Nhập thiếu thông tin quê quán / Mã sinh viên không tồn tại trong danh sách..." rows="3" required></textarea>
          </div>
          <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:12px;">
            <button type="button" class="btn btn-outline" onclick="cancelReject()">Hủy bỏ</button>
            <button type="submit" class="btn btn-danger">❌ Xác nhận Từ chối & Gửi mail</button>
          </div>
        </form>
      </div>

    </div>
    
    <!-- Action buttons for PENDING tab -->
    <div class="modal-actions" id="modalActionsPending">
      <button onclick="closeModal()" class="btn btn-outline">Đóng</button>
      <button onclick="showRejectBox()" class="btn btn-danger">❌ Từ chối</button>
      <form method="post" style="margin:0;">
        <input type="hidden" name="action" value="approve">
        <input type="hidden" name="id" id="approve_id">
        <button type="submit" class="btn btn-success">✅ Duyệt & Đồng bộ</button>
      </form>
    </div>

    <!-- Action buttons for Approved / Rejected tabs -->
    <div class="modal-actions" id="modalActionsClosed" style="display:none;">
      <button onclick="closeModal()" class="btn btn-outline">Đóng</button>
    </div>
  </div>
</div>

<script>
function openDetailsModal(row) {
  // Gán thông tin sinh viên vào modal
  document.getElementById('modal_name').textContent = row.ho_ten;
  document.getElementById('modal_ma').textContent = row.ma_gvsv;
  document.getElementById('modal_lop').textContent = row.lop;
  document.getElementById('modal_chuc_vu').textContent = row.chuc_vu ? row.chuc_vu : '—';
  document.getElementById('modal_sdt').textContent = row.sdt;
  document.getElementById('modal_email').textContent = row.email;
  document.getElementById('modal_gioi_tinh').textContent = row.gioi_tinh ? row.gioi_tinh : 'Chưa chọn';
  document.getElementById('modal_ngay_sinh').textContent = row.ngay_sinh ? formatDateString(row.ngay_sinh) : 'Chưa nhập';
  document.getElementById('modal_dan_toc').textContent = row.dan_toc ? row.dan_toc : '—';
  document.getElementById('modal_chibo').textContent = row.chi_bo_cong_nhan ? row.chi_bo_cong_nhan : 'Chưa chọn';
  document.getElementById('modal_que_quan').textContent = row.que_quan ? row.que_quan : '—';
  document.getElementById('modal_ghi_chu').textContent = row.ghi_chu ? row.ghi_chu : 'Không có ghi chú';

  // ID phê duyệt / từ chối
  document.getElementById('approve_id').value = row.id;
  document.getElementById('reject_id').value = row.id;

  // Ẩn/Hiện hành động tùy thuộc vào trạng thái hồ sơ
  const tab = '<?= $tabFilter ?>';
  const actionsPending = document.getElementById('modalActionsPending');
  const actionsClosed = document.getElementById('modalActionsClosed');
  const rejectView = document.getElementById('modal_ly_do_reject_view');
  
  // Reset Reject form box
  cancelReject();

  if (tab === 'pending') {
    actionsPending.style.display = 'flex';
    actionsClosed.style.display = 'none';
    rejectView.style.display = 'none';
  } else {
    actionsPending.style.display = 'none';
    actionsClosed.style.display = 'flex';
    if (tab === 'rejected' && row.ly_do_tu_choi) {
      rejectView.style.display = 'block';
      document.getElementById('modal_ly_do_reject_text').innerHTML = row.ly_do_tu_choi.replace(/\n/g, '<br>');
    } else {
      rejectView.style.display = 'none';
    }
  }

  document.getElementById('detailsModal').classList.add('open');
}

function showRejectBox() {
  document.getElementById('rejectReasonBox').style.display = 'block';
  document.getElementById('modalActionsPending').style.display = 'none';
  // Scroll modal body to bottom
  const modalBody = document.querySelector('.modal-body');
  setTimeout(() => { modalBody.scrollTop = modalBody.scrollHeight; }, 50);
}

function cancelReject() {
  document.getElementById('rejectReasonBox').style.display = 'none';
  if ('<?= $tabFilter ?>' === 'pending') {
    document.getElementById('modalActionsPending').style.display = 'flex';
  }
  document.getElementById('rejectForm').reset();
}

function closeModal() {
  document.getElementById('detailsModal').classList.remove('open');
}

// Bấm ra vùng ngoài modal để đóng
document.getElementById('detailsModal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

// Format date Y-m-d to d/m/Y cho JavaScript hiển thị
function formatDateString(str) {
  if (!str) return '';
  const pts = str.split('-');
  if (pts.length === 3) return pts[2] + '/' + pts[1] + '/' + pts[0];
  return str;
}
</script>

<?php require_once dirname(__DIR__) . '/Giao_dien/footer.php'; ?>
