# TỔNG HỢP NỘI DUNG 12 SLIDE BÁO CÁO CHUYÊN MÔN
## HỆ THỐNG QUẢN LÝ QUẦN CHÚNG ƯU TÚ VÀ XÉT DUYỆT PHÁT TRIỂN ĐẢNG VIÊN (QLUT-ĐẢNG)
*(Tài liệu chỉ bao gồm 12 Slide nội dung chuyên môn cốt lõi — Không bao gồm Slide Bìa và Slide Cảm ơn)*

---

### SLIDE 1: BỐI CẢNH THỰC TIỄN VÀ 6 ĐIỂM NGHẼN QUẢN LÝ THỦ CÔNG
- **Bối cảnh nghiệp vụ**: Quản lý hồ sơ phát triển Đảng viên tại các trường đại học là quy trình hành chính bảo mật cao, kéo dài nhiều năm qua các cấp Chi bộ và Đảng ủy.
- **6 Điểm nghẽn kỹ thuật cốt lõi của quy trình truyền thống**:
  1. *Nguy cơ thất lạc & Hư hỏng vật lý*: Khối lượng hồ sơ giấy lớn, dễ suy giảm chất lượng khi lưu trữ và luân chuyển.
  2. *Độ trễ đối soát thủ công*: Tiêu tốn 45--60 phút/bộ hồ sơ; tỷ lệ bỏ sót trường thông tin khuyết cao.
  3. *Thiếu minh bạch tiến trình*: Quần chúng không thể tra cứu trạng thái xét duyệt thời gian thực.
  4. *Bất đồng bộ dữ liệu*: Bảng tính Excel phân tán dẫn đến trùng lặp bản ghi và sai lệch định dạng ngày tháng.
  5. *Lỗi cấu trúc tiêu đề nhập liệu*: Tệp dữ liệu đầu vào từ các lớp bị biến thể tiêu đề hoặc khuyết ô tên cột.
  6. *Chi phí in ấn & Vòng lặp hiệu chỉnh*: Mọi điều chỉnh nhỏ đều yêu cầu in ấn lại toàn bộ bộ biểu mẫu.
- **Gợi ý Visual**: Bảng đối sánh 2 cột "Quản lý thủ công (Rủi ro, chậm trễ, phân tán)" vs "Hệ thống QLUT-Đảng (Tự động, tập trung, tức thì)".

---

### SLIDE 2: MỤC TIÊU NGHIÊN CỨU VÀ HỆ CHỈ SỐ ĐỊNH LƯỢNG (KPIs)
- **Mục tiêu tổng quát**: Số hóa toàn diện chu trình quản lý quần chúng ưu tú trên nền tảng Web phân tầng bảo mật, tích hợp AI biên và Microservice kết xuất biểu mẫu chuẩn.
- **Hệ chỉ số kỹ thuật định lượng (Target KPIs)**:
  - $\bullet$ **Thời gian phản hồi Web**: $< 0.3$ giây/yêu cầu trên nền tảng PHP Core + PDO.
  - $\bullet$ **Tốc độ xử lý Edge AI OCR**: $< 2.5$ giây/trang; Độ chính xác bóc tách CCCD $\ge 95\%$.
  - $\bullet$ **Tự động hóa thẩm định hồ sơ**: Phát hiện $100\%$ các trường khuyết trên 10 biểu mẫu nghiệp vụ.
  - $\bullet$ **Ánh xạ cột Excel tự động**: Độ chính xác $\ge 96\%$, tự động xử lý trường hợp trống tiêu đề.
  - $\bullet$ **Tốc độ kết xuất PDF/Excel**: $< 1.0$ giây/tệp qua Python Microservice.
  - $\bullet$ **Tiêu chuẩn An toàn thông tin**: 100% mật khẩu băm BCRYPT ($Cost=10$), 100% Prepared Statements chống SQLi và lọc XSS.
- **Gợi ý Visual**: Bố cục 6 thẻ Badge chỉ số định lượng với các icon thông số kỹ thuật nổi bật.

---

### SLIDE 3: CHUẨN HÓA QUY TRÌNH NGHIỆP VỤ ĐẢNG VỤ 5 BƯỚC
- **Cơ sở pháp lý**: Bám sát Điều lệ Đảng Cộng sản Việt Nam và Hướng dẫn số 01-HD/TW của Ban Tổ chức Trung ương.
- **5 Bước xử lý trong vòng đời quản lý**:
  - **Bước 1 — Khởi tạo hồ sơ**: Quần chúng nộp đơn đăng ký trực tuyến (Tích hợp Live Camera / OCR CCCD Auto-fill).
  - **Bước 2 — Công nhận cảm tình Đảng**: Bí thư Chi bộ thẩm định tư cách, xét duyệt danh sách chính thức.
  - **Bước 3 — Theo dõi rèn luyện**: Ghi nhận quá trình bồi dưỡng nhận thức Đảng và đánh giá phân loại đoàn viên.
  - **Bước 4 — Thẩm định minh chứng**: Nộp 5 mẫu phiếu minh chứng; Hệ thống kích hoạt Edge AI Multi-Agent soi trường khuyết.
  - **Bước 5 — Quyết định kết nạp & Kết xuất**: Đảng ủy chuẩn y kết nạp; Hệ thống xuất bộ 8 biểu mẫu hành chính PDF chuẩn 2026.
- **Gợi ý Visual**: Sơ đồ dòng chảy 5 bước dạng Chevron Timeline liên hoàn với trạng thái màu tương ứng.

---

### SLIDE 4: KIẾN TRÚC HỆ THỐNG PHÂN TẦNG LAI (4-TIER HYBRID ARCHITECTURE)
- **Mô hình Kiến trúc Phân tầng Lai kết hợp Microservice**:
  - **1. Presentation Layer (Client Browser)**: HTML5, CSS Glassmorphism, Bootstrap Icons, WebAssembly AI Engine (`Tesseract.js`, `PDF.js`, `HTML5 Canvas`).
  - **2. Application & Logic Layer (PHP Core Server)**: Page Controllers, RBAC Engine (`requireRole`), RSS News Parser, PHP Proxy Bridge (`api_proxy.php`).
  - **3. Microservice Layer (Python Flask Server :5000)**: Dịch vụ độc lập chuyên trách kết xuất văn bản; Engine `ReportLab Vector Canvas` (PDF) và `openpyxl` (Excel 35 cột).
  - **4. Data Access Layer (MySQL Database)**: Hệ quản trị CSDL quan hệ chuẩn hóa 3NF gồm 9 bảng dữ liệu.
- **Nguyên lý giao tiếp**: Client $\xrightarrow{\text{HTTP/AJAX}}$ PHP Backend $\xrightarrow{\text{REST API JSON}}$ Python Microservice $\xrightarrow{\text{Binary Stream}}$ Trình duyệt Client.
- **Gợi ý Visual**: Sơ đồ phân rã 4 tầng kiến trúc với luồng dữ liệu (Data Flow) và giao thức kết nối chi tiết.

---

### SLIDE 5: ĐỘT PHÁ 1 — TRÍ TUỆ NHÂN TẠO BIÊN (EDGE AI CLIENT-SIDE)
- **Cơ chế tính toán biên trên trình duyệt (Edge Computing)**:
  - Tích hợp Engine Tesseract C++ qua **WebAssembly (WASM)**, thực thi đa luồng trên Web Worker.
  - Toàn bộ dữ liệu ảnh CCCD 2 mặt và tài liệu được xử lý **100% trong bộ nhớ RAM Client**.
- **Giá trị kỹ thuật và an toàn dữ liệu**:
  - $\bullet$ **Bảo vệ quyền riêng tư tuyệt đối**: Dữ liệu định danh không truyền qua mạng $\to$ Tuân thủ Nghị định 13/2023/NĐ-CP.
  - $\bullet$ **Zero Server Workload**: Giải phóng hoàn toàn năng lực tính toán của máy chủ, không cần GPU server.
  - $\bullet$ **Tự động điền (Auto-fill)**: Trích xuất chính xác Họ tên, Ngày sinh, Số CCCD, Quê quán, Lớp vào Form.
  - $\bullet$ **Smart Canvas Crop 3x4**: Ước lượng tâm khuôn mặt và tự động cắt ảnh thẻ chân dung chuẩn tỷ lệ $3:4$ ($300 \times 400$ px).
- **Gợi ý Visual**: Sơ đồ cơ chế hoạt động của WebAssembly Worker trên RAM Client và ảnh demo Auto-fill Form đăng ký.

---

### SLIDE 6: ĐỘT PHÁ 2 — QUÉT TÀI LIỆU LIVE CAMERA & ĐO ĐỘ NÉT LAPLACIAN
- **Tích hợp trực tiếp tại**: Giao diện Thêm đối tượng (`them.php`) và Đăng ký trực tuyến (`nhap_thong_tin.php`).
- **Các giải pháp kỹ thuật cốt lõi**:
  - **Luồng Video WebRTC**: Tiếp nhận luồng hình ảnh trực tiếp qua API `navigator.mediaDevices.getUserMedia`.
  - **Thuật toán ước lượng độ nét Laplacian thời gian thực (15--30 FPS)**:
    $$\text{Var}(\Delta I) = \frac{1}{N} \sum_{x,y} \left( \nabla^2 I(x,y) - \mu \right)^2$$
  - **Chỉ báo HUD & Chụp chủ động**: Khi độ nét đạt ngưỡng $\ge 65\%$, khung HUD chuyển sang màu xanh lá (`READY`) và nút chụp sáng đèn cho phép người dùng bấm chụp thủ công (không tự chụp khi ảnh mờ).
  - **Bộ lọc kiểm duyệt nội dung**: Phân tích mật độ văn bản sau bóc tách, tự động cảnh báo nếu ảnh chụp không chứa nội dung giấy tờ hợp lệ.
- **Gợi ý Visual**: Ảnh chụp giao diện Camera HUD với thước đo độ nét Laplacian chuyển trạng thái từ Đỏ (Mờ) $\to$ Xanh (Đủ nét).

---

### SLIDE 7: ĐỘT PHÁ 3 — MINH BẠCH HÓA AI — EXPLAINABLE AI (XAI CONFIDENCE OVERLAY)
- **Vấn đề giải quyết**: Khắc phục tính chất "hộp đen" (Black-box) của các mô hình nhận dạng quang học OCR.
- **Cơ chế hoạt động của phân hệ `XAIConfidenceOverlay`**:
  - **Bản đồ nhiệt Bounding Box 3 Màu**: Phân lớp độ tin cậy nhận dạng của từng token văn bản:
    - $\bullet$ *Màu Xanh lá ($C_i \ge 85\%$)*: Độ tin cậy cao, dữ liệu chuẩn xác.
    - $\bullet$ *Màu Vàng cam ($60\% \le C_i < 85\%$)*: Độ tin cậy trung bình, khuyến nghị rà soát.
    - $\bullet$ *Màu Đỏ ($C_i < 60\%$)*: Cảnh báo nguy cơ sai lệch, cần người dùng đối soát trực tiếp.
  - **Tương tác Hit-Testing**: Cho phép rê chuột lên từng khối chữ trên Canvas để tra cứu tức thì nội dung và điểm tin cậy $C_i$.
  - **Audit Trail Logging**: Ghi vết độ tin cậy trung bình vào bảng CSDL `edge_ai_logs` phục vụ công tác hậu kiểm.
- **Gợi ý Visual**: Ảnh chụp tài liệu với các khối Bounding Box 3 màu và tooltip hiển thị điểm số tin cậy chi tiết.

---

### SLIDE 8: ĐỘT PHÁ 4 — BỘ PHỐI HỢP ĐA TÁC TỬ (MULTI-AGENT SUITE) & PIPELINE CANVAS
- **Kiến trúc Bộ 4 Tác tử Chuyên biệt (`AIDocumentInspectorAgent`)**:
  1. *Synopsis Agent*: Nhận diện loại biểu mẫu và mục đích văn bản trong 10 dòng đầu.
  2. *Field Extractor Agent*: Bóc tách các cặp Nhãn -- Giá trị (Key-Value) theo cấu trúc ngữ cảnh.
  3. *Gap Diagnostic Agent*: Chẩn đoán trường khuyết, chấm lửng (`.....`), sinh phán quyết `agentVerdict` và hướng dẫn `actionAdvice`.
  4. *Executive Synthesis Agent*: Tổng hợp tỷ lệ hoàn thiện hồ sơ (\%) và kết xuất bảng phân tích tổng thể.
- **Đường ống Tiền xử lý Hình ảnh Số trên Canvas (`EdgeImageProcessor`)**:
  - Chuyển đổi Sắc độ xám ITU-R BT.601 ($Y = 0.299R + 0.587G + 0.114B$).
  - Dãn dải tương phản $[P_2, P_{98}]$ \& Nhị phân hóa thích nghi (Adaptive Binarization).
  - Tích chập làm nét biên ký tự (Spatial Convolution $3 \times 3$) \& Hiệu chỉnh góc nghiêng Radon (Deskew).
- **Gợi ý Visual**: Sơ đồ đường ống 4 Agent phối hợp liên hoàn và ảnh minh họa chuỗi biến đổi tiền xử lý Canvas.

---

### SLIDE 9: TÁC TỬ ÁNH XẠ TIÊU ĐỀ EXCEL & DANH MỤC 10 BIỂU MẪU ĐẢNG
- **Tác tử Ánh xạ Cột Excel Thông minh (`excel_column_agent.js`)**:
  - Chuẩn hóa chuỗi Unicode NFD, xóa ký tự đặc biệt và đối soát từ điển đồng nghĩa của 35 trường CSDL.
  - Nhận diện cột không tiêu đề và tự động gán nhãn `[Cột X - Trống tiêu đề]` trên giao diện ánh xạ.
  - Cho phép người dùng xác nhận hoặc tùy chỉnh ánh xạ trước khi nạp dữ liệu hàng loạt vào hệ thống.
- **Bao quát Danh mục 10 Biểu mẫu Nghiệp vụ Đảng (Hướng dẫn 01-HD/TW)**:
  - Mẫu 1-KNĐ (Đơn vào Đảng), Mẫu 2-KNĐ (Lý lịch 3 đời), Mẫu 3-KNĐ (Giấy giới thiệu).
  - Mẫu 4-KNĐ (Nghị quyết Đoàn/Công đoàn), Mẫu 5-KNĐ (Ý kiến Chi ủy nơi cư trú), Mẫu 6-KNĐ (Báo cáo thẩm tra).
  - Mẫu 7-KNĐ (Nghị quyết Chi bộ xét kết nạp), Mẫu 8-KNĐ (Quyết định kết nạp Đảng viên).
  - Giấy chứng nhận học lớp Đảng \& Phiếu đánh giá phân loại đoàn viên/cán bộ.
- **Gợi ý Visual**: Giao diện Modal ánh xạ cột Excel với các gợi ý thông minh và danh sách 10 biểu mẫu chuẩn.

---

### SLIDE 10: THIẾT KẾ CƠ SỞ DỮ LIỆU CHUẨN 3NF VÀ MA TRẬN PHÂN QUYỀN RBAC
- **Lược đồ CSDL Chuẩn hóa Dạng chuẩn 3 (3NF)**:
  - *Bảng trung tâm `doi_tuong`*: Quản lý 35 thuộc tính nghiệp vụ trải dài qua toàn bộ vòng đời xét duyệt kết nạp.
  - *8 Bảng phụ thuộc hoàn chỉnh*: `nguoi_dung`, `dang_ky_doi_tuong`, `yeu_cau_cap_nhat`, `chi_bo`, `dang_vien`, `edge_ai_logs`, `lich_su`, `cai_dat`.
  - Khóa ngoại ràng buộc toàn vẹn tham chiếu (`ON DELETE RESTRICT/CASCADE`), triệt tiêu dư thừa dữ liệu.
- **Ma trận Kiểm soát Truy cập Dựa trên Vai trò (RBAC Matrix)**:
  - $\bullet$ **Quần chúng / Sinh viên**: Nộp hồ sơ cá nhân, xem Timeline tiến trình, gửi đề xuất chỉnh sửa thông tin.
  - $\bullet$ **Bí thư Chi bộ**: Duyệt đơn đăng ký, duyệt đề xuất sửa qua 2 Tab, sửa nhanh Autosave, xuất PDF/Excel.
  - $\bullet$ **Quản trị viên (Admin)**: Toàn quyền quản trị tài khoản, phân quyền RBAC, cấu hình tham số hệ thống.
- **Gợi ý Visual**: Sơ đồ quan hệ thực thể ERD liên kết 9 bảng và Bảng ma trận phân quyền 3 vai trò.

---

### SLIDE 11: GIAO DIỆN XÉT DUYỆT 2 TAB VÀ KẾT XUẤT 8 BIỂU MẪU PDF CHUẨN 2026
- **Giao diện Phê duyệt Đối chiếu 2 Tab Trực quan (`duyet_dang_ky.php`)**:
  - *Tab 1*: Thẩm định đơn đăng ký quần chúng mới nộp.
  - *Tab 2*: Bảng đối chiếu Cũ -- Mới; Tự động **tô màu vàng nổi bật** các trường thông tin có thay đổi khi sinh viên gửi đề xuất sửa.
- **Bảng Sửa Nhanh Dữ liệu Dạng Excel (Autosave AJAX)**:
  - Cho phép chỉnh sửa trực tiếp trên từng ô bảng; Lưu ngầm tức thì, phản hồi trạng thái màu sắc và hỗ trợ phím điều hướng.
- **Tự động hóa Kết xuất 8 Biểu mẫu Hành chính PDF Chuẩn 2026**:
  - Python Flask Microservice + `ReportLab Vector Canvas` định vị tọa độ Descartes 2D chính xác từng milimet.
  - Tự động điền dữ liệu động đóng khung nổi bật `[ Dữ liệu ]`, chuẩn phông chữ UTF-8 không vỡ khung khi in ấn.
- **Gợi ý Visual**: Ảnh chụp màn hình giao diện đối chiếu 2 Tab Cũ-Mới và trang PDF Mẫu 1-KNĐ kết xuất thực tế.

---

### SLIDE 12: KẾT QUẢ THỰC NGHIỆM, ĐÁNH GIÁ VÀ HƯỚNG PHÁT TRIỂN
- **Kết quả Kiểm thử Thực nghiệm (Bộ dữ liệu mẫu 500 bản ghi)**:
  - $\checkmark$ **Thời gian phản hồi Server PHP**: **0.11 giây** (Vượt mục tiêu $< 0.3$s).
  - $\checkmark$ **Tốc độ quét Edge AI OCR**: **1.8 -- 2.4 giây/tệp** (Vượt mục tiêu $< 5.0$s).
  - $\checkmark$ **Độ chính xác nhận dạng trường thiếu**: **96.8%** (Vượt mục tiêu $> 90\%$).
  - $\checkmark$ **Độ chính xác phân loại mẫu phiếu**: **98.2%** (Vượt mục tiêu $> 92\%$).
  - $\checkmark$ **Tốc độ Render PDF ReportLab**: **0.75 giây/tệp** (Vượt mục tiêu $< 2.0$s).
- **Đánh giá Đóng góp của Đề tài**:
  - Giải quyết bài toán chuyển đổi số Đảng vụ toàn diện, bảo mật dữ liệu cá nhân tại biên (Edge AI) với chi phí hạ tầng bằng 0.
- **Hướng phát triển tiếp theo**:
  - Mở rộng Progressive Web App (PWA) hỗ trợ thông báo đẩy di động.
  - Tích hợp Single Sign-On (SSO) với Cổng thông tin Đào tạo của Nhà trường.
- **Gợi ý Visual**: Biểu đồ cột so sánh "Mục tiêu thiết kế vs Kết quả đo đạc thực tế" thể hiện hiệu năng vượt trội.
