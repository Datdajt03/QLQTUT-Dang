# 🤖 AI_Module - Hệ thống Trợ lý Edge AI (Client-Side Intelligence)

Module Edge AI hoạt động trực tiếp tại trình duyệt người dùng (Client-side) mà không gây tải lag cho Server PHP/MySQL, đảm bảo tính bảo mật, phản hồi thời gian thực và trải nghiệm người dùng tối ưu.

---

## 🚀 Các Tính năng Nổi bật trong AI Module

### 1. 🔍 Tự động OCR Trích xuất & Điền Form Đăng ký (`edge_ai_autofill.js`)
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

---

### 2. 📸 Kiểm tra & Tự động Cắt/Chỉnh sửa Ảnh chân dung (Smart Avatar Crop 3x4)
- **Cơ chế AI**: Tự động phân tích tâm khuôn mặt (Face Detection) và tỉ lệ khung hình.
- **Auto-Crop 3x4**: Tự động căn chỉnh và cắt ảnh chân dung về tỷ lệ chuẩn 300x400 (ảnh thẻ 3x4 hành chính) bằng HTML5 Canvas trước khi gửi lên máy chủ.
- **Lợi ích**: Đảm bảo 100% ảnh đại diện và ảnh trên các Mẫu Phiếu PDF Kết Nạp Đảng 2026 (Mẫu 2-KNĐ) sắc nét và đúng quy chuẩn Đảng vụ.

---

### 3. 📄 Engine Soi 5 Document Models & Thẩm định Thông tin Khuyết (`edge_ai_ocr.js`)
Edge AI tích hợp bộ 5 mô hình soi tệp minh chứng và kiểm tra chi tiết các trường thông tin bắt buộc trong từng loại phiếu:

| STT | Tên Model Phiếu/Hồ sơ | Mã Model | Các Trường Thông Tin Chi Tiết Soi Bắt Buộc |
| :--- | :--- | :--- | :--- |
| **1** | **Bản tự nhận xét / Tự kiểm điểm** | `ban_tu_nhan_xet` | Họ tên, Ngày sinh, Ưu điểm/Thành tích, Khuyết điểm/Hạn chế, Phương hướng phấn đấu, Ngày tháng & Chữ ký |
| **2** | **Giấy chứng nhận bồi dưỡng nhận thức về Đảng** | `giay_chung_nhan` | Đơn vị cấp (ĐH Tây Bắc / Trung tâm chính trị), Họ tên học viên, Ngày sinh, Xếp loại, Số QĐ/Số chứng nhận, Ngày cấp |
| **3** | **Sơ yếu lý lịch / CCCD / Thẻ SV** | `ho_so_ca_nhan` | Họ tên, Ngày sinh, Quê quán/Nguyên quán, Mã SV/Số CCCD, Lớp/Khoa sinh hoạt |
| **4** | **Phiếu đánh giá chất lượng đoàn viên** | `phieu_danh_gia` | Họ tên đoàn viên, Tên Chi đoàn, Kết quả xếp loại đoàn viên, Xác nhận/Chữ ký Bí thư Chi đoàn |
| **5** | **Minh chứng hoạt động phong trào / Giấy khen** | `minh_chung_hoat_dong` | Tên hoạt động (Hiến máu, Tình nguyện...), Họ tên người nhận, Đơn vị khen thưởng, Thời gian thực hiện |

**Báo cáo Đánh giá Chi tiết:** Thay vì thông báo chung chung, Edge AI đưa ra bảng phân tích rõ ràng:
- **Tệp phiếu nào còn thiếu hoàn toàn** (chưa nộp).
- **Tệp phiếu nào đã nộp nhưng bị khuyết thông tin chi tiết nào ở bên trong phiếu**, kèm đề xuất khắc phục cụ thể.
- **Tự động lưu vết:** Đẩy file về `uploads/ho_so_minh_chung/` và lưu nhật ký đánh giá vào bảng MySQL `edge_ai_logs` qua API `api_save_ai_check.php`.

---

### 4. 🧠 Agent Phân loại & Ánh xạ Tên cột Excel Thông minh (`excel_column_agent.js`)
- **Đặt vấn đề Thực tế**: Khi import file Excel/CSV từ các lớp/chi bộ, tiêu đề cột thường bị viết tắt (`Qli`, `QL`, `Quản lý`, `MSSV`, `Hoten`...) hoặc bị trống tiêu đề.
- **Kiến trúc & Cơ chế Agent (`AI_Module/excel_column_agent.js`)**:
  1. **Chuẩn hóa Chuỗi (`normalizeHeader`)**: Tách dấu tiếng Việt Unicode NFD, xóa ký tự đặc biệt.
  2. **Đối soát Từ khóa (`DB_COLUMNS_DICTIONARY`)**: Khớp ma trận 2 chiều tìm kiếm từ khóa đồng nghĩa với độ tin cậy Badge (`Chính xác`, `Gợi ý`, `Chưa rõ`).
  3. **Phát hiện Cột Trống Tiêu Đề**: Tự động đánh dấu vị trí `⚠️ Cột A (Trống tiêu đề)` để buộc chọn trường CSDL.
  4. **Giao diện Modal Tab Agent**: Bật Modal Tab cho phép cán bộ quản lý xem xét và đổi chọn lại trường CSDL trước khi xác nhận Import.

### 5. 🔬 Universal Dynamic Document Field Inspector (`AI_Module/document_inspector.js`)
- **Mục tiêu**: Tự động nhận diện tiêu đề, trích xuất cấu trúc trường nhãn và **thẩm định cảnh báo trường bị thiếu/trống cho MỌI LOẠI PHIẾU BẤT KỲ** mà người dùng nộp lên.
- **Chiến lược Thẩm định Kép (Dual Inspection Strategy)**:
  1. **Chiến lược A (Khớp 5 Mẫu Phiếu Kết nạp Đảng tiêu chuẩn):** Kiểm tra đối soát danh sách từ khóa mở rộng của 5 Mẫu phiếu tiêu chuẩn (*Bản tự nhận xét, Giấy chứng nhận, Sơ yếu lý lịch, Phiếu đánh giá, Minh chứng*).
  2. **Chiến lược B (Mẫu Phiếu Tùy Chỉnh / Bất kỳ tệp phiếu nào khác):**
     - **Thuật toán `extractUniversalFormStructure`**: Tự động bóc tách Tiêu đề phiếu từ 8 dòng đầu tệp và dùng biểu thức chính quy (Regex) tự động quét toàn bộ các cặp `[Nhãn_Trường]: [Nội_dung / Chấm_lửng / Ô_trống]`.
     - **Hàm `cleanFieldValue`**: Lọc sạch các khoảng trắng rác, chấm lửng (`.....`), gạch dưới (`_____`). Nếu sau nhãn không có dữ liệu thực tế ➔ Tự động gán nhãn **`[CẢNH BÁO TRỐNG/THIẾU]`** và in đỏ nổi bật tên trường bị thiếu của phiếu tùy chỉnh đó.
- **Báo cáo Tỷ lệ Phần trăm Đầy đủ**: $\text{ScorePercent} = (\text{Số trường đã điền} / \text{Tổng số trường phát hiện trong phiếu}) \times 100\%$.

---

## 📁 Cấu trúc Thư mục AI_Module

```
AI_Module/
├── edge_ai_autofill.js       ← Engine OCR Client-side điền form & Smart Canvas Crop ảnh thẻ 3x4
├── excel_column_agent.js     ← Client-side Agent phân tích & ánh xạ tiêu đề cột Excel thông minh
├── document_inspector.js     ← Module chuyên dụng soi & cảnh báo các trường thông tin bị thiếu trong phiếu
├── readme_ai.md              ← Tài liệu kỹ thuật chi tiết hệ thống Edge AI Engine
```
