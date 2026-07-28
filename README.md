# QLQTUT-Dang

## Thiết kế Website Quản lý Quần chúng Ưu tú Phục vụ Kết nạp Đảng

Hệ thống PHP + MySQL (XAMPP) quản lý toàn bộ quy trình kết nạp đảng viên.

---

## 🚀 Cài đặt & Chạy

### Yêu cầu
- XAMPP (Apache + PHP 8.x + MySQL)
- Trình duyệt web

### Các bước
1. Clone repo vào thư mục `C:\xampp\htdocs\web1`
2. Mở XAMPP → Start **Apache** và **MySQL**
3. Truy cập `http://localhost/web1/setup.php` → tạo database tự động
4. Truy cập `http://localhost/web1/` để vào hệ thống

---

## 📁 Cấu trúc thư mục

```
web1/
├── index.php                    ← Dashboard chính
├── config.php                   ← Cấu hình database
├── setup.php                    ← Tạo DB tự động
├── db.sql                       ← Schema database
├── assets/
│   └── style.css                ← CSS chính (dark theme đỏ-vàng)
├── includes/
│   ├── header.php               ← Header + Sidebar dùng chung
│   └── footer.php               ← Footer + JS dùng chung
├── Chucnang/                    ← Chức năng chính
│   ├── danh_sach.php            ← Danh sách + tìm kiếm + lọc
│   ├── them.php                 ← Thêm đối tượng mới
│   ├── chi_tiet.php             ← Xem chi tiết hồ sơ
│   ├── sua.php                  ← Sửa thông tin
│   ├── xoa.php                  ← Xóa đối tượng
│   ├── xuat_excel.php           ← Xuất Excel/CSV
│   └── import_excel.php         ← Import từ Excel/CSV
└── Tabphu/                      ← Tab phụ
    ├── thong_ke.php             ← Thống kê & Biểu đồ
    ├── chi_bo.php               ← Quản lý Chi bộ
    ├── dang_vien.php            ← Quản lý Đảng viên giúp đỡ
    ├── tim_kiem.php             ← Tìm kiếm nâng cao
    └── cai_dat.php              ← Cài đặt hệ thống
```

---

## ✨ Tính năng

| Chức năng | Mô tả |
|-----------|-------|
| Dashboard | Thống kê tổng quan, biểu đồ theo tháng và trạng thái |
| Danh sách | Tìm kiếm, lọc theo lớp/trạng thái, phân trang |
| Thêm mới | Form 33 trường chia 6 nhóm theo quy trình kết nạp |
| Chi tiết | Timeline tiến trình 5 bước, tab lịch sử thao tác |
| Import | Upload file Excel/CSV drag-and-drop |
| Xuất Excel | Xuất CSV UTF-8 mở được bằng Excel |
| Thống kê | 4 loại biểu đồ: line, bar, doughnut, horizontal |
| Quản lý Chi bộ | CRUD danh sách chi bộ |
| Quản lý Đảng viên | CRUD đảng viên giúp đỡ/hướng dẫn |
| Tìm kiếm nâng cao | Lọc theo nhiều tiêu chí kết hợp |
| Cài đặt | Thông tin hệ thống, đổi mật khẩu |

---

## 📊 Cấu trúc Database

- `doi_tuong` – Bảng chính (33 trường theo file Excel)
- `chi_bo` – Danh sách chi bộ
- `dang_vien` – Danh sách đảng viên giúp đỡ
- `lich_su` – Lịch sử thao tác
- `cai_dat` – Cài đặt hệ thống

---

## 🎨 Thiết kế
- Dark mode với màu chủ đạo **đỏ Đảng** (#C8102E) + **vàng** (#FFD700)
- Font Roboto, biểu đồ Chart.js
- Responsive (desktop-first)

---

*Đồ án môn học – Nhóm sinh viên*
