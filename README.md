<div align="center">

# ⭐ WEBSITE QUẢN LÝ QUẦN CHÚNG ƯU TÚ PHỤC VỤ KẾT NẠP ĐẢNG ⭐

### 🇻🇳 *Hệ thống số hóa quy trình phát triển Đảng viên tại trường Đại học TÂY BẮC* 🇻🇳

<a href="https://utb.edu.vn" target="_blank">
  <img src="https://utb.edu.vn/wp-content/uploads/2022/09/logo.png" alt="Trường Đại học Tây Bắc" height="80"/>
</a>

[![🏫 Trường Đại học Tây Bắc](https://img.shields.io/badge/🏫%20Trường%20ĐH-TÂY%20BẮC-C8102E?style=for-the-badge&labelColor=FFD700)](https://utb.edu.vn)

[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Python](https://img.shields.io/badge/Python-3.10+-3776AB?style=for-the-badge&logo=python&logoColor=white)](https://python.org)
[![Flask](https://img.shields.io/badge/Flask-3.x-000000?style=for-the-badge&logo=flask&logoColor=white)](https://flask.palletsprojects.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![XAMPP](https://img.shields.io/badge/XAMPP-8.2-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)](https://apachefriends.org)

[![GitHub](https://img.shields.io/badge/GitHub-Datdajt03%2FQLQTUT--Dang-C8102E?style=for-the-badge&logo=github&logoColor=white)](https://github.com/Datdajt03/QLQTUT-Dang)
[![License](https://img.shields.io/badge/License-Academic-FFD700?style=for-the-badge)](.)
[![Status](https://img.shields.io/badge/Status-✅%20Hoàn%20thành-success?style=for-the-badge)](.)

---

### 👥 Nhóm Thực hiện

| # | Họ và Tên | Phân công |
|:---:|---|---|
| 1 | **Lò Mạnh Đạt** | Backend PHP, Database |
| 2 | **Nguyễn Huy Hoàng** | Frontend CSS/JS, UI/UX |
| 3 | **Tòng Lưu Anh Tú** | Python API, Báo cáo |
| 4 | **Phạm Thị Thanh Hảo** | Python API, Báo cáo |

---

</div>

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

### 4. Đề xuất Cập nhật & Dashboard Người dùng chính thức [NEW]
- **Đề xuất chỉnh sửa trực tuyến**: Cho phép quần chúng đã được duyệt chính thức tự gửi yêu cầu cập nhật thông tin cá nhân (email, SĐT, lớp, quê quán...) qua trang `cap_nhat_thong_tin.php` (Họ tên và Mã SV được khóa chỉ đọc để bảo mật).
- **Giao diện so sánh của Quản lý**: Đề xuất cập nhật hiển thị tại Tab riêng biệt trong trang `duyet_dang_ky.php`, so sánh chi tiết các trường thay đổi (Cũ ➔ Mới) trực quan để Quản lý duyệt hoặc từ chối kèm lý do.
- **Dashboard Người dùng chính thức**: Khi hồ sơ được duyệt, Dashboard tự động chuyển thành trang thông tin chính thức có timeline 5 bước kết nạp Đảng trực quan và bảng danh sách thành viên cùng Lớp hoặc cùng Chi bộ Đảng.

### 5. Tích hợp Tin tức Đa nguồn (Dân trí, Báo Nhân Dân, Báo Đảng Cộng sản) [NEW]
- **Bản tin đầu trang**: Tích hợp thanh chọn tin tức trực tuyến với 3 nguồn báo chính thống: *Báo Dân trí*, *Báo Nhân Dân*, và *Báo điện tử Đảng Cộng sản Việt Nam* ngay tại đầu trang chủ Dashboard (`index.php`) cho mọi tài khoản.
- **Cơ chế hoạt động**: Sử dụng parser RSS động của PHP kết hợp dự phòng luồng tin từ Báo điện tử Chính phủ hoặc dữ liệu dự phòng cục bộ khi trang Báo Đảng Cộng sản chặn cURL (Cookie wall). Có thiết lập timeout 3 giây để đảm bảo tải trang cực kỳ nhanh chóng.

---

## 🚀 Hướng dẫn Cài đặt & Khởi chạy Chi tiết

Dự án gồm **2 thành phần** cần chạy song song:
| Thành phần | Công nghệ | Cổng | Vai trò |
|---|---|---|---|
| Web Server (PHP) | XAMPP Apache + MySQL | `80` | Giao diện chính, xử lý nghiệp vụ |
| Export API (Python) | Python Flask | `5000` | Xuất file Excel (`.xlsx`) và PDF |

> ⚠️ **Lưu ý quan trọng**: Trang web hoạt động bình thường khi không có Python API. Chỉ cần chạy Python API khi muốn sử dụng tính năng **Xuất Excel/PDF** tại mục *Tiện ích & Báo cáo → Xuất dữ liệu*.

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

| Vai trò | Username | Password |
|---|---|---|
| Admin | `Admin` | `Admin123` |
| Quản lý | `testql` | `Admin123` |
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

| Thư viện | Phiên bản | Vai trò |
|---|---|---|
| `flask` | ≥ 3.0.0 | Web framework API server |
| `flask-cors` | ≥ 4.0.0 | Cho phép PHP gọi API cross-origin |
| `pymysql` | ≥ 1.1.0 | Kết nối MySQL từ Python |
| `openpyxl` | ≥ 3.1.2 | Tạo và định dạng file Excel `.xlsx` |
| `reportlab` | ≥ 4.0.0 | Tạo file PDF có hỗ trợ tiếng Việt |

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
**Nguyên nhân**: Chưa tích chọn "Add Python to PATH" khi cài Python.  
**Giải pháp**:
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



## 📁 Sơ đồ cấu trúc Thư mục mới

```
web1/
├── index.php                      ← Dashboard phân quyền theo vai trò (chọn lọc nội dung hiển thị)
├── config.php                     ← Cấu hình database PDO, múi giờ, tự động tạo bảng dữ liệu
├── python_api/                    ← Backend Python xuất báo cáo Excel
│   ├── app.py                     ← API xử lý sinh file Excel định dạng cao cấp
│   ├── requirements.txt           ← Danh sách thư viện Python cần dùng
│   └── start_api.bat              ← Script khởi chạy nhanh bằng một click chuột
├── uploads/
│   ├── avatars/                   ← Thư mục tự tạo chứa ảnh đại diện của quần chúng
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
│   ├── chi_tiet.php               ← Trang chi tiết hồ sơ & Timeline tiến trình 5 bước
│   ├── sua.php                    ← Form chỉnh sửa thông tin & Avatar
│   ├── xoa.php                    ← Xử lý xóa đối tượng
│   ├── sua_nhanh.php              ← Bảng Excel chỉnh sửa thông tin trực tiếp, tự động lưu
│   ├── api_sua_nhanh.php          ← API xử lý lưu dữ liệu sửa nhanh qua AJAX
│   ├── duyet_dang_ky.php          ← Giao diện phê duyệt/từ chối hồ sơ đăng ký trực tuyến
│   ├── api_proxy.php              ← File trung gian PHP Proxy kết nối Python Server
│   ├── cap_nhat_thong_tin.php     ← [NEW] Form đề xuất cập nhật thông tin cá nhân dành cho quần chúng/sinh viên
│   ├── thanh_vien_chi_bo.php     ← [NEW] Trang xem danh sách thành viên cùng lớp hoặc chi bộ sinh hoạt dành cho sinh viên
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
- `doi_tuong`: Lưu trữ thông tin lý lịch của quần chúng ưu tú theo quy trình kết nạp Đảng.
- `dang_ky_doi_tuong`: Lưu trữ hồ sơ sinh viên đăng ký trực tuyến chờ phê duyệt.
- `yeu_cau_cap_nhat`: [NEW] Lưu trữ các đề xuất chỉnh sửa/cập nhật thông tin của quần chúng chờ phê duyệt.
- `chi_bo`: Quản lý danh mục các Chi bộ Đảng trong đơn vị.
- `dang_vien`: Quản lý danh sách các đảng viên được phân công hướng dẫn.
- `lich_su`: Ghi lại toàn bộ lịch sử thao tác để giám sát hệ thống.
- `cai_dat`: Lưu trữ các hằng số hệ thống.

---

## 📝 Báo cáo bài tập lớn (Phân tích & Thiết kế Đề tài)
Để xem và soạn thảo tài liệu báo cáo học phần bài tập lớn chi tiết bao gồm mô tả phân tích thiết kế hệ thống chuyên sâu (Quy trình nghiệp vụ, sơ đồ Use Case, Activity, Sequence), thiết kế sơ đồ database ERD và đặc tả bảng dữ liệu chi tiết, vui lòng truy cập và cập nhật trực tiếp tại file:
👉 **[chitiet.md (Báo cáo Phân tích Thiết kế Hệ thống Đồ án)](chitiet.md)**

---
*Phát triển bởi Nhóm sinh viên – Đồ án môn học Thiết kế Website Quản lý Quần chúng Ưu tú.*
