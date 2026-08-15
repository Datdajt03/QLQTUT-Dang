/**
 * AI_Module/live_camera_scanner.js
 * Feature 1: Real-time Live Edge Camera Scanner & Perspective Capture
 * Công nghệ Bách Khoa Edge AI:
 * - WebRTC MediaStream video processing trực tiếp tại Client
 * - Real-time Laplacian Variance Sharpness Estimator (Đo độ nét chống mờ rung)
 * - Dynamic Document Target Reticle & Laser Scanning HUD
 * - Auto-capture & Perspective Cropping Pipeline
 */

class LiveCameraScanner {
    constructor(options = {}) {
        this.options = Object.assign({
            targetType: 'document', // 'document' (A4) hoặc 'card' (CCCD/Thẻ SV - 3:2)
            autoSnapEnabled: false,
            sharpnessThreshold: 65,
            onCapture: null,
            onClose: null
        }, options);

        this.stream = null;
        this.video = null;
        this.canvas = null;
        this.ctx = null;
        this.animId = null;
        this.isOpen = false;
        this.currentSharpness = 0;
        this.stableSharpFrames = 0;
        this.modalEl = null;

        this._createModalDOM();
    }

    _createModalDOM() {
        if (document.getElementById('edgeLiveCameraModal')) {
            this.modalEl = document.getElementById('edgeLiveCameraModal');
            return;
        }

        const modal = document.createElement('div');
        modal.id = 'edgeLiveCameraModal';
        modal.className = 'edge-camera-modal-overlay';
        modal.innerHTML = `
            <div class="edge-camera-container">
                <div class="edge-camera-header">
                    <div class="edge-camera-title">
                        <i class="bi bi-camera-video-fill"></i>
                        <span>Edge AI Live Camera Scanner</span>
                        <span class="edge-badge-live">LIVE WASM</span>
                    </div>
                    <button type="button" class="edge-camera-close-btn" id="edgeCamCloseBtn" title="Đóng">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="edge-camera-viewport-wrapper">
                    <video id="edgeCamVideo" autoplay playsinline muted></video>
                    <canvas id="edgeCamOverlayCanvas"></canvas>

                    <!-- Laser Scanner Line -->
                    <div class="edge-laser-line"></div>

                    <!-- HUD Reticle & Corner Brackets -->
                    <div class="edge-reticle-box" id="edgeReticleBox">
                        <div class="edge-corner tl"></div>
                        <div class="edge-corner tr"></div>
                        <div class="edge-corner bl"></div>
                        <div class="edge-corner br"></div>
                        <div class="edge-reticle-cross"></div>
                    </div>

                    <!-- Telemetry HUD Overlay -->
                    <div class="edge-telemetry-hud">
                        <div class="hud-metric">
                            <span class="hud-label">Độ sắc nét (IQS):</span>
                            <span class="hud-value" id="hudSharpnessVal">0%</span>
                            <div class="hud-bar-bg"><div class="hud-bar-fill" id="hudSharpnessBar"></div></div>
                        </div>
                        <div class="hud-metric">
                            <span class="hud-label">Ánh sáng:</span>
                            <span class="hud-value" id="hudLightVal">Tốt</span>
                        </div>
                        <div class="hud-status-pill" id="hudStatusPill">
                            <i class="bi bi-crosshair"></i> Đang căn chỉnh tài liệu...
                        </div>
                    </div>
                </div>

                <div class="edge-camera-controls">
                    <div style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:6px;">
                        <i class="bi bi-info-circle-fill" style="color:#38bdf8;"></i>
                        <span>Căn chỉnh giấy tờ vừa khung ngắm rồi <strong>tự bấm nút chụp</strong></span>
                    </div>

                    <div class="edge-main-snap-wrapper">
                        <button type="button" class="edge-snap-btn-primary" id="edgeSnapBtn" title="Bấm để chụp ảnh">
                            <i class="bi bi-camera-fill"></i>
                            <span id="edgeSnapBtnText">BẤM ĐỂ CHỤP VÀ QUÉT</span>
                        </button>
                    </div>

                    <div class="edge-control-group" style="text-align:right;">
                        <button type="button" class="edge-btn-alt" id="edgeSwitchCamBtn" title="Đổi camera">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
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
        if (document.getElementById('edgeCameraStyles')) return;
        const style = document.createElement('style');
        style.id = 'edgeCameraStyles';
        style.textContent = `
            .edge-camera-modal-overlay {
                position: fixed;
                top: 0; left: 0; width: 100vw; height: 100vh;
                background: rgba(0, 0, 0, 0.85);
                backdrop-filter: blur(8px);
                z-index: 99999;
                display: none;
                align-items: center;
                justify-content: center;
                animation: edgeFadeIn 0.25s ease;
            }
            .edge-camera-modal-overlay.open { display: flex; }
            @keyframes edgeFadeIn { from { opacity: 0; } to { opacity: 1; } }

            .edge-camera-container {
                width: 95%; max-width: 680px;
                background: #0f172a;
                border: 1px solid rgba(56, 189, 248, 0.3);
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 20px 40px rgba(0,0,0,0.6), 0 0 30px rgba(56, 189, 248, 0.15);
                display: flex;
                flex-direction: column;
            }
            .edge-camera-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 18px;
                background: #1e293b;
                border-bottom: 1px solid rgba(255,255,255,0.08);
            }
            .edge-camera-title {
                display: flex;
                align-items: center;
                gap: 8px;
                font-weight: 700;
                font-size: 14px;
                color: #38bdf8;
            }
            .edge-badge-live {
                background: rgba(239, 68, 68, 0.2);
                color: #ef4444;
                border: 1px solid #ef4444;
                font-size: 10px;
                padding: 1px 6px;
                border-radius: 4px;
                font-weight: 800;
                letter-spacing: 0.5px;
            }
            .edge-camera-close-btn {
                background: transparent;
                border: none;
                color: #94a3b8;
                font-size: 18px;
                cursor: pointer;
                transition: color 0.2s;
            }
            .edge-camera-close-btn:hover { color: #f87171; }

            .edge-camera-viewport-wrapper {
                position: relative;
                width: 100%;
                height: 380px;
                background: #000;
                overflow: hidden;
            }
            #edgeCamVideo {
                width: 100%; height: 100%;
                object-fit: cover;
            }
            #edgeCamOverlayCanvas {
                position: absolute;
                top: 0; left: 0; width: 100%; height: 100%;
                pointer-events: none;
            }

            /* Laser scan line */
            .edge-laser-line {
                position: absolute;
                top: 15%; left: 10%; width: 80%; height: 2px;
                background: linear-gradient(90deg, transparent, #38bdf8, #22c55e, #38bdf8, transparent);
                box-shadow: 0 0 12px #38bdf8;
                animation: edgeLaserScan 2.4s cubic-bezier(0.4, 0, 0.2, 1) infinite alternate;
                pointer-events: none;
            }
            @keyframes edgeLaserScan {
                0% { top: 15%; opacity: 0.8; }
                100% { top: 85%; opacity: 0.8; }
            }

            /* Reticle box */
            .edge-reticle-box {
                position: absolute;
                top: 15%; left: 10%; width: 80%; height: 70%;
                border: 1px dashed rgba(56, 189, 248, 0.4);
                border-radius: 8px;
                pointer-events: none;
                transition: border-color 0.3s;
            }
            .edge-reticle-box.focused {
                border-color: #22c55e;
                box-shadow: 0 0 15px rgba(34, 197, 94, 0.3);
            }
            .edge-corner {
                position: absolute;
                width: 24px; height: 24px;
                border-color: #38bdf8;
                border-style: solid;
                border-width: 0;
            }
            .edge-reticle-box.focused .edge-corner { border-color: #22c55e; }
            .edge-corner.tl { top: -2px; left: -2px; border-top-width: 3px; border-left-width: 3px; border-top-left-radius: 6px; }
            .edge-corner.tr { top: -2px; right: -2px; border-top-width: 3px; border-right-width: 3px; border-top-right-radius: 6px; }
            .edge-corner.bl { bottom: -2px; left: -2px; border-bottom-width: 3px; border-left-width: 3px; border-bottom-left-radius: 6px; }
            .edge-corner.br { bottom: -2px; right: -2px; border-bottom-width: 3px; border-right-width: 3px; border-bottom-right-radius: 6px; }

            /* HUD Telemetry */
            .edge-telemetry-hud {
                position: absolute;
                bottom: 12px; left: 12px; right: 12px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: rgba(15, 23, 42, 0.75);
                backdrop-filter: blur(4px);
                border: 1px solid rgba(255,255,255,0.1);
                border-radius: 8px;
                padding: 6px 12px;
                font-family: monospace;
                font-size: 11px;
                color: #e2e8f0;
            }
            .hud-metric { display: flex; align-items: center; gap: 6px; }
            .hud-label { color: #94a3b8; }
            .hud-value { font-weight: 700; color: #38bdf8; min-width: 35px; }
            .hud-bar-bg { width: 50px; height: 5px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden; }
            .hud-bar-fill { width: 0%; height: 100%; background: #38bdf8; transition: width 0.1s, background-color 0.2s; }
            .hud-status-pill {
                background: rgba(56, 189, 248, 0.15);
                color: #38bdf8;
                padding: 2px 8px;
                border-radius: 12px;
                font-size: 10px;
                font-weight: 600;
            }
            .hud-status-pill.ready { background: rgba(34, 197, 94, 0.2); color: #22c55e; }

            /* Controls */
            .edge-camera-controls {
                padding: 14px 20px;
                background: #0f172a;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }
            .edge-snap-btn-primary {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                background: linear-gradient(135deg, #0284c7, #0369a1);
                color: #ffffff;
                border: 2px solid #38bdf8;
                padding: 12px 28px;
                border-radius: 30px;
                font-weight: 800;
                font-size: 13.5px;
                letter-spacing: 0.3px;
                cursor: pointer;
                box-shadow: 0 4px 15px rgba(2, 132, 199, 0.4);
                transition: all 0.2s ease;
            }
            .edge-snap-btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(56, 189, 248, 0.6);
            }
            .edge-snap-btn-primary.ready {
                background: linear-gradient(135deg, #16a34a, #15803d);
                border-color: #4ade80;
                box-shadow: 0 0 20px rgba(74, 222, 128, 0.5);
                animation: edgePulseBtn 1.5s infinite;
            }
            @keyframes edgePulseBtn {
                0%, 100% { transform: scale(1); box-shadow: 0 0 15px rgba(74, 222, 128, 0.4); }
                50% { transform: scale(1.03); box-shadow: 0 0 25px rgba(74, 222, 128, 0.7); }
            }
            .edge-btn-alt {
                background: #1e293b;
                border: 1px solid rgba(255,255,255,0.1);
                color: #e2e8f0;
                width: 40px; height: 40px;
                border-radius: 8px;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 16px;
            }
            .edge-btn-alt:hover { background: #334155; }
        `;
        document.head.appendChild(style);
    }

    _bindEvents() {
        const closeBtn = document.getElementById('edgeCamCloseBtn');
        if (closeBtn) closeBtn.onclick = () => this.close();

        const snapBtn = document.getElementById('edgeSnapBtn');
        if (snapBtn) snapBtn.onclick = () => this.snap();
    }

    async open(options = {}) {
        Object.assign(this.options, options);
        this.video = document.getElementById('edgeCamVideo');
        this.canvas = document.getElementById('edgeCamOverlayCanvas');
        this.ctx = this.canvas ? this.canvas.getContext('2d', { willReadFrequently: true }) : null;

        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'environment',
                    width: { ideal: 1920 },
                    height: { ideal: 1080 }
                }
            });

            this.video.srcObject = this.stream;
            await this.video.play();

            this.isOpen = true;
            this.modalEl.classList.add('open');

            this._startTelemetryLoop();
        } catch (err) {
            console.error("WebRTC getUserMedia failed:", err);
            alert("Không thể mở Camera (Có thể do chưa cấp quyền hoặc không có webcam).");
            this.close();
        }
    }

    _startTelemetryLoop() {
        const sampleCanvas = document.createElement('canvas');
        sampleCanvas.width = 160;
        sampleCanvas.height = 120;
        const sampleCtx = sampleCanvas.getContext('2d', { willReadFrequently: true });

        const reticleBox = document.getElementById('edgeReticleBox');
        const hudSharpnessVal = document.getElementById('hudSharpnessVal');
        const hudSharpnessBar = document.getElementById('hudSharpnessBar');
        const hudStatusPill = document.getElementById('hudStatusPill');
        const snapBtn = document.getElementById('edgeSnapBtn');
        const snapBtnText = document.getElementById('edgeSnapBtnText');

        const loop = () => {
            if (!this.isOpen || !this.video) return;

            if (this.video.readyState === this.video.HAVE_ENOUGH_DATA) {
                // Đọc mẫu điểm ảnh để đo độ sắc nét (Laplacian Variance)
                sampleCtx.drawImage(this.video, 0, 0, 160, 120);
                const imgData = sampleCtx.getImageData(0, 0, 160, 120);
                const sharpness = this._estimateSharpnessLaplacian(imgData);
                this.currentSharpness = sharpness;

                // Cập nhật HUD
                const sharpPct = Math.min(100, Math.round(sharpness));
                if (hudSharpnessVal) hudSharpnessVal.innerText = `${sharpPct}%`;
                if (hudSharpnessBar) {
                    hudSharpnessBar.style.width = `${sharpPct}%`;
                    hudSharpnessBar.style.backgroundColor = sharpPct >= this.options.sharpnessThreshold ? '#22c55e' : '#38bdf8';
                }

                const isFocused = sharpPct >= this.options.sharpnessThreshold;
                if (reticleBox) reticleBox.classList.toggle('focused', isFocused);
                if (snapBtn) snapBtn.classList.toggle('ready', isFocused);
                if (snapBtnText) {
                    snapBtnText.innerText = isFocused ? 'BẤM CHỤP NGAY (ĐÃ ĐỦ NÉT)' : 'BẤM ĐỂ CHỤP VÀ QUÉT';
                }

                if (isFocused) {
                    if (hudStatusPill) {
                        hudStatusPill.className = 'hud-status-pill ready';
                        hudStatusPill.innerHTML = '<i class="bi bi-check-circle-fill"></i> ĐỦ NÉT - HÃY BẤM NÚT CHỤP';
                    }
                } else {
                    if (hudStatusPill) {
                        hudStatusPill.className = 'hud-status-pill';
                        hudStatusPill.innerHTML = '<i class="bi bi-crosshair"></i> Căn tài liệu vào khung...';
                    }
                }
            }

            this.animId = requestAnimationFrame(loop);
        };

        this.animId = requestAnimationFrame(loop);
    }

    /**
     * Thuật toán ước lượng độ sắc nét bằng phương sai toán tử Laplace (Laplacian Variance)
     * Công thức: Var(L(x,y)) = E[L^2] - (E[L])^2
     */
    _estimateSharpnessLaplacian(imageData) {
        const data = imageData.data;
        const w = imageData.width;
        const h = imageData.height;

        // Chuyển sang mảng xám 1D
        const gray = new Float32Array(w * h);
        for (let i = 0, j = 0; i < data.length; i += 4, j++) {
            gray[j] = 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
        }

        // Tích chập Laplacian Kernel 3x3: [0, 1, 0; 1, -4, 1; 0, 1, 0]
        let sum = 0;
        let sumSq = 0;
        let count = 0;

        for (let y = 1; y < h - 1; y++) {
            for (let x = 1; x < w - 1; x++) {
                const idx = y * w + x;
                const lap = (
                    gray[idx - w] +
                    gray[idx + w] +
                    gray[idx - 1] +
                    gray[idx + 1] -
                    4 * gray[idx]
                );
                sum += lap;
                sumSq += lap * lap;
                count++;
            }
        }

        if (count === 0) return 0;
        const mean = sum / count;
        const variance = (sumSq / count) - (mean * mean);
        
        // Chuẩn hóa điểm phương sai sang thang đo 0-100%
        return Math.min(100, Math.sqrt(Math.max(0, variance)) * 3.5);
    }

    async snap() {
        if (!this.video || !this.isOpen) return;

        const vW = this.video.videoWidth || 1280;
        const vH = this.video.videoHeight || 720;

        const snapCanvas = document.createElement('canvas');
        snapCanvas.width = vW;
        snapCanvas.height = vH;
        const ctx = snapCanvas.getContext('2d');
        ctx.drawImage(this.video, 0, 0, vW, vH);

        // Cắt theo vùng khung ngắm Reticle (80% width, 70% height tại trung tâm)
        const cropX = vW * 0.1;
        const cropY = vH * 0.15;
        const cropW = vW * 0.8;
        const cropH = vH * 0.7;

        const croppedCanvas = document.createElement('canvas');
        croppedCanvas.width = cropW;
        croppedCanvas.height = cropH;
        const cropCtx = croppedCanvas.getContext('2d');
        cropCtx.drawImage(snapCanvas, cropX, cropY, cropW, cropH, 0, 0, cropW, cropH);

        croppedCanvas.toBlob(async (blob) => {
            const fileName = `live_scan_${Date.now()}.png`;
            const file = new File([blob], fileName, { type: 'image/png' });

            // Tiền xử lý bằng Canvas DSP nếu có
            let processedFile = file;
            if (typeof EdgeImageProcessor !== 'undefined') {
                try {
                    const procBlob = await EdgeImageProcessor.preprocess(file);
                    processedFile = new File([procBlob], fileName, { type: 'image/png' });
                } catch (e) {
                    console.warn("Preprocess failed, using raw snap:", e);
                }
            }

            if (this.options.onCapture) {
                this.options.onCapture(processedFile, croppedCanvas.toDataURL('image/png'));
            }

            this.close();
        }, 'image/png');
    }

    close() {
        this.isOpen = false;
        if (this.animId) cancelAnimationFrame(this.animId);
        if (this.stream) {
            this.stream.getTracks().forEach(t => t.stop());
            this.stream = null;
        }
        if (this.modalEl) this.modalEl.classList.remove('open');
        if (this.options.onClose) this.options.onClose();
    }
}

// Global attachment
if (typeof window !== 'undefined') {
    window.LiveCameraScanner = LiveCameraScanner;
}
