/**
 * AI_Module/excel_column_agent.js
 * Intelligent Column Mapper Agent for Excel/CSV File Imports
 */

const DB_COLUMNS_DICTIONARY = [
    { field: 'ma_gvsv', label: 'Mã GV/SV', keywords: ['mã', 'ma_gvsv', 'masv', 'magv', 'mssv', 'mã sv', 'mã gv/sv', 'stt/mã', 'mã số sinh viên'] },
    { field: 'ho_ten', label: 'Họ và tên', keywords: ['họ và tên', 'ho_ten', 'họ tên', 'hoten', 'full name', 'tên', 'tên sinh viên'] },
    { field: 'sdt', label: 'Số điện thoại (SĐT)', keywords: ['sđt', 'sdt', 'điện thoại', 'so dien thoai', 'phone', 'mobile'] },
    { field: 'gioi_tinh', label: 'Giới tính', keywords: ['giới tính', 'gioi_tinh', 'phái', 'sex', 'gender'] },
    { field: 'ngay_sinh', label: 'Ngày sinh', keywords: ['ngày sinh', 'ngay_sinh', 'dob', 'sinh nhật', 'ngày tháng năm sinh'] },
    { field: 'dan_toc', label: 'Dân tộc', keywords: ['dân tộc', 'dan_toc', 'ethno', 'ethnicity'] },
    { field: 'que_quan', label: 'Quê quán', keywords: ['quê quán', 'que_quan', 'hộ khẩu', 'thường trú', 'quê', 'nguyên quán'] },
    { field: 'chuc_vu', label: 'Chức vụ', keywords: ['chức vụ', 'chuc_vu', 'quản lý', 'qli', 'ql', 'chức danh', 'vị trí', 'bán cán sự'] },
    { field: 'lop', label: 'Lớp sinh hoạt', keywords: ['lớp', 'lop', 'chi đoàn', 'lớp học', 'khóa', 'chi đoàn lớp'] },
    { field: 'chi_bo_cong_nhan', label: 'Chi bộ công nhận', keywords: ['chi bộ', 'chi_bo', 'chi bộ công nhận', 'đơn vị chi bộ', 'chi bộ sinh hoạt'] },
    { field: 'so_bc_cam_tinh', label: 'Số BC cảm tình Đảng', keywords: ['báo cáo', 'bc cảm tình', 'số bc', 'so_bc_cam_tinh'] },
    { field: 'ngay_hop_cam_tinh', label: 'Ngày họp CB công nhận', keywords: ['ngày họp', 'ngay_hop_cam_tinh', 'ngày nhận thức'] },
    { field: 'dang_vien_giup_do', label: 'Đảng viên giúp đỡ', keywords: ['đảng viên', 'giúp đỡ', 'dang_vien_giup_do', 'người giới thiệu', 'đv giúp đỡ'] },
    { field: 'ngay_phan_cong_giup_do', label: 'Ngày phân công giúp đỡ', keywords: ['ngày phân công', 'ngay_phan_cong_giup_do'] },
    { field: 'so_qd_mo_lop', label: 'Số QĐ mở lớp BD', keywords: ['qđ mở lớp', 'số qđ mở lớp', 'so_qd_mo_lop'] },
    { field: 'ngay_qd_mo_lop', label: 'Ngày QĐ mở lớp', keywords: ['ngày qđ mở lớp', 'ngay_qd_mo_lop'] },
    { field: 'tg_lop_boi_duong', label: 'Thời gian lớp BD', keywords: ['thời gian lớp', 'tg_lop_boi_duong'] },
    { field: 'ngay_cap_cc', label: 'Ngày cấp chứng chỉ', keywords: ['ngày cấp', 'ngay_cap_cc', 'ngày cấp cc'] },
    { field: 'so_qd_cc', label: 'Số QĐ chứng chỉ', keywords: ['số chứng chỉ', 'số qđ cc', 'so_qd_cc'] },
    { field: 'don_vi_cap_cc', label: 'Đơn vị cấp chứng chỉ', keywords: ['đơn vị cấp', 'don_vi_cap_cc'] },
    { field: 'ten_dv_congtac_khi_cap_cc', label: 'Tên ĐV công tác khi cấp CC', keywords: ['đv công tác', 'ten_dv_congtac_khi_cap_cc'] },
    { field: 'ten_chibo_khi_cap_cc', label: 'Tên Chi bộ khi cấp CC', keywords: ['chi bộ khi cấp', 'ten_chibo_khi_cap_cc'] },
    { field: 'ten_danguy_khi_cap_cc', label: 'Tên Đảng ủy khi cấp CC', keywords: ['đảng ủy khi cấp', 'ten_danguy_khi_cap_cc'] },
    { field: 'ten_tinhuy_khi_cap_cc', label: 'Tên Tỉnh ủy khi cấp CC', keywords: ['tỉnh ủy khi cấp', 'ten_tinhuy_khi_cap_cc'] },
    { field: 'ma_so', label: 'Mã số hồ sơ', keywords: ['mã số', 'ma_so', 'mã hồ sơ'] },
    { field: 'ket_nap_dang', label: 'Kết nạp Đảng', keywords: ['kết nạp', 'ket_nap_dang', 'ngày xét'] },
    { field: 'ngay_quyet_dinh', label: 'Ngày quyết định', keywords: ['ngày quyết định', 'ngay_quyet_dinh'] },
    { field: 'so_qd_ket_nap', label: 'Số QĐ kết nạp', keywords: ['số qđ kết nạp', 'so_qd_ket_nap'] },
    { field: 'ngay_ket_nap', label: 'Ngày kết nạp', keywords: ['ngày kết nạp', 'ngay_ket_nap'] },
    { field: 'dang_vien_huong_dan', label: 'Đảng viên hướng dẫn', keywords: ['đảng viên hướng dẫn', 'dang_vien_huong_dan'] },
    { field: 'ngay_chuyen_sinh_hoat', label: 'Ngày chuyển sinh hoạt', keywords: ['ngày chuyển', 'ngay_chuyen_sinh_hoat'] },
    { field: 'noi_chuyen_toi', label: 'Nơi chuyển tới', keywords: ['nơi chuyển', 'noi_chuyen_toi'] },
    { field: 'trang_thai', label: 'Trạng thái kết nạp', keywords: ['trạng thái', 'trang_thai', 'tình trạng'] }
];

/**
 * Clean & Normalize Vietnamese Header String
 */
function normalizeHeader(str) {
    if (!str) return '';
    return str.toString().toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd').replace(/[^a-z0-9]/g, ' ').trim();
}

/**
 * Smart Column Matching Agent
 * Calculates similarity score between uploaded header and DB Dictionary
 */
function matchExcelColumn(uploadedHeader) {
    if (!uploadedHeader) return { field: '', confidence: 0 };
    const norm = normalizeHeader(uploadedHeader);

    let bestMatch = null;
    let maxScore = 0;

    // Special Check for Explicit ID tags: [ID: ho_ten]
    const idMatch = uploadedHeader.toString().match(/\[ID:\s*([a_z0_9_]+)\]/i);
    if (idMatch && idMatch[1]) {
        const targetField = idMatch[1].toLowerCase().trim();
        const found = DB_COLUMNS_DICTIONARY.find(item => item.field === targetField);
        if (found) {
            return { field: found.field, label: found.label, confidence: 1.0 };
        }
    }

    for (const item of DB_COLUMNS_DICTIONARY) {
        for (const kw of item.keywords) {
            const normKw = normalizeHeader(kw);
            if (norm === normKw) {
                return { field: item.field, label: item.label, confidence: 1.0 };
            }
            if (norm.includes(normKw) || normKw.includes(norm)) {
                const score = 0.8;
                if (score > maxScore) {
                    maxScore = score;
                    bestMatch = { field: item.field, label: item.label, confidence: score };
                }
            }
        }
    }

    return bestMatch || { field: '', label: '', confidence: 0 };
}

window.ExcelColumnAgent = {
    dictionary: DB_COLUMNS_DICTIONARY,
    matchColumn: matchExcelColumn,
    normalize: normalizeHeader
};
