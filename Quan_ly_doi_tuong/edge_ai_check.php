<?php
// Quan_ly_doi_tuong/edge_ai_check.php
$pageTitle = 'Edge AI - Kiểm tra Hồ sơ Tự động';
require_once dirname(__DIR__) . '/Giao_dien/header.php';
?>

<!-- Tesseract.js & PDF.js for Edge AI Processing -->
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>

<style>
    .edge-ai-box {
        --bg-color: #0f172a;
        --card-bg: #1e293b;
        --accent-color: #38bdf8;
        --text-main: #f8fafc;
        --text-sub: #94a3b8;
        --success: #22c55e;
        --warning: #f59e0b;
        --danger: #ef4444;
        --border: #334155;
        color: var(--text-main);
    }

    .grid-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .grid-layout { grid-template-columns: 1fr; }
    }

    .card-ai {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 20px;
        border: 1px solid var(--border);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
    }

    .drop-zone {
        border: 2px dashed var(--accent-color);
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: background 0.2s ease;
        background: rgba(56, 189, 248, 0.05);
    }

    .drop-zone:hover { background: rgba(56, 189, 248, 0.15); }
    .drop-zone p { margin: 5px 0; color: var(--text-sub); }
    .file-input { display: none; }
    .file-list { margin-top: 15px; }

    .file-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #0f172a;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
    }

    .status-pending { background: #334155; color: #cbd5e1; }
    .status-processing { background: #1e40af; color: #93c5fd; }
    .status-success { background: #14532d; color: #86efac; }

    .analysis-box {
        font-family: inherit;
        background: #0f172a;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid var(--border);
        white-space: normal;
        line-height: 1.5;
        min-height: 250px;
        max-height: 600px;
        overflow-y: auto;
    }

    .btn-ai {
        background: var(--accent-color);
        color: #0f172a;
        border: none;
        padding: 12px 24px;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        width: 100%;
        margin-top: 15px;
        font-size: 16px;
        transition: opacity 0.2s ease;
    }

    .btn-ai:hover { opacity: 0.9; }
    .btn-ai:disabled { background: #475569; cursor: not-allowed; }

    .progress-bar-container {
        width: 100%;
        height: 8px;
        background: #334155;
        border-radius: 4px;
        margin-top: 15px;
        overflow: hidden;
        display: none;
    }

    .progress-bar {
        height: 100%;
        width: 0%;
        background: var(--accent-color);
        transition: width 0.3s ease;
    }

    .pipeline-info {
        margin-top: 15px;
        padding: 12px;
        background: rgba(15,23,42,0.6);
        border: 1px solid var(--border);
        border-radius: 8px;
    }

    .pipeline-info .step {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        color: var(--text-sub);
        padding: 3px 0;
    }

    .pipeline-info .step-icon {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: rgba(56,189,248,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        flex-shrink: 0;
    }
</style>

<div class="page-header">
  <div class="page-header-left">
    <div class="breadcrumb">
      <a href="<?= BASE_URL ?>index.php">Dashboard</a><span class="sep">›</span>
      <span class="current">Edge AI Kiểm tra Hồ sơ</span>
    </div>
    <div class="page-title">Edge AI <span>Quét & Kiểm Tra Phiếu Tự Động</span></div>
  </div>
  <div style="display:flex;gap:10px;">
    <a href="<?= BASE_URL ?>index.php" class="btn btn-outline">Quay lại Trang chủ</a>
  </div>
</div>

<div class="edge-ai-box">
    <div class="grid-layout">
        <!-- Panel Upload -->
        <div class="card-ai">
            <h3 style="margin-bottom:12px;color:var(--accent-color);">Tải Lên Phiếu Bản Mềm Cần Kiểm Tra</h3>

            <div class="drop-zone" id="dropZone" onclick="document.getElementById('fileInput').click()">
                <p>Kéo & thả tài liệu bản mềm vào đây hoặc click để chọn</p>
                <small style="color: var(--text-sub)">(Hỗ trợ PDF, PNG, JPG, WebP — Tối đa 10MB/file)<br>Upload 1 hoặc nhiều phiếu — AI sẽ tự nhận diện loại phiếu & soi thiếu trường</small>
                <input type="file" id="fileInput" class="file-input" multiple accept="image/*,.pdf">
            </div>

            <div class="progress-bar-container" id="progressContainer">
                <div class="progress-bar" id="progressBar"></div>
            </div>

            <div class="file-list" id="fileList"></div>

            <div class="pipeline-info">
                <div style="font-size:12px;font-weight:bold;color:var(--accent-color);margin-bottom:6px;"><i class="bi bi-cpu-fill" style="margin-right:4px;"></i> AI Pipeline — 5 Agent xử lý tuần tự:</div>
                <div class="step"><span class="step-icon">0</span> <strong>Image Processor:</strong> Tăng contrast, giảm nhiễu, binarize ảnh</div>
                <div class="step"><span class="step-icon">1</span> <strong>Document Classifier:</strong> Nhận diện loại phiếu tự động</div>
                <div class="step"><span class="step-icon">2</span> <strong>Field Extractor:</strong> Trích xuất cặp Nhãn-Giá trị</div>
                <div class="step"><span class="step-icon">3</span> <strong>Gap Diagnostic:</strong> Kiểm tra trường nào có / trống</div>
                <div class="step"><span class="step-icon">4</span> <strong>Result Exporter:</strong> Xuất kết quả JSON / CSV</div>
            </div>

            <button class="btn-ai" id="btnAnalyze" onclick="startEdgeAnalysis()" disabled><i class="bi bi-lightning-charge-fill" style="margin-right:6px;"></i> Khởi Động AI Kiểm Tra</button>
        </div>

        <!-- Panel Kết Quả -->
        <div class="card-ai">
            <h3 style="margin-bottom:12px;color:var(--accent-color);"><i class="bi bi-clipboard2-data-fill" style="margin-right:6px;"></i> Kết Quả Phân Tích Từng Phiếu</h3>
            <div class="analysis-box" id="analysisResult">
                Vui lòng tải lên phiếu cần kiểm tra (1 hoặc nhiều file) và nhấn "Khởi Động AI Kiểm Tra"...
            </div>

            <button class="btn-ai" id="btnSave" style="background: var(--success); display: none;" onclick="saveCheckResults()"><i class="bi bi-floppy-fill" style="margin-right:6px;"></i> Lưu Kết Quả Vào Hệ Thống</button>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>AI_Module/edge_image_processor.js"></script>
<script src="<?= BASE_URL ?>AI_Module/document_inspector.js"></script>
<script src="<?= BASE_URL ?>AI_Module/result_export_agent.js"></script>
<script src="<?= BASE_URL ?>AI_Module/live_camera_scanner.js"></script>
<script src="<?= BASE_URL ?>AI_Module/xai_confidence_overlay.js"></script>
<script src="edge_ai_ocr.js"></script>

<?php require_once dirname(__DIR__) . '/Giao_dien/footer.php'; ?>
