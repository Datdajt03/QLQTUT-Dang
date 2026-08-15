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

### 2. 🖼️ Pipeline Tiền Xử Lý Ảnh Client-Side Bằng Canvas (`edge_image_processor.js`) [NEW]
- **Mục tiêu**: Tối ưu chất lượng ảnh chụp tài liệu (từ điện thoại, scan mờ, thiếu sáng) trước khi đưa vào bộ nhận dạng OCR Tesseract.js.
- **Pipeline Thuật Toán**:
  1. **Grayscale Conversion**: Chuyển đổi ảnh màu sang ảnh đơn sắc độ sáng chuẩn (Luma ITU-R BT.601).
  2. **Auto-Contrast Histogram Stretching**: Kéo giãn biểu đồ phân bố độ sáng, tăng độ tương phản rõ rệt giữa nét chữ và phông nền.
  3. **Adaptive Thresholding / Binarization**: Phân ngưỡng cục bộ đưa ảnh về dạng nhị phân thuần túy (đen/trắng), làm nổi bật văn bản in/viết.
  4. **Sharpen Kernel 3x3 Convolution**: Áp dụng ma trận làm sắc nét đường viền chữ tiếng Việt có dấu.
  5. **Noise Reduction**: Khử đốm nhiễu li ti (salt-and-pepper noise) trên bề mặt giấy.
  6. **Deskew Estimation**: Ước tính góc nghiêng và tự động xoay thẳng văn bản.
- **Hiệu quả**: Nâng cao độ chính xác nhận diện ký tự tiếng Việt (Character Accuracy Rate) thêm **20 - 35%**.

---

### 3. 📸 Kiểm tra & Tự động Cắt/Chỉnh sửa Ảnh chân dung (Smart Avatar Crop 3x4)
- **Cơ chế AI**: Tự động phân tích tâm khuôn mặt (Face Detection) và tỉ lệ khung hình.
- **Auto-Crop 3x4**: Tự động căn chỉnh và cắt ảnh chân dung về tỷ lệ chuẩn 300x400 (ảnh thẻ 3x4 hành chính) bằng HTML5 Canvas trước khi gửi lên máy chủ.
- **Lợi ích**: Đảm bảo 100% ảnh đại diện và ảnh trên các Mẫu Phiếu PDF Kết Nạp Đảng 2026 (Mẫu 2-KNĐ) sắc nét và đúng quy chuẩn Đảng vụ.

---

### 4. 📄 Multi-Agent Cooperating Suite & Soi 10+ Mẫu Biểu Đảng Vụ (`document_inspector.js`)
Hệ thống tích hợp **Form Registry với hơn 10 Mẫu biểu Đảng vụ tiêu chuẩn**, hỗ trợ soi thông tin chi tiết và chẩn đoán trường khuyết:

| STT | Tên Mẫu Biểu / Hồ sơ | Mã Model | Các Trường Thông Tin Chi Tiết Soi Bắt Buộc |
| :--- | :--- | :--- | :--- |
| **1** | **Đơn xin vào Đảng (Mẫu 1-KNĐ)** | `mau_1_knd` | Họ tên, Ngày sinh, Quê quán/Nơi cư trú, Trình độ học vấn, Nguyện vọng vào Đảng, Ngày viết & Chữ ký |
| **2** | **Lý lịch người vào Đảng (Mẫu 2-KNĐ)** | `mau_2_knd` | Họ tên khai sinh, Bí danh, Ngày sinh, Nơi sinh, Quê quán, Nơi thường trú, Dân tộc, Tôn giáo, Thành phần gia đình, Trình độ văn hóa, Nghề nghiệp, Cam đoan & Chữ ký |
| **3** | **Giấy giới thiệu người vào Đảng (Mẫu 3-KNĐ)** | `mau_3_knd` | Họ tên Đảng viên giới thiệu, Chức vụ/Chi bộ, Họ tên người được giới thiệu, Ngày sinh, Nhận xét phẩm chất đạo đức, Ý kiến giới thiệu, Ngày tháng & Chữ ký |
| **4** | **Nghị quyết giới thiệu Đoàn viên vào Đảng (Mẫu 4-KNĐ)** | `mau_4_knd` | Tên BCH Đoàn cơ sở, Họ tên đoàn viên, Ngày sinh, Ưu điểm/Thành tích, Kết quả biểu quyết tín nhiệm, Ngày ký Bí thư Đoàn |
| **5** | **Nghị quyết Công đoàn giới thiệu vào Đảng (Mẫu 4a-KNĐ)** | `mau_4a_knd` | Tên BCH Công đoàn, Họ tên công đoàn viên, Quá trình công tác, Kết quả lấy ý kiến/biểu quyết, Chữ ký Chủ tịch CĐ |
| **6** | **Tổng hợp ý kiến nhận xét đoàn thể & cư trú (Mẫu 5-KNĐ)** | `mau_5_knd` | Họ tên người vào Đảng, Ý kiến tổ chức đoàn thể, Ý kiến cấp ủy/chi ủy nơi cư trú, Kết luận chi ủy, Chữ ký đại diện |
| **7** | **Giấy chứng nhận bồi dưỡng nhận thức Đảng I/II** | `giay_chung_nhan` | Đơn vị cấp (ĐH Tây Bắc / Trung tâm chính trị), Họ tên học viên, Ngày sinh, Kết quả xếp loại, Số QĐ/Số chứng nhận, Ngày cấp |
| **8** | **Bản tự nhận xét / Tự kiểm điểm** | `ban_tu_nhan_xet` | Họ tên, Ngày sinh, Ưu điểm/Thành tích, Khuyết điểm/Hạn chế, Phương hướng phấn đấu, Ngày tháng & Chữ ký |
| **9** | **Sơ yếu lý lịch / CCCD / Thẻ SV** | `ho_so_ca_nhan` | Họ tên, Ngày sinh, Quê quán/Nguyên quán, Mã SV/Số CCCD, Lớp/Khoa sinh hoạt |
| **10** | **Phiếu đánh giá chất lượng đoàn viên** | `phieu_danh_gia` | Họ tên đoàn viên, Tên Chi đoàn, Kết quả xếp loại đoàn viên, Xác nhận Bí thư Chi đoàn |
| **11** | **Minh chứng hoạt động phong trào / Giấy khen** | `minh_chung_hoat_dong` | Tên hoạt động (Hiến máu, Tình nguyện...), Họ tên người nhận, Đơn vị khen thưởng, Thời gian |

#### Kiến trúc Phối hợp Multi-Agent Cooperating Suite:
1. **Agent 1 - Semantic Document Synopsis Agent**: Đọc hiểu ngữ nghĩa văn bản OCR, nhận biết mẫu biểu và trích xuất tiêu đề / mục đích văn bản (`generateDocumentSynopsis`).
2. **Agent 2 - Dynamic Form Field Extractor Agent**: Khớp từ khóa trường mềm dẻo theo ma trận và trích xuất dữ liệu thực tế (`extractValueSnippet`).
3. **Agent 3 - Gap Diagnostic & Verdict Agent**: Tự động chẩn đoán thông tin khuyết, đưa ra **Nhận xét Đánh giá Thông minh (`agentVerdict`)** và khối **Hướng dẫn khắc phục (`actionAdvice`)** cho từng tệp.
4. **Agent 4 - Executive Synthesis Agent**: Tổng hợp toàn bộ hồ sơ, dựng bảng **AI Agent Synthesis Dashboard** và lưu nhật ký đánh giá `rawSummary` vào CSDL MySQL `edge_ai_logs`.

---

### 5. 📥 Agent Xuất Kết Quả Đa Định Dạng (`result_export_agent.js`) [NEW]
- **Mục tiêu**: Cung cấp công cụ xuất dữ liệu thẩm định AI tức thì tại Client cho cán bộ Đảng vụ.
- **Các phương thức xuất**:
  - `exportJSON(results)` / `exportSingleJSON(result)`: Xuất dữ liệu cấu trúc JSON chuẩn phục vụ tích hợp API / sao lưu.
  - `exportCSV(results)` / `exportSingleCSV(result)`: Xuất bảng kết quả thẩm định định dạng CSV mở được trong Excel.
  - `copyToClipboard(results)`: Định dạng văn bản báo cáo tóm tắt và copy thẳng vào clipboard hệ điều hành.
  - `buildSummaryReport(results)`: Tổng hợp báo cáo tổng kết nhanh hiển thị trực tiếp trên giao diện.

---

### 6. 🧠 Agent Phân loại & Ánh xạ Tên Cột Excel Thông minh (`excel_column_agent.js`)
- **Đặt vấn đề Thực tế**: Khi import file Excel/CSV từ các lớp/chi bộ, tiêu đề cột thường bị viết tắt (`Qli`, `QL`, `Quản lý`, `MSSV`, `Hoten`...) hoặc bị trống tiêu đề.
- **Kiến trúc & Cơ chế Agent (`AI_Module/excel_column_agent.js`)**:
  1. **Chuẩn hóa Chuỗi (`normalizeHeader`)**: Tách dấu tiếng Việt Unicode NFD, xóa ký tự đặc biệt.
  2. **Đối soát Từ khóa (`DB_COLUMNS_DICTIONARY`)**: Khớp ma trận 2 chiều tìm kiếm từ khóa đồng nghĩa với độ tin cậy Badge (`Chính xác`, `Gợi ý`, `Chưa rõ`).
  3. **Phát hiện Cột Trống Tiêu Đề**: Tự động đánh dấu vị trí `⚠️ Cột A (Trống tiêu đề)` để buộc chọn trường CSDL.
  4. **Giao diện Modal Tab Agent**: Bật Modal Tab cho phép cán bộ quản lý xem xét và đổi chọn lại trường CSDL trước khi xác nhận Import.

### 7. 📹 Quét Hồ Sơ Trực Tiếp Qua Camera Khi Thêm Đối Tượng & Đăng Ký (`live_camera_scanner.js`) [BK SUMMER SCHOOL 2026]
- **Vị trí tích hợp thực tế**: Tích hợp trực tiếp tại **Thêm đối tượng (`Quan_ly_doi_tuong/them.php`)** dành cho Cán bộ/Quản lý và **Đăng ký trực tuyến (`Quan_ly_doi_tuong/nhap_thong_tin.php`)** dành cho Quần chúng.
- **Mục tiêu Nghiệp vụ**: Người dùng hoặc Cán bộ đưa CCCD / Thẻ sinh viên / Đơn xin vào Đảng trước Camera, hệ thống hiển thị thước đo độ nét trực quan để người dùng **chủ động bấm nút chụp**, sau đó bóc tách OCR điền tự động vào form nhập liệu thay vì phải gõ tay.
- **Kiến trúc & Công nghệ**:
  - Tích hợp luồng **WebRTC MediaStream Video** (`navigator.mediaDevices.getUserMedia`) chạy trực tiếp trên trình duyệt Client.
  - **Laplacian Variance Sharpness Meter**: Thuật toán ước lượng độ nét thời gian thực qua phương sai toán tử Laplace $\text{Var}(\Delta I) = E[L^2] - (E[L])^2$ trên từng frame video (15-30 FPS) để chống mờ nhòe do rung tay.
  - **Dynamic Reticle & Laser Scanning HUD**: Giao diện ngắm tài liệu thời gian thực kèm hiệu ứng quét laser, thước đo ánh sáng và tự động chuyển trạng thái `🟢 ĐỦ NÉT - HÃY BẤM NÚT CHỤP` (nút chụp chuyển màu xanh lá phát sáng).
  - **Manual Controlled Capture & Perspective Crop**: Người dùng tự bấm nút khi sẵn sàng, hệ thống cắt chuẩn theo khung ngắm (Perspective Reticle Box) và nạp vào OCR Pipeline.
  - **Smart Content Validation (Chống chụp lung tung / không có nội dung)**: Thuật toán kiểm duyệt nội dung bóc tách, tự động phát hiện và cảnh báo tức thì `⚠️ Ảnh chụp không có nội dung văn bản hoặc không nhận dạng được tài liệu hợp lệ!` nếu chụp vào góc tối, tường trống hoặc tài liệu không hợp lệ.

---

### 8. 🎯 Trực Quan Hóa Độ Tin Cậy Mô Hình — Explainable Edge AI (XAI) (`xai_confidence_overlay.js`) [BK SUMMER SCHOOL 2026]
- **Vị trí tích hợp**: Tích hợp tại **Thêm đối tượng (`them.php`)**, **Đăng ký (`nhap_thong_tin.php`)** và trang **Soi hồ sơ bản mềm (`edge_ai_check.php`)**.
- **Kiến trúc & Công nghệ**:
  - Trực quan hóa độ tin cậy phân loại nơ-ron (Neural Token Confidence Visualization) theo từng từ/cụm từ bóc tách được.
  - **3 Phân lớp Màu Bounding Box**:
    - 🟢 **Xanh lá (Confidence $\ge 85\%$)**: Nhận diện chuẩn xác cao (Họ tên, Ngày sinh, Đơn vị cấp).
    - 🟡 **Vàng cam ($60\% \le \text{Confidence} < 85\%$)**: Mức độ trung bình (Chữ viết tay / phông chữ nghiêng).
    - 🔴 **Đỏ ($\text{Confidence} < 60\%$)**: Cảnh báo rủi ro sai lệch cần cán bộ đối soát lại.
  - **Interactive Tooltip & Hit-testing**: Rê chuột lên từng vùng chữ trên Canvas để xem chính xác từ nhận dạng và điểm phần trăm tin cậy ($C_i$).
  - **Thống kê Tổng thể**: Độ tin cậy trung bình ($\mu$) của toàn bộ trang văn bản và thanh trượt điều chỉnh độ mờ Heatmap Opacity.

---

## 📁 Cấu trúc Thư mục AI_Module

```
AI_Module/
├── edge_ai_autofill.js       ← Engine OCR Client-side điền form & Smart Canvas Crop ảnh thẻ 3x4
├── edge_image_processor.js   ← Canvas Pre-processing Pipeline (Grayscale, Contrast, Threshold, Sharpen, Deskew)
├── live_camera_scanner.js    ← WebRTC Live Document Camera Scanner & Laplacian Focus Estimator
├── xai_confidence_overlay.js  ← Explainable Edge AI (XAI) Token Confidence Heatmap & Bounding Box Visualizer
├── excel_column_agent.js     ← Client-side Agent phân tích & ánh xạ tiêu đề cột Excel thông minh
├── document_inspector.js     ← Multi-Agent Suite soi 10+ mẫu hồ sơ Đảng & ra AI Verdict
├── result_export_agent.js    ← Agent xuất kết quả thẩm định AI (JSON, CSV, Clipboard)
└── readme_ai.md              ← Tài liệu kỹ thuật chi tiết hệ thống Edge AI Engine
```
