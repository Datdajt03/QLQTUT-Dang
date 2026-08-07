# 🇻🇳 Website Quản lý Quần chúng Ưu tú phục vụ Kết nạp Đảng

> **Tên đề tài:** Thiết kế Website quản lý quần chúng ưu tú phục vụ kết nạp Đảng
> **Công nghệ:** PHP thuần MVC kết hợp Python Flask API & MySQL (XAMPP)

---

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

---

## 🚀 Hướng dẫn Cài đặt & Khởi chạy Chi tiết

Để khởi chạy dự án hoàn chỉnh cả phần Frontend (PHP) và Backend nâng cao (Python), bạn vui lòng thực hiện theo các bước chi tiết sau:

### ⚙️ Bước 1: Chuẩn bị môi trường
1. Tải và cài đặt **[XAMPP](https://www.apachefriends.org/)** (Khuyên dùng phiên bản PHP 8.x).
2. Tải và cài đặt **[Python](https://www.python.org/downloads/)** (Phiên bản 3.10 trở lên, nhớ tích chọn **"Add Python to PATH"** trong quá trình cài đặt).

### 📂 Bước 2: Sao chép Mã nguồn
1. Giải nén hoặc di chuyển thư mục mã nguồn này vào thư mục gốc của XAMPP:
   `C:\xampp\htdocs\web1`
   *(Đảm bảo đường dẫn chính xác là `C:\xampp\htdocs\web1\index.php`)*

### 🏛️ Bước 3: Khởi động Apache & MySQL trên XAMPP
1. Tìm và mở ứng dụng **XAMPP Control Panel**.
2. Nhấn nút **Start** ở dòng **Apache**.
3. Nhấn nút **Start** ở dòng **MySQL**.

### 🗄️ Bước 4: Khởi tạo Database Tự động
1. Mở trình duyệt web của bạn.
2. Truy cập đường dẫn: **[http://localhost/web1/Cau_hinh/setup.php](http://localhost/web1/Cau_hinh/setup.php)**
3. Trang thiết lập sẽ tự động kết nối cơ sở dữ liệu MySQL, tạo database `quan_ly_ket_nap_dang`, cài đặt các cấu trúc bảng và nạp dữ liệu mẫu.
4. Khi nhận được thông báo thành công, nhấn nút **"Vào trang Dashboard"** để được chuyển tới màn hình đăng nhập hệ thống: **[http://localhost/web1/User/login.php](http://localhost/web1/User/login.php)**.
   * **Tài khoản Admin mặc định**: Username: `Admin` | Password: `Admin123`
   * Mặc định form đăng nhập sẽ chọn vai trò là **Người dùng thường**, nếu đăng nhập bằng tài khoản Quản lý/Admin, bạn vui lòng nhấp chọn vai trò tương ứng trên màn hình.

### 🐍 Bước 5: Khởi động Python Flask API (Dành cho tính năng Xuất Excel nâng cao)
1. Truy cập vào thư mục: `C:\xampp\htdocs\web1\python_api`
2. Nhấp đúp chuột để khởi chạy file: **`start_api.bat`**
3. Cửa sổ dòng lệnh (CMD) sẽ hiện lên và tự động cài đặt thư viện cần thiết và khởi chạy API server tại cổng `5000`.

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

## 📝 Báo cáo bài tập lớn (Chi tiết Đề tài)
Để xem và soạn thảo tài liệu báo cáo học phần bài tập lớn chi tiết bao gồm mô tả phân tích đề tài, thiết kế sơ đồ database ERD, phân công công việc của các thành viên, vui lòng truy cập và cập nhật trực tiếp tại file:
👉 **[chitiet.md (Biểu mẫu Báo cáo Đồ án)](chitiet.md)**

---
*Phát triển bởi Nhóm sinh viên – Đồ án môn học Thiết kế Website Quản lý Quần chúng Ưu tú.*
