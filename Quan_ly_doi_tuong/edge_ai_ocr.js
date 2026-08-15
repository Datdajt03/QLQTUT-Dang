// Quan_ly_doi_tuong/edge_ai_ocr.js
// Edge AI Engine v2: Per-File Analysis + Image Processor + Export Agent

const DOCUMENT_MODELS = [
    {
        key: 'ban_tu_nhan_xet', name: 'Bản tự nhận xét / Tự kiểm điểm cá nhân', label: '[Bản tự nhận xét]',
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
        key: 'giay_chung_nhan', name: 'Giấy chứng nhận bồi dưỡng nhận thức về Đảng', label: '[Giấy chứng nhận]',
        typeKeywords: ['giấy chứng nhận', 'chứng nhận bồi dưỡng', 'bồi dưỡng nhận thức', 'lớp cảm tình đảng', 'nhận thức về đảng', 'chứng chỉ nhận thức'],
        requiredFields: [
            { fieldKey: 'ten_don_vi', fieldName: 'Đơn vị cấp', keywords: ['đại học tây bắc', 'trung tâm chính trị', 'đảng ủy', 'ban chấp hành'] },
            { fieldKey: 'ho_ten', fieldName: 'Họ và tên học viên', keywords: ['họ và tên', 'chứng nhận đồng chí', 'trao cho', 'học viên'] },
            { fieldKey: 'ngay_sinh', fieldName: 'Ngày sinh', keywords: ['sinh ngày', 'ngày sinh'] },
            { fieldKey: 'xep_loai', fieldName: 'Kết quả xếp loại', keywords: ['xếp loại', 'loại giỏi', 'loại khá', 'loại xuất sắc', 'đạt loại', 'kết quả học tập'] },
            { fieldKey: 'so_qd_cc', fieldName: 'Số quyết định / Số chứng nhận', keywords: ['số:', 'số quyết định', 'số cn', 'số sổ'] },
            { fieldKey: 'ngay_cap', fieldName: 'Ngày tháng ký cấp', keywords: ['ngày cấp', 'ngày tháng năm', 'ký ngày'] }
        ]
    },
    {
        key: 'ho_so_ca_nhan', name: 'Sơ yếu lý lịch / CCCD / Thẻ sinh viên', label: '[Sơ yếu lý lịch]',
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
        key: 'phieu_danh_gia', name: 'Phiếu đánh giá chất lượng đoàn viên', label: '[Phiếu đánh giá]',
        typeKeywords: ['phiếu đánh giá', 'xếp loại đoàn viên', 'phân loại chất lượng', 'giấy giới thiệu quần chúng', 'đoàn thanh niên', 'nhận xét của đoàn thanh niên'],
        requiredFields: [
            { fieldKey: 'ten_doan_vien', fieldName: 'Họ tên đoàn viên / Quần chúng', keywords: ['đoàn viên', 'họ và tên', 'quần chúng', 'tên tôi là'] },
            { fieldKey: 'chi_doan', fieldName: 'Tên Chi đoàn / Liên chi đoàn', keywords: ['chi đoàn', 'đoàn cơ sở', 'liên chi đoàn'] },
            { fieldKey: 'ket_qua_danh_gia', fieldName: 'Xếp loại đoàn viên', keywords: ['hoàn thành xuất sắc', 'hoàn thành tốt', 'đoàn viên ưu tú', 'xếp loại'] },
            { fieldKey: 'nguoi_gioi_thieu', fieldName: 'Bí thư Chi đoàn xác nhận', keywords: ['bí thư', 't/m ban chấp hành', 'ký tên', 'đại diện', 'xác nhận'] }
        ]
    },
    {
        key: 'minh_chung_hoat_dong', name: 'Minh chứng hoạt động phong trào / Giấy khen', label: '[Minh chứng phong trào]',
        typeKeywords: ['giấy khen', 'bằng khen', 'chứng nhận tham gia', 'hiến máu', 'mùa hè xanh', 'tình nguyện', 'hoạt động phong trào', 'thành tích'],
        requiredFields: [
            { fieldKey: 'ten_hoat_dong', fieldName: 'Tên hoạt động / Phong trào', keywords: ['hiến máu', 'tình nguyện', 'mùa hè xanh', 'hoạt động', 'thành tích', 'đã tham gia'] },
            { fieldKey: 'ho_ten_nguoi_nhan', fieldName: 'Họ và tên cá nhân nhận', keywords: ['khen thưởng', 'trao tặng', 'đồng chí', 'sinh viên', 'họ và tên'] },
            { fieldKey: 'don_vi_khen_thuong', fieldName: 'Đơn vị khen thưởng', keywords: ['ban chấp hành', 'hội sinh viên', 'đoàn trường', 'giám đốc', 'hiệu trưởng'] },
            { fieldKey: 'thoi_gian_cap', fieldName: 'Thời gian cấp giấy', keywords: ['năm học', 'ngày tháng năm', 'tháng'] }
        ]
    }
];

let uploadedFiles = [];
let analysisOutputData = null;
let fileInspectionResults = []; // Per-file results

const inspectorEngine = (typeof AIDocumentInspectorAgent !== 'undefined')
    ? new AIDocumentInspectorAgent(DOCUMENT_MODELS)
    : ((typeof DocumentFieldInspector !== 'undefined') ? new DocumentFieldInspector(DOCUMENT_MODELS) : null);

if (typeof pdfjsLib !== 'undefined') {
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
}

const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
if (fileInput) fileInput.addEventListener('change', (e) => handleFiles(e.target.files));
if (dropZone) {
    dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.style.background = 'rgba(56, 189, 248, 0.2)'; });
    dropZone.addEventListener('dragleave', () => { dropZone.style.background = 'rgba(56, 189, 248, 0.05)'; });
    dropZone.addEventListener('drop', (e) => { e.preventDefault(); dropZone.style.background = 'rgba(56, 189, 248, 0.05)'; handleFiles(e.dataTransfer.files); });
}

function handleFiles(files) {
    const MAX_SIZE = 10 * 1024 * 1024;
    for (let file of files) {
        if (file.size > MAX_SIZE) { alert(`Tệp "${file.name}" vượt quá 10MB.`); continue; }
        if (!uploadedFiles.some(f => f.name === file.name)) {
            uploadedFiles.push({ file, name: file.name, size: file.size, status: 'pending', extractedText: '', inspectionResult: null });
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
    uploadedFiles.forEach(item => {
        const div = document.createElement('div');
        div.className = 'file-item';
        let badgeClass = 'status-pending', badgeText = 'Chờ xử lý';
        if (item.status === 'preprocessing') { badgeClass = 'status-processing'; badgeText = '🖼️ Xử lý ảnh...'; }
        else if (item.status === 'processing') { badgeClass = 'status-processing'; badgeText = '🔍 Đang quét OCR...'; }
        else if (item.status === 'analyzing') { badgeClass = 'status-processing'; badgeText = '🤖 AI phân tích...'; }
        else if (item.status === 'success') { badgeClass = 'status-success'; badgeText = '✅ Hoàn tất'; }
        div.innerHTML = `<span>${item.name}</span><span class="status-badge ${badgeClass}">${badgeText}</span>`;
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
    if (resultBox) resultBox.innerHTML = '<div style="color:var(--accent-color);font-weight:bold;">Đang kích hoạt AI Pipeline (Agent 0→1→2→3→5)...</div>';

    fileInspectionResults = [];
    let completed = 0;
    let worker = null;

    try {
        if (typeof Tesseract !== 'undefined') worker = await Tesseract.createWorker('vie');
    } catch (e) { console.warn("Tesseract init failed:", e); }

    for (let i = 0; i < uploadedFiles.length; i++) {
        const item = uploadedFiles[i];

        // Agent 0: Image preprocessing
        item.status = 'preprocessing';
        renderFileList();
        let processedFile = item.file;
        if (item.file.type !== 'application/pdf' && typeof EdgeImageProcessor !== 'undefined') {
            try { processedFile = await EdgeImageProcessor.preprocess(item.file); } catch(e) { console.warn('Agent 0 skip:', e); }
        }

        // OCR
        item.status = 'processing';
        renderFileList();
        item.words = [];
        item.processedImage = processedFile;
        try {
            if (item.file.type === 'application/pdf') {
                item.extractedText = await extractTextFromPDF(item.file);
            } else if (worker) {
                const ret = await worker.recognize(processedFile);
                item.extractedText = ret.data.text || '';
                item.words = ret.data.words || [];
            } else {
                item.extractedText = item.name;
            }
        } catch (err) {
            console.error('OCR Error:', item.name, err);
            item.extractedText = item.name.toLowerCase();
        }

        // Agent 1-3: Classify + Extract + Inspect
        item.status = 'analyzing';
        renderFileList();

        try {
            let result;
            if (inspectorEngine && typeof inspectorEngine.inspectDocumentFile === 'function') {
                result = inspectorEngine.inspectDocumentFile(item.extractedText, item.name);
            } else {
                result = fallbackInspect(item.extractedText, item.name);
            }

            result.words = item.words;
            result.image = item.processedImage;
            fileInspectionResults.push(result);
            item.status = 'success';
            renderFileList();
        } catch (err) {
            console.error(`Error processing ${item.name}:`, err);
            item.status = 'error';
            renderFileList();
            fileInspectionResults.push({
                fileName: item.name,
                status: 'ERROR',
                model: { name: 'Lỗi xử lý', label: 'Không thể đọc file' },
                foundFields: [],
                missingFields: [],
                scorePercent: 0,
                agentVerdict: 'Không thể đọc nội dung file: ' + err.message,
                actionAdvice: 'Vui lòng kiểm tra định dạng và chất lượng tệp.',
                words: [],
                image: item.processedImage
            });
        }
    }

    if (progressBar) progressBar.style.width = '100%';
    setTimeout(() => { if (progressContainer) progressContainer.style.display = 'none'; }, 600);

    renderInspectionResults();
}

async function extractTextFromPDF(file) {
    const arrayBuffer = await file.arrayBuffer();
    const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
    let fullText = '';
    for (let i = 1; i <= pdf.numPages; i++) {
        const page = await pdf.getPage(i);
        const textContent = await page.getTextContent();
        fullText += textContent.items.map(item => item.str).join(' ') + ' ';
    }
    return fullText;
}

function fallbackInspect(text, fileName) {
    return {
        fileName: fileName,
        status: 'UNRECOGNIZED',
        model: { name: 'Tệp tùy chỉnh', label: '' },
        foundFields: [],
        missingFields: [],
        scorePercent: 50,
        agentVerdict: 'Tệp đã được quét OCR',
        actionAdvice: ''
    };
}

function renderInspectionResults() {
    const resultBox = document.getElementById('analysisResult');
    if (!resultBox || fileInspectionResults.length === 0) return;

    const totalFiles = fileInspectionResults.length;
    const validFiles = fileInspectionResults.filter(r => r.status === 'VALID').length;
    const totalFields = fileInspectionResults.reduce((s, r) => s + (r.foundFields || []).length + (r.missingFields || []).length, 0);
    const filledFields = fileInspectionResults.reduce((s, r) => s + (r.foundFields || []).length, 0);
    const emptyFields = totalFields - filledFields;
    const overallScore = totalFields > 0 ? Math.round((filledFields / totalFields) * 100) : 100;

    let html = `<div style="font-family:inherit;color:var(--text-main);">`;

    html += `
        <div style="background:rgba(15,23,42,0.85);border:1px solid var(--accent-color);border-radius:8px;padding:14px;margin-bottom:16px;">
            <div style="font-weight:bold;color:var(--accent-color);font-size:14px;margin-bottom:6px;"><i class="bi bi-cpu-fill" style="margin-right:6px;"></i> KẾT QUẢ PHÂN TÍCH AI — ${totalFiles} tệp</div>
            <div style="display:flex;flex-wrap:wrap;gap:12px;font-size:12px;color:var(--text-sub);">
                <span><i class="bi bi-files" style="margin-right:4px;"></i> Tổng tệp: <strong>${totalFiles}</strong></span>
                <span><i class="bi bi-check-circle-fill" style="color:#22c55e;margin-right:4px;"></i> Đạt chuẩn: <strong>${validFiles}</strong></span>
                <span><i class="bi bi-card-checklist" style="margin-right:4px;"></i> Trường phát hiện: <strong>${totalFields}</strong></span>
                <span><i class="bi bi-check2-square" style="color:#22c55e;margin-right:4px;"></i> Đã điền: <strong>${filledFields}</strong></span>
                <span><i class="bi bi-x-circle-fill" style="color:#ef4444;margin-right:4px;"></i> Còn trống: <strong>${emptyFields}</strong></span>
                <span><i class="bi bi-pie-chart-fill" style="margin-right:4px;"></i> Tỷ lệ: <strong>${overallScore}%</strong></span>
            </div>
        </div>`;

    fileInspectionResults.forEach((result, idx) => {
        const isValid = result.status === 'VALID';
        const borderColor = isValid ? '#22c55e' : (result.status === 'UNRECOGNIZED' ? '#94a3b8' : '#f59e0b');
        const bgColor = isValid ? 'rgba(34,197,94,0.05)' : (result.status === 'UNRECOGNIZED' ? 'rgba(148,163,184,0.05)' : 'rgba(245,158,11,0.05)');
        const statusIcon = isValid ? '<i class="bi bi-check-circle-fill" style="color:#22c55e;margin-right:4px;"></i>' : (result.status === 'UNRECOGNIZED' ? '<i class="bi bi-question-circle-fill" style="color:#94a3b8;margin-right:4px;"></i>' : '<i class="bi bi-exclamation-triangle-fill" style="color:#f59e0b;margin-right:4px;"></i>');
        const typeName = result.model ? result.model.name : 'Không nhận diện';
        const typeLabel = result.model ? result.model.label : '';
        const hasWords = result.words && result.words.length > 0;

        html += `
        <div style="background:${bgColor};border:1px solid ${borderColor};border-radius:8px;margin-bottom:12px;overflow:hidden;">
            <div style="padding:12px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid rgba(255,255,255,0.05);">
                <div>
                    <div style="font-weight:bold;color:var(--text-main);font-size:13px;">${statusIcon} <i class="bi bi-file-earmark-text" style="margin-right:4px;"></i> ${result.fileName}</div>
                    <div style="font-size:11px;color:var(--text-sub);margin-top:2px;">Loại phiếu: <strong style="color:var(--accent-color);">${typeName}</strong> ${typeLabel}</div>
                </div>
                <div style="display:flex;gap:6px;align-items:center;">
                    ${hasWords ? `<button type="button" onclick="openXAIViewer(${idx})" style="background:rgba(56,189,248,0.15);color:var(--accent-color);border:1px solid var(--accent-color);padding:4px 10px;border-radius:4px;font-size:11px;cursor:pointer;font-weight:600;" title="Xem Bounding Box & Độ tin cậy OCR"><i class="bi bi-bullseye"></i> Bản Đồ XAI</button>` : ''}
                    <button type="button" onclick="exportSingleFile(${idx},'json')" style="background:rgba(56,189,248,0.15);color:var(--accent-color);border:1px solid var(--accent-color);padding:4px 10px;border-radius:4px;font-size:11px;cursor:pointer;" title="Xuất JSON"><i class="bi bi-file-earmark-code"></i> JSON</button>
                    <button type="button" onclick="exportSingleFile(${idx},'csv')" style="background:rgba(34,197,94,0.15);color:#22c55e;border:1px solid #22c55e;padding:4px 10px;border-radius:4px;font-size:11px;cursor:pointer;" title="Xuất CSV"><i class="bi bi-file-earmark-spreadsheet"></i> CSV</button>
                </div>
            </div>`;

        const allFields = [...(result.foundFields || []), ...(result.missingFields || [])];
        if (allFields.length > 0) {
            html += `<div style="padding:0;"><table style="width:100%;border-collapse:collapse;font-size:12px;">
                <thead><tr style="background:rgba(0,0,0,0.2);">
                    <th style="padding:8px 12px;text-align:left;color:var(--text-sub);font-weight:600;">Trường thông tin</th>
                    <th style="padding:8px 12px;text-align:left;color:var(--text-sub);font-weight:600;">Giá trị phát hiện</th>
                    <th style="padding:8px 12px;text-align:center;color:var(--text-sub);font-weight:600;width:80px;">Trạng thái</th>
                </tr></thead><tbody>`;

            (result.foundFields || []).forEach(f => {
                html += `<tr style="border-top:1px solid rgba(255,255,255,0.04);">
                    <td style="padding:6px 12px;color:var(--text-main);">${f.fieldName}</td>
                    <td style="padding:6px 12px;color:#86efac;font-style:italic;">${f.extractedValue ? `"${f.extractedValue}"` : '(có)'}</td>
                    <td style="padding:6px 12px;text-align:center;"><span style="background:rgba(34,197,94,0.2);color:#22c55e;padding:2px 8px;border-radius:3px;font-size:10px;font-weight:bold;">ĐÃ CÓ</span></td>
                </tr>`;
            });

            (result.missingFields || []).forEach(f => {
                html += `<tr style="border-top:1px solid rgba(255,255,255,0.04);background:rgba(239,68,68,0.04);">
                    <td style="padding:6px 12px;color:#fca5a5;font-weight:600;">${f.fieldName}</td>
                    <td style="padding:6px 12px;color:var(--text-sub);font-style:italic;">(trống)</td>
                    <td style="padding:6px 12px;text-align:center;"><span style="background:rgba(239,68,68,0.2);color:#ef4444;padding:2px 8px;border-radius:3px;font-size:10px;font-weight:bold;">TRỐNG</span></td>
                </tr>`;
            });

            html += `</tbody></table></div>`;
        } else {
            if (!result.missingFields || result.missingFields.length === 0) {
                html += `<div style="padding:16px;text-align:center;color:#22c55e;font-size:12px;font-weight:bold;"><i class="bi bi-check2-all" style="margin-right:6px;"></i> Không phát hiện thiếu trường thông tin nào.</div>`;
            } else {
                html += `<div style="padding:16px;text-align:center;color:var(--text-sub);font-size:12px;">Không phát hiện được trường thông tin — kiểm tra lại chất lượng ảnh/tệp.</div>`;
            }
        }

        if (result.status === 'VALID' || !result.missingFields || result.missingFields.length === 0) {
            html += `<div style="padding:8px 16px;background:rgba(34,197,94,0.1);font-size:11px;color:#22c55e;font-weight:bold;"><i class="bi bi-check2-all" style="margin-right:6px;"></i> Không phát hiện thiếu trường thông tin nào.</div>`;
        } else if (result.actionAdvice) {
            html += `<div style="padding:8px 16px;background:rgba(0,0,0,0.15);font-size:11px;color:var(--accent-color);"><i class="bi bi-lightbulb-fill" style="margin-right:6px;"></i> ${result.actionAdvice}</div>`;
        }

        html += `</div>`;
    });

    html += `
        <div style="display:flex;gap:8px;justify-content:center;padding:12px 0;flex-wrap:wrap;">
            <button type="button" onclick="exportAllJSON()" style="background:var(--accent-color);color:#0f172a;border:none;padding:8px 18px;border-radius:6px;font-weight:bold;cursor:pointer;font-size:13px;"><i class="bi bi-file-earmark-code-fill" style="margin-right:6px;"></i> Xuất tất cả JSON</button>
            <button type="button" onclick="exportAllCSV()" style="background:#22c55e;color:#0f172a;border:none;padding:8px 18px;border-radius:6px;font-weight:bold;cursor:pointer;font-size:13px;"><i class="bi bi-file-earmark-spreadsheet-fill" style="margin-right:6px;"></i> Xuất tất cả CSV</button>
            <button type="button" onclick="copyAllResults()" style="background:#8b5cf6;color:white;border:none;padding:8px 18px;border-radius:6px;font-weight:bold;cursor:pointer;font-size:13px;"><i class="bi bi-clipboard-check-fill" style="margin-right:6px;"></i> Copy Clipboard</button>
        </div>`;

    html += `</div>`;
    resultBox.innerHTML = html;

    // Prepare save data
    analysisOutputData = {
        isComplete: emptyFields === 0,
        totalFiles: totalFiles,
        filledFields: filledFields,
        emptyFields: emptyFields,
        overallScore: overallScore,
        rawSummary: buildRawSummary(),
        perFileData: fileInspectionResults.map(r => ({
            fileName: r.fileName,
            type: r.model ? r.model.name : 'custom',
            foundFields: (r.foundFields || []).map(f => ({ name: f.fieldName, value: f.extractedValue })),
            missingFields: (r.missingFields || []).map(f => f.fieldName)
        }))
    };

    const btnSave = document.getElementById('btnSave');
    const btnAnalyze = document.getElementById('btnAnalyze');
    if (btnSave) btnSave.style.display = 'block';
    if (btnAnalyze) btnAnalyze.disabled = false;
}

function buildRawSummary() {
    let text = `KẾT QUẢ KIỂM TRA AI — ${fileInspectionResults.length} tệp\n`;
    text += `────────────────────────────────────────\n`;
    fileInspectionResults.forEach(r => {
        const typeName = r.model ? r.model.name : 'Không nhận diện';
        const icon = r.status === 'VALID' ? '✅' : '⚠️';
        text += `${icon} ${r.fileName} (${typeName})\n`;
        (r.foundFields || []).forEach(f => { text += `   ✅ ${f.fieldName}: ${f.extractedValue || '(có)'}\n`; });
        (r.missingFields || []).forEach(f => { text += `   ❌ ${f.fieldName}: (trống)\n`; });
    });
    return text;
}

// Export functions
function exportSingleFile(idx, format) {
    if (!fileInspectionResults[idx] || typeof ResultExportAgent === 'undefined') return;
    if (format === 'json') ResultExportAgent.exportSingleJSON(fileInspectionResults[idx]);
    else ResultExportAgent.exportSingleCSV(fileInspectionResults[idx]);
}

function exportAllJSON() {
    if (typeof ResultExportAgent !== 'undefined') ResultExportAgent.exportJSON(fileInspectionResults);
}

function exportAllCSV() {
    if (typeof ResultExportAgent !== 'undefined') ResultExportAgent.exportCSV(fileInspectionResults);
}

async function copyAllResults() {
    if (typeof ResultExportAgent !== 'undefined') {
        const ok = await ResultExportAgent.copyToClipboard(fileInspectionResults);
        if (ok) alert('Đã copy kết quả vào clipboard!');
    }
}

async function saveCheckResults() {
    if (!analysisOutputData) return;
    const formData = new FormData();
    formData.append('analysisData', JSON.stringify(analysisOutputData));
    uploadedFiles.forEach(item => { formData.append('files[]', item.file); });
    try {
        const response = await fetch('api_save_ai_check.php', { method: 'POST', body: formData });
        const resData = await response.json();
        alert(resData.success ? ('Thông báo: ' + resData.message) : ('Lỗi: ' + resData.message));
    } catch (err) {
        console.error(err);
        alert('Lỗi gửi yêu cầu lưu hồ sơ');
    }
}

// Handler mở Live Camera Scanner cho tài liệu
function openLiveCameraDocScanner() {
    if (typeof LiveCameraScanner === 'undefined') {
        alert('Thư viện LiveCameraScanner chưa sẵn sàng.');
        return;
    }
    const scanner = new LiveCameraScanner({
        targetType: 'document',
        autoSnapEnabled: true,
        sharpnessThreshold: 65,
        onCapture: (file, dataUrl) => {
            handleFiles([file]);
        }
    });
    scanner.open();
}

// Handler mở Explainable AI (XAI) Overlay
const globalXAIViewer = (typeof XAIConfidenceOverlay !== 'undefined') ? new XAIConfidenceOverlay() : null;
function openXAIViewer(idx) {
    const item = fileInspectionResults[idx];
    if (!item || !item.image) {
        alert("Không có dữ liệu hình ảnh cho tệp này hoặc đây là tệp PDF văn bản số.");
        return;
    }
    if (!globalXAIViewer) {
        alert("Thư viện XAIConfidenceOverlay chưa sẵn sàng.");
        return;
    }
    globalXAIViewer.open(item.image, item.words || []);
}

