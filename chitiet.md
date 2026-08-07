# BÁO CÁO BÀI TẬP LỚN: XÂY DỰNG WEBSITE QUẢN LÝ QUẦN CHÚNG ƯU TÚ

* **Môn học:** Thiết kế Website / Phát triển ứng dụng Web
* **Đề tài:** Thiết kế Website quản lý quần chúng ưu tú phục vụ kết nạp Đảng
* **Nhóm thực hiện:** [Điền tên nhóm của bạn vào đây]
* **Thành viên:**
  1. [Họ và tên SV 1] - [MSSV 1] - [Phân công công việc]
  2. [Họ và tên SV 2] - [MSSV 2] - [Phân công công việc]
  3. [Họ và tên SV 3] - [MSSV 3] - [Phân công công việc]

---

## I. GIỚI THIỆU ĐỀ TÀI
Nêu lý do chọn đề tài, mục tiêu của website quản lý quần chúng ưu tú, và ý nghĩa thực tiễn trong công tác phát triển Đảng viên mới tại trường Đại học/Cơ quan.

---

## II. PHÂN TÍCH HỆ THỐNG & YÊU CẦU CHỨC NĂNG

### 1. Phân tích đối tượng sử dụng (Actor) và Phân quyền (Role-based access)
Hệ thống được chia thành 3 quyền truy cập chính sau khi tái cấu trúc:
* **Người dùng thường (Sinh viên / Quần chúng)**:
  * Đăng ký tài khoản cá nhân (mặc định nhận quyền này).
  * Gửi hồ sơ đề xuất trực tuyến (nhập thông tin cá nhân lý lịch cá nhân).
  * Theo dõi tiến trình duyệt hồ sơ trực tiếp trên trang cá nhân của mình.
* **Quản lý (Bí thư Chi bộ / Cán bộ văn phòng Đảng)**:
  * Xem danh sách đối tượng quần chúng, chi tiết timeline 5 bước kết nạp.
  * Phê duyệt hoặc từ chối đơn đăng ký trực tuyến của sinh viên, nhập lý do từ chối (hệ thống tự động gửi email thông báo).
  * Sử dụng các tính năng nâng cao: Import Excel, Xuất Excel nâng cao, Thống kê biểu đồ Chart.js, Sửa nhanh Excel trực tiếp.
  * Không có quyền cấu hình hệ thống cấp cao.
* **Admin (Quản trị viên hệ thống)**:
  * Có tất cả các quyền của Quản lý.
  * Quản trị cấu hình hệ thống: đổi tên trường, tên Đảng bộ, email quản trị.
  * Quản lý các tài khoản người dùng và phân chia quyền hạn.

### 2. Các chức năng cốt lõi (Use Cases)
* **Quản lý Hồ sơ Quần chúng**: Thêm mới, sửa thông tin, tải lên ảnh đại diện, xem timeline 5 bước (Cảm tình Đảng, Giúp đỡ, Nhận thức Đảng, Kết nạp Đảng, Chuyển sinh hoạt).
* **Bảng Excel Chỉnh sửa Trực tiếp**: Cho phép nhập liệu trực quan trực tiếp trên lưới Excel mà không cần tải lại trang thông qua cơ chế AJAX truyền tải thời gian thực.
* **Quy trình Phê duyệt & Gửi Mail tự động**: Quản lý duyệt hồ sơ và phản hồi tự động gửi tới email sinh viên.
* **Nhập/Xuất Dữ liệu Excel nâng cao**: Kết hợp backend Python Flask API để sinh ra các file Excel báo cáo định dạng chuẩn thẩm mỹ cao.
* **Thống kê & Tìm kiếm nâng cao**: Các biểu đồ cột/tròn trực quan thống kê đối tượng theo tháng, trạng thái, chi bộ, và bộ lọc tìm kiếm đa năng.

---

## III. THIẾT KẾ CƠ SỞ DỮ LIỆU (DATABASE SCHEMA)

Sơ đồ quan hệ thực thể (ERD) và các bảng dữ liệu chính trong MySQL:

```mermaid
erDiagram
    NGUOI_DUNG {
        int id PK
        string username UNIQUE
        string password
        string ho_ten
        enum vai_tro
        timestamp created_at
    }
    DOI_TUONG {
        int id PK
        string ma_gvsv
        string ho_ten
        string lop
        string sdt
        string email
        string gioi_tinh
        date ngay_sinh
        string dan_toc
        string que_quan
        string chi_bo_cong_nhan
        string trang_thai
        date ngay_hop_cam_tinh
        date ngay_ket_nap
        string ghi_chu
    }
    DANG_KY_DOI_TUONG {
        int id PK
        string ma_gvsv
        string ho_ten
        string sdt
        string email
        string lop
        string chi_bo_cong_nhan
        string trang_thai
        string ly_do_tu_choi
    }
    CHI_BO {
        int id PK
        string ten_chi_bo
        string ma_chi_bo
        string dang_uy
    }
    DANG_VIEN {
        int id PK
        string ho_ten
        int chi_bo_id FK
        string chuc_vu
    }
    LICH_SU {
        int id PK
        int doi_tuong_id FK
        string hanh_dong
        string mo_ta
        string nguoi_thuc_hien
        timestamp thoi_gian
    }
```

---

## IV. CHI TIẾT CÔNG NGHỆ & CẤU TRÚC TRIỂN KHAI

### 1. Kiến trúc phân tách theo cấu trúc phân tích hệ thống (Vietnamese Structure)
Dự án được phân cấp rõ ràng theo các module chức năng chính:
* **`Cau_hinh/`**: Đảm nhiệm cài đặt hệ thống và cơ sở dữ liệu (`db.sql`, `setup.php`).
* **`Giao_dien/`**: Giao diện UI/UX tối ưu Dark Mode và Responsive (`header.php`, `footer.php`, CSS, hình ảnh).
* **`Quan_ly_doi_tuong/`**: Nghiệp vụ quản lý chính đối với quần chúng ưu tú và đơn đăng ký trực tuyến.
* **`Quan_ly_danh_muc/`**: Danh mục phân quyền chi bộ và đảng viên hỗ trợ.
* **`Thong_ke_bao_cao/`**: Biểu đồ phân tích và công cụ xuất nhập dữ liệu file Excel.
* **`He_thong/`**: Cấu hình cấu trúc trường học và thay đổi mật khẩu hệ thống.
* **`User/`**: Bảo mật đăng nhập, đăng ký tài khoản, kiểm tra phiên làm việc (session cookie) và xác thực quyền hạn.

### 2. Công nghệ sử dụng
* **Frontend**: HTML5, Vanilla CSS3 (Hệ màu Dark mode phối hợp Đỏ-Vàng sang trọng, Responsive linh hoạt), JavaScript ES6 (AJAX Fetch API).
* **Backend**: PHP 8.x thuần kết nối CSDL qua PDO bảo mật chống SQL Injection.
* **Python API (Flask)**: Phục vụ xuất báo cáo định dạng cao cấp qua các thư viện xử lý bảng tính chuyên dụng.
* **Cơ sở dữ liệu**: MySQL (hệ quản trị cơ sở dữ liệu XAMPP).

---

## V. KẾT LUẬN & HƯỚNG PHÁT TRIỂN
* **Ưu điểm**: Giao diện Dark mode hiện đại bắt mắt; Phân quyền chặt chẽ; Tính năng sửa trực tiếp kiểu Excel tối ưu hóa hiệu suất nhập liệu; Có hệ thống log lịch sử rõ ràng.
* **Hạn chế**: Cần cấu hình môi trường Python để sử dụng một số chức năng báo cáo chuyên sâu.
* **Hướng phát triển**: Tích hợp chữ ký số vào phê duyệt hồ sơ; Kết nối trực tiếp hệ thống email thực (SMTP); Xây dựng ứng dụng di động hỗ trợ quản lý Đảng viên nhanh chóng.
