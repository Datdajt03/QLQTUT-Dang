// Quan_ly_doi_tuong/edge_ai_ocr.js
// Edge AI Engine: Kết nối với Module DocumentFieldInspector để Soi Thẩm Định Trường Thông Tin Thiếu Mọi Loại Phiếu

// 5 Cấu trúc Models tiêu chuẩn cho Hồ sơ Kết nạp Đảng
const DOCUMENT_MODELS = [
    {
        key: 'ban_tu_nhan_xet',
        name: 'Bản tự nhận xét / Tự kiểm điểm cá nhân',
        label: '[Bản tự nhận xét]',
        typeKeywords: ['bản tự nhận xét', 'bản kiểm điểm', 'tự đánh giá', 'tự nhận xét', 'nhận xét cá nhân', 'bản tự kiểm điểm'],
        requiredFields: [
            { fieldKey: 'ho_ten', fieldName: 'Họ và tên người nhận xét', keywords: ['họ và tên', 'tôi tên là', 'họ tên'] },
            { fieldKey: 'ngay_sinh', fieldName: 'Ngày tháng năm sinh', keywords: ['ngày sinh', 'sinh ngày'] },
            { fieldKey: 'uu_diem', fieldName: 'Ưu điểm / Kết quả đạt được', keywords: ['ưu điểm', 'thành tích', 'kết quả công tác', 'ưu điểm nổi bật', 'phẩm chất chính trị'] },
            { fieldKey: 'khuyet_diem', fieldName: 'Khuyết điểm / Hạn chế', keywords: ['khuyết điểm', 'hạn chế', 'tồn tại', 'nhược điểm'] },
            { fieldKey: 'phuong_huong', fieldName: 'Phương hướng phấn đấu', keywords: ['phương hướng', 'hướng phấn đấu', 'giải pháp khắc phục', 'cam kết'] },
            { fieldKey: 'ngay_thang_chu_ky', fieldName: 'Ngày tháng & Chữ ký xác nhận', keywords: ['ngày tháng năm', 'người tự nhận xét', 'kí tên', 'ký tên', 'chữ ký'] }
        ]
    },
    {
        key: 'giay_chung_nhan',
        name: 'Giấy chứng nhận bồi dưỡng nhận thức về Đảng',
        label: '[Giấy chứng nhận]',
        typeKeywords: ['giấy chứng nhận', 'chứng nhận bồi dưỡng', 'bồi dưỡng nhận thức', 'lớp cảm tình đảng', 'nhận thức về đảng', 'chứng chỉ nhận thức'],
        requiredFields: [
            { fieldKey: 'ten_don_vi', fieldName: 'Đơn vị cấp (ĐH Tây Bắc / Trung tâm chính trị)', keywords: ['đại học tây bắc', 'trung tâm chính trị', 'đảng ủy', 'ban chấp hành'] },
            { fieldKey: 'ho_ten', fieldName: 'Họ và tên học viên', keywords: ['họ và tên', 'chứng nhận đồng chí', 'trao cho', 'học viên'] },
            { fieldKey: 'ngay_sinh', fieldName: 'Ngày sinh', keywords: ['sinh ngày', 'ngày sinh'] },
            { fieldKey: 'xep_loai', fieldName: 'Kết quả xếp loại (Giỏi/Khá/TB)', keywords: ['xếp loại', 'loại giỏi', 'loại khá', 'loại xuất sắc', 'đạt loại', 'kết quả học tập'] },
            { fieldKey: 'so_qd_cc', fieldName: 'Số quyết định / Số chứng nhận', keywords: ['số:', 'số quyết định', 'số cn', 'số sổ'] },
            { fieldKey: 'ngay_cap', fieldName: 'Ngày tháng ký cấp chứng nhận', keywords: ['ngày cấp', 'ngày tháng năm', 'ký ngày'] }
        ]
    },
    {
        key: 'ho_so_ca_nhan',
        name: 'Sơ yếu lý lịch / CCCD / Thẻ sinh viên',
        label: '[Sơ yếu lý lịch]',
        typeKeywords: ['sơ yếu lý lịch', 'lý lịch người xin vào đảng', 'căn cước công dân', 'thẻ sinh viên', 'thông tin cá nhân', 'lý lịch cá nhân'],
        requiredFields: [
            { fieldKey: 'ho_ten', fieldName: 'Họ và tên', keywords: ['họ và tên', 'họ tên'] },
            { fieldKey: 'ngay_sinh', fieldName: 'Ngày tháng năm sinh', keywords: ['ngày sinh', 'sinh ngày'] },
            { fieldKey: 'que_quan', fieldName: 'Quê quán / Nguyên quán', keywords: ['quê quán', 'nguyên quán', 'thường trú'] },
            { fieldKey: 'ma_sv', fieldName: 'Mã sinh viên / Số CCCD', keywords: ['mã sinh viên', 'mssv', 'mã sv', 'số cccd', 'số cmnd'] },
            { fieldKey: 'lop_don_vi', fieldName: 'Lớp sinh hoạt / Khoa', keywords: ['lớp', 'khoa', 'chi bộ', 'đơn vị'] }
        ]
    },
    {
        key: 'phieu_danh_gia',
        name: 'Phiếu đánh giá chất lượng đoàn viên / Giấy giới thiệu',
        label: '[Phiếu đánh giá]',
        typeKeywords: ['phiếu đánh giá', 'xếp loại đoàn viên', 'phân loại chất lượng', 'giấy giới thiệu quần chúng', 'đoàn thanh niên', 'nhận xét của đoàn thanh niên'],
        requiredFields: [
            { fieldKey: 'ten_doan_vien', fieldName: 'Họ tên đoàn viên / Quần chúng', keywords: ['đoàn viên', 'họ và tên', 'quần chúng', 'tên tôi là'] },
            { fieldKey: 'chi_doan', fieldName: 'Tên Chi đoàn / Liên chi đoàn', keywords: ['chi đoàn', 'đoàn cơ sở', 'liên chi đoàn'] },
            { fieldKey: 'ket_qua_danh_gia', fieldName: 'Xếp loại đoàn viên (Xuất sắc/Tốt)', keywords: ['hoàn thành xuất sắc', 'hoàn thành tốt', 'đoàn viên ưu tú', 'xếp loại'] },
            { fieldKey: 'nguoi_gioi_thieu', fieldName: 'Bí thư Chi đoàn / Ban chấp hành xác nhận', keywords: ['bí thư', 't/m ban chấp hành', 'ký tên', 'đại diện', 'xác nhận'] }
        ]
    },
    {
        key: 'minh_chung_hoat_dong',
        name: 'Minh chứng hoạt động phong trào / Giấy khen',
        label: '[Minh chứng phong trào]',
        typeKeywords: ['giấy khen', 'bằng khen', 'chứng nhận tham gia', 'hiến máu', 'mùa hè xanh', 'tình nguyện', 'hoạt động phong trào', 'thành tích'],
        requiredFields: [
            { fieldKey: 'ten_hoat_dong', fieldName: 'Tên hoạt động / Phong trào tham gia', keywords: ['hiến máu', 'tình nguyện', 'mùa hè xanh', 'hoạt động', 'thành tích', 'đã tham gia'] },
            { fieldKey: 'ho_ten_nguoi_nhan', fieldName: 'Họ và tên cá nhân nhận', keywords: ['khen thưởng', 'trao tặng', 'đồng chí', 'sinh viên', 'họ và tên'] },
            { fieldKey: 'don_vi_khen_thuong', fieldName: 'Đơn vị khen thưởng / Chứng nhận', keywords: ['ban chấp hành', 'hội sinh viên', 'đoàn trường', 'giám đốc', 'hiệu trưởng'] },
            { fieldKey: 'thoi_gian_cap', fieldName: 'Thời gian thực hiện / Cấp giấy', keywords: ['năm học', 'ngày tháng năm', 'tháng'] }
        ]
    }
];

let uploadedFiles = [];
let analysisOutputData = null;

// Khởi tạo Module Thẩm định chuyên dụng
const inspectorEngine = (typeof DocumentFieldInspector !== 'undefined')
    ? new DocumentFieldInspector(DOCUMENT_MODELS)
    : null;

// Khởi tạo PDF.js Worker
if (typeof pdfjsLib !== 'undefined') {
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
}

const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');

if (fileInput) {
    fileInput.addEventListener('change', (e) => handleFiles(e.target.files));
}

if (dropZone) {
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.style.background = 'rgba(56, 189, 248, 0.2)';
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.style.background = 'rgba(56, 189, 248, 0.05)';
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.style.background = 'rgba(56, 189, 248, 0.05)';
        handleFiles(e.dataTransfer.files);
    });
}

function handleFiles(files) {
    const MAX_SIZE = 10 * 1024 * 1024; // 10MB
    for (let file of files) {
        if (file.size > MAX_SIZE) {
            alert(`Thông báo: Tệp "${file.name}" vượt quá dung lượng cho phép (Tối đa 10MB).`);
            continue;
        }
        if (!uploadedFiles.some(f => f.name === file.name)) {
            uploadedFiles.push({
                file: file,
                name: file.name,
                size: file.size,
                status: 'pending',
                extractedText: '',
                matchedModel: null,
                inspectionResult: null
            });
        }
    }
    renderFileList();
    const btn = document.getElementById('btnAnalyze');
    if (btn) btn.disabled = uploadedFiles.length === 0;
}

function renderFileList() {
    const list = document.getElementById('fileList');
    if (!list) return;
    list.innerHTML = '';
    uploadedFiles.forEach((item) => {
        const div = document.createElement('div');
        div.className = 'file-item';
        let badgeClass = 'status-pending';
        let badgeText = 'Chờ xử lý';

        if (item.status === 'processing') {
            badgeClass = 'status-processing';
            badgeText = 'Đang quét Edge AI...';
        } else if (item.status === 'success') {
            badgeClass = 'status-success';
            badgeText = item.matchedModel ? `Đã nhận diện: ${item.matchedModel.name}` : 'Đã quét Universal OCR';
        }

        div.innerHTML = `
            <span>[Tệp] ${item.name} ${item.matchedModel ? `<small style="color:var(--accent-color);">(${item.matchedModel.label})</small>` : ''}</span>
            <span class="status-badge ${badgeClass}">${badgeText}</span>
        `;
        list.appendChild(div);
    });
}

async function startEdgeAnalysis() {
    const btnAnalyze = document.getElementById('btnAnalyze');
    const resultBox = document.getElementById('analysisResult');
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('progressBar');

    if (btnAnalyze) btnAnalyze.disabled = true;
    if (progressContainer) progressContainer.style.display = 'block';
    if (resultBox) {
        resultBox.innerHTML = '<div style="color:var(--accent-color);font-weight:bold;">Đang kích hoạt Universal Document Field Inspector & Edge AI Engine...</div>';
    }

    let completed = 0;
    let worker = null;

    try {
        if (typeof Tesseract !== 'undefined') {
            worker = await Tesseract.createWorker('vie');
        }
    } catch (e) {
        console.warn("Could not create Tesseract worker:", e);
    }

    for (let i = 0; i < uploadedFiles.length; i++) {
        const item = uploadedFiles[i];
        item.status = 'processing';
        renderFileList();

        try {
            if (item.file.type === 'application/pdf') {
                item.extractedText = await extractTextFromPDF(item.file);
            } else if (worker) {
                const ret = await worker.recognize(item.file);
                item.extractedText = ret.data.text || '';
            } else {
                item.extractedText = item.name;
            }
            item.status = 'success';
        } catch (err) {
            console.error('OCR Error for file', item.name, err);
            item.extractedText = item.name.toLowerCase();
            item.status = 'success';
        }

        completed++;
        if (progressBar) progressBar.style.width = `${(completed / uploadedFiles.length) * 100}%`;
        renderFileList();
    }

    if (worker) {
        try {
            await worker.terminate();
        } catch(e){}
    }

    // Chạy Module Thẩm định chuyên dụng soi trường thiếu cho MỌI LOẠI PHIẾU
    runEdgeModelAnalysis();
}

async function extractTextFromPDF(file) {
    const arrayBuffer = await file.arrayBuffer();
    const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
    let fullText = '';

    for (let i = 1; i <= pdf.numPages; i++) {
        const page = await pdf.getPage(i);
        const textContent = await page.getTextContent();
        const pageText = textContent.items.map(item => item.str).join(' ');
        fullText += pageText + ' ';
    }
    return fullText;
}

// Báo cáo chi tiết các trường thông tin khuyết dựa trên Module Universal DocumentFieldInspector
function runEdgeModelAnalysis() {
    const resultBox = document.getElementById('analysisResult');

    if (!inspectorEngine) {
        resultBox.innerHTML = '<div style="color:#ef4444;">Lỗi: Chưa nạp được Module DocumentFieldInspector.</div>';
        return;
    }

    // Thẩm định tổng thể các tệp (Hỗ trợ cả 5 Mẫu Bắt buộc & Mẫu Phiếu Tùy Chỉnh)
    const portfolioReport = inspectorEngine.inspectPortfolio(uploadedFiles);
    const map = portfolioReport.modelStatusMap;
    const customInspections = portfolioReport.customFormInspections || [];

    let html = `
        <div style="font-family: inherit; color: var(--text-main);">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid var(--border); padding-bottom: 10px; margin-bottom: 15px;">
                <h4 style="margin:0; color: var(--accent-color); font-size:16px;">KẾT QUẢ THẨM ĐỊNH TRƯỜNG THÔNG TIN THIẾU TỔNG QUÁT (UNIVERSAL INSPECTOR)</h4>
                <span class="status-badge ${portfolioReport.isFullyValid ? 'status-success' : 'status-warning'}">
                    ${portfolioReport.isFullyValid ? 'HỒ SƠ ĐẦY ĐỦ 100%' : 'CẢNH BÁO: CẦN BỔ SUNG TRƯỜNG THIẾU'}
                </span>
            </div>

            <!-- DANH SÁCH 5 MẪU PHIẾU BẮT BUỘC -->
            <div style="margin-bottom: 18px;">
                <strong style="color:var(--text-main); font-size:14px;">Báo cáo Chi tiết 5 Mẫu Phiếu Kết nạp Đảng Tiêu chuẩn:</strong>
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px; margin-top: 10px;">
    `;

    DOCUMENT_MODELS.forEach(m => {
        const entry = map[m.key];
        let color = '#ef4444';
        let bg = 'rgba(239, 68, 68, 0.1)';
        let statusBadge = '<span style="color:#ef4444; font-weight:bold;">Chưa nộp tệp phiếu này</span>';

        if (entry.status === 'VALID') {
            color = '#22c55e';
            bg = 'rgba(34, 197, 94, 0.1)';
            statusBadge = '<span style="color:#22c55e; font-weight:bold;">Đã nộp (Đầy đủ 100%)</span>';
        } else if (entry.status === 'INCOMPLETE') {
            color = '#f59e0b';
            bg = 'rgba(245, 158, 11, 0.1)';
            statusBadge = `<span style="color:#f59e0b; font-weight:bold;">CẢNH BÁO THIẾU ${entry.missingFields.length} TRƯỜNG THÔNG TIN</span>`;
        }

        html += `
            <div style="background:${bg}; border:1px solid ${color}; border-radius:8px; padding:12px; font-size:12px;">
                <div style="font-weight:bold; color:${color}; font-size:13px; margin-bottom:6px;">${m.name}</div>
                <div style="margin-bottom:8px; color:var(--text-sub);">${statusBadge}</div>

                <div style="border-top:1px dashed ${color}; padding-top:6px; margin-top:6px;">
                    <div style="font-weight:bold; margin-bottom:4px; color:var(--text-main);">Danh sách Trường Thông tin:</div>
                    <ul style="margin:0; padding-left:16px; color:var(--text-main);">
        `;

        entry.foundFields.forEach(ff => {
            html += `<li style="color:#22c55e; margin-bottom:3px;">
                <strong>[ĐÃ CÓ]</strong> ${ff.fieldName}
                ${ff.extractedValue ? `<div style="color:var(--text-sub); font-size:11px;">➜ Dữ liệu: <em>"${ff.extractedValue}"</em></div>` : ''}
            </li>`;
        });

        entry.missingFields.forEach(mf => {
            html += `<li style="color:#ef4444; font-weight:bold; margin-bottom:3px;">
                <strong>[CẢNH BÁO THIẾU]</strong> ${mf.fieldName}
            </li>`;
        });

        html += `
                    </ul>
                </div>
            </div>
        `;
    });

    html += `
                </div>
            </div>
    `;

    // PHẦN HIỂN THỊ PHIẾU TÙY CHỈNH KHI NGƯỜI DÙNG NỘP PHIẾU KHÁC (UNIVERSAL DYNAMIC FORM CARDS)
    if (customInspections.length > 0) {
        html += `
            <div style="margin-bottom: 18px;">
                <strong style="color:var(--accent-color); font-size:14px;">Báo cáo Chi tiết Các Mẫu Phiếu Tùy Chỉnh / Tệp Khác Đã Nộp (${customInspections.length} tệp):</strong>
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px; margin-top: 10px;">
        `;

        customInspections.forEach(ci => {
            let color = ci.status === 'VALID' ? '#22c55e' : '#f59e0b';
            let bg = ci.status === 'VALID' ? 'rgba(34, 197, 94, 0.1)' : 'rgba(245, 158, 11, 0.1)';
            let statusBadge = ci.status === 'VALID'
                ? '<span style="color:#22c55e; font-weight:bold;">Đã điền ĐẦY ĐỦ</span>'
                : `<span style="color:#f59e0b; font-weight:bold;">CẢNH BÁO THIẾU ${ci.missingFields.length} TRƯỜNG</span>`;

            html += `
                <div style="background:${bg}; border:1px solid ${color}; border-radius:8px; padding:12px; font-size:12px;">
                    <div style="font-weight:bold; color:${color}; font-size:13px; margin-bottom:4px;">📄 ${ci.model.name}</div>
                    <div style="color:var(--text-sub); font-size:11px; margin-bottom:6px;">Tệp gốc: <code>${ci.fileName}</code></div>
                    <div style="margin-bottom:8px;">${statusBadge} (Đạt ${ci.scorePercent}%)</div>

                    <div style="border-top:1px dashed ${color}; padding-top:6px;">
                        <div style="font-weight:bold; margin-bottom:4px; color:var(--text-main);">Các trường phát hiện tự động:</div>
                        <ul style="margin:0; padding-left:16px;">
            `;

            ci.foundFields.forEach(ff => {
                html += `<li style="color:#22c55e; margin-bottom:2px;">
                    <strong>[ĐÃ ĐIỀN]</strong> ${ff.fieldName}
                    ${ff.extractedValue ? `<div style="color:var(--text-sub); font-size:11px;">➜ Dữ liệu: <em>"${ff.extractedValue}"</em></div>` : ''}
                </li>`;
            });

            ci.missingFields.forEach(mf => {
                html += `<li style="color:#ef4444; font-weight:bold; margin-bottom:2px;">
                    <strong>[CẢNH BÁO TRỐNG/THIẾU]</strong> ${mf.fieldName}
                </li>`;
            });

            if (ci.totalFields === 0) {
                html += `<li style="color:#94a3b8;">Chưa phát hiện nhãn trường thông tin định dạng [Tên_trường]: [Nội_dung].</li>`;
            }

            html += `
                        </ul>
                    </div>
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;
    }

    // CẢNH BÁO TỔNG HỢP CÁC TRƯỜNG BỊ THIẾU TRONG TOÀN BỘ TỆP
    let allMissingAlerts = [];
    DOCUMENT_MODELS.forEach(m => {
        const entry = map[m.key];
        if (entry.status === 'MISSING') {
            allMissingAlerts.push(`Mẫu phiếu <strong>${m.name}</strong>: ❌ Chưa nộp tệp phiếu.`);
        } else if (entry.status === 'INCOMPLETE') {
            const missingNames = entry.missingFields.map(f => f.fieldName).join(', ');
            allMissingAlerts.push(`Mẫu phiếu <strong>${m.name}</strong>: ⚠️ Thiếu các trường [<strong>${missingNames}</strong>].`);
        }
    });

    customInspections.forEach(ci => {
        if (ci.missingFields.length > 0) {
            const missingNames = ci.missingFields.map(f => f.fieldName).join(', ');
            allMissingAlerts.push(`Tệp phiếu tùy chỉnh <strong>${ci.model.name}</strong> (Tệp <code>${ci.fileName}</code>): ⚠️ Đang trống/thiếu các trường [<strong>${missingNames}</strong>].`);
        }
    });

    if (allMissingAlerts.length > 0) {
        html += `
            <div style="background: rgba(239, 68, 68, 0.15); border-left: 4px solid #ef4444; padding: 12px; border-radius: 6px; margin-bottom: 15px;">
                <strong style="color: #fca5a5;">DANH SÁCH THÔNG TIN CẦN BỔ SUNG NGAY CỦA CÁC PHIẾU:</strong>
                <ul style="margin: 6px 0 0 18px; padding: 0; color: #fca5a5; font-size: 13px;">
                    ${allMissingAlerts.map(alert => `<li>${alert}</li>`).join('')}
                </ul>
            </div>
        `;
    }

    // ĐỀ XUẤT HƯỚNG BỔ SUNG
    html += `
        <div style="background: rgba(56, 189, 248, 0.1); border-left: 4px solid var(--accent-color); padding: 12px; border-radius: 6px;">
            <strong style="color: var(--accent-color);">HƯỚNG DẪN KHẮC PHỤC:</strong>
            ${!portfolioReport.isFullyValid ? `
                <div style="color: #bae6fd; font-size: 13px; margin-top: 4px;">Vui lòng kiểm tra và điền bổ sung đầy đủ các trường bị cảnh báo đỏ trong các tệp trên trước khi lưu hồ sơ chính thức.</div>
            ` : `
                <div style="color: #86efac; font-size: 13px; margin-top: 4px;">Chúc mừng! Hồ sơ hoàn toàn đầy đủ 100% tất cả các trường thông tin chi tiết.</div>
            `}
        </div>
    `;

    html += `</div>`;

    resultBox.innerHTML = html;

    // Chuẩn bị rawSummary cho DB
    let rawSummaryText = `BÁO CÁO MODULE UNIVERSAL DOCUMENT INSPECTOR\n`;
    rawSummaryText += `────────────────────────────────────────\n`;
    DOCUMENT_MODELS.forEach(m => {
        const entry = map[m.key];
        if (entry.status === 'VALID') {
            rawSummaryText += `[Thành công] ${m.name}: Đầy đủ 100%\n`;
        } else if (entry.status === 'INCOMPLETE') {
            const missingNames = entry.missingFields.map(f => f.fieldName).join(', ');
            rawSummaryText += `[Cảnh báo Thiếu] ${m.name}: Khuyết [${missingNames}]\n`;
        } else {
            rawSummaryText += `[Thiếu tệp] ${m.name}: Chưa nộp tệp\n`;
        }
    });

    customInspections.forEach(ci => {
        if (ci.status === 'VALID') {
            rawSummaryText += `[Tùy chỉnh Đầy đủ] ${ci.model.name} (${ci.fileName}): Đầy đủ 100%\n`;
        } else {
            const missingNames = ci.missingFields.map(f => f.fieldName).join(', ');
            rawSummaryText += `[Tùy chỉnh Thiếu] ${ci.model.name} (${ci.fileName}): Trống [${missingNames}]\n`;
        }
    });

    analysisOutputData = {
        isComplete: portfolioReport.isFullyValid,
        missingModelCount: portfolioReport.missingModelCount,
        incompleteModelCount: portfolioReport.incompleteModelCount,
        rawSummary: rawSummaryText
    };

    const btnSave = document.getElementById('btnSave');
    const btnAnalyze = document.getElementById('btnAnalyze');

    if (btnSave) btnSave.style.display = 'block';
    if (btnAnalyze) btnAnalyze.disabled = false;
}

async function saveCheckResults() {
    if (!analysisOutputData) return;

    const formData = new FormData();
    formData.append('analysisData', JSON.stringify(analysisOutputData));

    uploadedFiles.forEach((item) => {
        formData.append('files[]', item.file);
    });

    try {
        const response = await fetch('api_save_ai_check.php', {
            method: 'POST',
            body: formData
        });
        const resData = await response.json();
        if (resData.success) {
            alert('Thông báo: ' + resData.message);
        } else {
            alert('Lỗi: ' + resData.message);
        }
    } catch (err) {
        console.error(err);
        alert('Lỗi gửi yêu cầu lưu hồ sơ lên Server PHP');
    }
}
