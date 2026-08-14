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

### 5. 🤖 Multi-Agent Suite: AI Inspector & Gap Diagnostic Agent (`AI_Module/document_inspector.js`)
- **Mục tiêu**: Phối hợp đa Agent AI để tự động đọc hiểu, nhận định lý do, ra kết luận đánh giá thông minh (**AI Agent Verdict**) và đưa ra khuyến nghị khắc phục cụ thể cho từng tệp tệp nộp.
- **Kiến trúc Phối hợp Multi-Agent**:
  1. **Agent 1 - Semantic Document Synopsis Agent**: Tự động đọc và bóc tách Tiêu đề / Mục đích tệp (`generateDocumentSynopsis`).
  2. **Agent 2 - Dynamic Form Field Extractor Agent**: Bóc tách ma trận nhãn trường `[Nhãn_Trường]: [Nội_dung / Ô_trống]`.
  3. **Agent 3 - Gap Diagnostic & Verdict Agent**: Tự động đánh giá lý do tệp bị thiếu/trống trường, xuất kết luận nhận xét thông minh (`agentVerdict`) và khuyến nghị hướng sửa (`actionAdvice`).
  4. **Agent 4 - Executive Synthesis Agent**: Tổng hợp toàn bộ bộ hồ sơ, đưa ra bảng **Báo cáo Kết luận AI Agent Synthesis Dashboard** ở cấp độ toàn hệ thống.

---

## 📁 Cấu trúc Thư mục AI_Module

```
AI_Module/
├── edge_ai_autofill.js       ← Engine OCR Client-side điền form & Smart Canvas Crop ảnh thẻ 3x4
├── excel_column_agent.js     ← Client-side Agent phân tích & ánh xạ tiêu đề cột Excel thông minh
├── document_inspector.js     ← Module chuyên dụng soi & cảnh báo các trường thông tin bị thiếu trong phiếu
├── readme_ai.md              ← Tài liệu kỹ thuật chi tiết hệ thống Edge AI Engine
```
