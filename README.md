# 🇻🇳 Website Quản lý Quần chúng Ưu tú phục vụ Kết nạp Đảng

> **Tên đề tài:** Thiết kế Website quản lý quần chúng ưu tú phục vụ kết nạp Đảng
> **Công nghệ:** PHP thuần MVC kết hợp Python Flask API & MySQL (XAMPP)

---

## 🎨 Giao diện & Trải nghiệm Người dùng (UX/UI)
- **Hệ màu Đảng bộ:** Đỏ cờ chủ đạo (`#C8102E`), Vàng kim điểm nhấn (`#FFD700`) trên nền Dark Mode hiện đại, dịu mắt và vô cùng sang trọng.
- **Responsive Design:** Tối ưu hiển thị mượt mà trên mọi thiết bị (Desktop, Laptop, Máy tính bảng, Điện thoại di động).
- **Collapsible Sidebar:** Danh mục **Tab phụ (Tiện ích)** được thiết kế dạng Accordion thông minh, tự động thu gọn để tối ưu diện tích làm việc và chỉ mở rộng khi cần thiết. Thụt hẳn vào hoàn toàn gọn gàng khi thu nhỏ thanh bên trên máy tính.
- **Avatar Upload:** Hỗ trợ tải lên ảnh chân dung sắc nét, có tính năng xem trước (preview) thời gian thực và tự động tạo thư mục lưu trữ bảo mật cao.

---

## 🌟 Tính năng Nổi bật Mới Cập nhật

### 1. Bảng Excel Chỉnh sửa Trực tiếp (Direct Excel Edit)
- Giao diện bảng tính tương tác kiểu Excel tại đường dẫn `/Chucnang/sua_nhanh.php`.
- Hiển thị trực tiếp các trường nhập liệu (`input`, `select`, `date picker`) khít trong lưới bảng mà không cần nhấn đúp hay mở trang chi tiết.
- Cơ chế **Tự động lưu (Autosave)** khi chuyển ô (blur) hoặc thay đổi giá trị (change) qua AJAX kết nối với API `/Chucnang/api_sua_nhanh.php`.
- Hiệu ứng nhấp nháy màu nền ô (Flash animation) phản hồi trực quan: Vàng khi đang lưu, Xanh khi lưu thành công, Đỏ khi gặp lỗi.
- Hỗ trợ di chuyển điều hướng bàn phím bằng phím mũi tên `↑` `↓` `←` `→`, `Enter`, `Tab` và hủy thay đổi bằng `Esc`.
- Tích hợp thanh tìm kiếm văn bản và bộ lọc trạng thái hồ sơ trực tiếp.

### 2. Form đăng ký Sinh viên & Duyệt hồ sơ (Gửi Gmail thông báo)
- **Form đăng ký trực tuyến**: Trang công khai `/nhap_thong_tin.php` cho phép sinh viên tự điền hồ sơ đề xuất (Họ tên, mã SV, lớp, email, chi bộ đề xuất...) gửi lên hệ thống.
- **Màn hình phê duyệt (Admin)**: Quản lý hồ sơ tại `/Chucnang/duyet_dang_ky.php` với 3 phân loại: *Chờ duyệt*, *Đã duyệt*, *Đã từ chối*. Có huy hiệu đếm số hồ sơ chờ duyệt hiển thị thời gian thực trên thanh Sidebar.
  - **Phê duyệt**: Đưa thẳng thông tin sinh viên vào danh sách chính thức `doi_tuong`, đồng thời gửi thư chúc mừng qua Gmail sinh viên.
  - **Từ chối**: Admin nhập lý do từ chối, chuyển hồ sơ sang trạng thái từ chối và gửi email nêu rõ lý do bác bỏ.
- **Giả lập Gmail (Email Log)**: Toàn bộ email gửi đi (HTML định dạng đẹp mắt) đều được tự động lưu log tại thư mục `uploads/email_logs.txt` dưới dạng file văn bản để dễ dàng theo dõi và kiểm tra trong môi trường XAMPP local.

---

## 🚀 Hướng dẫn Cài đặt & Khởi chạy Chi tiết

Để khởi chạy dự án hoàn chỉnh cả phần Frontend (PHP) và Backend nâng cao (Python), bạn vui lòng thực hiện theo các bước chi tiết sau:

### ⚙️ Bước 1: Chuẩn bị môi trường
1. Tải và cài đặt **[XAMPP](https://www.apachefriends.org/)** (Khuyên dùng phiên bản PHP 8.x).
2. Tải và cài đặt **[Python](https://www.python.org/downloads/)** (Phiên bản 3.10 trở lên, nhớ tích chọn **"Add Python to PATH"** trong quá trình cài đặt).

---

### 📂 Bước 2: Sao chép Mã nguồn
1. Giải nén hoặc di chuyển thư mục mã nguồn này vào thư mục gốc của XAMPP:
   `C:\xampp\htdocs\web1`
   *(Đảm bảo đường dẫn chính xác là `C:\xampp\htdocs\web1\index.php`)*

---

### 🏛️ Bước 3: Khởi động Apache & MySQL trên XAMPP
1. Tìm và mở ứng dụng **XAMPP Control Panel**.
2. Nhấn nút **Start** ở dòng **Apache**.
3. Nhấn nút **Start** ở dòng **MySQL**.
4. Đảm bảo cả hai dòng đều chuyển sang màu xanh lá cây.

---

### 🗄️ Bước 4: Khởi tạo Database Tự động
1. Mở trình duyệt web của bạn (Chrome, Edge, Firefox...).
2. Truy cập đường dẫn: **[http://localhost/web1/setup.php](http://localhost/web1/setup.php)**
3. Trang thiết lập sẽ tự động kết nối cơ sở dữ liệu MySQL, tạo database `quan_ly_ket_nap_dang`, cài đặt các cấu trúc bảng và nạp dữ liệu mẫu gồm 21 quần chúng ưu tú từ danh sách Excel gốc.
4. Khi nhận được thông báo thành công, nhấn nút **"Vào trang Dashboard"** hoặc truy cập: **[http://localhost/web1/](http://localhost/web1/)**.

---

### 🐍 Bước 5: Khởi động Python Flask API (Dành cho tính năng Xuất Excel nâng cao)
Hệ thống sử dụng một server phụ bằng Python để xuất dữ liệu Excel được định dạng thẩm mỹ cao.
1. Truy cập vào thư mục: `C:\xampp\htdocs\web1\python_api`
2. Nhấp đúp chuột để khởi chạy file: **`start_api.bat`**
3. Cửa sổ dòng lệnh (CMD) sẽ hiện lên và tự động:
   - Phát hiện phiên bản Python trên máy tính.
   - Tự động tải và cài đặt các thư viện cần thiết trong file `requirements.txt` (nếu chưa có).
   - Khởi chạy API server tại cổng `5000`.
4. Khi bạn thấy dòng chữ `Running at: http://localhost:5000`, hãy giữ nguyên cửa sổ này hoạt động trong suốt quá trình sử dụng website.

---

## 📁 Sơ đồ cấu trúc Thư mục

```
web1/
├── index.php                      ← Dashboard tổng quan, biểu đồ thống kê nhanh
├── config.php                     ← Cấu hình database PDO, múi giờ, hàm bổ trợ
├── setup.php                      ← Trang tự động tạo cơ sở dữ liệu giao diện web
├── db.sql                         ← File cấu trúc bảng SQL dự phòng
├── nhap_thong_tin.php             ← [NEW] Form đăng ký trực tuyến công khai dành cho sinh viên
├── assets/
│   └── style.css                  ← Hệ thống CSS stylesheet tùy biến (Chủ đạo Đỏ-Vàng)
├── includes/
│   ├── header.php                 ← Thanh công cụ đầu trang & Sidebar Accordion (đã fix thụt lề khi collapse)
│   └── footer.php                 ← Bản quyền chân trang & Xử lý hiệu ứng JS
├── Chucnang/                      ← Các tính năng quản lý nghiệp vụ cốt lõi
│   ├── danh_sach.php              ← Danh sách đối tượng, bộ lọc đa năng & phân trang
│   ├── them.php                   ← Form thêm mới đối tượng (6 nhóm thông tin)
│   ├── chi_tiet.php               ← Trang chi tiết hồ sơ & Timeline tiến trình 5 bước
│   ├── sua.php                    ← Form chỉnh sửa thông tin & Avatar
│   ├── xoa.php                    ← Xử lý xóa đối tượng an toàn
│   ├── sua_nhanh.php              ← [NEW] Bảng Excel chỉnh sửa thông tin trực tiếp, tự động lưu
│   ├── api_sua_nhanh.php          ← [NEW] API xử lý lưu dữ liệu sửa nhanh qua AJAX
│   ├── duyet_dang_ky.php          ← [NEW] Giao diện Admin phê duyệt, từ chối hồ sơ đăng ký trực tuyến
│   ├── xuat_excel.php             ← Trình thuật sĩ xuất Excel cao cấp (3 loại xuất)
│   ├── import_excel.php           ← Trang nhập dữ liệu từ file Excel/CSV kéo thả
│   └── api_proxy.php              ← File trung gian PHP Proxy kết nối Python Server
├── Tabphu/                        ← Các trang bổ trợ quản trị
│   ├── thong_ke.php               ← Trang báo cáo chi tiết với 4 loại biểu đồ Chart.js
│   ├── chi_bo.php                 ← Danh mục quản lý các Chi bộ
│   ├── dang_vien.php              ← Danh mục quản lý Đảng viên giúp đỡ
│   ├── tim_kiem.php               ← Tìm kiếm nâng cao kết hợp nhiều trường dữ liệu
│   └── cai_dat.php                ← Quản lý cài đặt hệ thống & Đổi mật khẩu
├── python_api/                    ← Backend Python xuất báo cáo Excel
│   ├── app.py                     ← API xử lý sinh file Excel định dạng cao cấp
│   ├── requirements.txt           ← Danh sách thư viện Python cần dùng
│   └── start_api.bat              ← Script khởi chạy nhanh bằng một click chuột
└── uploads/
    ├── avatars/                   ← Thư mục tự tạo chứa ảnh đại diện của quần chúng
    └── email_logs.txt             ← [NEW] File log ghi nhận lịch sử gửi email chúc mừng/từ chối sinh viên
```

---

## ✨ Chi tiết Tính năng Xuất Excel (Python Backend)
Trình thuật sĩ xuất Excel cung cấp **3 loại tùy chọn xuất** chuyên sâu:
1. **Xuất toàn bộ danh sách:** Tự động tạo bảng dữ liệu tổng hợp dựa trên bộ lọc phạm vi được chọn (Toàn trường, Lớp cụ thể hoặc Chi bộ cụ thể).
2. **Xuất hồ sơ 1 người:** Xuất ra 1 file Excel riêng biệt, tự động dàn trang và định dạng theo biểu mẫu hồ sơ lý lịch cá nhân chi tiết của đối tượng đó.
3. **Xuất danh sách tự chọn:** Người dùng có thể tích chọn nhiều người bằng checkbox trên danh sách hiển thị để xuất file Excel danh sách chọn lọc.

---

## 🗄️ Cơ sở Dữ liệu (MySQL Schema)
Hệ thống quản lý cơ sở dữ liệu quan hệ chặt chẽ với các bảng:
- `doi_tuong`: Lưu trữ 34 cột thông tin lý lịch của quần chúng ưu tú theo quy trình kết nạp Đảng, liên kết với cột `avatar` và `email`.
- `dang_ky_doi_tuong`: Lưu trữ hồ sơ sinh viên đăng ký trực tuyến chờ Admin duyệt.
- `chi_bo`: Quản lý danh mục các Chi bộ Đảng trong đơn vị.
- `dang_vien`: Quản lý danh sách các đảng viên được phân công hướng dẫn, giúp đỡ.
- `lich_su`: Ghi lại toàn bộ lịch sử thao tác của kiểm trị viên (Thêm, Sửa, Xóa) để giám sát hệ thống.
- `cai_dat`: Lưu trữ các hằng số hệ thống (Tên trường, Đảng ủy, Email, Mật khẩu quản trị...).

---
*Phát triển bởi Nhóm sinh viên – Đồ án môn học Thiết kế Website Quản lý Quần chúng Ưu tú.*
