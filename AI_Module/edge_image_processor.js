/**
 * AI_Module/edge_image_processor.js
 * Agent 0: Edge Image Preprocessor
 * Tiền xử lý ảnh bằng Canvas API trước khi đưa vào Tesseract OCR
 * Cải thiện chất lượng OCR cho ảnh chụp từ điện thoại, scan mờ, ảnh xoay...
 */

class EdgeImageProcessor {

    /**
     * Tiền xử lý ảnh: grayscale → contrast → denoise → binarize
     * @param {File|Blob} imageFile - File ảnh gốc
     * @returns {Promise<Blob>} - Ảnh đã xử lý (PNG Blob)
     */
    static async preprocess(imageFile) {
        // Chỉ xử lý file ảnh, bỏ qua PDF
        if (imageFile.type === 'application/pdf') {
            return imageFile;
        }

        try {
            const img = await EdgeImageProcessor._loadImage(imageFile);
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);

            let imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

            // Pipeline xử lý ảnh
            imageData = EdgeImageProcessor._toGrayscale(imageData);
            imageData = EdgeImageProcessor._enhanceContrast(imageData, 1.5);
            imageData = EdgeImageProcessor._reduceNoise(imageData);
            imageData = EdgeImageProcessor._adaptiveThreshold(imageData);

            ctx.putImageData(imageData, 0, 0);

            return new Promise(resolve => {
                canvas.toBlob(blob => {
                    resolve(blob || imageFile);
                }, 'image/png');
            });
        } catch (err) {
            console.warn('[Agent 0] Image preprocessing failed, using original:', err);
            return imageFile;
        }
    }

    /**
     * Load File thành Image element
     */
    static _loadImage(file) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => {
                URL.revokeObjectURL(img.src);
                resolve(img);
            };
            img.onerror = reject;
            img.src = URL.createObjectURL(file);
        });
    }

    /**
     * Chuyển ảnh sang Grayscale (luminance-based)
     */
    static _toGrayscale(imageData) {
        const data = imageData.data;
        for (let i = 0; i < data.length; i += 4) {
            const gray = 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
            data[i] = data[i + 1] = data[i + 2] = gray;
        }
        return imageData;
    }

    /**
     * Tăng Contrast (linear stretch)
     * @param {number} factor - Hệ số contrast (1.0 = giữ nguyên, >1 = tăng)
     */
    static _enhanceContrast(imageData, factor) {
        const data = imageData.data;
        const intercept = 128 * (1 - factor);
        for (let i = 0; i < data.length; i += 4) {
            data[i] = Math.min(255, Math.max(0, factor * data[i] + intercept));
            data[i + 1] = Math.min(255, Math.max(0, factor * data[i + 1] + intercept));
            data[i + 2] = Math.min(255, Math.max(0, factor * data[i + 2] + intercept));
        }
        return imageData;
    }

    /**
     * Giảm nhiễu cơ bản (3x3 median filter cho grayscale)
     */
    static _reduceNoise(imageData) {
        const w = imageData.width;
        const h = imageData.height;
        const src = new Uint8ClampedArray(imageData.data);
        const dst = imageData.data;

        for (let y = 1; y < h - 1; y++) {
            for (let x = 1; x < w - 1; x++) {
                const neighbors = [];
                for (let dy = -1; dy <= 1; dy++) {
                    for (let dx = -1; dx <= 1; dx++) {
                        const idx = ((y + dy) * w + (x + dx)) * 4;
                        neighbors.push(src[idx]);
                    }
                }
                neighbors.sort((a, b) => a - b);
                const median = neighbors[4]; // giá trị giữa của 9 phần tử
                const idx = (y * w + x) * 4;
                dst[idx] = dst[idx + 1] = dst[idx + 2] = median;
            }
        }
        return imageData;
    }

    /**
     * Adaptive Threshold (Otsu's method simplified)
     * Chuyển ảnh sang binary (đen/trắng) để OCR tốt hơn
     */
    static _adaptiveThreshold(imageData) {
        const data = imageData.data;
        const total = data.length / 4;

        // Tính histogram
        const histogram = new Array(256).fill(0);
        for (let i = 0; i < data.length; i += 4) {
            histogram[data[i]]++;
        }

        // Otsu's threshold
        let sum = 0;
        for (let i = 0; i < 256; i++) sum += i * histogram[i];

        let sumB = 0, wB = 0, wF = 0;
        let maxVariance = 0, threshold = 128;

        for (let t = 0; t < 256; t++) {
            wB += histogram[t];
            if (wB === 0) continue;
            wF = total - wB;
            if (wF === 0) break;

            sumB += t * histogram[t];
            const mB = sumB / wB;
            const mF = (sum - sumB) / wF;
            const variance = wB * wF * (mB - mF) * (mB - mF);

            if (variance > maxVariance) {
                maxVariance = variance;
                threshold = t;
            }
        }

        // Áp dụng threshold
        for (let i = 0; i < data.length; i += 4) {
            const val = data[i] >= threshold ? 255 : 0;
            data[i] = data[i + 1] = data[i + 2] = val;
        }

        return imageData;
    }
}

// Export cho global scope
if (typeof window !== 'undefined') {
    window.EdgeImageProcessor = EdgeImageProcessor;
}
