<?php
// Chucnang/xuat_excel.php – Xuất dữ liệu ra Excel (CSV-based XLSX-compatible)
require_once dirname(__DIR__) . '/config.php';

$db = getDB();

// Filters from querystring
$search    = trim($_GET['search'] ?? '');
$trangThai = $_GET['trang_thai'] ?? '';
$lop       = $_GET['lop'] ?? '';

$where  = ['1=1']; $params = [];
if ($search)    { $where[] = "(ho_ten LIKE ? OR ma_gvsv LIKE ? OR sdt LIKE ? OR lop LIKE ?)"; $params = array_merge($params, ["%$search%","%$search%","%$search%","%$search%"]); }
if ($trangThai) { $where[] = "trang_thai = ?"; $params[] = $trangThai; }
if ($lop)       { $where[] = "lop LIKE ?"; $params[] = "%$lop%"; }
$whereStr = implode(' AND ', $where);

$stmt = $db->prepare("SELECT * FROM doi_tuong WHERE $whereStr ORDER BY created_at ASC");
$stmt->execute($params);
$rows = $stmt->fetchAll();

$headers = [
    'STT','Mã GV/SV','Họ và tên','SĐT','Giới tính','Ngày sinh','Dân tộc','Quê quán','Chức vụ','Lớp',
    'Chi bộ công nhận','Số BC cảm tình Đảng','Ngày họp CB công nhận',
    'Đảng viên giúp đỡ','Ngày phân công giúp đỡ',
    'Số QĐ mở lớp BD','Ngày QĐ mở lớp','Thời gian lớp BD','Ngày cấp CC','Số QĐ CC BD',
    'Đơn vị cấp CC','ĐV công tác khi cấp CC','CB sinh hoạt khi cấp CC','Đảng uỷ khi cấp CC','Tỉnh uỷ khi cấp CC',
    'Mã số','Kết nạp Đảng','Ngày quyết định','Số QĐ kết nạp','Ngày kết nạp',
    'ĐV hướng dẫn','Ngày chuyển SH','Nơi chuyển tới','Trạng thái','Ghi chú'
];

$fields = [
    'ma_gvsv','ho_ten','sdt','gioi_tinh','ngay_sinh','dan_toc','que_quan','chuc_vu','lop',
    'chi_bo_cong_nhan','so_bc_cam_tinh','ngay_hop_cam_tinh',
    'dang_vien_giup_do','ngay_phan_cong_giup_do',
    'so_qd_mo_lop','ngay_qd_mo_lop','tg_lop_boi_duong','ngay_cap_cc','so_qd_cc',
    'don_vi_cap_cc','ten_dv_congtac_khi_cap_cc','ten_chibo_khi_cap_cc','ten_danguy_khi_cap_cc','ten_tinhuy_khi_cap_cc',
    'ma_so','ket_nap_dang','ngay_quyet_dinh','so_qd_ket_nap','ngay_ket_nap',
    'dang_vien_huong_dan','ngay_chuyen_sinh_hoat','noi_chuyen_toi','trang_thai','ghi_chu'
];

$dateFields = ['ngay_sinh','ngay_hop_cam_tinh','ngay_phan_cong_giup_do','ngay_qd_mo_lop','ngay_cap_cc','ngay_quyet_dinh','ngay_ket_nap','ngay_chuyen_sinh_hoat'];

$filename = 'DanhSach_KetNapDang_' . date('Ymd_His') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache');

$out = fopen('php://output', 'w');
// UTF-8 BOM for Excel
fwrite($out, "\xEF\xBB\xBF");

fputcsv($out, $headers);

foreach ($rows as $i => $row) {
    $line = [$i + 1];
    foreach ($fields as $f) {
        $val = $row[$f] ?? '';
        if (in_array($f, $dateFields) && $val) $val = formatDate($val);
        $line[] = $val;
    }
    fputcsv($out, $line);
}
fclose($out);
exit;
