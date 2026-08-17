# TỔNG HỢP NỘI DUNG 15 SLIDE BÁO CÁO CHUYÊN MÔN
## HỆ THỐNG QUẢN LÝ QUẦN CHÚNG ƯU TÚ VÀ XÉT DUYỆT PHÁT TRIỂN ĐẢNG VIÊN (QLUT-ĐẢNG)
*(15 Slide chuyên môn toàn diện — Kiến trúc chi tiết đặt ngay sau Tổng quan; Bổ sung Slide Đánh giá Hiệu năng Benchmark \& Giá trị thực tiễn)*

---

### 1. 6 ĐIỂM NGHẼN THỰC TẾ VÀ MỤC TIÊU SỐ HÓA HỆ THỐNG
- **6 Điểm nghẽn lớn của quy trình quản lý thủ công truyền thống**:
  1. *Thất lạc \& Hư hỏng*: Hồ sơ giấy tờ tích lũy qua nhiều năm dễ ẩm mốc, rách hỏng và thất lạc trong quá trình luân chuyển giữa các cấp Chi bộ và Đảng ủy.
  2. *Chậm trễ đối soát*: Cán bộ Đảng vụ mất 45--60 phút để kiểm tra thủ công một bộ hồ sơ, rất dễ bỏ sót các lỗi thiếu chữ ký, thiếu con dấu hoặc khuyết thông tin.
  3. *Thiếu minh bạch*: Quần chúng sinh viên không nắm bắt được hồ sơ của mình đang ở giai đoạn nào trong 5 bước, gây bị động và chậm tiến độ kết nạp.
  4. *Phân tán dữ liệu*: Việc lưu trữ danh sách bằng các file Excel rời rạc gây trùng lặp, không đồng bộ dữ liệu và sai lệch thông tin cá nhân.
  5. *Lỗi nhập liệu Excel*: Tệp Excel nộp từ các Chi bộ thường bị lệch cột, dùng nhiều cách viết tắt khác nhau hoặc khuyết tiêu đề cột, tốn nhiều công sức chỉnh sửa.
  6. *Lãng phí in ấn*: Chỉ cần sai sót một thông tin nhỏ phải in lại toàn bộ bộ biểu mẫu nhiều trang, gây lãng phí lớn về thời gian và kinh phí.
- **Mục tiêu đột phá của Hệ thống QLUT-Đảng**:
  - *Số hóa 100% vòng đời phát triển Đảng*: Quản lý tập trung từ khâu nộp đơn trực tuyến, theo dõi rèn luyện, thẩm định minh chứng đến ra quyết định kết nạp.
  - *Ứng dụng Edge AI tại Client*: Xử lý trực tiếp trên trình duyệt máy người dùng, tiết kiệm 100% chi phí đầu tư và thuê máy chủ AI GPU đắt đỏ.
  - *Hiệu năng thiết kế vượt trội*: Tốc độ tải trang web dưới 0.3 giây, quét nhận dạng tài liệu dưới 2.5 giây, kết xuất tệp PDF/Excel dưới 1.0 giây.
- **Gợi ý Visual**: Bảng đối sánh 2 cột "6 Khó khăn thủ công" vs "Giải pháp tự động QLUT-Đảng" kèm 3 Badge KPI.

---

### 2. QUY TRÌNH NGHIỆP VỤ 5 BƯỚC VÀ PHÂN QUYỀN RBAC 3 VAI TRÒ
- **Quy trình chuẩn hóa 5 bước (Bám sát Hướng dẫn 01-HD/TW của Ban Tổ chức Trung ương)**:
  - **Bước 1 — Nộp đơn đăng ký**: Sinh viên điền form trực tuyến; hệ thống tích hợp AI quét ảnh CCCD để tự động trích xuất và điền 100% dữ liệu.
  - **Bước 2 — Công nhận cảm tình Đảng**: Bí thư Chi bộ thẩm định phẩm chất, tiêu chuẩn và duyệt danh sách quần chúng ưu tú chính thức.
  - **Bước 3 — Theo dõi rèn luyện**: Ghi nhận kết quả học tập các lớp bồi dưỡng nhận thức về Đảng (Lớp 1 \& Lớp 2) và điểm rèn luyện, xếp loại đoàn viên hàng kỳ.
  - **Bước 4 — Thẩm định minh chứng**: Quần chúng nộp các biểu mẫu minh chứng; hệ thống Đa tác tử AI tự động quét kiểm tra và bắt lỗi thiếu thông tin.
  - **Bước 5 — Quyết định kết nạp \& Xuất hồ sơ**: Đảng ủy họp xét duyệt, hệ thống tự động kết xuất trọn bộ 8 biểu mẫu PDF in ấn A4 và file Excel báo cáo 35 cột.
- **Ma trận phân quyền chặt chẽ 3 nhóm người dùng (Role-Based Access Control - RBAC)**:
  - • *1. Sinh viên / Quần chúng*: Nộp hồ sơ đăng ký, theo dõi tiến trình cá nhân qua Timeline 5 bước, gửi yêu cầu đề xuất chỉnh sửa thông tin cá nhân.
  - • *2. Quản lý (Bí thư Chi bộ / Cán bộ Đảng vụ)*: Duyệt đơn đăng ký, đối chiếu sửa đổi qua 2 Tab Cũ-Mới, sửa nhanh danh sách bằng bảng Autosave, xuất báo cáo và 8 biểu mẫu PDF.
  - • *3. Quản trị viên (Admin)*: Quản lý danh sách tài khoản, phân quyền vai trò, đặt lại mật khẩu, cấu hình thông tin Đảng bộ và an ninh bảo mật.
- **Nguyên tắc an toàn**: AI chỉ đóng vai trò trợ lý phát hiện lỗi và đưa ra khuyến nghị; quyền phê duyệt cuối cùng hoàn toàn thuộc thẩm quyền của cán bộ Đảng vụ.
- **Gợi ý Visual**: Sơ đồ Timeline 5 bước liên hoàn kết hợp bảng phân quyền 3 vai trò RBAC trực quan.

---

### 3. KIẾN TRÚC HỆ THỐNG PHÂN TẦNG LAI VÀ 3 TRỤ CỘT CÔNG NGHỆ
- **Mô hình kiến trúc 4 tầng phân lập (Multi-Tier Hybrid Architecture)**:
  - **1. Presentation Layer (Client Browser)**: HTML5, CSS Glassmorphism Dark Mode, WebAssembly AI Engine (`Tesseract.js`, `Canvas API`, `WebRTC Camera`).
  - **2. Application Layer (PHP Core Server)**: Đảm nhiệm Page Controllers, Auth Engine (`requireRole`), phân quyền RBAC, RSS News Parser và Proxy Bridge (`api_proxy.php`).
  - **3. Microservice Layer (Python Flask :5000)**: Xử lý logic nghiệp vụ backend chuyên sâu; ReportLab Vector Engine (PDF 8 mẫu) và openpyxl Engine (Excel 35 cột).
  - **4. Data Access Layer (MySQL Database)**: Hệ quản trị CSDL quan hệ 9 bảng chuẩn hóa 3NF (`ql_dangvien`), thực thi truy vấn an toàn qua PDO Prepared Statements.
- **Phân định rõ ràng 3 Trụ cột Kỹ thuật Cốt lõi**:
  - *Trụ cột 1 (Edge AI)*: Thực thi trong RAM máy khách, đo nét Camera, bóc tách CCCD tự điền form và cắt ảnh thẻ 3x4.
  - *Trụ cột 2 (Agent AI)*: Bộ 4 tác tử thông minh soi lỗi trường khuyết và tác tử tự động ánh xạ cấu trúc cột Excel.
  - *Trụ cột 3 (Python Microservice)*: Máy chủ Flask chạy nền độc lập chuyên xử lý logic backend, kết xuất 8 biểu mẫu PDF và file Excel 35 cột.
- **Gợi ý Visual**: Sơ đồ 4 khối kiến trúc phân tầng kết hợp luồng dữ liệu Data Flow liên tầng.

---

### 4. CHI TIẾT KIẾN TRÚC PHÂN TẦNG LAI VÀ LUỒNG ĐIỀU PHỐI DỮ LIỆU
*(Slide đặt ngay sau Slide 3: Giải thích rõ ràng hơn Kiến trúc phân tầng lai)*
- **Cơ chế Phân lập Trọng tải (Workload Decoupling)**:
  - Tách bạch tuyệt đối giữa **Tác vụ tương tác nhanh (I/O Bound)** do PHP xử lý và **Tác vụ tính toán nặng (CPU Bound)** do Client RAM (Edge AI) và Python Microservice đảm nhiệm.
  - Máy chủ Web PHP không phải gánh các thuật toán xử lý ảnh hay render tài liệu vector, giúp thời gian phản hồi giao diện luôn đạt $<0.3$ giây.
- **Vòng đời Luồng dữ liệu Liên tầng (Data Flow & Event Lifecycle)**:
  1. *Khởi tạo tại Client*: Trình duyệt thực thi Edge AI trong RAM máy khách $\to$ nén dữ liệu chuẩn hóa gửi lên PHP qua AJAX JSON.
  2. *Xác thực tại PHP Core*: Kiểm tra Session, CSRF Token, đối soát quyền RBAC và ghi nhận trạng thái vào CSDL MySQL 3NF.
  3. *Ủy quyền Xử lý qua Proxy*: Khi có yêu cầu xuất file hoặc tính toán phức tạp, PHP đóng gói JSON chuyển tiếp qua REST API nội bộ tới Python Microservice (:5000).
  4. *Phản hồi Nhị phân Trực tiếp*: Python xử lý logic, tạo tệp nhị phân và stream ngược lại Client để tải xuống tức thì.
- **Gợi ý Visual**: Sơ đồ vòng đời điều phối dữ liệu từ Client $\to$ PHP Core $\to$ Python Microservice $\to$ MySQL.

---

### 5. THIẾT KẾ CƠ SỞ DỮ LIỆU CHUẨN HÓA 3NF VÀ LƯỢC ĐỒ 9 BẢNG
- **Lược đồ CSDL Chuẩn hóa Dạng chuẩn 3 (3NF)**:
  - *Bảng trung tâm `doi_tuong` (35 trường)*: Quản lý trọn vẹn thông tin qua 5 giai đoạn vòng đời:
    - *Định danh cá nhân*: Họ tên, Mã SV, CCCD, Ngày sinh, Giới tính, Quê quán, Lớp học, Dân tộc, Tôn giáo, SĐT, Email.
    - *Tổ chức Đảng vụ*: Chi bộ quản lý, Đảng viên chính thức được phân công kèm cặp.
    - *Mốc thời gian nghiệp vụ*: Ngày nộp đơn, Ngày công nhận cảm tình, Ngày học lớp nhận thức Đảng (Lớp 1 \& Lớp 2), Ngày xét kết nạp.
    - *Đánh giá \& Minh chứng*: Điểm rèn luyện, Xếp loại đoàn viên, Đường dẫn lưu trữ tệp minh chứng.
    - *Trạng thái vòng đời*: Chờ duyệt $\to$ Đang theo dõi $\to$ Đã kết nạp $\to$ Từ chối.
  - *8 Bảng phụ thuộc hoàn chỉnh*:
    1. `nguoi_dung`: Quản lý tài khoản, mã hóa mật khẩu, định danh vai trò (Role-based).
    2. `chi_bo`: Quản lý danh mục các Chi bộ Đảng trực thuộc Đảng bộ.
    3. `yeu_cau_cap_nhat`: Lưu trữ các đề xuất xin chỉnh sửa thông tin cá nhân.
    4. `lich_su`: Ghi nhận nhật ký thay đổi trạng thái và thao tác của người dùng.
    5. `dang_ky_doi_tuong`: Tiếp nhận hồ sơ đăng ký mới của sinh viên chờ duyệt.
    6. `dang_vien`: Quản lý danh sách Đảng viên chính thức được phân công kèm cặp.
    7. `edge_ai_logs`: Lưu vết nhật ký quét AI, điểm tin cậy và lỗi thiếu trường thông tin.
    8. `cai_dat`: Quản trị tham số cấu hình chung của nhà trường và Đảng bộ.
- **Ràng buộc Toàn vẹn Tham chiếu (Foreign Keys)**: Khóa ngoại (`ON DELETE RESTRICT/CASCADE`) triệt tiêu 100% dư thừa và chống sai lệch dữ liệu.
- **Gợi ý Visual**: Sơ đồ ERD 9 bảng chi tiết với bảng trung tâm `doi_tuong` và 8 bảng vệ tinh kết nối đối xứng.

---

### 6. CƠ CHẾ BẢO MẬT ĐA TẦNG VÀ AN TOÀN DỮ LIỆU CÁ NHÂN
- **Giải pháp Bảo mật Toàn diện (Multi-Layer Defense-in-Depth)**:
  - **Mã hóa BCRYPT an toàn**: Băm mật khẩu một chiều bằng thuật toán BCRYPT ($Cost=10$), chống giải mã ngược ngay cả khi cơ sở dữ liệu bị lộ.
  - **Chống SQL Injection**: 100% truy vấn CSDL qua PDO Prepared Statements (tham số hóa dữ liệu), triệt tiêu hoàn toàn nguy cơ chèn mã độc SQL.
  - **Chống XSS \& CSRF Token**: Tự động làm sạch dữ liệu đầu ra bằng bộ lọc HTML entities và sinh mã Token ngẫu nhiên cho từng thao tác form (CSRF Protection).
  - **Bảo vệ phiên làm việc**: Tự động tạo lại Session ID (`session_regenerate_id`) ngay khi đăng nhập thành công chống tấn công đánh cắp phiên (Session Fixation).
  - **Kiểm soát truy cập & Rate Limiting**: Tự động khóa tạm thời tài khoản khi đăng nhập sai quá 5 lần trong 15 phút.
- **An toàn Dữ liệu Cá nhân & Quyền riêng tư (Privacy by Design)**:
  - Ảnh chụp và dữ liệu CCCD được xử lý hoàn toàn trong RAM máy khách (Edge AI), không lưu trữ ảnh thô trên máy chủ web.
  - Kiểm duyệt nghiêm ngặt định dạng tệp tải lên (chỉ chấp nhận PDF, JPEG, PNG với dung lượng giới hạn).
- **Gợi ý Visual**: Sơ đồ 4 lớp khiên bảo mật bảo vệ luồng dữ liệu từ Trình duyệt đến Cơ sở dữ liệu.

---

### 7. CHI TIẾT CƠ CHẾ BẢO MẬT: BẢO VỆ PHIÊN, RATE LIMITING VÀ CÔ LẬP DỮ LIỆU
*(Slide chuyên sâu: Làm rõ cơ chế an ninh, xác thực và cô lập tài nguyên)*
- **Cơ chế Kiểm soát Phiên \& Chống Leo thang Đặc quyền**:
  - *RBAC Middleware (`requireRole`)*: Mọi Request tới các trang quản trị đều được chặn bắt tại Middleware, kiểm tra session đăng nhập và quyền hạn tương ứng trước khi xử lý logic.
  - *Session Regeneration*: Ngăn chặn tấn công chiếm quyền điều khiển phiên (Session Hijacking / Fixation) bằng cách hủy session cũ và cấp định danh mới ngay sau khi đăng nhập.
  - *Bộ đếm Rate Limiting CSDL*: Ghi nhận IP và thời điểm nhập sai mật khẩu, kích hoạt lệnh cấm truy cập tạm thời 15 phút nếu vượt quá ngưỡng 5 lần thử.
- **Cơ chế Cô lập Bộ nhớ \& Bảo vệ Dữ liệu Nhạy cảm**:
  - *Tham số hóa PDO BindParam*: Phân tách hoàn toàn giữa chuỗi câu lệnh SQL và dữ liệu người dùng truyền vào, vô hiệu hóa mọi chuỗi tiêm nhiễm độc hại.
  - *Cô lập RAM Client (WASM Sandboxing)*: Web Worker thực thi nhận dạng ảnh CCCD trong một luồng độc lập, tự giải phóng bộ nhớ (Garbage Collection) ngay khi trích xuất xong dữ liệu, không để rò rỉ dữ liệu thẻ CCCD ra môi trường ngoài.
- **Gợi ý Visual**: Sơ đồ kiểm soát an ninh 3 chặng: Request $\to$ RBAC Middleware $\to$ Rate Limiter $\to$ PDO Sandbox.

---

### 8. GIAO DIỆN QUẢN TRỊ: DASHBOARD, DUYỆT 2 TAB VÀ SỬA NHANH
*(Slide giữ trọn vẹn 2 ảnh chụp thực tế giao diện Người dùng và Quản lý)*
- **Giao diện Người dùng (Sinh viên / Quần chúng) — [`giaodiennguoidung.png`](file:///a:/xamapp/htdocs/web1/Bao_cao_slide_latex/giaodiennguoidung.png)**:
  - *Profile Card*: Hiển thị ảnh thẻ 3x4, họ tên, chi bộ, trạng thái hồ sơ và thông tin định danh.
  - *Timeline 5 bước*: Trực quan hóa tiến trình xét duyệt của sinh viên theo thời gian thực.
  - *Danh sách cùng lớp*: Tra cứu bạn bè cùng lớp đã được công nhận cảm tình Đảng.
  - *Gửi đề xuất sửa đổi*: Sinh viên chủ động xin cập nhật SĐT, Email, Lớp sinh hoạt.
- **Giao diện Quản lý (Bí thư Chi bộ / Đảng ủy) — [`giaodienquanli.png`](file:///a:/xamapp/htdocs/web1/Bao_cao_slide_latex/giaodienquanli.png)**:
  - *Thống kê tổng quan*: Số liệu 4 trạng thái hồ sơ (Chờ duyệt, Theo dõi, Đã kết nạp, Từ chối).
  - *Bản tin Thời sự Đa nguồn*: Tích hợp 3 báo chính thống (*Dân trí, Nhân Dân, Báo Đảng CSVN*), tự động chuyển nguồn dự phòng khi mất kết nối.
  - *Phê duyệt đối chiếu 2 Tab*: Tab 2 **tô màu Vàng nổi bật** các ô có thay đổi giữa dữ liệu cũ và dữ liệu mới, giúp Bí thư đối soát trong 3 giây.
  - *Bảng Sửa Nhanh Autosave*: Bấm sửa trực tiếp trên ô, tự lưu ngầm khi chuyển ô (blur), đổi màu phản hồi trực quan (Vàng: Đang lưu ➔ Xanh: Thành công ➔ Đỏ: Lỗi).
- **Gợi ý Visual**: 2 Ảnh chụp giao diện thực tế `giaodiennguoidung.png` \& `giaodienquanli.png` đặt song song 2 cột.

---

### 9. EDGE AI — XỬ LÝ HÌNH ẢNH CAMERA & TIỀN XỬ LÝ CANVAS
*(Trụ cột 1: Edge AI — Xử lý hình ảnh tại Client)*
- **Xử lý Luồng Camera Live (WebRTC Camera Stream)**:
  - Mở trực tiếp Camera trên máy tính/điện thoại qua trình duyệt mà không cần cài đặt app ngoài.
  - **Thuật toán đo độ nét Laplacian thời gian thực**:
    - *Ảnh mờ / Rung tay*: Khung hiển thị viền ĐỎ cảnh báo và khóa nút chụp.
    - *Ảnh rõ nét ($L \ge 65\%$)*: Khung tự chuyển viền XANH LÁ và kích hoạt nút chụp chủ động.
  - Tự động kiểm tra mật độ nội dung, cảnh báo nếu ảnh chụp không phải là giấy tờ hợp lệ.
- **Đường ống Tiền xử lý Ảnh trên HTML5 Canvas (`EdgeImageProcessor`)**:
  - *Lọc xám chuẩn hóa*: Chuyển ảnh màu sang ảnh xám để làm nổi bật nét chữ.
  - *Cân bằng sáng tự động*: Bù sáng, xóa bóng tay và khử vết lóa đèn flash.
  - *Tách nét chữ (Adaptive Binarization)*: Tách chữ đen khỏi nền giấy trắng, làm đậm các nét chữ mờ.
  - *Nắn góc nghiêng (Deskew)*: Tự động ước lượng góc xoay và nắn thẳng tài liệu chụp nghiêng ➔ Tăng 20--35% độ chính xác OCR.
  - *Tự động cắt ảnh thẻ 3x4*: Nhận diện vị trí khuôn mặt, căn chỉnh tâm và cắt đúng tỷ lệ $3:4$ ($300 \times 400$ px).
- **Gợi ý Visual**: Sơ đồ chuỗi biến đổi hình ảnh: Camera HUD viền Xanh ➔ Ảnh gốc ➔ Canvas làm nét \& Deskew ➔ Ảnh thẻ 3x4.

---

### 10. EDGE AI — NHẬN DẠNG CCCD TỰ ĐIỀN FORM VÀ MINH BẠCH HÓA XAI
*(Trụ cột 1: Edge AI — Nhận dạng văn bản & XAI)*
- **Trợ lý OCR Tự động điền Form (Auto-fill trên WebAssembly)**:
  - Tích hợp Engine Tesseract C++ chạy trực tiếp trong bộ nhớ RAM Client qua Web Workers.
  - Quần chúng kéo thả ảnh CCCD 2 mặt ➔ AI bóc tách văn bản trong 2 giây mà **không gửi ảnh về máy chủ**.
  - Biểu thức Regex ngữ cảnh tự nhận dạng chính xác: *Họ và tên, Ngày sinh, Số CCCD, Giới tính, Quê quán, Lớp học*.
  - Điền tự động 100% vào form đăng ký, giảm 90% công sức gõ thủ công cho sinh viên.
  - **Bảo vệ dữ liệu cá nhân**: Xử lý hoàn toàn tại máy khách, hỗ trợ bảo vệ dữ liệu cá nhân theo phạm vi thiết kế.
- **Minh bạch hóa AI (Explainable AI - XAI Confidence Overlay)**:
  - Xóa bỏ tính "hộp đen" của OCR: Phủ các khung viền Bounding Box 3 màu trực quan lên từng từ trên ảnh:
    - 🟢 *Khung Xanh lá* ($\ge 85\%$): Độ tin cậy rất cao, dữ liệu an toàn.
    - 🟡 *Khung Vàng* ($60-84\%$): Độ tin cậy trung bình, khuyến nghị liếc qua kiểm tra.
    - 🔴 *Khung Đỏ* ($<60\%$): Độ tin cậy thấp, cảnh báo người dùng cần đối soát kỹ.
  - Rê chuột vào từng khối chữ để xem chữ gốc và điểm tin cậy, tự động ghi nhật ký vào `edge_ai_logs`.
- **Gợi ý Visual**: Sơ đồ form đăng ký tự điền thông tin kết hợp ảnh giấy tờ có phủ các khung Bounding Box Xanh - Vàng - Đỏ.

---

### 11. AGENT AI — HỆ THỐNG THẨM ĐỊNH HỒ SƠ MINH CHỨNG ĐA TÁC TỬ
*(Trụ cột 2: Agent AI — Thẩm định hồ sơ)*
- **Hệ thống 4 Tác tử AI Chuyên trách Phối hợp (`document_inspector.js`)**:
  1. **Synopsis Agent (Tác tử Nhận diện)**: Quét 10 dòng đầu, nhận biết loại giấy tờ trong kho 10 biểu mẫu Đảng vụ.
  2. **Field Extractor Agent (Tác tử Trích xuất)**: Bóc tách các cặp thông tin cốt lõi (Họ tên, Ngày sinh, Điểm rèn luyện, Số quyết định...).
  3. **Gap Diagnostic Agent (Tác tử Bắt lỗi)**: So sánh với mẫu chuẩn, phát hiện ngay các vị trí bị bỏ trống, điền thiếu hoặc để dấu chấm lửng (`.....`), sinh hướng dẫn điền bổ sung.
  4. **Executive Synthesis Agent (Tác tử Tổng hợp & Phán quyết)**: Tính mức độ hoàn thiện (%) và đưa ra kết luận rõ ràng:
     - *Hồ sơ Đạt*: Đầy đủ 100% thông tin và có đầy đủ chữ ký, con dấu hợp lệ.
     - *Cần bổ sung*: Thiếu một số trường thông tin phụ (hệ thống chỉ rõ vị trí và đưa ra lời khuyên điền thêm).
     - *Không hợp lệ*: Thiếu quá nhiều thông tin cốt lõi hoặc thiếu con dấu xác nhận.
- **Xuất dữ liệu thẩm định**: Hỗ trợ xuất file JSON, CSV, sao chép nhanh Clipboard và lưu lịch sử vào CSDL.
- **Gợi ý Visual**: Sơ đồ luồng xử lý liên hoàn của 4 tác tử AI từ tiếp nhận tệp ➔ bóc tách ➔ bắt lỗi thiếu trường ➔ ra phán quyết.

---

### 12. AGENT AI — TÁC TỬ TỰ ĐỘNG ÁNH XẠ TIÊU ĐỀ CỘT EXCEL
*(Trụ cột 2: Agent AI — Xử lý cấu trúc dữ liệu Excel)*
- **Tác tử Ánh xạ Cột AI (`excel_column_agent.js`)**:
  - Giải quyết triệt để lỗi nhập liệu phân tán: Nạp danh sách hàng trăm sinh viên từ file Excel/CSV vào hệ thống chỉ bằng 1 thao tác kéo thả.
  - **Tự động nhận diện tiêu đề biến thể**:
    - Tự động chuẩn hóa chuỗi Unicode, loại bỏ ký tự đặc biệt.
    - Đối soát với từ điển từ khóa 35 trường CSDL MySQL để nhận diện các tiêu đề viết tắt (ví dụ: `Hoten`, `Họ và tên`, `MSSV`, `Mã SV`, `Qli`...).
  - **Cảnh báo cột khuyết tiêu đề**: Tự động phát hiện các cột trống tên và gắn nhãn cảnh báo viền đỏ `⚠️ [Cột A - Chưa có tiêu đề]`.
- **Giao diện Modal Tab Đối chiếu & Kiểm soát**:
  - Bảng so sánh 2 cột trực quan: Cột file Excel $\leftrightarrow$ Dropdown gợi ý trường CSDL tương ứng.
  - Cho phép người quản lý linh hoạt đổi lại trường theo ý muốn trước khi xác nhận nạp dữ liệu.
  - Xem trước 10 dòng đầu và tự động kiểm tra chặn trùng lặp Mã SV trước khi ghi vào CSDL.
- **Gợi ý Visual**: Sơ đồ đối chiếu 2 cột Excel vs CSDL MySQL kèm bảng Modal Tab gợi ý tự động của AI.

---

### 13. PYTHON MICROSERVICE — KIẾN TRÚC DỊCH VỤ VÀ PHP PROXY BRIDGE
*(Trụ cột 3: Python Microservice — Kiến trúc hạ tầng dịch vụ)*
- **Kiến trúc Microservice-Lite Độc lập**:
  - Máy chủ Python Flask chạy độc lập tại cổng `5000`, chuyên trách toàn bộ các tác vụ xử lý tệp nặng (tạo PDF và xử lý bảng tính Excel lớn).
  - Tách biệt hoàn toàn khỏi Web Server chính, đảm bảo máy chủ Web PHP luôn nhẹ và phản hồi tức thì ($<0.3$s).
- **Cơ chế Cầu nối Proxy (`api_proxy.php`)**:
  - Client gửi yêu cầu xuất file lên PHP Backend.
  - PHP đóng gói dữ liệu từ CSDL MySQL thành chuỗi JSON chuẩn và gửi qua REST API nội bộ tới Python Microservice.
  - Python tiếp nhận dữ liệu, render file nhị phân và truyền ngược lại qua luồng Binary Stream để người dùng tải xuống tức thì.
- **Ưu điểm kiến trúc**:
  - Độc lập nền tảng, dễ dàng mở rộng và nâng cấp thư viện mà không ảnh hưởng đến mã nguồn PHP.
  - Cô lập hoàn toàn lỗi: Nếu tiến trình tạo file gặp sự cố, hệ thống Web chính vẫn hoạt động bình thường 100\%.
- **Gợi ý Visual**: Sơ đồ kiến trúc kết nối REST API giữa PHP Core Server và Python Flask Microservice (:5000).

---

### 14. PYTHON MICROSERVICE — XỬ LÝ LOGIC BACKEND VÀ KẾT XUẤT TÀI LIỆU
*(Trụ cột 3: Python Microservice — Xử lý nghiệp vụ Backend, Kết xuất 8 PDF \& Excel 35 Cột)*
- **Xử lý Logic Nghiệp vụ Phía Backend**:
  - *Tổng hợp dữ liệu đa bảng*: Gom nhóm thông tin cá nhân, quá trình rèn luyện, kết quả học lớp Đảng và phân loại Chi bộ từ CSDL MySQL thành một cấu trúc đối tượng dữ liệu hoàn chỉnh.
  - *Xác thực điều kiện thẩm định*: Kiểm tra tự động các mốc thời gian (ngày cảm tình, ngày học lớp Đảng) và điều kiện hợp lệ trước khi cho phép kích hoạt tiến trình tạo hồ sơ.
  - *Chuẩn hóa chuỗi văn bản*: Xử lý định dạng ngày tháng tiếng Việt chuẩn hành chính, bẻ dòng tự động (Word Wrapping) và tính toán khoảng cách dòng.
- **Kỹ thuật Kết xuất 8 Mẫu biểu PDF (ReportLab Vector Canvas)**:
  - Định vị tọa độ không gian chính xác từng milimet trên trang in A4 chuẩn thể thức văn bản hành chính Đảng.
  - Tự động điền dữ liệu CSDL và **bôi đỏ nổi bật `[ Dữ liệu ]`** giúp nhận diện trực quan để sao chép hoặc kiểm tra.
  - Trọn bộ 8 biểu mẫu: *Đơn (1-KNĐ), Lý lịch (2-KNĐ), Giấy giới thiệu (3-KNĐ), Nghị quyết Đoàn TN (4-KNĐ), Công đoàn (4a-KNĐ), Ý kiến Chi ủy (5-KNĐ), Chứng nhận học lớp Đảng (CN-NTVĐ1 \& 2)*.
- **Kỹ thuật Kết xuất Báo cáo Excel 35 Cột (openpyxl)**:
  - Xuất 35 cột thuộc tính trọn vẹn theo toàn trường, chi bộ hoặc lớp trong **0.32 giây**.
  - Header đỏ cờ trang nghiêm, thuật toán Auto-fit tự co giãn độ rộng cột theo ký tự thực tế, không bị tràn ô.
- **Gợi ý Visual**: Sơ đồ chu trình xử lý Logic Backend $\to$ ReportLab 8 Mẫu PDF \& openpyxl Excel 35 Cột.

---

### 15. ĐÁNH GIÁ HIỆU NĂNG, TÍNH KHẢ THI VÀ GIÁ TRỊ THỰC TIỄN ĐỘT PHÁ
*(Slide bổ sung quan trọng: Bảng Benchmark đối sánh định lượng \& Giá trị thực tiễn)*
- **Bảng Đối sánh Định lượng Hiệu năng (Benchmark Trước vs Sau số hóa)**:
  1. *Thời gian nhập liệu hồ sơ*: $45-60$ phút/bộ $\to$ **Dưới 2.5 giây** (Nhờ Edge AI CCCD Auto-fill).
  2. *Thời gian rà soát hồ sơ*: $30-45$ phút/bộ $\to$ **Dưới 5 giây** (Nhờ 4-Agent Multi-Agent Suite).
  3. *Tốc độ kết xuất 8 PDF \& Excel*: Thủ công nhiều giờ $\to$ **0.32 - 1.0 giây** (Nhờ Python Microservice).
  4. *Chi phí máy chủ AI GPU*: Rất đắt đỏ $\to$ **0 VNĐ** (Chạy $100\%$ Client RAM WebAssembly).
  5. *Độ chính xác bóc tách & Chuẩn hóa*: Sai sót do mắt thường $\to$ **Đạt $\ge 95\%$** (Có XAI đối soát).
  6. *Tính toàn vẹn dữ liệu*: Rời rạc nhiều file $\to$ **100% chuẩn 3NF**, triệt tiêu trùng lặp.
- **Giá trị Thực tiễn & Khả năng Mở rộng**:
  - Tiết kiệm hàng trăm giờ lao động hành chính mỗi năm cho Bí thư Chi bộ và Văn phòng Đảng ủy.
  - Minh bạch hóa lộ trình kết nạp, hỗ trợ công tác quy hoạch phát triển Đảng viên bền vững.
- **Gợi ý Visual**: Bảng đối sánh Benchmark định lượng 6 tiêu chí kết hợp hộp tổng kết Giá trị thực tiễn.
