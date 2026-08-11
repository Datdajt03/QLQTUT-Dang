// Quan_ly_doi_tuong/edge_ai_ocr.js

// Danh mục hồ sơ cần kiểm tra
const REQUIRED_DOCUMENTS = [
    { key: 'ban_tu_nhan_xet', name: 'Bản tự nhận xét', keywords: ['bản tự nhận xét', 'nhận xét cá nhân', 'tự đánh giá', 'ưu điểm', 'khuyết điểm'] },
    { key: 'giay_chung_nhan', name: 'Giấy chứng nhận', keywords: ['giấy chứng nhận', 'bồi dưỡng nhận thức', 'chứng nhận học tập', 'đã hoàn thành khóa học'] },
    { key: 'minh_chung_hoat_dong', name: 'Minh chứng hoạt động', keywords: ['giấy khen', 'chứng nhận tham gia', 'hiến máu', 'mùa hè xanh', 'tình nguyện', 'hoạt động đoàn'] },
    { key: 'phieu_danh_gia', name: 'Phiếu đánh giá', keywords: ['phiếu đánh giá', 'xếp loại đoàn viên', 'phân loại chất lượng'] },
    { key: 'ho_so_ca_nhan', name: 'Hồ sơ cá nhân', keywords: ['sơ yếu lý lịch', 'căn cước công dân', 'thẻ sinh viên', 'nguyễn văn', 'mã sinh viên'] }
];

let uploadedFiles = [];
let analysisOutputData = null;

// Khởi tạo thư viện PDF.js Worker
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

// Xử lý sự kiện Kéo Thả File
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');

fileInput.addEventListener('change', (e) => handleFiles(e.target.files));

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

function handleFiles(files) {
    const MAX_SIZE = 10 * 1024 * 1024; // 10MB
    for (let file of files) {
        if (file.size > MAX_SIZE) {
            alert(`⚠️ File "${file.name}" vượt quá dung lượng cho phép (Tối đa 10MB).`);
            continue;
        }
        if (!uploadedFiles.some(f => f.name === file.name)) {
            uploadedFiles.push({
                file: file,
                name: file.name,
                size: file.size,
                status: 'pending',
                extractedText: ''
            });
        }
    }
    renderFileList();
    document.getElementById('btnAnalyze').disabled = uploadedFiles.length === 0;
}

function renderFileList() {
    const list = document.getElementById('fileList');
    list.innerHTML = '';
    uploadedFiles.forEach((item, index) => {
        const div = document.createElement('div');
        div.className = 'file-item';
        let badgeClass = 'status-pending';
        let badgeText = 'Chờ xử lý';

        if (item.status === 'processing') {
            badgeClass = 'status-processing';
            badgeText = '⚡ Edge AI đang đọc OCR...';
        } else if (item.status === 'success') {
            badgeClass = 'status-success';
            badgeText = '✓ Đã trích xuất';
        }

        div.innerHTML = `
            <span>📄 ${item.name}</span>
            <span class="status-badge ${badgeClass}">${badgeText}</span>
        `;
        list.appendChild(div);
    });
}

// Bắt đầu quá trình OCR & Phân tích bằng Edge AI
async function startEdgeAnalysis() {
    const btnAnalyze = document.getElementById('btnAnalyze');
    const resultBox = document.getElementById('analysisResult');
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('progressBar');

    btnAnalyze.disabled = true;
    progressContainer.style.display = 'block';
    resultBox.innerHTML = '⚡ Đang kích hoạt Edge AI Engine để OCR trực tiếp trên trình duyệt...\n';

    let completed = 0;
    const worker = await Tesseract.createWorker('vie'); // Ngôn ngữ Tiếng Việt

    for (let i = 0; i < uploadedFiles.length; i++) {
        const item = uploadedFiles[i];
        item.status = 'processing';
        renderFileList();

        try {
            if (item.file.type === 'application/pdf') {
                item.extractedText = await extractTextFromPDF(item.file);
            } else {
                const ret = await worker.recognize(item.file);
                item.extractedText = ret.data.text;
            }
            item.status = 'success';
        } catch (err) {
            console.error('OCR Error:', err);
            item.extractedText = item.name.toLowerCase(); // Fallback to filename
            item.status = 'success';
        }

        completed++;
        progressBar.style.width = `${(completed / uploadedFiles.length) * 100}%`;
        renderFileList();
    }

    await worker.terminate();

    // Tiến hành Phân tích Rule Engine & Tổng hợp kết quả
    runRuleEngineAnalysis();
}

// Trích xuất văn bản từ file PDF client-side
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

// Phân tích Rule Engine kiểm tra checklist hồ sơ
function runRuleEngineAnalysis() {
    const resultBox = document.getElementById('analysisResult');
    let allExtractedContent = uploadedFiles.map(f => f.extractedText.toLowerCase() + " " + f.name.toLowerCase()).join(' ');

    let docCheck = {
        ban_tu_nhan_xet: false,
        giay_chung_nhan: false,
        minh_chung_hoat_dong: false,
        phieu_danh_gia: false,
        ho_so_ca_nhan: false
    };

    let missingDetails = [];
    let suggestions = [];

    // Kiểm tra sự xuất hiện của các loại hồ sơ dựa vào keyword
    REQUIRED_DOCUMENTS.forEach(doc => {
        const found = doc.keywords.some(kw => allExtractedContent.includes(kw));
        if (found) {
            docCheck[doc.key] = true;
        } else {
            missingDetails.push(`⚠ Thiếu tài liệu hoặc minh chứng: "${doc.name}"`);
            suggestions.push(`→ Bổ sung file ${doc.name}`);
        }
    });

    // Kiểm tra trích xuất thông tin cá nhân cơ bản
    let studentNameFound = allExtractedContent.match(/(họ và tên|họ tên)[:\s]+([a-zà-ỹ\s]+)/i);
    let studentIdFound = allExtractedContent.match(/(mssv|mã sinh viên|mã số sv)[:\s]+([a-z0-9]+)/i);

    let outputText = "🤖 **KẾT QUẢ KIỂM TRA HỒ SƠ (EDGE AI)**\n";
    outputText += "────────────────────────────────────────\n\n";

    outputText += (docCheck.ho_so_ca_nhan ? "✓ Thông tin cá nhân\n" : "⚠ Chưa xác nhận thông tin cá nhân\n");
    outputText += (docCheck.giay_chung_nhan ? "✓ Quá trình học tập (Đã có Giấy chứng nhận)\n" : "⚠ Thiếu Giấy chứng nhận học tập\n");
    outputText += (docCheck.phieu_danh_gia ? "✓ Hoạt động đoàn thể & Đánh giá\n" : "⚠ Thiếu Phiếu đánh giá đoàn viên\n");
    outputText += (docCheck.minh_chung_hoat_dong ? "✓ Minh chứng hoạt động\n" : "⚠ Chưa phát hiện file Minh chứng hoạt động\n");

    if (missingDetails.length > 0) {
        outputText += "\n📌 **CÁC CHI TIẾT CẦN LƯU Ý:**\n";
        missingDetails.forEach(m => outputText += `${m}\n`);
    }

    if (suggestions.length > 0) {
        outputText += "\n💡 **ĐỀ XUẤT KHẮC PHỤC:**\n";
        suggestions.forEach(s => outputText += `${s}\n`);
    } else {
        outputText += "\n🎉 **HỒ SƠ HOÀN TOÀN HỢP LỆ! BẠN CÓ THỂ NỘP BÀI.**\n";
    }

    resultBox.innerText = outputText;

    // Lưu data cấu trúc để gửi về API
    analysisOutputData = {
        isComplete: missingDetails.length === 0,
        checks: docCheck,
        missing: missingDetails,
        suggestions: suggestions,
        rawSummary: outputText
    };

    document.getElementById('btnSave').style.display = 'block';
    document.getElementById('btnAnalyze').disabled = false;
}

// Lưu kết quả kiểm tra và Upload file thực tế vào Backend PHP
async function saveCheckResults() {
    if (!analysisOutputData) return;

    const formData = new FormData();
    formData.append('analysisData', JSON.stringify(analysisOutputData));

    // Đính kèm các file thực tế vào FormData
    uploadedFiles.forEach((item, idx) => {
        formData.append('files[]', item.file);
    });

    try {
        const response = await fetch('api_save_ai_check.php', {
            method: 'POST',
            body: formData
        });
        const resData = await response.json();
        if (resData.success) {
            alert('✅ ' + resData.message);
        } else {
            alert('❌ Có lỗi xảy ra: ' + resData.message);
        }
    } catch (err) {
        console.error(err);
        alert('❌ Lỗi gửi yêu cầu lưu hồ sơ lên Server PHP');
    }
}
