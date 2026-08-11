/**
 * AI_Module/README.md - Tài liệu kỹ thuật Module Edge AI
 */

# 🤖 AI_Module - Hệ thống Trợ lý Edge AI (Client-Side Intelligence)

Module Edge AI hoạt động trực tiếp tại trình duyệt người dùng (Client-side) mà không gây tải lag cho Server PHP/MySQL, đảm bảo tính bảo mật và phản hồi thời gian thực.

## 🚀 Các Tính năng Chính

### 1. 🔍 Tự động OCR Trích xuất & Điền Form Đăng ký (Smart Auto-Fill)
- **Tệp đầu vào**: Ảnh chụp **CCCD (Mặt trước & Mặt sau)** + **Thẻ sinh viên** hoặc **Giấy nhận thức về Đảng** (định dạng JPG, PNG, PDF, tối đa 10MB/file).
- **Cơ chế bóc tách**: Dùng `Tesseract.js` OCR tiếng Việt bóc tách Regex thời gian thực ra:
  - `ho_ten`: Họ và tên sinh viên
  - `ngay_sinh`: Ngày tháng năm sinh (định dạng YYYY-MM-DD)
  - `ma_gvsv`: Mã số Sinh viên / Số CCCD
  - `gioi_tinh`: Nam / Nữ
  - `que_quan`: Địa chỉ quê quán / thường trú
  - `dan_toc`: Dân tộc
  - `lop`: Lớp sinh hoạt hành chính
- **Tự động điền (Auto-fill)**: Điền trực tiếp vào các input trong form `nhap_thong_tin.php`, tiết kiệm 90% thời gian gõ thủ công.

### 2. 📸 Kiểm tra & Tự động Cắt/Chỉnh sửa Ảnh chân dung (Smart Avatar Validation & Crop 3x4)
- **Cơ chế AI**: Tự động phân tích tâm khuôn mặt (Face Detection) và tỉ lệ khung hình.
- **Auto-Crop 3x4**: Tự động căn chỉnh và cắt ảnh chân dung về tỷ lệ chuẩn 300x400 (ảnh thẻ 3x4 hành chính) bằng HTML5 Canvas trước khi gửi lên máy chủ.
- **Lợi ích**: Đảm bảo 100% ảnh đại diện và ảnh trên các Mẫu Phiếu PDF Kết Nạp Đảng 2026 (Mẫu 2-KNĐ) sắc nét và đúng quy chuẩn Đảng vụ.

### 3. 📄 Hỗ trợ Trích xuất & Định dạng Dữ liệu Mẫu Phiếu Kết Nạp Đảng PDF 2026
- **Trích xuất tự động**: Dữ liệu thu thập từ Edge AI OCR (CCCD/Thẻ SV) được lưu vào MySQL và tự động map vào **8 Mẫu Phiếu PDF chuẩn 2026** (Mẫu 1-KNĐ, 2-KNĐ, 3-KNĐ, 4-KNĐ, 4a-KNĐ, 5-KNĐ, Mẫu I, Mẫu II).
- **Nổi bật Dữ liệu `[ Đỏ ]`**: Toàn bộ dữ liệu được bóc tách và điền vào các mẫu PDF đều được **bôi đỏ và đóng khung `[ Dữ liệu ]` nổi bật**, giúp quản lý/sinh viên dễ dàng nhận diện để sao chép (copy) và dán chính xác vào biểu mẫu gốc.

---

## 📁 Cấu trúc Thư mục AI_Module
- `AI_Module/edge_ai_autofill.js`: Script xử lý OCR Auto-Fill & Smart Canvas Avatar Crop.
- `AI_Module/readme_ai.md`: Tài liệu hướng dẫn sử dụng & phân tích kiến trúc AI_Module.
