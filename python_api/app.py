# Flask API cho xuất Excel – Hệ thống Kết nạp Đảng
# pip install -r requirements.txt
# python app.py

from flask import Flask, request, send_file, jsonify
from flask_cors import CORS
import pymysql
import pymysql.cursors
import openpyxl
from openpyxl.styles import (Font, PatternFill, Alignment, Border, Side,
                              GradientFill)
from openpyxl.utils import get_column_letter
from openpyxl.drawing.image import Image as XLImage
import io
import os
from datetime import datetime, date

app = Flask(__name__)
CORS(app)  # Allow PHP frontend to call

# ─── Database config ──────────────────────────────────────────────────────────
DB_CFG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'db': 'quan_ly_ket_nap_dang',
    'charset': 'utf8mb4',
    'cursorclass': pymysql.cursors.DictCursor,
    'autocommit': True,
}

def get_db():
    return pymysql.connect(**DB_CFG)

# ─── Constants ────────────────────────────────────────────────────────────────
RED   = 'C8102E'
GOLD  = 'FFD700'
WHITE = 'FFFFFF'
LIGHT = 'FFF3F3'
STRIPE= 'FFF8F8'

HEADERS = [
    'STT','Mã GV/SV','Họ và tên','SĐT','Giới tính','Ngày sinh','Dân tộc',
    'Quê quán','Chức vụ','Lớp','Chi bộ công nhận','Số BC cảm tình Đảng',
    'Ngày họp CB công nhận','Đảng viên giúp đỡ','Ngày phân công giúp đỡ',
    'Số QĐ mở lớp BD','Ngày QĐ mở lớp','Thời gian lớp BD','Ngày cấp CC',
    'Số QĐ CC BD','Đơn vị cấp CC','ĐV công tác khi cấp CC',
    'CB sinh hoạt khi cấp CC','Đảng uỷ khi cấp CC','Tỉnh uỷ khi cấp CC',
    'Mã số','Kết nạp Đảng','Ngày quyết định','Số QĐ kết nạp','Ngày kết nạp',
    'ĐV hướng dẫn','Ngày chuyển SH','Nơi chuyển tới','Trạng thái',
]

FIELDS = [
    'ma_gvsv','ho_ten','sdt','gioi_tinh','ngay_sinh','dan_toc','que_quan',
    'chuc_vu','lop','chi_bo_cong_nhan','so_bc_cam_tinh','ngay_hop_cam_tinh',
    'dang_vien_giup_do','ngay_phan_cong_giup_do','so_qd_mo_lop',
    'ngay_qd_mo_lop','tg_lop_boi_duong','ngay_cap_cc','so_qd_cc',
    'don_vi_cap_cc','ten_dv_congtac_khi_cap_cc','ten_chibo_khi_cap_cc',
    'ten_danguy_khi_cap_cc','ten_tinhuy_khi_cap_cc','ma_so','ket_nap_dang',
    'ngay_quyet_dinh','so_qd_ket_nap','ngay_ket_nap','dang_vien_huong_dan',
    'ngay_chuyen_sinh_hoat','noi_chuyen_toi','trang_thai',
]

DATE_FIELDS = {
    'ngay_sinh','ngay_hop_cam_tinh','ngay_phan_cong_giup_do','ngay_qd_mo_lop',
    'ngay_cap_cc','ngay_quyet_dinh','ngay_ket_nap','ngay_chuyen_sinh_hoat',
}

COL_WIDTHS = [
    6,12,28,14,10,14,14,28,16,22,28,22,18,22,18,
    18,16,28,16,16,22,28,22,22,22,12,18,16,18,16,
    22,16,25,16,
]

# ─── Helpers ──────────────────────────────────────────────────────────────────
def fmt_date(v):
    if not v: return ''
    if isinstance(v, (date,)): return v.strftime('%d/%m/%Y')
    return str(v)

def serialize_row(r):
    out = {}
    for k, v in r.items():
        if isinstance(v, (date,)): out[k] = fmt_date(v)
        elif v is None: out[k] = ''
        else: out[k] = v
    return out

def thin_border():
    s = Side(style='thin', color='CCCCCC')
    return Border(left=s, right=s, top=s, bottom=s)

def make_header_style():
    return {
        'font': Font(name='Times New Roman', bold=True, size=10, color=WHITE),
        'fill': PatternFill(start_color=RED, end_color=RED, fill_type='solid'),
        'align': Alignment(horizontal='center', vertical='center', wrap_text=True),
        'border': thin_border(),
    }

# ─── Workbook builders ────────────────────────────────────────────────────────
def build_list_workbook(rows: list, subtitle: str = '') -> openpyxl.Workbook:
    """Type 1 & 3: multi-row list workbook"""
    wb = openpyxl.Workbook()
    ws = wb.active
    ws.title = 'Danh sách'

    n_cols = len(HEADERS)
    last_col = get_column_letter(n_cols)

    # ── Title ──
    ws.merge_cells(f'A1:{last_col}1')
    c = ws['A1']
    c.value = 'DANH SÁCH QUẦN CHÚNG ƯU TÚ PHỤC VỤ KẾT NẠP ĐẢNG'
    c.font = Font(name='Times New Roman', bold=True, size=14, color=RED)
    c.alignment = Alignment(horizontal='center', vertical='center')
    c.fill = PatternFill(start_color=LIGHT, end_color=LIGHT, fill_type='solid')
    ws.row_dimensions[1].height = 32

    # ── Subtitle ──
    ws.merge_cells(f'A2:{last_col}2')
    sub = subtitle or ''
    ws['A2'].value = (f'{sub}  |  ' if sub else '') + f'Xuất ngày: {datetime.now().strftime("%d/%m/%Y %H:%M")}  |  Tổng: {len(rows)} người'
    ws['A2'].font = Font(name='Times New Roman', italic=True, size=10, color='555555')
    ws['A2'].alignment = Alignment(horizontal='center')
    ws.row_dimensions[2].height = 18

    # ── Star divider ──
    ws.merge_cells(f'A3:{last_col}3')
    ws['A3'].value = '⭐ ' * 10
    ws['A3'].alignment = Alignment(horizontal='center')
    ws['A3'].fill = PatternFill(start_color='C8102E', fill_type='solid')
    ws.row_dimensions[3].height = 8

    # ── Header row ──
    hs = make_header_style()
    ws.append(HEADERS)
    for col in range(1, n_cols + 1):
        cell = ws.cell(row=4, column=col)
        cell.font  = hs['font']
        cell.fill  = hs['fill']
        cell.alignment = hs['align']
        cell.border = hs['border']
    ws.row_dimensions[4].height = 42

    # ── Data rows ──
    border = thin_border()
    for i, row in enumerate(rows, 1):
        data = [i]
        for f in FIELDS[1:]:  # skip ma_gvsv which is HEADERS[1]
            v = row.get(f) or ''
            if f in DATE_FIELDS: v = fmt_date(v)
            data.append(str(v) if v else '')
        # Insert ma_gvsv as 2nd item
        data.insert(1, str(row.get('ma_gvsv') or ''))
        ws.append(data)

        dr = 4 + i
        fill_color = WHITE if i % 2 == 1 else STRIPE
        fill = PatternFill(start_color=fill_color, end_color=fill_color, fill_type='solid')
        for col in range(1, n_cols + 1):
            cell = ws.cell(row=dr, column=col)
            cell.font   = Font(name='Times New Roman', size=10)
            cell.fill   = fill
            cell.border = border
            cell.alignment = Alignment(vertical='center', wrap_text=True)
        ws.cell(row=dr, column=1).alignment = Alignment(horizontal='center', vertical='center')
        ws.row_dimensions[dr].height = 20

    # ── Tổng kết ──
    summary_row = 4 + len(rows) + 1
    ws.merge_cells(f'A{summary_row}:{last_col}{summary_row}')
    sc = ws[f'A{summary_row}']
    sc.value = f'Tổng số: {len(rows)} người'
    sc.font = Font(name='Times New Roman', bold=True, size=11, color=RED)
    sc.alignment = Alignment(horizontal='right', vertical='center', indent=2)
    sc.fill = PatternFill(start_color=LIGHT, fill_type='solid')
    ws.row_dimensions[summary_row].height = 24

    # ── Column widths ──
    for i, w in enumerate(COL_WIDTHS[:n_cols], 1):
        ws.column_dimensions[get_column_letter(i)].width = w

    ws.freeze_panes = 'A5'
    ws.print_title_rows = '1:4'
    ws.page_setup.orientation = 'landscape'
    ws.page_setup.fitToPage = True

    return wb


def build_profile_workbook(row: dict) -> openpyxl.Workbook:
    """Type 2: single person detailed profile"""
    wb = openpyxl.Workbook()
    ws = wb.active
    ws.title = 'Hồ sơ'

    border = thin_border()
    red_fill = PatternFill(start_color=RED, fill_type='solid')
    light_fill = PatternFill(start_color=LIGHT, fill_type='solid')
    stripe_fill = PatternFill(start_color=STRIPE, fill_type='solid')

    ws.column_dimensions['A'].width = 3
    ws.column_dimensions['B'].width = 38
    ws.column_dimensions['C'].width = 32
    ws.column_dimensions['D'].width = 32

    # ── Title ──
    ws.merge_cells('A1:D1')
    ws['A1'].value = 'HỒ SƠ QUẦN CHÚNG ƯU TÚ PHỤC VỤ KẾT NẠP ĐẢNG'
    ws['A1'].font = Font(name='Times New Roman', bold=True, size=15, color=RED)
    ws['A1'].alignment = Alignment(horizontal='center', vertical='center')
    ws['A1'].fill = light_fill
    ws.row_dimensions[1].height = 36

    # ── Name ──
    ws.merge_cells('A2:D2')
    ws['A2'].value = row.get('ho_ten', '')
    ws['A2'].font = Font(name='Times New Roman', bold=True, size=14, color='111111')
    ws['A2'].alignment = Alignment(horizontal='center', vertical='center')
    ws.row_dimensions[2].height = 30

    # ── Status badge ──
    ws.merge_cells('A3:D3')
    ws['A3'].value = f"Trạng thái: {row.get('trang_thai','')}  |  Lớp: {row.get('lop','')}  |  Mã: {row.get('ma_gvsv','')}"
    ws['A3'].font = Font(name='Times New Roman', italic=True, size=11, color='666666')
    ws['A3'].alignment = Alignment(horizontal='center')
    ws.row_dimensions[3].height = 20

    # ── Divider ──
    ws.merge_cells('A4:D4')
    ws['A4'].fill = PatternFill(start_color=RED, fill_type='solid')
    ws.row_dimensions[4].height = 6

    def add_section(title, fields, start_row):
        ws.merge_cells(f'A{start_row}:D{start_row}')
        c = ws[f'A{start_row}']
        c.value = title
        c.font = Font(name='Times New Roman', bold=True, size=11, color=WHITE)
        c.fill = red_fill
        c.alignment = Alignment(horizontal='left', vertical='center', indent=2)
        c.border = border
        ws.row_dimensions[start_row].height = 24
        r = start_row + 1
        for i, (label, value) in enumerate(fields):
            fill = light_fill if i % 2 == 0 else stripe_fill
            # Label cell
            lc = ws[f'B{r}']
            lc.value = label
            lc.font = Font(name='Times New Roman', bold=True, size=10, color='333333')
            lc.fill = fill
            lc.border = border
            lc.alignment = Alignment(vertical='center', indent=2)
            # Value cells (merge C:D)
            ws.merge_cells(f'C{r}:D{r}')
            vc = ws[f'C{r}']
            vc.value = str(value) if value else '—'
            vc.font = Font(name='Times New Roman', size=10)
            vc.fill = fill
            vc.border = border
            vc.alignment = Alignment(vertical='center', indent=2, wrap_text=True)
            ws.row_dimensions[r].height = 20
            r += 1
        return r + 1  # spacing

    sections = [
        ('I.  THÔNG TIN CÁ NHÂN', [
            ('Mã GV/SV',   row.get('ma_gvsv','')),
            ('Giới tính',  row.get('gioi_tinh','')),
            ('Ngày sinh',  fmt_date(row.get('ngay_sinh'))),
            ('Dân tộc',    row.get('dan_toc','')),
            ('Quê quán',   row.get('que_quan','')),
            ('SĐT',        row.get('sdt','')),
            ('Chức vụ',    row.get('chuc_vu','')),
            ('Lớp',        row.get('lop','')),
        ]),
        ('II.  CHI BỘ & CẢM TÌNH ĐẢNG', [
            ('Chi bộ công nhận',         row.get('chi_bo_cong_nhan','')),
            ('Số BC cảm tình Đảng',       row.get('so_bc_cam_tinh','')),
            ('Ngày họp CB công nhận',     fmt_date(row.get('ngay_hop_cam_tinh'))),
            ('Đảng viên giúp đỡ',         row.get('dang_vien_giup_do','')),
            ('Ngày phân công giúp đỡ',    fmt_date(row.get('ngay_phan_cong_giup_do'))),
        ]),
        ('III.  LỚP BỒI DƯỠNG NHẬN THỨC VỀ ĐẢNG', [
            ('Số QĐ mở lớp BD',           row.get('so_qd_mo_lop','')),
            ('Ngày QĐ mở lớp',            fmt_date(row.get('ngay_qd_mo_lop'))),
            ('Thời gian lớp BD',           row.get('tg_lop_boi_duong','')),
            ('Ngày cấp CC',                fmt_date(row.get('ngay_cap_cc'))),
            ('Số QĐ CC BD',                row.get('so_qd_cc','')),
            ('Đơn vị cấp CC',              row.get('don_vi_cap_cc','')),
            ('ĐV công tác khi cấp CC',     row.get('ten_dv_congtac_khi_cap_cc','')),
            ('CB sinh hoạt khi cấp CC',    row.get('ten_chibo_khi_cap_cc','')),
            ('Đảng uỷ khi cấp CC',         row.get('ten_danguy_khi_cap_cc','')),
            ('Tỉnh uỷ khi cấp CC',         row.get('ten_tinhuy_khi_cap_cc','')),
        ]),
        ('IV.  KẾT NẠP ĐẢNG', [
            ('Mã số',                      row.get('ma_so','')),
            ('Kết nạp Đảng',               row.get('ket_nap_dang','')),
            ('Ngày quyết định',            fmt_date(row.get('ngay_quyet_dinh'))),
            ('Số QĐ kết nạp',              row.get('so_qd_ket_nap','')),
            ('Ngày kết nạp',               fmt_date(row.get('ngay_ket_nap'))),
            ('Đảng viên hướng dẫn',        row.get('dang_vien_huong_dan','')),
        ]),
        ('V.  CHUYỂN SINH HOẠT', [
            ('Ngày chuyển sinh hoạt',      fmt_date(row.get('ngay_chuyen_sinh_hoat'))),
            ('Nơi chuyển tới',             row.get('noi_chuyen_toi','')),
            ('Trạng thái hiện tại',        row.get('trang_thai','')),
        ]),
    ]

    current = 5
    for title, fields in sections:
        current = add_section(title, fields, current)

    # Footer
    ws.merge_cells(f'A{current}:D{current}')
    ws[f'A{current}'].value = f'In ngày: {datetime.now().strftime("%d/%m/%Y %H:%M")}  |  Hệ thống Quản lý Kết nạp Đảng'
    ws[f'A{current}'].font = Font(name='Times New Roman', italic=True, size=9, color='999999')
    ws[f'A{current}'].alignment = Alignment(horizontal='right', indent=2)
    ws.row_dimensions[current].height = 20

    return wb

# ─── Routes ───────────────────────────────────────────────────────────────────
@app.route('/health')
def health():
    try:
        db = get_db()
        with db.cursor() as cur:
            cur.execute("SELECT COUNT(*) as n FROM doi_tuong")
            n = cur.fetchone()['n']
        db.close()
        return jsonify({'status': 'ok', 'total': n, 'version': '1.0'})
    except Exception as e:
        return jsonify({'status': 'error', 'msg': str(e)}), 500


@app.route('/api/filters')
def get_filters():
    db = get_db()
    try:
        with db.cursor() as cur:
            cur.execute("SELECT DISTINCT lop FROM doi_tuong WHERE lop IS NOT NULL AND lop!='' ORDER BY lop")
            lops = [r['lop'] for r in cur.fetchall()]
            cur.execute("SELECT DISTINCT chi_bo_cong_nhan FROM doi_tuong WHERE chi_bo_cong_nhan IS NOT NULL AND chi_bo_cong_nhan!='' ORDER BY chi_bo_cong_nhan")
            chibos = [r['chi_bo_cong_nhan'] for r in cur.fetchall()]
            cur.execute("SELECT COUNT(*) as n FROM doi_tuong")
            total = cur.fetchone()['n']
        return jsonify({'lops': lops, 'chibos': chibos, 'total': total})
    finally:
        db.close()


@app.route('/api/list')
def get_list():
    ft = request.args.get('filter_type', 'all')
    fv = request.args.get('filter_value', '')
    db = get_db()
    try:
        with db.cursor() as cur:
            if ft == 'lop' and fv:
                cur.execute("SELECT id,ma_gvsv,ho_ten,gioi_tinh,lop,chi_bo_cong_nhan,trang_thai,ngay_ket_nap,avatar FROM doi_tuong WHERE lop=%s ORDER BY ho_ten", (fv,))
            elif ft == 'chibo' and fv:
                cur.execute("SELECT id,ma_gvsv,ho_ten,gioi_tinh,lop,chi_bo_cong_nhan,trang_thai,ngay_ket_nap,avatar FROM doi_tuong WHERE chi_bo_cong_nhan=%s ORDER BY ho_ten", (fv,))
            else:
                cur.execute("SELECT id,ma_gvsv,ho_ten,gioi_tinh,lop,chi_bo_cong_nhan,trang_thai,ngay_ket_nap,avatar FROM doi_tuong ORDER BY ho_ten")
            rows = [serialize_row(r) for r in cur.fetchall()]
        return jsonify(rows)
    finally:
        db.close()


@app.route('/api/export/all', methods=['POST'])
def export_all():
    data = request.json or {}
    ft = data.get('filter_type', 'all')
    fv = data.get('filter_value', '')
    db = get_db()
    try:
        with db.cursor() as cur:
            if ft == 'lop' and fv:
                cur.execute("SELECT * FROM doi_tuong WHERE lop=%s ORDER BY ho_ten", (fv,))
                subtitle = f'Lớp: {fv}'
                fname = f'DanhSach_Lop_{fv.replace(" ","_")}'
            elif ft == 'chibo' and fv:
                cur.execute("SELECT * FROM doi_tuong WHERE chi_bo_cong_nhan=%s ORDER BY ho_ten", (fv,))
                subtitle = f'Chi bộ: {fv}'
                fname = f'DanhSach_ChiBo'
            else:
                cur.execute("SELECT * FROM doi_tuong ORDER BY ho_ten")
                subtitle = 'Toàn trường'
                fname = 'DanhSach_ToanBo'
            rows = cur.fetchall()

        wb = build_list_workbook(rows, subtitle)
        buf = io.BytesIO()
        wb.save(buf); buf.seek(0)
        dl = f'{fname}_{datetime.now().strftime("%Y%m%d_%H%M")}.xlsx'
        return send_file(buf, as_attachment=True, download_name=dl,
                         mimetype='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
    finally:
        db.close()


@app.route('/api/export/single/<int:pid>')
def export_single(pid):
    db = get_db()
    try:
        with db.cursor() as cur:
            cur.execute("SELECT * FROM doi_tuong WHERE id=%s", (pid,))
            row = cur.fetchone()
        if not row:
            return jsonify({'error': 'Not found'}), 404

        wb = build_profile_workbook(row)
        buf = io.BytesIO()
        wb.save(buf); buf.seek(0)
        name = (row.get('ho_ten') or 'HoSo').replace(' ', '_')
        dl = f'HoSo_{name}_{datetime.now().strftime("%Y%m%d")}.xlsx'
        return send_file(buf, as_attachment=True, download_name=dl,
                         mimetype='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
    finally:
        db.close()


@app.route('/api/export/selected', methods=['POST'])
def export_selected():
    data = request.json or {}
    ids = data.get('ids', [])
    if not ids:
        return jsonify({'error': 'No IDs'}), 400
    db = get_db()
    try:
        with db.cursor() as cur:
            ph = ','.join(['%s'] * len(ids))
            cur.execute(f"SELECT * FROM doi_tuong WHERE id IN ({ph}) ORDER BY ho_ten", ids)
            rows = cur.fetchall()
        wb = build_list_workbook(rows, f'Danh sách chọn lọc ({len(rows)} người)')
        buf = io.BytesIO()
        wb.save(buf); buf.seek(0)
        dl = f'DanhSach_ChonLoc_{datetime.now().strftime("%Y%m%d_%H%M")}.xlsx'
        return send_file(buf, as_attachment=True, download_name=dl,
                         mimetype='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
    finally:
        db.close()


if __name__ == '__main__':
    print("=" * 50)
    print("  Flask API – Hệ thống Kết nạp Đảng v1.0")
    print("  Running at: http://localhost:5000")
    print("  Health check: http://localhost:5000/health")
    print("=" * 50)
    app.run(host='0.0.0.0', port=5000, debug=False)
