<div style="text-align: justify">

## 🎨 Giao diện & Trải nghiệm Người dùng (UX/UI)

- **Hệ màu Đảng bộ:** Đỏ cờ chủ đạo (`#C8102E`), Vàng kim điểm nhấn (`#FFD700`) trên nền Dark Mode hiện đại, dịu mắt và vô cùng sang trọng.
- **Responsive Design:** Tối ưu hiển thị mượt mà trên mọi thiết bị (Desktop, Laptop, Máy tính bảng, Điện thoại di động).
- **Collapsible Sidebar:** Danh mục tiện ích được thiết kế dạng Accordion thông minh, tự động thu gọn để tối ưu diện tích làm việc và chỉ mở rộng khi cần thiết. Thụt hẳn vào gọn gàng khi thu nhỏ thanh bên trên máy tính.
- **Avatar Upload:** Hỗ trợ tải lên ảnh chân dung sắc nét, có tính năng xem trước (preview) thời gian thực và tự động tạo thư mục lưu trữ bảo mật cao.

---

## 🌟 Tính năng Nổi bật Mới Cập nhật

### 1. Bảng Excel Chỉnh sửa Trực tiếp (Direct Excel Edit)

- Giao diện bảng tính tương tác kiểu Excel tại đường dẫn `/Quan_ly_doi_tuong/sua_nhanh.php`.
- Hiển thị trực tiếp các trường nhập liệu (`input`, `select`, `date picker`) khít trong lưới bảng mà không cần nhấn đúp hay mở trang chi tiết.
- Cơ chế **Tự động lưu (Autosave)** khi chuyển ô (blur) hoặc thay đổi giá trị (change) qua AJAX kết nối với API `/Quan_ly_doi_tuong/api_sua_nhanh.php`.
- Hiệu ứng nhấp nháy màu nền ô (Flash animation) phản hồi trực quan: Vàng khi đang lưu, Xanh khi lưu thành công, Đỏ khi gặp lỗi.
- Hỗ trợ di chuyển điều hướng bàn phím bằng phím mũi tên `↑` `↓` `←` `→`, `Enter`, `Tab` và hủy thay đổi bằng `Esc`.

### 2. Form đăng ký Sinh viên & Duyệt hồ sơ (Gửi Gmail thông báo)

- **Form đăng ký trực tuyến**: Trang `/Quan_ly_doi_tuong/nhap_thong_tin.php` cho phép sinh viên tự điền hồ sơ đề xuất (Họ tên, mã SV, lớp, email, chi bộ đề xuất...) gửi lên hệ thống.
- **Màn hình phê duyệt (Quản lý/Admin)**: Quản lý hồ sơ tại `/Quan_ly_doi_tuong/duyet_dang_ky.php` với 3 phân loại: *Chờ duyệt*, *Đã duyệt*, *Đã từ chối*. Có huy hiệu đếm số hồ sơ chờ duyệt hiển thị thời gian thực trên thanh Sidebar.
  - **Phê duyệt**: Đưa thẳng thông tin sinh viên vào danh sách chính thức `doi_tuong`, đồng thời gửi thư chúc mừng qua Gmail sinh viên.
  - **Từ chối**: Admin nhập lý do từ chối, chuyển hồ sơ sang trạng thái từ chối và gửi email nêu rõ lý do bác bỏ.
- **Giả lập Gmail (Email Log)**: Toàn bộ email gửi đi (HTML định dạng đẹp mắt) đều được tự động lưu log tại thư mục `uploads/email_logs.txt` dưới dạng file văn bản để dễ dàng theo dõi và kiểm tra trong môi trường XAMPP local.

### 3. Phân quyền Người dùng 3 Lớp (Thư mục `/User/`)

Hệ thống bổ sung hệ cơ sở đăng ký, đăng nhập và xác thực phân quyền 3 cấp độ:

- **Người dùng thường (Sinh viên)**: Giao diện Dashboard được tinh gọn, chỉ hiển thị danh sách, trạng thái hồ sơ của bản thân và nút gửi hồ sơ đăng ký mới. Bị hạn chế truy cập vào các nghiệp vụ quản trị.
- **Quản lý (Bí thư Chi bộ)**: Sử dụng đầy đủ các công cụ quản lý đối tượng, duyệt đơn đăng ký, thống kê biểu đồ và import/export Excel. Không được vào phần cài đặt cấu hình hệ thống.
- **Admin (Quản trị viên)**: Quyền hạn tối cao hệ thống bao gồm quản lý tài khoản người dùng và cài đặt chung của ứng dụng.

### 4. Đề xuất Cập nhật & Dashboard Người dùng chính thức

- **Đề xuất chỉnh sửa trực tuyến**: Cho phép quần chúng đã được duyệt chính thức tự gửi yêu cầu cập nhật thông tin cá nhân (email, SĐT, lớp, quê quán...) qua trang `cap_nhat_thong_tin.php` (Họ tên và Mã SV được khóa chỉ đọc để bảo mật).
- **Giao diện so sánh của Quản lý**: Đề xuất cập nhật hiển thị tại Tab riêng biệt trong trang `duyet_dang_ky.php`, so sánh chi tiết các trường thay đổi (Cũ ➔ Mới) trực quan để Quản lý duyệt hoặc từ chối kèm lý do.
- **Dashboard Người dùng chính thức**: Khi hồ sơ được duyệt, Dashboard tự động chuyển thành trang thông tin chính thức có timeline 5 bước kết nạp Đảng trực quan và bảng danh sách thành viên cùng Lớp hoặc cùng Chi bộ Đảng.

### 5. Tích hợp Tin tức Đa nguồn (Dân trí, Báo Nhân Dân, Báo Đảng Cộng sản)

- **Bản tin đầu trang**: Tích hợp thanh chọn tin tức trực tuyến với 3 nguồn báo chính thống: *Báo Dân trí*, *Báo Nhân Dân*, và *Báo điện tử Đảng Cộng sản Việt Nam* ngay tại đầu trang chủ Dashboard (`index.php`) cho mọi tài khoản.
- **Cơ chế hoạt động**: Sử dụng parser RSS động của PHP kết hợp dự phòng luồng tin từ Báo điện tử Chính phủ hoặc dữ liệu dự phòng cục bộ khi trang Báo Đảng Cộng sản chặn cURL (Cookie wall). Có thiết lập timeout 3 giây để đảm bảo tải trang cực kỳ nhanh chóng.

### 6. Edge AI Module (`AI_Module`): Smart Auto-Fill & Smart Avatar Crop [NEW]

- **Trợ lý OCR Tự Động Điền Hồ Sơ (Smart Auto-Fill)**: Cho phép sinh viên tải lên **ảnh chụp CCCD (Mặt trước & Mặt sau) + Thẻ sinh viên** (hoặc Giấy nhận thức về Đảng). Engine Edge AI (`AI_Module/edge_ai_autofill.js`) dùng Tesseract.js trích xuất trực tiếp *Họ tên, Ngày sinh, Mã SV, Giới tính, Quê quán, Dân tộc, Lớp* và **tự động điền (Auto-fill)** vào form đăng ký `nhap_thong_tin.php`, tiết kiệm 90% thời gian gõ thủ công.
- **Kiểm tra & Tự động Cắt Ảnh Chân dung (Smart Avatar Validation & Crop 3x4)**: Tự động nhận diện khuôn mặt và dùng HTML5 Canvas cắt ảnh chân dung về đúng tỷ lệ ảnh thẻ chuẩn 3x4 (300x400) sắc nét trước khi tải lên máy chủ.
- **Agent Phân loại & Ánh xạ Tên Cột Excel (`AI_Module/excel_column_agent.js`)**: Khi tải lên file Excel/CSV tại `Thong_ke_bao_cao/import_excel.php`, AI Agent tự động phát hiện các tiêu đề cột ghi tắt/sai lệch (như `Qli`, `QL`, `Quản lý`, `Mã SV`...) và bật Modal hiển thị bảng chọn cột bên trái để ánh xạ chuẩn xác vào các trường CSDL trước khi lưu.
- **Thẩm định Hồ sơ Minh chứng (Client-Side AI)**: OCR kiểm tra tính đầy đủ của file PDF/Image minh chứng (dưới 10MB/file), lưu file vào `uploads/ho_so_minh_chung/` và lưu log phân tích vào MySQL `edge_ai_logs`.
- **Sơ đồ Luồng Hoạt Động**: Xem chi tiết sơ đồ Activity & Sequence Diagram của Edge AI tại 👉 **[chitiet.md (Mục 4c &amp; 5c)](chitiet.md#4c-quy-trình-smart-auto-fill-cccd--cắt-ảnh-thẻ-3x4-ai_module)**.

### 7. Xuất 8 Mẫu Phiếu Kết Nạp Đảng Ra PDF Chuẩn 2026 & Trích Xuất Dữ Liệu Cần Thiết [NEW]

- **Tự động trích xuất & điền dữ liệu đầy đủ vào PDF**: Hệ thống tự động trích xuất toàn bộ dữ liệu cần thiết từ CSDL MySQL (Họ tên, Ngày sinh, Nơi sinh/Quê quán, Dân tộc, Lớp, Chi bộ công nhận, Đảng viên giúp đỡ, Số chứng chỉ...) và kết xuất trực tiếp ra định dạng PDF chuẩn thể thức hành chính 2026.
- **Hỗ trợ xuất PDF 8 biểu mẫu chuẩn mới nhất 2026** (lưu trữ tại thư mục `Bieu_mau_dang_ky_ket_ap_dang`):
  1. **Mẫu 1-KNĐ**: Đơn xin vào Đảng (Cập nhật PDF 2026)
  2. **Mẫu 2-KNĐ [MỚI 2026]**: Lý lịch người vào Đảng (Sơ lược lý lịch & cam đoan PDF)
  3. **Mẫu 3-KNĐ**: Giấy giới thiệu của Đảng viên chính thức (Xuất PDF)
  4. **Mẫu 4-KNĐ**: Nghị quyết giới thiệu Đoàn viên ưu tú vào Đảng (Xuất PDF)
  5. **Mẫu 4a-KNĐ**: Nghị quyết giới thiệu Đoàn viên Công đoàn vào Đảng (Xuất PDF)
  6. **Mẫu 5-KNĐ**: Tổng hợp ý kiến nhận xét của các đoàn thể & Chi ủy nơi cư trú (Xuất PDF)
  7. **Mẫu CN-NTVĐ1**: Giấy chứng nhận lớp Bồi dưỡng nhận thức về Đảng - Trung tâm chính trị (Xuất PDF)
  8. **Mẫu CN-NTVĐ1-2**: Giấy chứng nhận lớp Bồi dưỡng nhận thức về Đảng - Cấp ủy cấp (Xuất PDF)
- **Mục đích xuất PDF & Nổi bật dữ liệu điền sẵn**: Giúp quản lý Đảng vụ trích xuất đầy đủ các dữ liệu hồ sơ cá nhân cần thiết. Toàn bộ các giá trị dữ liệu động được trích xuất (Họ tên, Ngày sinh, Quê quán, Lớp, Chi bộ, Đảng viên giúp đỡ...) đều được **tô viền và tô chữ màu Đỏ nổi bật `[ Dữ liệu ]`**, giúp người dùng dễ dàng nhận biết, sao chép (copy) và dán chính xác vào biểu mẫu gốc khi cần.
- **Cơ chế Thẩm tra & Tự động bật Modal cảnh báo khi thiếu thông tin**: Khi bấm xuất bất kỳ biểu mẫu PDF nào, nếu hệ thống phát hiện hồ sơ còn khuyết các trường bắt buộc (như *Quê quán, Dân tộc, Chi bộ...*), hệ thống sẽ dừng xuất file và **hiển thị 1 Tab Modal cảnh báo** liệt kê chính xác danh sách các thông tin thiếu kèm nút **"✏️ Điền thông tin ngay"** để bổ sung trực tiếp.
- **Xuất PDF sắc nét & Chuẩn phông tiếng Việt**: Khởi tạo và tải tệp PDF qua Python ReportLab API chuẩn phông Times New Roman sắc nét, nhanh chóng.

### 8. Xóa Hàng Loạt Nhiều Đối Tượng (Bulk Delete Action) & Mẫu Excel Điền Chuẩn [NEW]

- **Nút Xóa Hàng Loạt Đối Tượng**: Trang `Quan_ly_doi_tuong/danh_sach.php` tích hợp cột Checkbox chọn từng người và ô **"Select All"** ở đầu bảng. Khi chọn một hoặc nhiều đối tượng, nút **`🗑️ Xóa đối tượng đã chọn (N)`** lập tức xuất hiện ở góc trên.
- **Xác Nhận Modal An Toàn**: Bấm xóa hàng loạt sẽ hiển thị cửa sổ Modal xác nhận số lượng đối tượng cần xóa để ngăn ngừa thao tác nhầm lẫn.
- **Mẫu Excel Điền Chuẩn Kèm ID Cột (`/api/export/template`)**: Cung cấp tính năng tải mẫu Excel chuẩn gồm Tiêu đề & Mã ID trường dữ liệu (`[ID: ho_ten]`, `[ID: ma_gvsv]`,...) giúp các Lớp điền đúng 100%, không lo bị lệch cột khi Import.

---

## 🚀 Hướng dẫn Cài đặt & Khởi chạy Chi tiết

Dự án gồm **2 thành phần** cần chạy song song:

| Thành phần        | Công nghệ          | Cổng    | Vai trò                               |
| ------------------- | -------------------- | -------- | -------------------------------------- |
| Web Server (PHP)    | XAMPP Apache + MySQL | `80`   | Giao diện chính, xử lý nghiệp vụ |
| Export API (Python) | Python Flask         | `5000` | Xuất file Excel (`.xlsx`) và PDF   |

> ⚠️ **Lưu ý quan trọng**: Trang web hoạt động bình thường khi không có Python API. Chỉ cần chạy Python API khi muốn sử dụng tính năng **Xuất Excel/PDF** tại mục *Tiện ích & Báo cáo → Xuất dữ liệu*.

## 🚀 Cài Đặt 1-Click Tự Động Cho Máy Mới (`setup_newcomputer.bat`) [NEW]

Nếu bạn chuyển mã nguồn dự án sang máy tính mới, chỉ cần chạy file **`setup_newcomputer.bat`**:

```cmd
# Chạy trực tiếp file setup_newcomputer.bat tại thư mục gốc dự án
A:\xamapp\htdocs\web1\setup_newcomputer.bat
```

File script này sẽ tự động:

1. **Tạo thư mục cần thiết**: Tạo cấu trúc `uploads/`, `uploads/ho_so_minh_chung/`, `uploads/avatars/`.
2. **Kích hoạt Extension ZIP trong `php.ini`**: Tự động tìm `php.ini` và bỏ dấu comment `;extension=zip` thành `extension=zip` trong XAMPP.
3. **Cài đặt thư viện Python**: Tự động cài đặt `Flask`, `ReportLab`, `PyMySQL`, `openpyxl` từ `python_api/requirements.txt`.
4. **Nạp CSDL MySQL**: Tự động tạo CSDL `ql_dangvien` và import bảng từ `Cau_hinh/db.sql`.
5. **Khởi chạy Python Microservice**: Tự động khởi động Flask server tại `http://localhost:5000`.

---

### ⚙️ Bước 1: Cài đặt XAMPP

1. Tải XAMPP tại: **[https://www.apachefriends.org/](https://www.apachefriends.org/)** (khuyến nghị phiên bản **8.2.x** trở lên với PHP 8.x)
2. Cài đặt XAMPP vào đường dẫn mặc định `C:\xampp`
3. Mở **XAMPP Control Panel** và nhấn **Start** cho cả hai dịch vụ:
   - ✅ **Apache** – Web server PHP
   - ✅ **MySQL** – Cơ sở dữ liệu

---

### 📂 Bước 2: Sao chép Mã nguồn

Clone từ GitHub hoặc giải nén mã nguồn vào thư mục gốc của XAMPP:

```bash
# Cách 1: Clone từ Git
git clone https://github.com/Datdajt03/QLQTUT-Dang.git C:\xampp\htdocs\web1

# Cách 2: Giải nén thủ công
# → Giải nén vào C:\xampp\htdocs\web1\
```

Kiểm tra: đường dẫn `C:\xampp\htdocs\web1\index.php` phải tồn tại.

---

### 🗄️ Bước 3: Khởi tạo Database Tự động

1. Mở trình duyệt và truy cập: **[http://localhost/web1/Cau_hinh/setup.php](http://localhost/web1/Cau_hinh/setup.php)**
2. Trang sẽ **tự động**:
   - Kết nối MySQL với user `root` (không mật khẩu – mặc định XAMPP)
   - Tạo database `quan_ly_ket_nap_dang`
   - Tạo tất cả bảng và nạp dữ liệu mẫu demo
3. Khi thấy thông báo **"Cài đặt thành công"**, nhấn **"Vào trang Dashboard"**

**Tài khoản mặc định sau khi setup:**

| Vai trò      | Username     | Password     |
| ------------- | ------------ | ------------ |
| Admin         | `Admin`    | `Admin123` |
| Quản lý     | `testql`   | `Admin123` |
| Người dùng | `testuser` | `Admin123` |

> 💡 Khi đăng nhập, hãy chọn đúng vai trò tương ứng trên trang login.

---

### 🐍 Bước 4: Cài đặt Python & Môi trường Flask API

> Phần này dành cho tính năng **Xuất Excel/PDF**. Bỏ qua nếu không cần xuất báo cáo.

#### 4.1 – Cài đặt Python

1. Tải Python tại: **[https://www.python.org/downloads/](https://www.python.org/downloads/)** (phiên bản **3.10+**, khuyến nghị **3.11** hoặc **3.12**)
2. Khi cài đặt, **BẮT BUỘC** tích chọn ✅ **"Add Python to PATH"** (ở màn hình đầu tiên của trình cài đặt)
3. Kiểm tra cài đặt thành công bằng cách mở **Command Prompt** và gõ:
   ```bash
   python --version
   # Kết quả mong muốn: Python 3.x.x

   pip --version
   # Kết quả mong muốn: pip xx.x from ...
   ```

#### 4.2 – Cài đặt thư viện Python (cách nhanh)

Cách nhanh nhất là chạy trực tiếp file `start_api.bat` — nó sẽ tự động cài thư viện:

```
C:\xampp\htdocs\web1\python_api\start_api.bat
```

Hoặc cài thủ công qua Command Prompt:

```bash
cd C:\xampp\htdocs\web1\python_api
pip install -r requirements.txt
```

Danh sách thư viện sẽ được cài đặt:

| Thư viện     | Phiên bản | Vai trò                                  |
| -------------- | ----------- | ----------------------------------------- |
| `flask`      | ≥ 3.0.0    | Web framework API server                  |
| `flask-cors` | ≥ 4.0.0    | Cho phép PHP gọi API cross-origin       |
| `pymysql`    | ≥ 1.1.0    | Kết nối MySQL từ Python                |
| `openpyxl`   | ≥ 3.1.2    | Tạo và định dạng file Excel`.xlsx` |
| `reportlab`  | ≥ 4.0.0    | Tạo file PDF có hỗ trợ tiếng Việt   |

#### 4.3 – Khởi động Flask API

**Cách 1 (Khuyến nghị):** Nhấp đúp chuột vào file:

```
C:\xampp\htdocs\web1\python_api\start_api.bat
```

**Cách 2:** Chạy thủ công qua PowerShell hoặc CMD:

```bash
cd C:\xampp\htdocs\web1\python_api
# Windows – fix lỗi encoding tiếng Việt
set PYTHONIOENCODING=utf-8
set PYTHONUTF8=1
python app.py
```

Khi thấy màn hình hiển thị như sau là **thành công**:

```
[OK] Font TimesNewRoman da dang ky thanh cong!
==================================================
  Flask API (Excel + PDF) – He thong Ket nap Dang v1.1
  Running at: http://localhost:5000
==================================================
 * Running on http://127.0.0.1:5000
```

#### 4.4 – Kiểm tra API hoạt động

Mở trình duyệt, truy cập: **[http://localhost:5000/health](http://localhost:5000/health)**

Kết quả thành công sẽ trả về:

```json
{"status": "ok", "message": "Flask API dang hoat dong!"}
```

Sau đó vào **Dashboard → Tiện ích & Báo cáo → Xuất dữ liệu**, sẽ thấy banner xanh: **"✅ Python API đang chạy – Sẵn sàng xuất dữ liệu!"**

---

### 🔧 Xử lý Lỗi Thường Gặp

#### ❌ Lỗi: `'python' is not recognized as an internal or external command`

**Nguyên nhân**: Chưa tích chọn "Add Python to PATH" khi cài Python.**Giải pháp**:

- Gỡ và cài lại Python, nhớ tích chọn **"Add Python to PATH"** ở bước đầu
- Hoặc thêm thủ công: vào *System Properties → Environment Variables → PATH* và thêm đường dẫn `C:\Users\[TÊN]\AppData\Local\Programs\Python\Python3xx\`

#### ❌ Lỗi: `UnicodeEncodeError: 'charmap' codec can't encode character`

**Nguyên nhân**: Windows dùng encoding `cp1252` mặc định, không hỗ trợ tiếng Việt.
**Giải pháp**: Chạy qua `start_api.bat` thay vì gõ lệnh thủ công (file đã được cấu hình `PYTHONIOENCODING=utf-8` và `PYTHONUTF8=1`).

#### ❌ Lỗi: `Address already in use: Port 5000`

**Nguyên nhân**: Đã có một tiến trình khác đang chạy trên cổng 5000.
**Giải pháp**: Mở Task Manager (Ctrl+Shift+Esc) → Tab *Details* → Tìm `python.exe` → *End Task*. Sau đó khởi động lại API.

#### ❌ Lỗi: `ModuleNotFoundError: No module named 'flask'`

**Nguyên nhân**: Chưa cài đặt thư viện.
**Giải pháp**: Chạy lại `start_api.bat` (tự động cài) hoặc `pip install -r requirements.txt` thủ công.

#### ❌ Trang báo "⚠️ Python API chưa chạy"

**Nguyên nhân**: Flask API chưa được khởi động hoặc đã bị đóng.
**Giải pháp**: Chạy lại `start_api.bat`. Flask API cần **chạy nền song song** với XAMPP trong suốt quá trình sử dụng tính năng xuất báo cáo.

---

## 📁 Sơ đồ cấu trúc Thư mục

```
web1/
├── index.php                      ← Dashboard phân quyền theo vai trò (chọn lọc nội dung hiển thị)
├── config.php                     ← Cấu hình database PDO, múi giờ, tự động tạo bảng dữ liệu
├── Form_mau_xuat_phieu_thong_tin/   ← [NEW 2026] Thư mục chứa các mẫu văn bản hành chính .docx gốc 2026
│   └── Bieu_mau_dang_ky_ket_ap_dang/ ← Các tệp biểu mẫu chuẩn: 1-KNĐ, 2-KNĐ, 3-KNĐ, 4-KNĐ, 4a-KNĐ, 5-KNĐ...
├── python_api/                    ← Backend Python xuất báo cáo Excel & Mẫu PDF chuẩn 2026
│   ├── app.py                     ← REST API Flask xử lý sinh file Excel & ReportLab PDF Mẫu 2026
│   ├── requirements.txt           ← Danh sách thư viện Python cần dùng (openpyxl, reportlab, pymysql, flask)
│   └── start_api.bat              ← Script khởi chạy nhanh bằng một click chuột
├── uploads/
│   ├── avatars/                   ← Thư mục chứa ảnh đại diện của quần chúng
│   ├── ho_so_minh_chung/          ← [NEW] Thư mục chứa các tệp minh chứng đã upload (tối đa 10MB/file)
│   └── email_logs.txt             ← File log ghi nhận lịch sử gửi email cho sinh viên
├── Cau_hinh/                      ← Module Cấu hình và CSDL
│   ├── setup.php                  ← Trang tự động tạo cơ sở dữ liệu giao diện web
│   └── db.sql                     ← File cấu trúc bảng SQL dự phòng
├── Giao_dien/                     ← Giao diện chung và Assets
│   ├── header.php                 ← Thanh công cụ đầu trang & Sidebar phân quyền
│   ├── footer.php                 ← Bản quyền chân trang & Xử lý hiệu ứng JS
│   ├── assets/
│   │   └── style.css              ← Hệ thống CSS stylesheet Dark Mode (Đỏ-Vàng)
│   └── pic/                       ← Ảnh và logo giao diện
├── Quan_ly_doi_tuong/             ← Module nghiệp vụ hồ sơ
│   ├── danh_sach.php              ← Danh sách đối tượng, bộ lọc đa năng & phân trang
│   ├── them.php                   ← Form thêm mới đối tượng
│   ├── chi_tiet.php               ← Trang chi tiết hồ sơ, Timeline 5 bước & Menu Xuất Mẫu 2026
│   ├── sua.php                    ← Form chỉnh sửa thông tin & Avatar
│   ├── xoa.php                    ← Xử lý xóa đối tượng
│   ├── sua_nhanh.php              ← Bảng Excel chỉnh sửa thông tin trực tiếp, tự động lưu
│   ├── api_sua_nhanh.php          ← API xử lý lưu dữ liệu sửa nhanh qua AJAX
│   ├── duyet_dang_ky.php          ← Giao diện phê duyệt/từ chối hồ sơ đăng ký trực tuyến
│   ├── edge_ai_check.php          ← [NEW] Giao diện Edge AI OCR quét kiểm tra hồ sơ minh chứng
│   ├── edge_ai_ocr.js             ← [NEW] Engine OCR Tesseract.js & PDF.js xử lý client-side
│   ├── api_save_ai_check.php      ← [NEW] API tiếp nhận file minh chứng (max 10MB) & lưu nhật ký AI
│   ├── api_proxy.php              ← File trung gian PHP Proxy kết nối Python Server
│   ├── cap_nhat_thong_tin.php     ← Form đề xuất cập nhật thông tin cá nhân dành cho quần chúng/sinh viên
│   ├── thanh_vien_chi_bo.php      ← Trang xem danh sách thành viên cùng lớp hoặc chi bộ sinh hoạt
│   └── nhap_thong_tin.php         ← Form đăng ký thông tin trực tuyến dành cho sinh viên
├── Quan_ly_danh_muc/             ← Module danh mục phân cấp hỗ trợ
│   ├── chi_bo.php                 ← Danh mục quản lý các Chi bộ
│   └── dang_vien.php              ← Danh mục quản lý Đảng viên giúp đỡ
├── Thong_ke_bao_cao/              ← Module thống kê, nhập xuất excel
│   ├── thong_ke.php               ← Trang báo cáo chi tiết với 4 loại biểu đồ Chart.js
│   ├── tim_kiem.php               ← Tìm kiếm nâng cao kết hợp nhiều trường dữ liệu
│   ├── xuat_excel.php             ← Trình thuật sĩ xuất Excel cao cấp (3 loại xuất)
│   └── import_excel.php           ← Trang nhập dữ liệu từ file Excel/CSV kéo thả
├── He_thong/                      ← Module Quản trị hệ thống nâng cao
│   └── cai_dat.php                ← Quản lý cài đặt thông tin trường học & mật khẩu chung
└── User/                          ← [NEW] Module Đăng nhập, đăng ký và phân quyền
    ├── login.php                  ← Trang đăng nhập hỗ trợ chọn vai trò (mặc định Người dùng thường)
    ├── register.php               ← Trang đăng ký tài khoản mới chọn vai trò
    ├── logout.php                 ← Xử lý đăng xuất tài khoản
    └── auth.php                   ← Bộ helper kiểm tra quyền hạn truy cập (requireRole, requireLogin)
```

---

## 🗄️ Cơ sở Dữ liệu (MySQL Schema)

Hệ thống quản lý cơ sở dữ liệu quan hệ chặt chẽ với các bảng:

- `nguoi_dung`: [NEW] Lưu trữ tài khoản đăng nhập và phân quyền (Người dùng thường, Quản lý, Admin).
- `doi_tuong`: Lưu trữ thông tin lý lịch của quần chúng ưu tú theo quy trình kết nạp Đảng (bao gồm cột `avatar`).
- `dang_ky_doi_tuong`: Lưu trữ hồ sơ sinh viên đăng ký trực tuyến chờ phê duyệt.
- `yeu_cau_cap_nhat`: [NEW] Lưu trữ các đề xuất chỉnh sửa/cập nhật thông tin của quần chúng chờ phê duyệt.
- `edge_ai_logs`: [NEW] Lưu trữ nhật ký phân tích AI OCR và danh sách file minh chứng tải lên (tối đa 10MB/file).
- `chi_bo`: Quản lý danh mục các Chi bộ Đảng trong đơn vị.
- `dang_vien`: Quản lý danh sách các đảng viên được phân công hướng dẫn.
- `lich_su`: Ghi lại toàn bộ lịch sử thao tác để giám sát hệ thống.
- `cai_dat`: Lưu trữ các hằng số hệ thống.

---

## 📝 Báo cáo bài tập lớn (Phân tích & Thiết kế Đề tài)

Để xem và soạn thảo tài liệu báo cáo học phần bài tập lớn chi tiết bao gồm mô tả phân tích thiết kế hệ thống chuyên sâu (Quy trình nghiệp vụ, sơ đồ Use Case, Activity, Sequence), thiết kế sơ đồ database ERD và đặc tả bảng dữ liệu chi tiết, vui lòng truy cập và cập nhật trực tiếp tại các tài liệu sau:

- 👉 **[chitiet.md (Báo cáo Phân tích Thiết kế Hệ thống Đồ án)](chitiet.md)**
- 🤖 **[AI_Module/readme_ai.md (Tài liệu kỹ thuật Chi tiết Module Edge AI)](AI_Module/readme_ai.md)**

---

*Phát triển bởi Nhóm sinh viên – Đồ án Quản lý Quần chúng Ưu tú.*

</div>
