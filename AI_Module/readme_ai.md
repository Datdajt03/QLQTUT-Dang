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

### 4. 🧠 Agent Phân loại & Ánh xạ Tên cột Excel Thông minh (Excel Column Mapper Agent)
- **Đặt vấn đề Thực tế**: Trong thực tế quản lý hồ sơ, các file Excel/CSV được tải lên từ nhiều chi bộ/lớp khác nhau thường có tên tiêu đề cột ghi tắt hoặc không đồng nhất (ví dụ: có người ghi `Qli`, `QL`, `Quản lý`, `Bán cán sự`, `MSSV`, `Hoten`, `Mã SV`...). Nếu chỉ dùng hàm import tĩnh theo thứ tự cột truyền thống, dữ liệu sẽ bị lệch dòng hoặc báo lỗi CSDL.
- **Kiến trúc & Cơ chế Hoạt động của Agent (`AI_Module/excel_column_agent.js`)**:
  - **Không tốn chi phí gọi API bên ngoài (Client-Side Intelligence)**: Agent chạy trực tiếp trên trình duyệt web của người dùng thông qua JavaScript ES6 module, đảm bảo tốc độ phản hồi tức thì và không lộ dữ liệu cá nhân ra máy chủ bên thứ 3.
  - **Thuật toán Chuẩn hóa Chuỗi (Normalization Pipeline)**: 
    1. Khi file `.xlsx` / `.csv` được chọn/kéo thả tại trang `Thong_ke_bao_cao/import_excel.php`, trình duyệt đọc trực tiếp dòng Header (dòng 1) qua thư viện `SheetJS (XLSX.js)`.
    2. Hàm `normalizeHeader()` tự động tách bỏ toàn bộ dấu tiếng Việt (`NFD Unicode`), chuyển chữ hoa thành chữ thường, xóa các ký tự đặc biệt để biến các tiêu đề lộn xộn thành chuỗi mã hóa sạch:
       - `'Qli'` ➔ `'qli'`
       - `'Chức Vụ'` ➔ `'chuc vu'`
       - `'Mã số Sinh Viên'` ➔ `'ma so sinh vien'`
  - **Thuật toán Đối soát Từ khóa & Điểm Tin cậy (Keyword Dictionary & Confidence Scoring)**:
    - Agent sở hữu **Từ điển Từ khóa Đồng nghĩa CSDL (`DB_COLUMNS_DICTIONARY`)** với bộ từ khóa phong phú cho toàn bộ 33 trường dữ liệu. Ví dụ đối với trường `chuc_vu`:
      ```javascript
      { field: 'chuc_vu', label: 'Chức vụ', keywords: ['chức vụ', 'chuc_vu', 'quản lý', 'qli', 'ql', 'chức danh', 'vị trí', 'bán cán sự'] }
      ```
    - Hàm `matchExcelColumn()` thực hiện tìm kiếm ma trận 2 chiều:
      - **Khớp tuyệt đối (Exact Match - Confidence 1.0 / Green Badge)**: Khi chuỗi tiêu đề trùng khớp hoàn toàn với một trong các từ khóa đồng nghĩa.
      - **Khớp mờ / Chứa chuỗi (Sub-string Search - Confidence 0.8 / Orange Badge)**: Khi tiêu đề chứa hoặc nằm trong cụm từ khóa đồng nghĩa.
      - **Cột Trống Tiêu Đề (Blank / Empty Header Cell - Red Warning Badge)**: Trường hợp cột trong file Excel bị để trống tiêu đề (ví dụ cột `A`, `B`, `C` không có tên), Agent tự động phát hiện, gán tên vị trí `⚠️ Cột A (Trống tiêu đề)` và tô đỏ dòng đó trên Modal để buộc/gợi ý người quản lý chọn trường CSDL mong muốn trước khi bấm Import.
  - **Quy trình Phản xạ & Giao diện Modal Tab AI Agent**:
    - Khi đọc xong file Excel, Agent ngay lập tức ngăn quá trình submit mặc định và **bật 1 Modal Tab AI Agent** đẹp mắt.
    - **Cột bên trái**: Hiển thị tên cột gốc đọc được từ file Excel (ví dụ `Qli` hoặc `⚠️ Cột B (Trống tiêu đề)`).
    - **Thẻ Select Dropdown ở giữa**: Tự động chọn sẵn trường CSDL tương ứng (`Chức vụ - chuc_vu`) do Agent vừa đoán (hoặc để trống nếu cột bị khuyết tên). Cho phép cán bộ quản lý bấm chọn/thay đổi lại bất kỳ trường CSDL nào mong muốn.
    - **Cột bên phải**: Đóng dấu Badge màu sắc minh họa cho độ tin cậy của Agent (`Chính xác`, `Gợi ý`, `Chưa rõ`, `Cột Trống (Cần chọn)`).
    - Người dùng xem xét, điều chỉnh nếu cần rồi bấm **"✅ Xác nhận & Tiến hành Import"** để đưa dữ liệu vào MySQL chính xác 100%.

---

## 📁 Cấu trúc Thư mục AI_Module
- `AI_Module/edge_ai_autofill.js`: Script xử lý OCR Auto-Fill & Smart Canvas Avatar Crop.
- `AI_Module/excel_column_agent.js`: AI Agent phân loại tên cột Excel & gợi ý ánh xạ trường CSDL.
- `AI_Module/readme_ai.md`: Tài liệu hướng dẫn sử dụng & phân tích kiến trúc AI_Module.
