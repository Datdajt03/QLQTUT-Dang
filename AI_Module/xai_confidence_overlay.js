/**
 * AI_Module/xai_confidence_overlay.js
 * Feature 5: Explainable Edge AI (XAI) - Dynamic Confidence Bounding Box & Heatmap Overlay
 * Công nghệ Bách Khoa Edge AI:
 * - Trực quan hóa độ tin cậy mô hình nơ-ron (Neural Token Confidence Visualization)
 * - Phân lớp mức độ tin cậy: Green (>=85%), Amber (60-84%), Red (<60%)
 * - Interactive Canvas Tooltip & Bounding Box Hit-Testing
 * - Metric Summary: Mean Confidence (μ), Low-Confidence Risk Warnings
 */

class XAIConfidenceOverlay {
    constructor() {
        this.modalEl = null;
        this.canvas = null;
        this.ctx = null;
        this.img = null;
        this.words = [];
        this.scale = 1;
        this.overlayOpacity = 0.45;
        this.hoveredWord = null;

        this._createDOM();
    }

    _createDOM() {
        if (document.getElementById('xaiOverlayModal')) {
            this.modalEl = document.getElementById('xaiOverlayModal');
            return;
        }

        const modal = document.createElement('div');
        modal.id = 'xaiOverlayModal';
        modal.className = 'xai-modal-overlay';
        modal.innerHTML = `
            <div class="xai-modal-container">
                <div class="xai-modal-header">
                    <div class="xai-title">
                        <i class="bi bi-bullseye"></i>
                        <span>Explainable Edge AI (XAI) — Bản Đồ Nhiệt Độ Tin Cậy OCR</span>
                        <span class="xai-badge">WASM ATTENTION HEATMAP</span>
                    </div>
                    <button type="button" class="xai-close-btn" id="xaiCloseBtn" title="Đóng">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Stats summary bar -->
                <div class="xai-stats-bar" id="xaiStatsBar">
                    <div class="xai-stat-pill">
                        <span class="xai-stat-label">Tổng từ nhận diện:</span>
                        <strong class="xai-stat-val" id="xaiTotalWords">0</strong>
                    </div>
                    <div class="xai-stat-pill">
                        <span class="xai-stat-label">Độ tin cậy TB (μ):</span>
                        <strong class="xai-stat-val" id="xaiAvgConf" style="color:#38bdf8;">0%</strong>
                    </div>
                    <div class="xai-stat-legend">
                        <span class="legend-dot green"></span> Cao (≥85%)
                        <span class="legend-dot amber"></span> Trung bình (60-84%)
                        <span class="legend-dot red"></span> Cảnh báo (&lt;60%)
                    </div>
                </div>

                <!-- Interactive Viewer Canvas -->
                <div class="xai-canvas-viewport" id="xaiCanvasViewport">
                    <canvas id="xaiViewerCanvas"></canvas>
                    <div class="xai-tooltip" id="xaiTooltip"></div>
                </div>

                <!-- Footer controls -->
                <div class="xai-modal-footer">
                    <div class="xai-footer-left">
                        <label style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:6px;">
                            Độ đậm Heatmap:
                            <input type="range" id="xaiOpacityRange" min="10" max="90" value="45" style="width:100px;cursor:pointer;">
                        </label>
                    </div>
                    <div class="xai-footer-right">
                        <button type="button" class="xai-btn-action" id="xaiCloseBtn2">Đóng cửa sổ XAI</button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        this.modalEl = modal;

        this._injectStyles();
        this._bindEvents();
    }

    _injectStyles() {
        if (document.getElementById('xaiOverlayStyles')) return;
        const style = document.createElement('style');
        style.id = 'xaiOverlayStyles';
        style.textContent = `
            .xai-modal-overlay {
                position: fixed;
                top: 0; left: 0; width: 100vw; height: 100vh;
                background: rgba(0, 0, 0, 0.88);
                backdrop-filter: blur(8px);
                z-index: 99999;
                display: none;
                align-items: center;
                justify-content: center;
                animation: xaiFadeIn 0.25s ease;
            }
            .xai-modal-overlay.open { display: flex; }
            @keyframes xaiFadeIn { from { opacity: 0; } to { opacity: 1; } }

            .xai-modal-container {
                width: 92%; max-width: 900px; max-height: 90vh;
                background: #0f172a;
                border: 1px solid rgba(56, 189, 248, 0.35);
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 25px 50px rgba(0,0,0,0.7), 0 0 30px rgba(56, 189, 248, 0.2);
                display: flex;
                flex-direction: column;
            }
            .xai-modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 18px;
                background: #1e293b;
                border-bottom: 1px solid rgba(255,255,255,0.08);
            }
            .xai-title {
                display: flex;
                align-items: center;
                gap: 8px;
                font-weight: 700;
                font-size: 14px;
                color: #38bdf8;
            }
            .xai-badge {
                background: rgba(56, 189, 248, 0.15);
                color: #38bdf8;
                border: 1px solid #38bdf8;
                font-size: 10px;
                padding: 1px 6px;
                border-radius: 4px;
                font-weight: 800;
            }
            .xai-close-btn {
                background: transparent;
                border: none;
                color: #94a3b8;
                font-size: 18px;
                cursor: pointer;
            }
            .xai-close-btn:hover { color: #f87171; }

            .xai-stats-bar {
                display: flex;
                flex-wrap: wrap;
                gap: 16px;
                align-items: center;
                padding: 8px 18px;
                background: rgba(15, 23, 42, 0.95);
                border-bottom: 1px solid rgba(255,255,255,0.05);
                font-size: 12px;
                color: #e2e8f0;
            }
            .xai-stat-pill { display: flex; align-items: center; gap: 6px; }
            .xai-stat-label { color: #94a3b8; }
            .xai-stat-val { font-weight: 700; }
            .xai-stat-legend {
                margin-left: auto;
                display: flex;
                align-items: center;
                gap: 12px;
                font-size: 11px;
                color: #cbd5e1;
            }
            .legend-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
            .legend-dot.green { background: #22c55e; box-shadow: 0 0 6px #22c55e; }
            .legend-dot.amber { background: #f59e0b; box-shadow: 0 0 6px #f59e0b; }
            .legend-dot.red { background: #ef4444; box-shadow: 0 0 6px #ef4444; }

            .xai-canvas-viewport {
                position: relative;
                width: 100%;
                flex-grow: 1;
                max-height: calc(90vh - 150px);
                background: #020617;
                overflow: auto;
                display: flex;
                justify-content: center;
                align-items: flex-start;
                padding: 16px;
            }
            #xaiViewerCanvas {
                box-shadow: 0 10px 30px rgba(0,0,0,0.8);
                border-radius: 4px;
                cursor: crosshair;
            }

            .xai-tooltip {
                position: absolute;
                background: rgba(15, 23, 42, 0.95);
                border: 1px solid #38bdf8;
                border-radius: 6px;
                padding: 6px 10px;
                color: #fff;
                font-size: 11px;
                font-family: monospace;
                pointer-events: none;
                display: none;
                z-index: 1000;
                box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            }

            .xai-modal-footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 18px;
                background: #1e293b;
                border-top: 1px solid rgba(255,255,255,0.08);
            }
            .xai-btn-action {
                background: #38bdf8;
                color: #0f172a;
                font-weight: 700;
                border: none;
                border-radius: 6px;
                padding: 6px 16px;
                font-size: 12px;
                cursor: pointer;
            }
            .xai-btn-action:hover { background: #7dd3fc; }
        `;
        document.head.appendChild(style);
    }

    _bindEvents() {
        const closeBtn = document.getElementById('xaiCloseBtn');
        const closeBtn2 = document.getElementById('xaiCloseBtn2');
        if (closeBtn) closeBtn.onclick = () => this.close();
        if (closeBtn2) closeBtn2.onclick = () => this.close();

        const opacityRange = document.getElementById('xaiOpacityRange');
        if (opacityRange) {
            opacityRange.oninput = (e) => {
                this.overlayOpacity = parseFloat(e.target.value) / 100;
                this._render();
            };
        }

        const canvas = document.getElementById('xaiViewerCanvas');
        if (canvas) {
            canvas.onmousemove = (e) => this._handleMouseMove(e);
            canvas.onmouseleave = () => this._handleMouseLeave();
        }
    }

    /**
     * Mở modal hiển thị XAI Overlay
     * @param {File|Blob|string} imageSource - File ảnh hoặc dataURL
     * @param {Array} words - Mảng kết quả OCR tokens từ Tesseract (ret.data.words)
     */
    async open(imageSource, words = []) {
        this.words = words || [];
        this.canvas = document.getElementById('xaiViewerCanvas');
        this.ctx = this.canvas.getContext('2d');

        try {
            this.img = await this._loadImage(imageSource);
            
            // Tính toán scale và kích thước canvas
            const maxW = 820;
            this.scale = this.img.width > maxW ? (maxW / this.img.width) : 1;

            this.canvas.width = this.img.width * this.scale;
            this.canvas.height = this.img.height * this.scale;

            this._updateStats();
            this._render();

            this.modalEl.classList.add('open');
        } catch (err) {
            console.error("XAI Load Image error:", err);
            alert("Không thể dựng bản đồ XAI cho tệp này.");
        }
    }

    _loadImage(src) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => resolve(img);
            img.onerror = reject;
            if (typeof src === 'string') {
                img.src = src;
            } else {
                img.src = URL.createObjectURL(src);
            }
        });
    }

    _updateStats() {
        const total = this.words.length;
        const totalConf = this.words.reduce((sum, w) => sum + (w.confidence || 0), 0);
        const avgConf = total > 0 ? Math.round(totalConf / total) : 0;

        const totalEl = document.getElementById('xaiTotalWords');
        const avgEl = document.getElementById('xaiAvgConf');
        if (totalEl) totalEl.innerText = total;
        if (avgEl) {
            avgEl.innerText = `${avgConf}%`;
            avgEl.style.color = avgConf >= 85 ? '#22c55e' : (avgConf >= 60 ? '#f59e0b' : '#ef4444');
        }
    }

    _render() {
        if (!this.ctx || !this.img) return;

        // Vẽ ảnh nền
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        this.ctx.drawImage(this.img, 0, 0, this.canvas.width, this.canvas.height);

        // Vẽ Bounding Boxes & Confidence Highlights
        this.words.forEach(w => {
            if (!w.bbox) return;

            const x0 = w.bbox.x0 * this.scale;
            const y0 = w.bbox.y0 * this.scale;
            const x1 = w.bbox.x1 * this.scale;
            const y1 = w.bbox.y1 * this.scale;
            const width = x1 - x0;
            const height = y1 - y0;

            const conf = w.confidence || 0;
            let fillColor, strokeColor;

            if (conf >= 85) {
                fillColor = `rgba(34, 197, 94, ${this.overlayOpacity})`;
                strokeColor = '#22c55e';
            } else if (conf >= 60) {
                fillColor = `rgba(245, 158, 11, ${this.overlayOpacity})`;
                strokeColor = '#f59e0b';
            } else {
                fillColor = `rgba(239, 68, 68, ${this.overlayOpacity + 0.15})`;
                strokeColor = '#ef4444';
            }

            // Vẽ khối fill
            this.ctx.fillStyle = fillColor;
            this.ctx.fillRect(x0, y0, width, height);

            // Vẽ viền
            this.ctx.strokeStyle = strokeColor;
            this.ctx.lineWidth = (this.hoveredWord === w) ? 2.5 : 1;
            this.ctx.strokeRect(x0, y0, width, height);
        });
    }

    _handleMouseMove(e) {
        const rect = this.canvas.getBoundingClientRect();
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;

        // Hit-test tìm word dưới chuột
        const found = this.words.find(w => {
            if (!w.bbox) return false;
            const x0 = w.bbox.x0 * this.scale;
            const y0 = w.bbox.y0 * this.scale;
            const x1 = w.bbox.x1 * this.scale;
            const y1 = w.bbox.y1 * this.scale;
            return mouseX >= x0 && mouseX <= x1 && mouseY >= y0 && mouseY <= y1;
        });

        const tooltip = document.getElementById('xaiTooltip');
        if (found) {
            this.hoveredWord = found;
            const conf = Math.round(found.confidence || 0);
            const confColor = conf >= 85 ? '#22c55e' : (conf >= 60 ? '#f59e0b' : '#ef4444');

            if (tooltip) {
                tooltip.style.display = 'block';
                tooltip.style.left = `${e.clientX + 14}px`;
                tooltip.style.top = `${e.clientY + 14}px`;
                tooltip.innerHTML = `
                    <div style="font-weight:bold;color:#38bdf8;">"${found.text}"</div>
                    <div style="margin-top:2px;">Độ tin cậy: <span style="color:${confColor};font-weight:bold;">${conf}%</span></div>
                `;
            }
        } else {
            this.hoveredWord = null;
            if (tooltip) tooltip.style.display = 'none';
        }

        this._render();
    }

    _handleMouseLeave() {
        this.hoveredWord = null;
        const tooltip = document.getElementById('xaiTooltip');
        if (tooltip) tooltip.style.display = 'none';
        this._render();
    }

    close() {
        if (this.modalEl) this.modalEl.classList.remove('open');
        const tooltip = document.getElementById('xaiTooltip');
        if (tooltip) tooltip.style.display = 'none';
    }
}

// Global attachment
if (typeof window !== 'undefined') {
    window.XAIConfidenceOverlay = XAIConfidenceOverlay;
}
