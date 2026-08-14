# CẤU TRÚC VÀ NGUYÊN LÝ HOẠT ĐỘNG DỰ ÁN QLQTUT-DANG
## QUẢN LÝ QUẦN CHÚNG ƯU TÚ PHỤC VỤ KẾT NẠP ĐẢNG

---

## I. TỔNG QUAN DỰ ÁN

- **Tên dự án:** Website Quản Lý Quần Chúng Ưu Tú Phục Vụ Kết Nạp Đảng
- **Đơn vị/Trường:** Đại Học Tây Bắc (UTB)
- **Kiến trúc Hệ thống:** **Phân tầng Lai & Microservice-Lite (Layered & Microservice-Lite Architecture)**
- **Phong cách Giao diện:** **Minimal Typography & Glassmorphism Dark Mode** (Tối giản chuẩn BEM, 100% không dùng Icon/Emoji rác, phân cấp chữ sắc nét)
- **Công nghệ nền tảng:**
  - **Backend chính:** PHP 8.x + MySQL (PDO Driver, Prepared Statements chống SQL Injection)
  - **Microservice Báo cáo & PDF Engine:** Python 3.x Flask (`python_api/app.py`) + `reportlab` + `openpyxl`
  - **Frontend UI/UX:** HTML5 + Vanilla CSS Modular System + JavaScript ES6
  - **Trí tuệ nhân tạo Client-side (Edge AI Engine):**
    - **OCR & Field Inspection:** `Tesseract.js` + `pdf.js` chạy trực tiếp tại Trình duyệt
    - **Edge AI Models:** 5 Model phân loại phiếu và kiểm tra thông tin chi tiết từng tệp (`edge_ai_ocr.js`)
    - **Auto-Fill CCCD/Thẻ SV & Smart Avatar Crop 3x4:** `AI_Module/edge_ai_autofill.js`
    - **AI Agent Ánh Xạ Cột Excel:** `AI_Module/excel_column_agent.js`

---

## II. THƯ MỤC & FILE TRONG DỰ ÁN

```
web1/
├── index.php                          ← Dashboard phân quyền (Sinh viên / Quản lý / Admin), Tin thời sự 3 nguồn
├── config.php                         ← Cấu hình PDO Database, BASE_URL, SITE_NAME, timezone, helper functions (e(), getDB())
├── setup_newcomputer.bat              ← Script 1-click tự động khởi tạo môi trường, DB & Flask Microservice
├── chitiet.md                         ← Báo cáo chi tiết đồ án (Business logic, Use cases, Sequence diagrams, ERD)
├── CAU_TRUC_DU_AN.md                  ← Document tổng hợp cấu trúc thư mục & kiến trúc hệ thống
├── README.md                          ← Hướng dẫn sử dụng & thông tin thành viên nhóm
│
├── Giao_dien/                         ← Cấu phần giao diện dùng chung
│   ├── header.php                     ← Thanh Header & Sidebar đa cấp (tự động gắn dynamic role class: role-user, role-manager, role-admin)
│   ├── footer.php                     ← Chân trang & Script khởi tạo UI
│   └── assets/
│       ├── style.css                  ← File CSS tổng nhập 6 module styling bên dưới bằng @import
│       └── styles/                    ← Thư mục module hóa CSS thiết kế riêng cho 3 giao diện (Tối giản Icon-Free)
│           ├── variables.css          ← Design tokens (bảng màu HSL, dark mode layers, shadows, radii)
│           ├── base.css               ← Layouts, Header, Sidebar, Scrollbar & Typography (Không dùng icon)
│           ├── components.css         ← Cards, Buttons, Data Tables, Form Controls, Badges, Modals, Timeline
│           ├── user.css               ← Giao diện & màu sắc riêng dành cho Sinh viên / Người dùng thường (Emerald Theme)
│           ├── manager.css            ← Giao diện & màu sắc riêng dành cho Quản lý / Bí thư Chi bộ (Crimson Theme)
│           └── admin.css              ← Giao diện & màu sắc riêng dành cho Admin / Quản trị viên (Violet Theme)
│
├── User/                              ← Quản lý Tài khoản & Phân quyền RBAC
│   ├── auth.php                       ← Hàm requireLogin(), getCurrentUser(), getFlash(), requireRole()
│   ├── login.php                      ← Trang Đăng nhập & Xác thực credentials
│   ├── register.php                   ← Trang Đăng ký tài khoản mới chọn vai trò
│   └── logout.php                     ← Hủy session & Đăng xuất
│
├── Quan_ly_doi_tuong/                 ← Quản lý Quần chúng & Edge AI Kiểm tra Hồ sơ
│   ├── danh_sach.php                  ← Danh sách quần chúng chính thức (Bộ lọc đa năng, Phân trang, Xóa hàng loạt)
│   ├── them.php                       ← Thêm đối tượng quần chúng mới (Form đầy đủ 35 trường)
│   ├── sua.php                        ← Chỉnh sửa chi tiết thông tin đối tượng
│   ├── xoa.php                        ← Xóa đơn lẻ hoặc Xóa hàng loạt đối tượng (Bulk delete)
│   ├── sua_nhanh.php                  ← Bảng chỉnh sửa trực tiếp dạng Excel (Autosave AJAX)
│   ├── api_sua_nhanh.php              ← API AJAX tiếp nhận dữ liệu autosave từ sua_nhanh.php
│   ├── nhap_thong_tin.php             ← Form đăng ký quần chúng dành cho Sinh viên (Auto-fill CCCD bằng AI)
│   ├── cap_nhat_thong_tin.php         ← Form đề xuất cập nhật thông tin cá nhân dành cho Sinh viên
│   ├── duyet_dang_ky.php              ← Giao diện 2 Tab Phê duyệt: Tab 1 (Đăng ký mới), Tab 2 (Đề xuất cập nhật)
│   ├── thanh_vien_chi_bo.php          ← Trang xem danh sách bạn cùng Lớp/Chi bộ đã được duyệt
│   ├── chi_tiet.php                   ← Xem hồ sơ chi tiết 1 đối tượng & In ấn
│   ├── edge_ai_check.php              ← Trang kiểm tra Hồ sơ minh chứng bằng Edge AI OCR Client-side
│   ├── edge_ai_ocr.js                 ← AI Engine: 5 Models phân loại tệp phiếu & Soi thông tin chi tiết từng trường
│   └── api_save_ai_check.php          ← API tiếp nhận tệp tải lên và lưu nhật ký đánh giá vào edge_ai_logs
│
├── Thong_ke_bao_cao/                  ← Báo cáo Thống kê & Xuất/Nhập Dữ liệu
│   ├── thong_ke.php                   ← Biểu đồ chỉ số phát triển Đảng viên (Chart.js Bar/Doughnut/Line)
│   ├── tim_kiem.php                   ← Tìm kiếm nâng cao kết hợp nhiều tiêu chí
│   ├── import_excel.php               ← Nhập dữ liệu Excel/CSV (Tích hợp AI Column Agent)
│   ├── xuat_excel.php                 ← Xuất file Excel toàn bộ hoặc Xuất Mẫu phiếu PDF 2026 (Có bôi đỏ dữ liệu)
│   └── api_proxy.php                  ← PHP Proxy chuyển tiếp Request sang Python Flask API (Cổng 5000)
│
├── Quan_ly_danh_muc/                  ← Danh mục Hệ thống
│   ├── chi_bo.php                     ← Quản lý danh mục Chi bộ Đảng (CRUD)
│   └── dang_vien.php               ← Quản lý danh mục Đảng viên hướng dẫn/giúp đỡ (CRUD)
│
├── He_thong/                          ← Quản trị Hệ thống
│   └── cai_dat.php                    ← Cài đặt Tên trường, Đảng bộ, Địa chỉ & Quản lý tài khoản Admin
│
├── AI_Module/                         ← Các Module AI bổ trợ Client-side
│   ├── edge_ai_autofill.js            ← OCR nhận diện CCCD/Thẻ SV & Cắt ảnh chân dung 3x4 tự động
│   ├── excel_column_agent.js          ← Client-side Agent phân tích & ánh xạ tiêu đề cột Excel thông minh
│   ├── document_inspector.js          ← Module chuyên dụng soi & cảnh báo các trường thông tin bị thiếu trong phiếu
│   └── readme_ai.md                   ← Hướng dẫn chi tiết các tính năng AI
│
├── python_api/                        ← Python Microservice Server
│   ├── app.py                         ← Flask Web Server (Export Excel openpyxl & PDF ReportLab 8 Mẫu 2026)
│   ├── requirements.txt               ← Danh sách thư viện Python (flask, pymysql, openpyxl, reportlab)
│   └── start_api.bat                  ← Script khởi chạy Flask API tại http://localhost:5000
│
├── Cau_hinh/                          ← Cấu hình Cơ sở dữ liệu
│   ├── db.sql                         ← Schema CSDL MySQL & dữ liệu mẫu ban đầu
│   └── setup.php                      ← Script tự động tạo bảng CSDL
│
├── uploads/                           ← Lưu trữ Tệp tải lên
│   ├── avatars/                       ← Ảnh chân dung quần chúng
│   └── ho_so_minh_chung/              ← Tệp minh chứng PDF/Ảnh được lưu từ Edge AI
│
└── Pic_for_all/                       ← Tài nguyên Hình ảnh & Logo Đại học Tây Bắc
```

---

## III. THIẾT KẾ KIẾN TRÚC HỆ THỐNG (SYSTEM ARCHITECTURE)

### 1. Mô hình Phân tầng (Layered Architecture)
- **Presentation Layer:** Giao diện HTML5/CSS3 chuẩn BEM, áp dụng phong cách Minimal Typography loại bỏ hoàn toàn icon rác.
- **Business Logic Layer:** Controller PHP xử lý nghiệp vụ, Helper `User/auth.php` phân quyền RBAC 3 lớp, Python Flask API Microservice xử lý file PDF/Excel.
- **Data Access Layer:** PHP PDO Connection với Prepared Statements truy vấn cơ sở dữ liệu MySQL.

### 2. Ma trận Phân quyền (RBAC Matrix)
- **User (Sinh viên):** Đăng ký hồ sơ, đề xuất cập nhật thông tin, xem Dashboard tiến trình 5 bước cá nhân, xem bạn cùng lớp, chạy Edge AI điền form & kiểm tra minh chứng.
- **Manager (Bí thư Chi bộ):** Đầy đủ quyền quản lý hồ sơ đối tượng, phê duyệt đơn đăng ký & đề xuất cập nhật, sửa nhanh Excel, xóa hàng loạt, import/export Excel/PDF, thống kê Chart.js.
- **Admin (Quản trị viên):** Quyền tối cao bao gồm toàn bộ chức năng Quản lý + Quản lý tài khoản người dùng & Cài đặt thông tin trường.

---

## IV. MÔ HÌNH DỮ LIỆU & BẢNG CSDL (DATABASE SCHEMA)

1. **`nguoi_dung`**: Tài khoản đăng nhập, mật khẩu băm (`password_hash`), vai trò (`Người dùng thường`, `Quản lý`, `Admin`).
2. **`doi_tuong`**: Hồ sơ quần chúng ưu tú chính thức (35 trường thông tin chi tiết: cá nhân, lớp, chi bộ, quá trình học cảm tính, quyết định kết nạp, ngày kết nạp...).
3. **`dang_ky_doi_tuong`**: Hàng chờ phê duyệt các đơn đăng ký trực tuyến từ Sinh viên.
4. **`yeu_cau_cap_nhat`**: Hàng chờ phê duyệt đề xuất thay đổi thông tin cá nhân của Quần chúng.
5. **`chi_bo`**: Danh mục các Chi bộ Đảng trực thuộc.
6. **`dang_vien`**: Danh mục các Đảng viên chính thức được phân công giúp đỡ quần chúng.
7. **`lich_su`**: Nhật ký ghi vết mọi thao tác thêm, sửa, xóa, duyệt trong hệ thống.
8. **`cai_dat`**: Lưu cấu hình tên trường, tên Đảng bộ, thông tin liên hệ toàn hệ thống.
9. **`edge_ai_logs`**: Nhật ký phân tích hồ sơ minh chứng từ Edge AI Engine (`user_id`, `trang_thai`, `raw_summary`, `files_json`).

---

## V. ĐẶC TẢ MÔ HÌNH EDGE AI KIỂM TRA HỒ SƠ (`edge_ai_ocr.js` & `document_inspector.js`)

Edge AI Engine chạy hoàn toàn client-side (không làm nặng Server), tích hợp **5 Document Models** tiêu chuẩn cho bộ hồ sơ phát triển Đảng:

| STT | Tên Model Phiếu/Hồ sơ | Mã Model | Các Trường Thông Tin Chi Tiết Soi Bắt Buộc |
| :--- | :--- | :--- | :--- |
| **1** | **Bản tự nhận xét / Tự kiểm điểm** | `ban_tu_nhan_xet` | Họ tên, Ngày sinh, Ưu điểm/Thành tích, Khuyết điểm/Hạn chế, Phương hướng phấn đấu, Ngày tháng & Chữ ký |
| **2** | **Giấy chứng nhận bồi dưỡng nhận thức về Đảng** | `giay_chung_nhan` | Đơn vị cấp (ĐH Tây Bắc / Trung tâm chính trị), Họ tên học viên, Ngày sinh, Xếp loại, Số QĐ/Số chứng nhận, Ngày cấp |
| **3** | **Sơ yếu lý lịch / CCCD / Thẻ SV** | `ho_so_ca_nhan` | Họ tên, Ngày sinh, Quê quán/Nguyên quán, Mã SV/Số CCCD, Lớp/Khoa sinh hoạt |
| **4** | **Phiếu đánh giá chất lượng đoàn viên** | `phieu_danh_gia` | Họ tên đoàn viên, Tên Chi đoàn, Kết quả xếp loại đoàn viên, Xác nhận/Chữ ký Bí thư Chi đoàn |
| **5** | **Minh chứng hoạt động phong trào / Giấy khen** | `minh_chung_hoat_dong` | Tên hoạt động (Hiến máu, Tình nguyện...), Họ tên người nhận, Đơn vị khen thưởng, Thời gian thực hiện |

### Quy trình & Thuật toán Thẩm định:
1. **OCR Trích xuất Client-side:** Tệp PDF/Ảnh tải lên được trích xuất văn bản bằng `pdf.js` / `Tesseract.js`.
2. **Weighted Keyword Matrix Classification (Phân loại Mô hình):** Chấm điểm từ khóa loại phiếu (+2 điểm) và tên file (+3 điểm) để gán tệp vào Mẫu phiếu chính xác nhất.
3. **Multi-Key Substring Matching & Regex Traversal (Soi Trường Thiếu & Trích xuất):**
   - Lớp `DocumentFieldInspector` duyệt ma trận từ khóa mở rộng của từng trường thông tin bắt buộc.
   - Nếu **không phát hiện từ khóa** ➔ Đánh dấu `missingFields` và phát cảnh báo đỏ `[CẢNH BÁO THIẾU]`.
   - Nếu **phát hiện từ khóa** ➔ Dùng `extractValueSnippet()` trích xuất đoạn dữ liệu thực tế (`extractedValue`).
4. **Portfolio Aggregation:** Tổng hợp trạng thái 5 loại phiếu: **✅ Đầy đủ 100%**, **⚠️ Đã nộp nhưng thiếu trường thông tin chi tiết**, **❌ Chưa nộp (Thiếu phiếu)**.
5. **Interactive UI Report & Persistence:** Hiển thị báo cáo trực quan và lưu nhật ký đánh giá vào MySQL `edge_ai_logs` qua `api_save_ai_check.php`.

---

## VI. CÁC ĐIỂM CẦN LƯU Ý KHI PHÁT TRIỂN / SỬA CODE

1. **Tham chiếu tài khoản Đăng nhập:**
   - Trong `Giao_dien/header.php`, biến lưu người dùng hiện tại là `$currentUser` (`$currentUser = getCurrentUser()`). Không dùng `$user` trong `header.php` để tránh lỗi `Undefined variable $user`.
2. **Giao diện Sinh viên vs Bí thư/Admin:**
   - `header.php` tự động kiểm tra `$vaiTro === 'Người dùng thường'` để tùy biến danh mục Sidebar (Hồ sơ cá nhân vs Nghiệp vụ Quản lý).
3. **Thiết kế Giao diện Tối giản Icon-Free:**
   - Hệ thống áp dụng chuẩn typography tối giản. Tuyệt đối không thêm `<span class="icon">` hoặc emoji trang trí rác vào các nút hay tiêu đề mới.
4. **Sửa nhanh dạng Excel (`sua_nhanh.php`):**
   - Sự kiện `blur` hoặc `change` trigger AJAX gửi POST tới `api_sua_nhanh.php`.
5. **Xuất PDF Mẫu chuẩn 2026 (`xuat_excel.php`):**
   - Kết nối tới Python Microservice Flask cổng 5000 qua `api_proxy.php`. Nếu Flask chưa chạy, script `setup_newcomputer.bat` hoặc `python_api/start_api.bat` sẽ tự động khởi chạy.
