# Kế hoạch Tái cấu trúc Thư mục & Setup Phân quyền Người dùng

Chúng ta sẽ thực hiện tái cấu trúc toàn bộ hệ thống thư mục theo cấu trúc phân tích hệ thống (bằng tiếng Việt) và phân chia lại file, đồng thời bổ sung thêm phân quyền người dùng (User) gồm 3 vai trò: Người dùng thường, Quản lý, Admin.

---

## Ý tưởng & Thiết kế Cấu trúc Thư mục

Chúng ta sẽ tổ chức hệ thống thành các module nghiệp vụ rõ ràng:
1. `Cau_hinh`: Lưu trữ các file thiết lập và cơ sở dữ liệu (`db.sql`, `setup.php`).
2. `Giao_dien`: Lưu trữ các phần giao diện dùng chung (`header.php`, `footer.php`), cùng với thư mục `assets` và `pic`.
3. `Quan_ly_doi_tuong`: Quản lý hồ sơ quần chúng ưu tú (`danh_sach.php`, `chi_tiet.php`, `them.php`, `sua.php`, `xoa.php`, `sua_nhanh.php`, `api_sua_nhanh.php`, `api_proxy.php`, `duyet_dang_ky.php`, `nhap_thong_tin.php`).
4. `Quan_ly_danh_muc`: Quản lý chi bộ, đảng viên giúp đỡ (`chi_bo.php`, `dang_vien.php`).
5. `Thong_ke_bao_cao`: Các chức năng lọc nâng cao, thống kê, báo cáo, xuất nhập dữ liệu (`thong_ke.php`, `tim_kiem.php`, `xuat_excel.php`, `import_excel.php`).
6. `He_thong`: Cài đặt cấu hình chung của ứng dụng (`cai_dat.php`).
7. `User`: Quản lý tài khoản, đăng nhập, đăng ký (`login.php`, `register.php`, `logout.php`, `auth.php`).

> [!NOTE]
> File `config.php` vẫn được giữ ở thư mục gốc để đóng vai trò bootstrapper trung tâm, giúp các thư mục con dễ dàng load cấu hình thông qua `dirname(__DIR__) . '/config.php'`.

---

## Ý tưởng Thiết kế Phân quyền Người dùng (folder `User`)

1. **Bảng Cơ sở Dữ liệu (`nguoi_dung`)**:
   - `id`: Khóa chính.
   - `username`: Tên đăng nhập (duy nhất).
   - `password`: Mật khẩu băm (bcrypt).
   - `ho_ten`: Họ tên người dùng.
   - `vai_tro`: Quyền hạn (`Người dùng thường`, `Quản lý`, `Admin`).
   - Mặc định khởi tạo tài khoản quản trị tối cao: Username: `Admin` và Password: `Admin123`.

2. **Chức năng Đăng ký & Đăng nhập**:
   - **Giao diện Đăng ký**: Cho phép đăng ký tài khoản. Có lựa chọn vai trò (Dropdown/Radio). Mặc định chọn là "Người dùng thường". Nếu muốn đăng ký làm Quản lý hoặc Admin, người dùng có thể nhấp chọn.
   - **Giao diện Đăng nhập**: Đăng nhập bằng tài khoản và lựa chọn vai trò đăng nhập tương ứng (Mặc định chọn là "Người dùng thường").
   - Hệ thống tự động kiểm tra và khởi tạo tài khoản `Admin` / `Admin123` trong database nếu chưa tồn tại.

3. **Chính sách Phân quyền Truy cập**:
   - **Người dùng thường**:
     - Chỉ xem Dashboard tổng quan ở chế độ hạn chế (không xem được danh sách chi tiết tất cả đối tượng).
     - Chỉ được xem danh sách hồ sơ đăng ký của bản thân và gửi hồ sơ đăng ký mới (`nhap_thong_tin.php`).
     - Không có quyền truy cập vào các thư mục quản lý đối tượng khác, danh mục, thống kê, hay cài đặt hệ thống.
   - **Quản lý**:
     - Xem Dashboard đầy đủ.
     - Truy cập `Quan_ly_doi_tuong`, `Quan_ly_danh_muc`, `Thong_ke_bao_cao`.
     - Có thể duyệt, thêm, sửa đối tượng nhưng không thể vào phần cài đặt hệ thống (`He_thong`).
   - **Admin**:
     - Quyền hạn tối cao, truy cập được tất cả các module bao gồm `He_thong/cai_dat.php` và quản lý danh sách tài khoản người dùng.

---

## Các thay đổi chi tiết

### 1. Di chuyển và phân chia lại file hiện có:
- **Di chuyển sang `Cau_hinh/`**:
  - `db.sql` -> `Cau_hinh/db.sql`
  - `setup.php` -> `Cau_hinh/setup.php`
- **Di chuyển sang `Giao_dien/`**:
  - `includes/header.php` -> `Giao_dien/header.php`
  - `includes/footer.php` -> `Giao_dien/footer.php`
  - `assets/` -> `Giao_dien/assets/`
  - `pic/` -> `Giao_dien/pic/`
- **Di chuyển sang `Quan_ly_doi_tuong/`**:
  - `Chucnang/danh_sach.php` -> `Quan_ly_doi_tuong/danh_sach.php`
  - `Chucnang/chi_tiet.php` -> `Quan_ly_doi_tuong/chi_tiet.php`
  - `Chucnang/them.php` -> `Quan_ly_doi_tuong/them.php`
  - `Chucnang/sua.php` -> `Quan_ly_doi_tuong/sua.php`
  - `Chucnang/xoa.php` -> `Quan_ly_doi_tuong/xoa.php`
  - `Chucnang/sua_nhanh.php` -> `Quan_ly_doi_tuong/sua_nhanh.php`
  - `Chucnang/api_sua_nhanh.php` -> `Quan_ly_doi_tuong/api_sua_nhanh.php`
  - `Chucnang/api_proxy.php` -> `Quan_ly_doi_tuong/api_proxy.php`
  - `Chucnang/duyet_dang_ky.php` -> `Quan_ly_doi_tuong/duyet_dang_ky.php`
  - `nhap_thong_tin.php` -> `Quan_ly_doi_tuong/nhap_thong_tin.php`
- **Di chuyển sang `Quan_ly_danh_muc/`**:
  - `Tabphu/chi_bo.php` -> `Quan_ly_danh_muc/chi_bo.php`
  - `Tabphu/dang_vien.php` -> `Quan_ly_danh_muc/dang_vien.php`
- **Di chuyển sang `Thong_ke_bao_cao/`**:
  - `Tabphu/thong_ke.php` -> `Thong_ke_bao_cao/thong_ke.php`
  - `Tabphu/tim_kiem.php` -> `Thong_ke_bao_cao/tim_kiem.php`
  - `Chucnang/xuat_excel.php` -> `Thong_ke_bao_cao/xuat_excel.php`
  - `Chucnang/import_excel.php` -> `Thong_ke_bao_cao/import_excel.php`
- **Di chuyển sang `He_thong/`**:
  - `Tabphu/cai_dat.php` -> `He_thong/cai_dat.php`

### 2. Tạo mới folder `User/`:
- `User/auth.php`: File kiểm tra session đăng nhập và phân quyền.
- `User/login.php`: Trang đăng nhập đẹp mắt, hỗ trợ chọn 3 quyền (Người dùng thường, Quản lý, Admin), mặc định là Người dùng thường.
- `User/register.php`: Trang đăng ký, hỗ trợ chọn quyền, mặc định Người dùng thường.
- `User/logout.php`: Xử lý đăng xuất.

### 3. Cập nhật các liên kết và cấu trúc:
- Điều chỉnh `header.php` để hiển thị menu theo quyền hạn của tài khoản đang đăng nhập.
- Thêm cơ chế tự động cài đặt bảng `nguoi_dung` và tài khoản `Admin` / `Admin123` vào `config.php` để đảm bảo luôn sẵn sàng chạy mà không cần setup thủ công phức tạp.
- Cập nhật tất cả các đường dẫn tương đối trong các file PHP sau khi di chuyển thư mục.

---

## Kế hoạch Kiểm thử & Xác nhận
1. **Kiểm thử Setup**: Chạy lại cài đặt để chắc chắn bảng `nguoi_dung` được tạo và tài khoản `Admin` / `Admin123` đã hoạt động.
2. **Kiểm thử Auth**: Đăng nhập với quyền Người dùng thường -> kiểm tra Dashboard có bị giới hạn không, có vào được các chức năng quản trị không.
3. **Kiểm thử Quản lý & Admin**: Đăng nhập bằng `Admin` / `Admin123` -> kiểm tra toàn quyền truy cập hệ thống.
