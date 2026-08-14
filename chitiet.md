# BÁO CÁO PROJECT

## QUẢN LÝ QUẦN CHÚNG ƯU TÚ PHỤC VỤ KẾT NẠP ĐẢNG

---

- **Môn học:** Thiết kế Website / Phát triển Ứng dụng Web / Phân tích Thiết kế Hệ thống
- **Đề tài:** Thiết kế Website quản lý quần chúng ưu tú phục vụ kết nạp Đảng
- **Công nghệ:** PHP 8.x + Python Flask + MySQL (XAMPP)
- **Repository:** https://github.com/Datdajt03/QLQTUT-Dang
- **Nhóm thực hiện:** [02]
- **Thành viên:**
  1. [LÒ MẠNH ĐẠT] – [MSSV 1] – [Phân công: Backend PHP, Database]
  2. [NGUYỄN HUY HOÀNG] – [MSSV 2] – [Phân công: Frontend Python API, Edge AI]
  3. [TÒNG LƯU ANH TÚ] – [MSSV 3] – [Phân công: Báo cáo CSS/JS, UI/UX]
  4. [PHẠM THỊ THANH HẢO] – [MSSV 3] – [Phân công: Python API, Báo cáo]

---

## I. GIỚI THIỆU ĐỀ TÀI

### 1. Lý do chọn đề tài

Công tác phát triển Đảng viên mới là nhiệm vụ chính trị quan trọng trong các tổ chức Đảng, đặc biệt tại các trường Đại học nhằm bồi dưỡng thế hệ trẻ ưu tú. Hiện nay, quy trình quản lý thông tin từ giai đoạn quần chúng ưu tú, đi học lớp cảm tình Đảng, hoàn thành nhận thức, đến khi ra quyết định kết nạp và làm lễ kết nạp trải qua nhiều bước và thủ tục thủ công, dễ gây thất lạc hồ sơ, chậm trễ thông tin và thiếu minh bạch.

Vì vậy, việc thiết kế một Website chuyên nghiệp để **số hóa quy trình**, **phê duyệt hồ sơ trực tuyến** và **theo dõi tiến trình kết nạp Đảng** là hết sức thiết thực, giúp công tác Đảng vụ hiện đại và hiệu quả hơn.

### 2. Mục tiêu đề tài

- **Số hóa quy trình:** Chuyển đổi toàn bộ việc nộp hồ sơ, xét duyệt và theo dõi sang môi trường trực tuyến.
- **Minh bạch thông tin:** Sinh viên tự theo dõi tiến trình kết nạp cá nhân và xem danh sách bạn cùng lớp/chi bộ đã được duyệt.
- **Tối ưu quản trị:** Cung cấp công cụ quản lý tập trung, sửa nhanh dạng Excel, import/export hàng loạt và thống kê trực quan bằng biểu đồ.
- **Bảo mật phân quyền:** Hệ thống phân 3 cấp quyền (Sinh viên / Quản lý / Admin) với xác thực session cookie bảo mật.
- **Tích hợp thông tin:** Hiển thị tin tức thời sự từ các báo chính thống (Dân trí, Nhân Dân, Đảng Cộng sản) ngay trên Dashboard.

### 3. Phạm vi đề tài

| Phạm vi                   | Mô tả                                                                         |
| ------------------------- | ----------------------------------------------------------------------------- |
| **Đối tượng sử dụng**     | Sinh viên – Bí thư Chi bộ – Quản trị viên trường                              |
| **Nền tảng**              | Web Application chạy trên XAMPP (localhost)                                   |
| **Ngôn ngữ**              | PHP 8.x (Backend chính) + Python 3.x (Export API) + JavaScript ES6 (Frontend) |
| **Cơ sở dữ liệu**         | MySQL 8.x                                                                     |
| **Môi trường triển khai** | Windows Server / Localhost XAMPP                                              |

---

## II. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG (SYSTEM ANALYSIS & DESIGN)

### 1. Tổng quan Kiến trúc Hệ thống (System Architecture Overview)

Hệ thống được thiết kế theo **Mô hình Phân tầng Lai (Layered & Microservice-Lite Architecture)** kết hợp giữa Web Core PHP, Microservice xử lý file độc lập bằng Python Flask và Engine Trí tuệ Nhân tạo Edge AI chạy trực tiếp tại Client-side.

```mermaid
graph TD
    subgraph Client["🖥️ CLIENT-SIDE LAYER (Trình duyệt Người dùng)"]
        UI_User["Giao diện Sinh viên / Quần chúng"]
        UI_Mgr["Giao diện Bí thư / Quản lý"]
        UI_Admin["Giao diện Quản trị viên"]
        EdgeAI["🧠 Edge AI Engine (Tesseract.js / PDF.js)<br>• Smart Auto-fill CCCD/Thẻ SV<br>• Phân loại 5 loại phiếu & Soi thông tin khuyết<br>• Smart Avatar Crop 3x4 Canvas"]
    end

    subgraph WebServer["⚙️ APPLICATION & BUSINESS LOGIC LAYER (PHP Core Server)"]
        Router["Router / Page Controllers"]
        RBAC["🛡️ RBAC Auth Engine (requireRole)"]
        CRUD["Module Quản lý Hồ sơ & Danh mục"]
        Proxy["🔌 PHP Proxy Agent (api_proxy.php)"]
    end

    subgraph Microservice["🐍 MICROSERVICE LAYER (Python Flask Server :5000)"]
        FlaskAPI["RESTful API Server (app.py)"]
        ExcelEngine["openpyxl Engine (Xuất Excel 35 cột)"]
        PDFEngine["ReportLab Engine (Xuất 8 Mẫu PDF 2026)"]
    end

    subgraph DataLayer["🗄️ DATA ACCESS LAYER (Database)"]
        PDO["PHP PDO Connection (Prepared Statements)"]
        MySQL[("MySQL Database (ql_dangvien)")]
    end

    Client -->|HTTP Request / Session| WebServer
    EdgeAI -->|Auto-fill / JSON Log| UI_User
    WebServer -->|Database Query| PDO
    PDO -->|Prepared SQL| MySQL
    Proxy -->|REST API Request| FlaskAPI
    FlaskAPI --> ExcelEngine
    FlaskAPI --> PDFEngine
    FlaskAPI -->|PyMySQL Connection| MySQL
```

#### a. Mô hình Phân tầng Chi tiết (3-Tier Layered Architecture):
1. **Lớp Hiển thị (Presentation Layer):**
   - Xây dựng bằng **Modular CSS System (BEM Standard)** tách biệt các bộ quy tắc (`variables.css`, `base.css`, `components.css`, `user.css`, `manager.css`, `admin.css`).
   - Phong cách **Minimal Typography & Glassmorphism Dark Mode** loại bỏ biểu tượng rác, tập trung tối đa vào độ tương phản chữ và cấu trúc dữ liệu.
2. **Lớp Nghiệp vụ & Ứng dụng (Business & Application Layer):**
   - **RBAC Auth Helper (`User/auth.php`):** Kiểm soát phân quyền 3 cấp độ (User, Manager, Admin) bằng cơ chế kiểm tra Session & Role khép kín.
   - **Microservice Python Export:** Xử lý các tác vụ tính toán nặng và sinh định dạng tệp chuẩn (.xlsx, .pdf) độc lập, không làm ảnh hưởng đến hiệu năng máy chủ Web PHP.
#### b. Cơ chế & Thuật toán Edge AI Thẩm định Mẫu Phiếu (`AI_Module/document_inspector.js`):
1. **Weighted Keyword Matrix Classification (Phân loại Mô hình Phiếu):**
   - Đánh giá từ khóa loại phiếu (`typeKeywords`) trong văn bản OCR (+2 điểm) và tên file (+3 điểm).
   - Xác định Mô hình Mẫu phiếu chính xác nhất trong 5 mô hình Đảng vụ (*Bản tự nhận xét, Giấy chứng nhận, Sơ yếu lý lịch, Phiếu đánh giá, Minh chứng*).
2. **Multi-Key Substring Matching & Regex Traversal (Soi Trường Thiếu & Trích xuất):**
   - Duyệt ma trận từ khóa mở rộng cho từng trường bắt buộc của Mẫu phiếu.
   - Nếu **không tìm thấy từ khóa** ➔ Gán ngay vào `missingFields` (`[CẢNH BÁO THIẾU]`) và bật cảnh báo đỏ.
   - Nếu **tìm thấy từ khóa** ➔ Dùng `extractValueSnippet()` trích xuất dữ liệu thực tế (`extractedValue`).
3. **Công thức Tỷ lệ Phần trăm Đầy đủ (% Completeness Score):**
   $$\text{ScorePercent} = \left( \frac{\text{Số trường phát hiện}}{\text{Tổng số trường bắt buộc của Mẫu phiếu}} \right) \times 100\%$$

---

### 2. Ma trận Phân quyền Hệ thống (RBAC Matrix)

Hệ thống phân định 3 vai trò người dùng (Role-Based Access Control) với bảng ma trận quyền hạn chi tiết:

| Nhóm Chức năng | Chi tiết Quyền hạn | 👤 User (Sinh viên) | 💼 Manager (Bí thư) | ⚙️ Admin (Quản trị) |
| :--- | :--- | :---: | :---: | :---: |
| **Xác thực & Tài khoản** | Đăng ký, Đăng nhập, Đổi mật khẩu cá nhân | ✅ | ✅ | ✅ |
| **Dashboard Cá nhân** | Xem Profile Card, Timeline 5 bước kết nạp | ✅ | ✅ | ✅ |
| | Xem tin tức thời sự 3 nguồn báo chính thống | ✅ | ✅ | ✅ |
| **Hồ sơ Đăng ký** | Gửi đơn đăng ký quần chúng ưu tú mới | ✅ | ❌ | ❌ |
| | Gửi đề xuất cập nhật thông tin cá nhân | ✅ | ❌ | ❌ |
| | Phê duyệt / Từ chối đơn đăng ký & Gửi email | ❌ | ✅ | ✅ |
| **Quản lý Hồ sơ** | Xem danh sách đối tượng chính thức | Chỉ xem bạn cùng Lớp | ✅ Toàn bộ | ✅ Toàn bộ |
| | Thêm / Sửa / Xóa hồ sơ quần chúng | ❌ | ✅ | ✅ |
| | **Sửa nhanh dạng Excel trực tiếp (Autosave)** | ❌ | ✅ | ✅ |
| | **Xóa hàng loạt nhiều đối tượng (Bulk Delete)** | ❌ | ✅ | ✅ |
| **AI & Minh chứng** | **Smart Auto-fill OCR CCCD & Crop Ảnh 3x4** | ✅ | ✅ | ✅ |
| | **Edge AI quét 5 loại phiếu & Soi thông tin khuyết** | ✅ | ✅ | ✅ |
| **Import / Export** | **Import dữ liệu Excel (Kèm AI Agent ánh xạ cột)** | ❌ | ✅ | ✅ |
| | Xuất file Excel 35 cột (.xlsx) toàn bộ | ❌ | ✅ | ✅ |
| | **Xuất 8 Mẫu phiếu PDF chuẩn 2026 (Có tô nổi)** | ❌ | ✅ | ✅ |
| **Quản trị Hệ thống** | Quản lý danh mục Chi bộ & Đảng viên | ❌ | ✅ | ✅ |
| | Quản lý tài khoản người dùng & Đặt lại mật khẩu | ❌ | ❌ | ✅ |
| | Cấu hình hằng số hệ thống (Tên trường, Đảng ủy) | ❌ | ❌ | ✅ |

---

### 3. Quy trình Nghiệp vụ (Business Workflows)

#### a. Quy trình Đăng ký & Phê duyệt Quần chúng mới

1. Sinh viên đăng nhập hệ thống bằng tài khoản **Người dùng thường**.
2. Truy cập trang **Form đăng ký trực tuyến** (`nhap_thong_tin.php`) – Họ tên và Mã SV tự động điền theo tài khoản để tránh giả mạo.
3. Điền đầy đủ thông tin: Lớp, Email, SĐT, Chi bộ đề xuất, Quê quán, Giới tính, Ngày sinh.
4. Hồ sơ lưu trạng thái **Chờ duyệt** trong bảng `dang_ky_doi_tuong`.
5. Quản lý (Bí thư) truy cập trang **Phê duyệt** (`duyet_dang_ky.php`):
   - **Duyệt:** Đồng bộ hồ sơ vào bảng chính thức `doi_tuong`, xóa khỏi hàng chờ, gửi email chúc mừng tự động.
   - **Từ chối:** Nhập lý do từ chối, cập nhật trạng thái, gửi email phản hồi lý do cho sinh viên.

#### b. Quy trình Đề xuất Cập nhật Thông tin

1. Quần chúng đã được duyệt đăng nhập, truy cập **Cập nhật thông tin** (`cap_nhat_thong_tin.php`).
2. Form tự động điền sẵn dữ liệu cũ; Họ tên và Mã SV bị khóa (readonly) để bảo mật.
3. Quần chúng chỉnh sửa thông tin cần cập nhật (SĐT, Email, Lớp, Quê quán, Chức vụ) và gửi yêu cầu.
4. Yêu cầu lưu vào bảng `yeu_cau_cap_nhat` trạng thái **Chờ duyệt**.
5. Quản lý truy cập Tab **Phê duyệt cập nhật** trong `duyet_dang_ky.php`:
   - Bảng so sánh **Cũ ➔ Mới** hiển thị từng trường thay đổi.
   - **Duyệt:** Ghi đè dữ liệu mới vào bảng `doi_tuong`, cập nhật trạng thái yêu cầu = Đã duyệt.
   - **Từ chối:** Cập nhật trạng thái = Đã từ chối, lưu lý do từ chối.

#### c. Quy trình Xuất Báo cáo Excel/PDF

1. Quản lý/Admin truy cập **Xuất dữ liệu** (`xuat_excel.php`).
2. Hệ thống kiểm tra tình trạng Python Flask API qua endpoint `/health`.
3. Người dùng chọn **Phạm vi** (Toàn trường / Theo lớp / Theo chi bộ).
4. Chọn **Định dạng** (Excel toàn bộ / PDF hồ sơ 1 người / PDF danh sách nhiều người).
5. PHP Proxy (`api_proxy.php`) chuyển tiếp yêu cầu đến Flask API (cổng 5000).
6. Flask API truy vấn MySQL, tạo file Excel (openpyxl) hoặc PDF (reportlab) trả về.
7. File được tải xuống trực tiếp qua trình duyệt.

---

### 2. Mô tả Chi tiết Giao diện & Chức năng

#### 👤 GIAO DIỆN NGƯỜI DÙNG THƯỜNG (Sinh viên / Quần chúng)

Giao diện được thiết kế tối giản, cá nhân hóa, hướng đến trải nghiệm theo dõi tiến trình cá nhân. Bao gồm các cấu phần sau:

**A. Bản tin Thời sự Đa nguồn (Đầu trang Dashboard)**

- Hiển thị 4 bài báo mới nhất dưới dạng lưới thẻ Card (4 cột, responsive xuống 2 cột / 1 cột trên mobile).
- Hỗ trợ **3 nguồn báo chính thống** có thể chuyển đổi bằng tab:
  - 📰 **Báo Dân trí** (dantri.com.vn) – Tin tức tổng hợp thời sự
  - 📰 **Báo Nhân Dân** (nhandan.vn) – Cơ quan ngôn luận của Đảng CSVN
  - 📰 **Báo Đảng Cộng sản** (dangcongsan.vn) – Báo điện tử chuyên đề Đảng
- Cơ chế: PHP RSS parser với timeout 3 giây + logic dự phòng khi Báo Đảng Cộng sản chặn crawler.
- Mỗi thẻ card gồm: ảnh thumbnail, tiêu đề bài viết, tóm tắt nội dung, thời gian đăng, link đọc bài gốc.

**B. Khối thông tin cá nhân (Profile Card)**

- Hiển thị toàn bộ thông tin hồ sơ quần chúng của tài khoản đang đăng nhập:
  - Ảnh đại diện chân dung (hỗ trợ upload, preview realtime; nếu chưa upload thì hiển thị avatar chữ cái đầu)
  - Mã Sinh viên / Giảng viên
  - Họ và tên đầy đủ
  - Ngày sinh, Giới tính, Dân tộc
  - Số điện thoại, Email liên hệ
  - Quê quán
  - Lớp hành chính, Chi bộ sinh hoạt
  - Chức vụ (nếu có)
  - Trạng thái hồ sơ (Đang theo dõi / Đã kết nạp)

**C. Biểu đồ tiến trình (Timeline 5 bước Kết nạp Đảng)**

- Sơ đồ tuyến tính ngang hiển thị 5 cột mốc quan trọng:
  1. 🎓 **Lớp cảm tình Đảng** – Ngày tham gia lớp bồi dưỡng, số quyết định mở lớp
  2. 👥 **Phân công Đảng viên giúp đỡ** – Tên đảng viên phụ trách, ngày phân công
  3. 📜 **Nhận chứng chỉ Nhận thức về Đảng** – Ngày cấp CC, số quyết định CC
  4. ⭐ **Quyết định Kết nạp** – Số QĐ, ngày ký quyết định, ngày kết nạp chính thức
  5. 🏅 **Đảng viên chính thức** – Ngày chuyển sinh hoạt Đảng, nơi chuyển tới
- Bước hiện tại được tô sáng màu Vàng kim `#FFD700`; bước hoàn thành màu Đỏ `#C8102E`; bước chưa đến màu xám nhạt.
- Hiển thị ngày cụ thể kèm theo mỗi cột mốc đã hoàn thành.

**D. Bảng danh sách Thành viên cùng Lớp/Chi bộ**

- Bảng hiển thị tất cả quần chúng ưu tú đã được **duyệt chính thức** trong cùng lớp học với tài khoản đang đăng nhập.
- Lọc theo: Lớp học (khớp chính xác toàn bộ chuỗi tên lớp) VÀ khóa học (ký tự 1-3 trong mã lớp, ví dụ K62, K63).
- Thông tin hiển thị mỗi dòng: STT, Ảnh đại diện, Mã SV, Họ tên, Lớp, Chi bộ, Trạng thái kết nạp.

**E. Menu Hành động (Sidebar)**

- Nếu **chưa có hồ sơ**: Hiển thị nút "✍️ Gửi hồ sơ đăng ký mới" → dẫn đến `nhap_thong_tin.php`
- Nếu **đã được duyệt chính thức**: Hiển thị nút "✏️ Yêu cầu cập nhật thông tin" → dẫn đến `cap_nhat_thong_tin.php`
- Mục "🏛️ Thành viên cùng Lớp" trong sidebar → dẫn đến `thanh_vien_chi_bo.php`

---

#### 💼 GIAO DIỆN QUẢN LÝ (Bí thư Chi bộ) & ADMIN (Quản trị viên)

Màn hình đầy đủ quyền quản trị, bao gồm toàn bộ công cụ xử lý dữ liệu:

**A. Bản tin Thời sự Đa nguồn (Đầu trang)**

- Giống cấu phần A của Người dùng (có thể chuyển đổi giữa 3 nguồn báo).

**B. Dashboard Widgets – 4 thẻ Chỉ số Tổng quan**

| Thẻ               | Dữ liệu hiển thị                    | Màu sắc |
| ----------------- | ----------------------------------- | ------- |
| 📋 Tổng đối tượng | Tổng số quần chúng trong hệ thống   | Đỏ      |
| 👀 Đang theo dõi  | Số người trạng thái "Đang theo dõi" | Vàng    |
| ✅ Đã kết nạp     | Số người trạng thái "Đã kết nạp"    | Xanh lá |
| 🔔 Chờ duyệt      | Số đơn đăng ký mới chờ xét duyệt    | Cam     |

**C. Biểu đồ Thống kê (Chart.js)**

- **Biểu đồ cột (Bar Chart):** Phân bổ số lượng quần chúng theo Chi bộ Đảng, trục X là tên chi bộ, trục Y là số lượng người.
- **Biểu đồ tròn (Doughnut Chart):** Tỷ lệ phần trăm trạng thái kết nạp (Đang theo dõi / Đã kết nạp / Đã chuyển).
- Màu sắc phối hợp hệ màu Đảng: Đỏ `#C8102E`, Vàng `#FFD700`, Xanh đậm.

**D. Danh sách Đối tượng (`danh_sach.php`)**

- Bộ lọc đa trường: Tên, Mã SV, Lớp, Chi bộ, Trạng thái, Giới tính.
- Phân trang (10/20/50 bản ghi mỗi trang), sắp xếp theo cột.
- Nút hành động mỗi dòng: Xem chi tiết, Sửa, Xóa (có xác nhận).
- Cột trạng thái hiển thị badge màu phân loại.

**E. Bảng sửa nhanh dạng Excel (`sua_nhanh.php`)**

- Toàn bộ dữ liệu hiển thị trực tiếp dưới dạng ô nhập liệu trong bảng (input, select, datepicker).
- **Autosave qua AJAX:** Mỗi khi rời ô (blur event) hoặc thay đổi select, hệ thống tự động gửi request đến `api_sua_nhanh.php` để lưu mà không tải lại trang.
- Phản hồi trực quan bằng flash màu ô: Vàng (đang lưu) → Xanh (lưu thành công) → Đỏ (lỗi).
- Hỗ trợ điều hướng bằng bàn phím: `↑ ↓ ← →`, `Enter`, `Tab`, `Esc`.

**F. Giao diện Phê duyệt 2 Tab (`duyet_dang_ky.php`)**

- **Tab 1 – Đơn đăng ký mới:** Hiển thị danh sách hồ sơ trạng thái "Chờ duyệt" từ sinh viên. Mỗi hồ sơ có nút Duyệt (xanh) và Từ chối (đỏ). Khi từ chối hiện popup nhập lý do.
- **Tab 2 – Đề xuất cập nhật:** Bảng so sánh 2 cột (Thông tin cũ | Thông tin mới đề xuất). Các trường thay đổi được tô vàng nổi bật. Nút Duyệt cập nhật / Từ chối kèm lý do.

**G. Import Excel (`import_excel.php`)**

- Giao diện kéo thả file Excel/CSV (drag & drop) hoặc chọn qua hộp thoại tệp.
- **AI Agent Phân loại & Ánh xạ Tên cột Excel Thông minh (`AI_Module/excel_column_agent.js`):**
  - **Tự động quét & Phân tích tên cột:** Khi chọn file, AI Agent dùng thuật toán `normalizeHeader()` loại bỏ toàn bộ dấu tiếng Việt và ký tự đặc biệt, sau đó chạy ma trận đối soát với Từ điển Từ khóa CSDL `DB_COLUMNS_DICTIONARY` để tự động nhận diện các tiêu đề viết tắt/biến thể (ví dụ: `Qli`, `QL`, `Quản lý`, `Bán cán sự`, `MSSV`, `Hoten`, `Mã SV`...).
  - **Xử lý Cột Trống Tiêu Đề:** Nếu trong file Excel có ô tiêu đề bị trống/thiếu (không có chữ), AI Agent tự động định danh theo vị trí chữ cái cột `⚠️ Cột A (Trống tiêu đề)`, `⚠️ Cột B (Trống tiêu đề)`..., tô viền đỏ nổi bật và đưa lên Modal để người dùng chọn trường CSDL cần đẩy vào.
  - **Modal Tab AI Agent:** Tự động hiển thị Modal Tab chứa bảng so sánh:
    - **Cột bên trái:** Tên tiêu đề thực tế từ file Excel người dùng tải lên (hoặc tên vị trí cột trống).
    - **Cột Chọn Trường CSDL:** Thẻ Dropdown tự động chọn sẵn trường CSDL ứng với dự đoán của Agent (`chuc_vu`, `ho_ten`, `ma_gvsv`...), đồng thời cho phép người quản lý bấm đổi chọn lại bất kỳ trường CSDL nào mong muốn.
    - **Cột Độ tin cậy (Confidence Badge):** Đánh giá độ tin cậy của thuật toán Agent (`High`, `Medium`, `Low`, `Cột Trống (Cần chọn)`).
- Hỗ trợ các định dạng: `.xlsx`, `.xls`, `.csv`.
- Bản xem trước (preview) 10 dòng đầu tiên trước khi xác nhận nhập.
- Xử lý trùng lặp: kiểm tra Mã SV đã tồn tại trước khi insert.

**H. Xuất dữ liệu báo cáo (`xuat_excel.php`) & Trích xuất Biểu mẫu PDF 2026**

- **Loại 1 – Excel toàn bộ (.xlsx):** Xuất danh sách theo phạm vi (toàn trường / theo lớp / theo chi bộ) với đầy đủ 35 cột dữ liệu hồ sơ. Định dạng cao cấp: header màu đỏ, dòng xen kẽ, tự động căn chỉnh độ rộng cột.
- **Loại 2 – PDF hồ sơ cá nhân:** Xuất file PDF hồ sơ đầy đủ của 1 người (chọn từ danh sách radio button). Font Times New Roman hỗ trợ tiếng Việt đầy đủ.
- **Loại 3 – PDF danh sách nhiều người:** Chọn nhiều người qua checkbox, xuất danh sách PDF tổng hợp.
- **Loại 4 – PDF Mẫu phiếu Kết nạp Đảng chuẩn 2026 (8 Mẫu chuẩn):** Tự động trích xuất toàn bộ dữ liệu cần thiết của quần chúng từ MySQL để kết xuất ra định dạng PDF chuẩn của các biểu mẫu theo bộ `Bieu_mau_dang_ky_ket_ap_dang` (Mẫu 1-KNĐ, 2-KNĐ, 3-KNĐ, 4-KNĐ, 4a-KNĐ, 5-KNĐ, Mẫu CN-NTVĐ1 & CN-NTVĐ1-2). Đặc biệt, toàn bộ dữ liệu động điền sẵn được **bôi đỏ đậm và đóng khung `[ Dữ liệu ]` nổi bật**, giúp người dùng dễ dàng nhận diện để sao chép (copy) và dán chính xác vào biểu mẫu gốc.
- **Cơ chế Kiểm tra & Tự động bật Modal khi thiếu trường:** Khi xuất bất kỳ biểu mẫu PDF nào, nếu dữ liệu cá nhân bị khuyết các trường bắt buộc (như _Quê quán, Dân tộc, Chi bộ công nhận, Đảng viên giúp đỡ..._), hệ thống dừng xuất và hiển thị Modal liệt kê chính xác các trường thiếu kèm nút **"✏️ Điền thông tin ngay"** để người dùng/quản lý cập nhật bổ sung trước khi kết xuất file PDF.

**I. Quản lý Chi bộ & Đảng viên**

- `chi_bo.php`: CRUD danh mục Chi bộ Đảng (Mã chi bộ, Tên chi bộ, Đảng ủy trực thuộc).
- `dang_vien.php`: CRUD danh mục Đảng viên được phân công giúp đỡ quần chúng.

**J. Thống kê & Báo cáo (`thong_ke.php`)**

- Trang thống kê toàn diện với 4 loại biểu đồ (Cột, Tròn, Đường, Hành lang) phân tích xu hướng phát triển đảng viên theo thời gian.
- Tìm kiếm nâng cao (`tim_kiem.php`): Kết hợp nhiều trường lọc đồng thời.

**K. Edge AI Module (`AI_Module`) & Kiểm tra Hồ sơ Minh chứng (`edge_ai_check.php`)**

- **Tự động OCR Điền Form (`AI_Module/edge_ai_autofill.js`):** Khi sinh viên nộp đơn đăng ký hoặc cập nhật hồ sơ, sinh viên tải lên **ảnh CCCD (Mặt trước & Mặt sau) + Thẻ sinh viên**. Engine AI chạy Tesseract.js trích xuất trực tiếp _Họ tên, Ngày sinh, Mã SV, Giới tính, Quê quán, Dân tộc, Lớp_ và tự động điền (Auto-fill) vào các ô input, giảm 90% thời gian gõ thủ công.
- **Smart Avatar Validation & Crop 3x4 (`AI_Module/edge_ai_autofill.js`):** Tự động nhận diện khuôn mặt trong ảnh chân dung và dùng Canvas cắt theo chuẩn tỉ lệ ảnh thẻ 3x4 (300x400) sắc nét trước khi tải lên máy chủ.
- **Excel Column Mapper Agent (`AI_Module/excel_column_agent.js`):** AI Agent Client-side phân loại tiêu đề cột Excel thông minh và mở Modal Tab cho phép người dùng chọn/ánh xạ chính xác tiêu đề cột ghi tắt vào CSDL trước khi Import.
- **Tự động Setup 1-Click (`setup_newcomputer.bat`):** Script tự động hóa toàn bộ quy trình thiết lập dự án khi sao chép sang máy tính mới: Tự động khởi tạo thư mục `uploads`, tự động bật `extension=zip` trong `php.ini`, tự động nạp Database `ql_dangvien` vào MySQL và cài đặt/khởi chạy Python Microservice Server.
- **Trích xuất văn bản OCR Minh chứng:** Sử dụng Tesseract.js & PDF.js chạy tại client để kiểm tra tính hợp lệ của file PDF/Ảnh minh chứng (**tối đa 10MB/file**).
- **Edge AI 5 Document Models Engine:** Phân loại tệp tải lên vào 5 Models chuẩn (`ban_tu_nhan_xet`, `giay_chung_nhan`, `ho_so_ca_nhan`, `phieu_danh_gia`, `minh_chung_hoat_dong`) và soi sâu từng trường thông tin bắt buộc trong phiếu (như *Số QĐ, Đơn vị cấp, Ngày sinh, Xếp loại, Chữ ký/Xác nhận Bí thư...*).
- **Báo cáo Chi tiết Phiếu & Thông tin khuyết:** Đưa ra thông tin chính xác phiếu nào **chưa nộp hoàn toàn** và phiếu nào đã nộp nhưng **bị thiếu thông tin chi tiết bên trong**, kèm đề xuất khắc phục cụ thể.
- **Lưu vết Hệ thống:** Tự động đẩy file thực tế về lưu tại `uploads/ho_so_minh_chung/` và lưu nhật ký đánh giá vào bảng MySQL `edge_ai_logs` qua API `api_save_ai_check.php`.

**L. Xóa Hàng Loạt Nhiều Đối Tượng & Mẫu Excel Điền Chuẩn (`danh_sach.php`)**

- **Xóa Hàng Loạt (Bulk Delete):** Tích hợp cột Checkbox chọn từng dòng và ô **"Select All"** ở đầu bảng `danh_sach.php`. Khi chọn một hoặc nhiều đối tượng, nút **`🗑️ Xóa đối tượng đã chọn (N)`** xuất hiện ở góc trên. Bấm xóa sẽ gửi danh sách ID qua POST tới `Quan_ly_doi_tuong/xoa.php` để thực hiện xóa an toàn toàn bộ trong một truy vấn SQL `DELETE FROM doi_tuong WHERE id IN (...)`.
- **Mẫu Excel Điền Chuẩn Kèm ID Cột (`/api/export/template`):** Cho phép tải tệp Excel mẫu gồm mã ID chuẩn `[ID: ho_ten]`, `[ID: ma_gvsv]` ở dòng 1 và Tiêu đề tiếng Việt ở dòng 2, gửi cho các Lớp điền để nhập dữ liệu không bao giờ bị lệch cột.

**M. Cài đặt Hệ thống (`cai_dat.php`) – Chỉ Admin**

- Cấu hình tên trường, tên Đảng bộ, thông tin liên hệ hiển thị toàn hệ thống.
- Đổi mật khẩu Admin và quản lý tài khoản người dùng.

---

### 3. Biểu đồ Use Case (Use Case Diagram)

```
Tác nhân: Người dùng thường (SV) | Quản lý (Bí thư) | Admin

NGƯỜI DÙNG THƯỜNG:
  - Đăng nhập / Đăng ký tài khoản
  - Xem Dashboard cá nhân (Profile Card + Timeline)
  - Xem tiến trình kết nạp 5 bước
  - Gửi hồ sơ đăng ký mới
  - Gửi đề xuất cập nhật thông tin
  - Xem danh sách bạn cùng Lớp/Chi bộ
  - Xem tin tức thời sự 3 nguồn báo

QUẢN LÝ (kế thừa toàn bộ quyền trên + thêm):
  - Xem danh sách đối tượng chính thức (bộ lọc đa năng)
  - Thêm / Sửa / Xóa hồ sơ quần chúng
  - Sửa nhanh trực tiếp dạng Excel (Autosave)
  - Phê duyệt / Từ chối đơn đăng ký mới
  - Phê duyệt / Từ chối đề xuất cập nhật
  - Import dữ liệu từ file Excel/CSV
  - Xuất báo cáo Excel (.xlsx) toàn bộ
  - Xuất hồ sơ PDF cá nhân / Danh sách PDF
  - Xem thống kê biểu đồ Chart.js
  - Tìm kiếm nâng cao
  - Quản lý danh mục Chi bộ & Đảng viên

ADMIN (kế thừa toàn bộ quyền Quản lý + thêm):
  - Quản lý tài khoản người dùng (tạo, đặt lại mật khẩu)
  - Cài đặt thông tin hệ thống (tên trường, tên Đảng bộ)
```

```mermaid
flowchart TD
    User["👤 Người dùng thường (Sinh viên)"]
    Manager["💼 Quản lý (Bí thư)"]
    Admin["⚙️ Quản trị viên (Admin)"]

    subgraph SG_User["Chức năng Sinh viên"]
        UC_Login["Đăng nhập / Đăng ký"]
        UC_Dash["Xem Dashboard cá nhân"]
        UC_Timeline["Xem tiến trình 5 bước"]
        UC_Reg["Gửi hồ sơ đăng ký mới"]
        UC_Update["Gửi đề xuất cập nhật"]
        UC_Class["Xem bạn cùng Lớp/Chi bộ"]
        UC_News["Xem tin tức 3 nguồn báo"]
    end

    subgraph SG_Manager["Chức năng Quản lý (Bí thư)"]
        UC_List["Danh sách đối tượng"]
        UC_CRUD["Thêm / Sửa / Xóa hồ sơ"]
        UC_ExcelEdit["Sửa nhanh dạng Excel"]
        UC_ApproveReg["Phê duyệt đơn đăng ký"]
        UC_ApproveUpd["Phê duyệt cập nhật"]
        UC_Import["Import Excel/CSV"]
        UC_Export["Xuất Excel/PDF qua Flask API"]
        UC_Stats["Thống kê biểu đồ Chart.js"]
        UC_Search["Tìm kiếm nâng cao"]
        UC_DM["Quản lý Chi bộ & Đảng viên"]
    end

    subgraph SG_Admin["Chức năng riêng Admin"]
        UC_Users["Quản lý tài khoản"]
        UC_Config["Cài đặt hệ thống"]
    end

    User --> UC_Login
    User --> UC_Dash
    User --> UC_Timeline
    User --> UC_Reg
    User --> UC_Update
    User --> UC_Class
    User --> UC_News

    Manager --> UC_Login
    Manager --> UC_List
    Manager --> UC_CRUD
    Manager --> UC_ExcelEdit
    Manager --> UC_ApproveReg
    Manager --> UC_ApproveUpd
    Manager --> UC_Import
    Manager --> UC_Export
    Manager --> UC_Stats
    Manager --> UC_Search
    Manager --> UC_DM
    Manager --> UC_News

    Admin --> UC_Users
    Admin --> UC_Config
    Admin --> SG_Manager
```

---

### 4. Biểu đồ Hoạt động (Activity Diagram)

#### 4a. Quy trình Phê duyệt Đăng ký

```mermaid
stateDiagram-v2
    [*] --> SV_DangNhap : Sinh viên đăng nhập
    SV_DangNhap --> DienForm : Điền Form đăng ký trực tuyến
    DienForm --> GuiHoSo : Bấm Gửi hồ sơ
    GuiHoSo --> ChoduYet : Trạng thái: Chờ duyệt (Lưu CSDL)
    ChoduYet --> QL_ThamDinh : Quản lý mở trang Phê duyệt
    QL_ThamDinh --> QuyetDinh : Xem xét thông tin hồ sơ

    QuyetDinh --> Approve : Quyết định Duyệt
    QuyetDinh --> Reject : Quyết định Từ chối

    Approve --> DongBo : Thêm vào bảng doi_tuong
    DongBo --> GuiMailOK : Gửi email chúc mừng
    GuiMailOK --> [*] : Hoàn tất

    Reject --> LuuLyDo : Lưu lý do từ chối
    LuuLyDo --> GuiMailFail : Gửi email phản hồi lý do
    GuiMailFail --> [*] : Hoàn tất
```

#### 4b. Quy trình Thẩm định Hồ sơ Minh chứng qua Edge AI OCR (Client-Side)

```mermaid
stateDiagram-v2
    [*] --> SV_TaiUp : Sinh viên/Bí thư chọn tệp PDF/Image minh chứng (<=10MB)
    SV_TaiUp --> DocClient : Tesseract.js / PDF.js nạp và trích xuất OCR tại Trình duyệt
    DocClient --> ChayRuleEngine : Rule-Engine kiểm tra từ khóa bắt buộc
    ChayRuleEngine --> KiemTraKetQua : Đánh giá mức độ đầy đủ của Hồ sơ

    state KiemTraKetQua <<choice>>
    KiemTraKetQua --> ThieuHoSo : Phát hiện thiếu từ khóa / Giấy tờ
    KiemTraKetQua --> DuHoSo : Hồ sơ đầy đủ & hợp lệ

    ThieuHoSo --> HienCanhBaoAI : Hiển thị cảnh báo đỏ & Lời khuyên khắc phục
    DuHoSo --> HienXacNhanOK : Hiển thị Badge Xanh "Hồ sơ đạt tiêu chuẩn"

    HienCanhBaoAI --> LuuServer : Gửi Request AJAX lưu tệp & Nhật ký AI
    HienXacNhanOK --> LuuServer : Gửi Request AJAX lưu tệp & Nhật ký AI

    LuuServer --> LuuDisk : Lưu file vào uploads/ho_so_minh_chung/
    LuuDisk --> LuuDB : Ghi log phân tích vào bảng MySQL edge_ai_logs
    LuuDB --> [*] : Hoàn tất
```

#### 4c. Quy trình Smart Auto-Fill CCCD & Cắt Ảnh Thẻ 3x4 (`AI_Module`)

```mermaid
stateDiagram-v2
    [*] --> ChonFile : Sinh viên chọn ảnh CCCD (2 mặt) + Thẻ SV hoặc Ảnh chân dung
    ChonFile --> PhanLoai : AI Module phân loại loại tệp nạp vào

    state PhanLoai <<choice>>
    PhanLoai --> ChayOCR : Tệp CCCD / Thẻ SV
    PhanLoai --> ChayCrop : Ảnh chân dung (Avatar)

    ChayOCR --> TesseractExec : Tesseract.js OCR trích xuất văn bản tiếng Việt
    TesseractExec --> RegexParse : Regex bóc tách: Họ tên, Ngày sinh, Mã SV, Quê quán, Dân tộc, Lớp
    RegexParse --> AutoFillInput : Tự động điền (Auto-fill) vào các ô input form
    AutoFillInput --> [*] : Hoàn tất điền form 90%

    ChayCrop --> DetectFace : AI phát hiện tâm khuôn mặt (Face Detection)
    DetectFace --> CanvasRender : HTML5 Canvas tự động Crop căn chỉnh tỉ lệ 3x4 (300x400)
    CanvasRender --> HienPreview : Hiển thị Preview sắc nét & Gắn vào form gửi Server
    HienPreview --> [*] : Hoàn tất
```

#### 4d. Quy trình Xuất Báo cáo Excel/PDF & Thẩm định Mẫu 2026

```mermaid
stateDiagram-v2
    [*] --> MoTrang : Quản lý mở trang Xuất dữ liệu / Chi tiết hồ sơ
    MoTrang --> KiemTraAPI : PHP kiểm tra Flask API /health
    KiemTraAPI --> APIOnline : API đang chạy
    KiemTraAPI --> APIOffline : API chưa khởi động

    APIOffline --> HienThiCanhBao : Hiển thị cảnh báo đỏ
    HienThiCanhBao --> KiemTraAPI : Người dùng bấm Thử lại

    APIOnline --> ChonPhamVi : Chọn phạm vi (Toàn trường/Lớp/Chi bộ) & Biểu mẫu
    ChonPhamVi --> BamXuat : Nhấn Xuất Mẫu Phiếu PDF (Mẫu 2026)

    BamXuat --> PHPProxy : Proxy gọi Flask API /api/export/form/{form}/{id}
    PHPProxy --> KiemTraTruong : Flask API thẩm định các trường dữ liệu bắt buộc

    state KiemTraTruong <<choice>>
    KiemTraTruong --> ThieuDuyLieu : Khuyết thông tin (Quê quán, Dân tộc, Chi bộ...)
    KiemTraTruong --> DuDuLieu : Thông tin đầy đủ 100%

    ThieuDuyLieu --> BietModal : Trả lỗi JSON (400) → Tự động bật Modal Cảnh báo thiếu trường
    BietModal --> BamSua : Người dùng bấm "✏️ Điền thông tin ngay"
    BamSua --> [*]

    DuDuLieu --> FlaskQuery : Query MySQL & map dữ liệu vào ReportLab PDF Engine
    FlaskQuery --> TaoFile : Tạo file PDF sắc nét chuẩn văn bản Đảng 2026
    TaoFile --> TaiXuong : Trả file về trình duyệt để tải xuống
    TaiXuong --> [*] : Hoàn tất
```

---

### 5. Biểu đồ Tuần tự (Sequence Diagram)

#### 5a. Đề xuất Cập nhật Thông tin

```mermaid
sequenceDiagram
    autonumber
    actor SV as Quần chúng (Sinh viên)
    participant UI as Giao diện Web (PHP)
    participant DB as MySQL Database
    actor QL as Quản lý (Bí thư)

    SV->>UI: Truy cập trang Đề xuất cập nhật
    UI->>DB: SELECT * FROM doi_tuong WHERE ma_gvsv = ?
    DB-->>UI: Trả về thông tin hiện tại
    UI-->>SV: Hiển thị form điền sẵn dữ liệu cũ (Họ tên & Mã SV khóa readonly)
    SV->>UI: Chỉnh sửa thông tin cần cập nhật → Bấm Gửi
    UI->>DB: INSERT INTO yeu_cau_cap_nhat (trạng_thái = Chờ duyệt)
    DB-->>UI: Xác nhận INSERT thành công
    UI-->>SV: Thông báo gửi thành công, chờ phê duyệt

    QL->>UI: Truy cập trang Duyệt thông tin → Tab Cập nhật
    UI->>DB: SELECT ycc.*, dt.* FROM yeu_cau_cap_nhat ycc JOIN doi_tuong dt ON ycc.doi_tuong_id = dt.id WHERE ycc.trang_thai = 'Chờ duyệt'
    DB-->>UI: Trả danh sách đề xuất kèm dữ liệu cũ để so sánh
    UI-->>QL: Hiển thị bảng so sánh Cũ vs Mới (trường thay đổi tô vàng)
    QL->>UI: Bấm Duyệt cập nhật
    UI->>DB: UPDATE doi_tuong SET sdt=?, email=?, lop=? WHERE id=?
    UI->>DB: UPDATE yeu_cau_cap_nhat SET trang_thai='Đã duyệt' WHERE id=?
    DB-->>UI: Xác nhận UPDATE thành công
    UI-->>QL: Hiển thị thông báo duyệt thành công
```

#### 5b. Xuất File Excel qua Python Flask API

```mermaid
sequenceDiagram
    autonumber
    actor QL as Quản lý
    participant PHP as PHP Web (xuat_excel.php)
    participant Proxy as PHP Proxy (api_proxy.php)
    participant Flask as Python Flask API (:5000)
    participant DB as MySQL Database

    QL->>PHP: Chọn phạm vi + định dạng → Nhấn Xuất tài liệu
    PHP->>Proxy: fetch POST api_proxy.php?path=api/export/all
    Proxy->>Flask: POST http://localhost:5000/api/export/all {filter_type, filter_value}
    Flask->>DB: SELECT * FROM doi_tuong WHERE lop=? hoặc chi_bo=?
    DB-->>Flask: Trả danh sách đối tượng (dict rows)
    Flask->>Flask: Tạo Workbook Excel (openpyxl) với 35 cột + định dạng màu
    Flask-->>Proxy: Trả file .xlsx (Content-Disposition: attachment)
    Proxy-->>PHP: Pipe file bytes về PHP response
    PHP-->>QL: Browser trigger tải xuống file DanhSach_Excel.xlsx
```

#### 5c. Thẩm định Hồ sơ qua Edge AI OCR Client-side & Lưu trữ Server

```mermaid
sequenceDiagram
    autonumber
    actor User as Sinh viên / Bí thư
    participant Browser as Trình duyệt (Edge AI JS)
    participant Tesseract as Engine Tesseract.js / PDF.js
    participant ServerAPI as REST API (api_save_ai_check.php)
    participant Disk as Thư mục uploads/ho_so_minh_chung
    participant DB as MySQL Database (edge_ai_logs)

    User->>Browser: Chọn hoặc Kéo thả tệp minh chứng (PDF/PNG/JPG <=10MB)
    Browser->>Tesseract: Đọc trích xuất chữ tiếng Việt từ tệp minh chứng
    Tesseract-->>Browser: Trả về chuỗi văn bản OCR trích xuất (Text)
    Browser->>Browser: Chạy Rule-Engine kiểm tra từ khóa bắt buộc & Giấy tờ thiếu
    Browser-->>User: Hiển thị kết quả kiểm tra & Badge đánh giá thời gian thực
    Browser->>ServerAPI: Request AJAX POST (FormData: file + JSON log kết quả)
    ServerAPI->>Disk: Lưu file vật lý vào uploads/ho_so_minh_chung/
    ServerAPI->>DB: INSERT INTO edge_ai_logs (ma_gvsv, ten_file, noi_dung_ocr, danh_gia)
    DB-->>ServerAPI: Xác nhận lưu DB thành công
    ServerAPI-->>Browser: Trả về HTTP 200 OK
    Browser-->>User: Hiển thị "Đã lưu vết minh chứng & Nhật ký AI vào hệ thống"
```

---

### 6. Phân quyền và Bảo mật (Security Model)

| Cơ chế                        | Chi tiết kỹ thuật                                                          |
| ----------------------------- | -------------------------------------------------------------------------- |
| **Session Authentication**    | `session_start()` + `$_SESSION['user_id']` kiểm tra mỗi request            |
| **Role-Based Access Control** | Hàm`requireRole(['Quản lý', 'Admin'])` đặt đầu mỗi file nghiệp vụ          |
| **SQL Injection Prevention**  | 100% truy vấn dùng PDO Prepared Statements (`?` placeholder)               |
| **XSS Prevention**            | Hàm`e()` bao bọc `htmlspecialchars()` cho mọi output ra HTML               |
| **Data Integrity**            | Khóa readonly Họ tên + Mã SV trong form đăng ký/cập nhật để chống mạo danh |
| **File Upload Security**      | Kiểm tra MIME type, giới hạn phần mở rộng, lưu ngoài webroot               |
| **CORS Control**              | Flask API dùng`flask-cors` chỉ cho phép origin từ localhost                |

---

## III. THIẾT KẾ CƠ SỞ DỮ LIỆU (DATABASE DESIGN)

### 1. Sơ đồ Quan hệ Thực thể (ERD)

```mermaid
erDiagram
    NGUOI_DUNG {
        int id PK
        varchar username
        varchar password
        varchar ho_ten
        enum vai_tro
        timestamp created_at
    }
    DOI_TUONG {
        int id PK
        varchar ma_gvsv
        varchar ho_ten
        varchar sdt
        varchar email
        varchar gioi_tinh
        date ngay_sinh
        varchar dan_toc
        text que_quan
        varchar chuc_vu
        varchar lop
        varchar chi_bo_cong_nhan
        varchar so_bc_cam_tinh
        date ngay_hop_cam_tinh
        varchar dang_vien_giup_do
        date ngay_phan_cong_giup_do
        varchar so_qd_mo_lop
        date ngay_qd_mo_lop
        varchar tg_lop_boi_duong
        date ngay_cap_cc
        varchar so_qd_cc
        varchar don_vi_cap_cc
        varchar ten_dv_congtac_khi_cap_cc
        varchar ten_chibo_khi_cap_cc
        varchar ten_danguy_khi_cap_cc
        varchar ten_tinhuy_khi_cap_cc
        varchar ma_so
        varchar ket_nap_dang
        date ngay_quyet_dinh
        varchar so_qd_ket_nap
        date ngay_ket_nap
        varchar dang_vien_huong_dan
        date ngay_chuyen_sinh_hoat
        varchar noi_chuyen_toi
        varchar trang_thai
        varchar avatar
        text ghi_chu
    }
    DANG_KY_DOI_TUONG {
        int id PK
        varchar ma_gvsv
        varchar ho_ten
        varchar sdt
        varchar email
        varchar lop
        varchar chi_bo_de_xuat
        varchar trang_thai
        text ly_do_tu_choi
        timestamp created_at
    }
    YEU_CAU_CAP_NHAT {
        int id PK
        int doi_tuong_id FK
        varchar sdt
        varchar email
        varchar lop
        text que_quan
        varchar chuc_vu
        enum trang_thai
        text ly_do_tu_choi
        timestamp created_at
    }
    CHI_BO {
        int id PK
        varchar ten_chi_bo
        varchar ma_chi_bo
        varchar dang_uy
    }
    DANG_VIEN {
        int id PK
        varchar ho_ten
        int chi_bo_id FK
        varchar chuc_vu
        varchar sdt
    }
    LICH_SU {
        int id PK
        int doi_tuong_id FK
        varchar hanh_dong
        text mo_ta
        varchar nguoi_thuc_hien
        timestamp thoi_gian
    }
    CAI_DAT {
        int id PK
        varchar ten_truong
        varchar ten_dang_bo
        varchar ma_truong
        varchar dia_chi
        varchar email_lien_he
    }

    DOI_TUONG ||--o{ YEU_CAU_CAP_NHAT : "gửi đề xuất"
    DOI_TUONG ||--o{ LICH_SU : "ghi nhật ký"
    CHI_BO ||--o{ DANG_VIEN : "quản lý"
    NGUOI_DUNG ||--o{ DANG_KY_DOI_TUONG : "nộp hồ sơ"
```

---

### 2. Đặc tả Chi tiết Các Bảng Dữ liệu (Data Dictionary)

#### Bảng 1: `nguoi_dung` (Tài khoản đăng nhập & phân quyền)

| Tên cột      | Kiểu dữ liệu | Ràng buộc                   | Mô tả                                               |
| :----------- | :----------- | :-------------------------- | :-------------------------------------------------- |
| `id`         | INT          | PK, Auto Increment          | Mã định danh tự tăng                                |
| `username`   | VARCHAR(100) | UNIQUE, NOT NULL            | Tên đăng nhập (Mã SV hoặc tên Admin)                |
| `password`   | VARCHAR(255) | NOT NULL                    | Mật khẩu đã được băm bằng password_hash() PHP       |
| `ho_ten`     | VARCHAR(255) |                             | Họ và tên đầy đủ hiển thị trên giao diện            |
| `vai_tro`    | ENUM         | DEFAULT 'Người dùng thường' | Ba giá trị: 'Người dùng thường', 'Quản lý', 'Admin' |
| `created_at` | TIMESTAMP    | DEFAULT CURRENT_TIMESTAMP   | Thời điểm tạo tài khoản                             |

#### Bảng 2: `doi_tuong` (Hồ sơ quần chúng ưu tú chính thức – 35 cột)

| Tên cột                     | Kiểu dữ liệu          | Mô tả                                                  |
| :-------------------------- | :-------------------- | :----------------------------------------------------- |
| `id`                        | INT PK                | Mã định danh tự tăng                                   |
| `ma_gvsv`                   | VARCHAR(50)           | Mã số Sinh viên / Giảng viên                           |
| `ho_ten`                    | VARCHAR(255) NOT NULL | Họ và tên đầy đủ                                       |
| `sdt`                       | VARCHAR(20)           | Số điện thoại liên hệ                                  |
| `email`                     | VARCHAR(255)          | Địa chỉ Email nhận thông báo                           |
| `gioi_tinh`                 | VARCHAR(10)           | Giới tính (Nam / Nữ)                                   |
| `ngay_sinh`                 | DATE                  | Ngày sinh (định dạng dd/mm/yyyy)                       |
| `dan_toc`                   | VARCHAR(50)           | Dân tộc                                                |
| `que_quan`                  | TEXT                  | Địa chỉ quê quán đầy đủ                                |
| `chuc_vu`                   | VARCHAR(100)          | Chức vụ trong lớp/khoa/trường                          |
| `lop`                       | VARCHAR(100)          | Lớp hành chính sinh hoạt                               |
| `chi_bo_cong_nhan`          | VARCHAR(255)          | Chi bộ Đảng theo dõi và công nhận                      |
| `so_bc_cam_tinh`            | VARCHAR(50)           | Số biên bản cảm tình Đảng                              |
| `ngay_hop_cam_tinh`         | DATE                  | Ngày họp chi bộ công nhận cảm tình                     |
| `dang_vien_giup_do`         | VARCHAR(255)          | Họ tên Đảng viên được phân công giúp đỡ                |
| `ngay_phan_cong_giup_do`    | DATE                  | Ngày chi bộ ra quyết định phân công giúp đỡ            |
| `so_qd_mo_lop`              | VARCHAR(50)           | Số quyết định mở lớp bồi dưỡng nhận thức               |
| `ngay_qd_mo_lop`            | DATE                  | Ngày ký quyết định mở lớp                              |
| `tg_lop_boi_duong`          | VARCHAR(100)          | Thời gian tổ chức lớp bồi dưỡng                        |
| `ngay_cap_cc`               | DATE                  | Ngày cấp chứng chỉ nhận thức về Đảng                   |
| `so_qd_cc`                  | VARCHAR(50)           | Số quyết định chứng chỉ                                |
| `don_vi_cap_cc`             | VARCHAR(255)          | Đơn vị cấp chứng chỉ                                   |
| `ten_dv_congtac_khi_cap_cc` | VARCHAR(255)          | Tên đơn vị công tác khi cấp CC                         |
| `ten_chibo_khi_cap_cc`      | VARCHAR(255)          | Tên chi bộ sinh hoạt khi cấp CC                        |
| `ten_danguy_khi_cap_cc`     | VARCHAR(255)          | Tên Đảng ủy khi cấp CC                                 |
| `ten_tinhuy_khi_cap_cc`     | VARCHAR(255)          | Tên Tỉnh ủy khi cấp CC                                 |
| `ma_so`                     | VARCHAR(50)           | Mã số hồ sơ phát triển Đảng                            |
| `ket_nap_dang`              | VARCHAR(100)          | Thông tin kết nạp Đảng                                 |
| `ngay_quyet_dinh`           | DATE                  | Ngày ký quyết định kết nạp                             |
| `so_qd_ket_nap`             | VARCHAR(50)           | Số quyết định kết nạp Đảng viên                        |
| `ngay_ket_nap`              | DATE                  | Ngày kết nạp chính thức vào Đảng                       |
| `dang_vien_huong_dan`       | VARCHAR(255)          | Đảng viên chính thức hướng dẫn 12 tháng                |
| `ngay_chuyen_sinh_hoat`     | DATE                  | Ngày làm thủ tục chuyển sinh hoạt Đảng                 |
| `noi_chuyen_toi`            | VARCHAR(255)          | Chi bộ/Đảng bộ chuyển tới                              |
| `trang_thai`                | VARCHAR(50)           | Trạng thái: 'Đang theo dõi', 'Đã kết nạp', 'Đã chuyển' |
| `avatar`                    | VARCHAR(255)          | Đường dẫn ảnh chân dung trong uploads/avatars/         |
| `ghi_chu`                   | TEXT                  | Ghi chú thêm của Bí thư                                |
| `created_at`                | TIMESTAMP             | Thời điểm tạo hồ sơ                                    |

#### Bảng 3: `dang_ky_doi_tuong` (Đơn đăng ký trực tuyến chờ duyệt)

| Tên cột          | Kiểu dữ liệu | Ràng buộc                 | Mô tả                                     |
| :--------------- | :----------- | :------------------------ | :---------------------------------------- |
| `id`             | INT          | PK, Auto Increment        | Mã số đơn đăng ký                         |
| `ma_gvsv`        | VARCHAR(50)  | NOT NULL                  | Mã số SV (lấy tự động từ session)         |
| `ho_ten`         | VARCHAR(255) | NOT NULL                  | Họ tên sinh viên (lấy tự động từ session) |
| `sdt`            | VARCHAR(20)  |                           | Số điện thoại liên hệ                     |
| `email`          | VARCHAR(255) | NOT NULL                  | Email nhận thông báo kết quả duyệt        |
| `lop`            | VARCHAR(100) | NOT NULL                  | Lớp hành chính                            |
| `chi_bo_de_xuat` | VARCHAR(255) |                           | Chi bộ đề xuất theo dõi sinh hoạt         |
| `que_quan`       | TEXT         |                           | Quê quán                                  |
| `gioi_tinh`      | VARCHAR(10)  |                           | Giới tính                                 |
| `ngay_sinh`      | DATE         |                           | Ngày sinh                                 |
| `trang_thai`     | VARCHAR(50)  | DEFAULT 'Chờ duyệt'       | 'Chờ duyệt', 'Đã duyệt', 'Đã từ chối'     |
| `ly_do_tu_choi`  | TEXT         |                           | Lý do từ chối của Quản lý                 |
| `created_at`     | TIMESTAMP    | DEFAULT CURRENT_TIMESTAMP | Thời điểm nộp đơn                         |

#### Bảng 4: `yeu_cau_cap_nhat` (Đề xuất cập nhật thông tin)

| Tên cột         | Kiểu dữ liệu | Ràng buộc                   | Mô tả                                 |
| :-------------- | :----------- | :-------------------------- | :------------------------------------ |
| `id`            | INT          | PK, Auto Increment          | Mã yêu cầu tự tăng                    |
| `doi_tuong_id`  | INT          | FK → doi_tuong.id, NOT NULL | Liên kết hồ sơ chính thức             |
| `sdt`           | VARCHAR(20)  | NOT NULL                    | Số điện thoại mới đề xuất             |
| `email`         | VARCHAR(255) | NOT NULL                    | Email mới đề xuất                     |
| `lop`           | VARCHAR(100) | NOT NULL                    | Lớp mới đề xuất                       |
| `que_quan`      | TEXT         |                             | Quê quán mới đề xuất                  |
| `chuc_vu`       | VARCHAR(100) |                             | Chức vụ mới đề xuất                   |
| `trang_thai`    | ENUM         | DEFAULT 'Chờ duyệt'         | 'Chờ duyệt', 'Đã duyệt', 'Đã từ chối' |
| `ly_do_tu_choi` | TEXT         |                             | Lý do Quản lý không duyệt cập nhật    |
| `created_at`    | TIMESTAMP    | DEFAULT CURRENT_TIMESTAMP   | Thời điểm gửi yêu cầu                 |

#### Bảng 5: `chi_bo` (Danh mục Chi bộ Đảng)

| Tên cột      | Kiểu dữ liệu          | Mô tả              |
| :----------- | :-------------------- | :----------------- |
| `id`         | INT PK                | Mã định danh       |
| `ten_chi_bo` | VARCHAR(255) NOT NULL | Tên đầy đủ chi bộ  |
| `ma_chi_bo`  | VARCHAR(50)           | Mã viết tắt chi bộ |
| `dang_uy`    | VARCHAR(255)          | Đảng ủy trực thuộc |

#### Bảng 6: `dang_vien` (Danh mục Đảng viên giúp đỡ)

| Tên cột     | Kiểu dữ liệu          | Mô tả                 |
| :---------- | :-------------------- | :-------------------- |
| `id`        | INT PK                | Mã định danh          |
| `ho_ten`    | VARCHAR(255) NOT NULL | Họ tên Đảng viên      |
| `chi_bo_id` | INT FK                | Chi bộ sinh hoạt      |
| `chuc_vu`   | VARCHAR(100)          | Chức vụ trong chi bộ  |
| `sdt`       | VARCHAR(20)           | Số điện thoại liên hệ |

#### Bảng 7: `lich_su` (Nhật ký thao tác hệ thống)

| Tên cột           | Kiểu dữ liệu | Mô tả                                        |
| :---------------- | :----------- | :------------------------------------------- |
| `id`              | INT PK       | Mã định danh                                 |
| `doi_tuong_id`    | INT FK       | Hồ sơ liên quan                              |
| `hanh_dong`       | VARCHAR(100) | Loại hành động (Duyệt, Từ chối, Sửa, Xóa...) |
| `mo_ta`           | TEXT         | Mô tả chi tiết thao tác                      |
| `nguoi_thuc_hien` | VARCHAR(100) | Username người thực hiện                     |
| `thoi_gian`       | TIMESTAMP    | Thời điểm xảy ra                             |

#### Bảng 8: `cai_dat` (Cấu hình hệ thống)

| Tên cột         | Kiểu dữ liệu | Mô tả                         |
| :-------------- | :----------- | :---------------------------- |
| `id`            | INT PK       | Mã định danh                  |
| `ten_truong`    | VARCHAR(255) | Tên trường Đại học            |
| `ten_dang_bo`   | VARCHAR(255) | Tên Đảng bộ / Chi bộ chủ quản |
| `ma_truong`     | VARCHAR(50)  | Mã trường                     |
| `dia_chi`       | TEXT         | Địa chỉ trường                |
| `email_lien_he` | VARCHAR(255) | Email liên hệ                 |

#### Bảng 9: `edge_ai_logs` (Nhật ký phân tích Edge AI OCR & Tệp minh chứng)

| Tên cột       | Kiểu dữ liệu | Mô tả                                                                 |
| :------------ | :----------- | :-------------------------------------------------------------------- |
| `id`          | INT PK       | Khóa chính tự tăng                                                    |
| `user_id`     | INT FK       | ID người dùng nộp minh chứng                                          |
| `trang_thai`  | VARCHAR(100) | Trạng thái AI đánh giá ('Đầy đủ hợp lệ', 'Cần bổ sung')               |
| `raw_summary` | TEXT         | Nội dung chi tiết báo cáo kiểm tra AI                                 |
| `files_json`  | TEXT         | Mảng JSON chứa đường dẫn tệp minh chứng (`uploads/ho_so_minh_chung/`) |
| `created_at`  | TIMESTAMP    | Thời điểm thực hiện quét OCR và lưu tệp                               |

---

## IV. CHI TIẾT CÔNG NGHỆ & CẤU TRÚC TRIỂN KHAI

### 1. Kiến trúc Thư mục Dự án

```
web1/
├── index.php                       ← Dashboard phân quyền (hiển thị khác nhau theo vai trò)
├── config.php                      ← Cấu hình DB PDO, BASE_URL, SITE_NAME, múi giờ
├── python_api/                     ← Backend Python Flask – Xuất Excel & PDF
│   ├── app.py                      ← API Flask với 5 endpoint (health, export all/single/selected)
│   ├── requirements.txt            ← Thư viện: flask, flask-cors, pymysql, openpyxl, reportlab
│   └── start_api.bat               ← Script 1-click cài thư viện & khởi động API (Windows)
├── uploads/
│   ├── avatars/                    ← Ảnh chân dung quần chúng (tự tạo khi upload)
│   └── email_logs.txt              ← Log gửi email (thay thế SMTP trong môi trường local)
├── Cau_hinh/
│   ├── setup.php                   ← Trang tự động tạo toàn bộ database và dữ liệu mẫu
│   └── db.sql                      ← File SQL cấu trúc bảng dự phòng
├── Giao_dien/
│   ├── header.php                  ← HTML <head>, Sidebar phân quyền 3 cấp, Header bar
│   ├── footer.php                  ← Đóng HTML, Script JS (sidebar toggle, accordion, flash)
│   └── assets/
│       └── style.css               ← 1016 dòng CSS Dark Mode hệ màu Đỏ-Vàng, Responsive
├── Quan_ly_doi_tuong/
│   ├── danh_sach.php               ← Danh sách đối tượng: bộ lọc + phân trang + sắp xếp
│   ├── them.php                    ← Form thêm mới hồ sơ (35 trường + upload avatar)
│   ├── chi_tiet.php                ← Hồ sơ chi tiết + Timeline 5 bước trực quan
│   ├── sua.php                     ← Form sửa đầy đủ + upload avatar mới
│   ├── xoa.php                     ← Xử lý xóa hồ sơ (kèm xóa file avatar)
│   ├── sua_nhanh.php               ← Bảng Excel trực tiếp, Autosave AJAX
│   ├── api_sua_nhanh.php           ← API PHP lưu dữ liệu sửa nhanh (JSON response)
│   ├── duyet_dang_ky.php           ← Phê duyệt 2 tab: Đăng ký mới + Cập nhật thông tin
│   ├── api_proxy.php               ← PHP Proxy chuyển tiếp request đến Flask:5000
│   ├── nhap_thong_tin.php          ← Form đăng ký trực tuyến dành cho sinh viên
│   ├── cap_nhat_thong_tin.php      ← Form đề xuất cập nhật cá nhân (điền sẵn, khóa Họ tên/Mã)
│   └── thanh_vien_chi_bo.php       ← Danh sách bạn cùng lớp đã được duyệt
├── Quan_ly_danh_muc/
│   ├── chi_bo.php                  ← CRUD Danh mục Chi bộ Đảng
│   └── dang_vien.php               ← CRUD Danh mục Đảng viên giúp đỡ
├── Thong_ke_bao_cao/
│   ├── thong_ke.php                ← 4 biểu đồ Chart.js: cột, tròn, đường, hành lang
│   ├── tim_kiem.php                ← Tìm kiếm nâng cao kết hợp nhiều trường lọc
│   ├── xuat_excel.php              ← Wizard xuất báo cáo: 3 định dạng, 3 phạm vi
│   └── import_excel.php            ← Import từ Excel/CSV kéo thả, preview trước khi nhập
├── He_thong/
│   └── cai_dat.php                 ← Cài đặt thông tin trường + đổi mật khẩu (chỉ Admin)
└── User/
    ├── login.php                   ← Đăng nhập + chọn vai trò + remember session
    ├── register.php                ← Đăng ký tài khoản mới (chọn vai trò)
    ├── logout.php                  ← Hủy session + redirect login
    └── auth.php                    ← requireLogin(), requireRole(), getCurrentUser(), getFlash()
```

### 2. Công nghệ sử dụng

| Lớp             | Công nghệ       | Phiên bản | Vai trò                                           |
| --------------- | --------------- | --------- | ------------------------------------------------- |
| **Frontend**    | HTML5           | —         | Cấu trúc trang                                    |
| **Frontend**    | Vanilla CSS3    | —         | 1016 dòng, Dark Mode Đỏ-Vàng, Responsive          |
| **Frontend**    | JavaScript ES6  | —         | AJAX Fetch API, Chart.js, Drag & Drop             |
| **Frontend**    | Chart.js        | 4.x (CDN) | Biểu đồ thống kê (Bar, Doughnut, Line)            |
| **Backend**     | PHP             | 8.x       | Logic nghiệp vụ chính, session, PDO               |
| **Backend**     | Python Flask    | 3.x       | REST API xuất báo cáo Excel + PDF                 |
| **Backend**     | Flask-CORS      | 4.x       | Cho phép PHP gọi cross-origin                     |
| **Database**    | MySQL           | 8.x       | Lưu trữ toàn bộ dữ liệu hệ thống                  |
| **ORM/Query**   | PDO (PHP)       | —         | Prepared Statements chống SQL Injection           |
| **Excel**       | openpyxl        | 3.1.x     | Tạo file .xlsx với định dạng cao cấp              |
| **PDF**         | reportlab       | 4.x       | Tạo file .pdf với font Times New Roman tiếng Việt |
| **RSS Parser**  | simplexml (PHP) | —         | Nạp tin tức từ Dân trí, Nhân Dân, Đảng Cộng sản   |
| **Web Server**  | Apache (XAMPP)  | 2.4.x     | Phục vụ PHP trên localhost                        |
| **Environment** | XAMPP           | 8.2.x     | Gói phát triển local (Apache + MySQL + PHP)       |

### 3. Kiến trúc Hệ thống (Architecture Overview)

```
Browser (Client)
    │
    ▼  HTTP Request (Port 80)
Apache/PHP (XAMPP) ──────────────────────────────────
    │                                                 │
    ├── index.php (Dashboard)                         │
    ├── Quan_ly_doi_tuong/ (Nghiệp vụ)               │
    ├── Thong_ke_bao_cao/ (Báo cáo)          MySQL ◄─┘
    ├── User/ (Xác thực)                    (Port 3306)
    │
    └── api_proxy.php ──► HTTP (Port 5000) ──► Python Flask API
                                                  │
                                          ├── openpyxl (Excel)
                                          └── reportlab (PDF)
```

---

## V. KẾT LUẬN & HƯỚNG PHÁT TRIỂN

### 1. Kết quả đạt được

Sau quá trình nghiên cứu, phân tích và triển khai, nhóm đã xây dựng thành công hệ thống Website quản lý quần chúng ưu tú phục vụ kết nạp Đảng với đầy đủ các chức năng đã đề ra:

| STT | Chức năng                                             | Trạng thái    |
| --- | ----------------------------------------------------- | ------------- |
| 1   | Đăng nhập / Đăng ký phân quyền 3 cấp                  | ✅ Hoàn thành |
| 2   | Dashboard thông tin cá nhân (Timeline 5 bước)         | ✅ Hoàn thành |
| 3   | Form đăng ký trực tuyến + Hệ thống phê duyệt          | ✅ Hoàn thành |
| 4   | Đề xuất cập nhật thông tin + So sánh Cũ/Mới           | ✅ Hoàn thành |
| 5   | Bảng sửa nhanh trực tiếp dạng Excel (Autosave)        | ✅ Hoàn thành |
| 6   | Import dữ liệu từ Excel/CSV kéo thả                   | ✅ Hoàn thành |
| 7   | Xuất Excel đầy đủ 35 cột qua Python Flask             | ✅ Hoàn thành |
| 8   | Xuất PDF hồ sơ cá nhân/danh sách qua Python Flask     | ✅ Hoàn thành |
| 9   | Thống kê biểu đồ Chart.js đa dạng                     | ✅ Hoàn thành |
| 10  | Xem danh sách bạn cùng Lớp/Chi bộ                     | ✅ Hoàn thành |
| 11  | Tin tức thời sự đa nguồn (3 báo)                      | ✅ Hoàn thành |
| 12  | Giao diện Dark Mode Responsive Đỏ-Vàng                | ✅ Hoàn thành |
| 13  | Gửi email thông báo (Log file trong môi trường local) | ✅ Hoàn thành |

### 2. Ưu điểm

- **Giao diện chuyên nghiệp:** Hệ màu Đỏ cờ Đảng – Vàng kim trên nền Dark Mode trang nghiêm, hiện đại, responsive trên mọi thiết bị.
- **Quy trình số hóa hoàn chỉnh:** Toàn bộ vòng đời hồ sơ từ nộp đơn → phê duyệt → theo dõi tiến trình → kết nạp đều được xử lý trong hệ thống.
- **Bảo mật chặt chẽ:** Phân quyền 3 cấp, PDO Prepared Statements chống SQL Injection, XSS prevention đầy đủ.
- **Trải nghiệm người dùng cao:** Autosave, Timeline trực quan, Dashboard cá nhân hóa, thẻ card tin tức.
- **Xuất báo cáo chuyên nghiệp:** File Excel định dạng cao cấp màu Đảng bộ + PDF hỗ trợ tiếng Việt qua Python.
- **Tích hợp thông tin thời sự:** 3 nguồn báo chính thống với cơ chế dự phòng thông minh.

### 3. Hạn chế

- **Phụ thuộc môi trường:** Cần cài Python và chạy Flask API thủ công khi muốn xuất báo cáo.
- **Email thực:** Chưa tích hợp SMTP thực (Gmail API) – email hiện tại chỉ ghi log file.
- **Đa người dùng đồng thời:** Chưa có cơ chế lock hàng khi nhiều Quản lý sửa cùng một hồ sơ.
- **Backup tự động:** Chưa có tính năng tự động sao lưu database định kỳ.

### 4. Hướng phát triển

- 📧 **Tích hợp SMTP Gmail thực tế:** Gửi email thông báo duyệt/từ chối trực tiếp đến sinh viên qua Gmail API OAuth2.
- 📱 **Ứng dụng mobile:** Phát triển bản app Android/iOS hỗ trợ Bí thư quản lý và phê duyệt di động.
- 🔔 **Thông báo realtime:** Tích hợp WebSocket để Quản lý nhận thông báo tức thì khi có hồ sơ mới.
- ☁️ **Triển khai Cloud:** Deploy lên VPS/Hosting có domain thực để dùng trong môi trường sản xuất.
- 📊 **Báo cáo nâng cao:** Biểu đồ phân tích xu hướng phát triển Đảng nhiều năm, so sánh giữa các khóa học.
- 🔒 **2FA Authentication:** Bổ sung xác thực 2 bước cho tài khoản Admin và Quản lý.

---

### 📝 Tài liệu Tham khảo Kỹ thuật Mở rộng

- 🤖 **[AI_Module/readme_ai.md - Tài liệu Kỹ thuật Chi tiết Module Edge AI &amp; OCR Auto-Fill](AI_Module/readme_ai.md)**

---

_Tài liệu được tạo và duy trì bởi nhóm sinh viên thực hiện Đồ án môn học._
_Mã nguồn dự án: https://github.com/Datdajt03/QLQTUT-Dang_
