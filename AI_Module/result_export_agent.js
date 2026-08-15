/**
 * AI_Module/result_export_agent.js
 * Agent 5: Result Processor & Export Agent
 * Xử lý output per-file, cho phép export dữ liệu JSON/CSV/Clipboard
 */

class ResultExportAgent {

    /**
     * Chuẩn hóa kết quả phân tích của 1 file thành structured data
     * @param {Object} inspectionResult - Kết quả từ Agent 3
     * @returns {Object} - Dữ liệu chuẩn hóa cho UI + export
     */
    static formatFileResult(inspectionResult) {
        const fields = [];

        // Trường đã có dữ liệu
        (inspectionResult.foundFields || []).forEach(f => {
            fields.push({
                name: f.fieldName,
                value: f.extractedValue || '',
                status: 'filled',
                key: f.fieldKey || f.fieldName
            });
        });

        // Trường phát hiện nhưng trống
        (inspectionResult.missingFields || []).forEach(f => {
            fields.push({
                name: f.fieldName,
                value: '',
                status: 'empty',
                key: f.fieldKey || f.fieldName
            });
        });

        return {
            fileName: inspectionResult.fileName,
            detectedType: inspectionResult.model ? inspectionResult.model.name : 'Không nhận diện',
            detectedLabel: inspectionResult.model ? inspectionResult.model.label : '',
            isRecognized: inspectionResult.isRecognized || false,
            fields: fields,
            totalDetected: fields.length,
            filledCount: fields.filter(f => f.status === 'filled').length,
            emptyCount: fields.filter(f => f.status === 'empty').length,
            scorePercent: inspectionResult.scorePercent || 0,
            status: inspectionResult.status,
            verdict: inspectionResult.agentVerdict || '',
            advice: inspectionResult.actionAdvice || ''
        };
    }

    /**
     * Format tất cả kết quả
     * @param {Array} inspectionResults - Mảng kết quả từ các file
     * @returns {Object} - Tổng hợp
     */
    static formatAllResults(inspectionResults) {
        const files = inspectionResults.map(r => ResultExportAgent.formatFileResult(r));
        const totalFiles = files.length;
        const passedFiles = files.filter(f => f.status === 'VALID').length;
        const totalFields = files.reduce((sum, f) => sum + f.totalDetected, 0);
        const filledFields = files.reduce((sum, f) => sum + f.filledCount, 0);
        const emptyFields = files.reduce((sum, f) => sum + f.emptyCount, 0);

        return {
            summary: {
                totalFiles,
                passedFiles,
                failedFiles: totalFiles - passedFiles,
                totalFields,
                filledFields,
                emptyFields,
                overallScore: totalFields > 0 ? Math.round((filledFields / totalFields) * 100) : 100
            },
            files: files,
            exportedAt: new Date().toISOString()
        };
    }

    /**
     * Xuất dữ liệu JSON và tải xuống
     */
    static exportJSON(allResults) {
        const data = ResultExportAgent.formatAllResults(allResults);
        const jsonStr = JSON.stringify(data, null, 2);
        const blob = new Blob([jsonStr], { type: 'application/json;charset=utf-8' });
        ResultExportAgent._downloadBlob(blob, `kiem_tra_ho_so_${ResultExportAgent._timestamp()}.json`);
        return data;
    }

    /**
     * Xuất dữ liệu CSV và tải xuống
     */
    static exportCSV(allResults) {
        const data = ResultExportAgent.formatAllResults(allResults);
        let csv = '\uFEFF'; // BOM for Excel UTF-8
        csv += 'Tên file,Loại phiếu,Tên trường,Giá trị,Trạng thái\n';

        data.files.forEach(file => {
            file.fields.forEach(field => {
                const escapedValue = (field.value || '').replace(/"/g, '""');
                const statusText = field.status === 'filled' ? 'Đã điền' : 'Còn trống';
                csv += `"${file.fileName}","${file.detectedType}","${field.name}","${escapedValue}","${statusText}"\n`;
            });
            // Dòng trống giữa các file
            if (file.fields.length === 0) {
                csv += `"${file.fileName}","${file.detectedType}","(Không phát hiện trường)","",""\n`;
            }
        });

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8' });
        ResultExportAgent._downloadBlob(blob, `kiem_tra_ho_so_${ResultExportAgent._timestamp()}.csv`);
        return csv;
    }

    /**
     * Xuất JSON cho 1 file duy nhất
     */
    static exportSingleJSON(inspectionResult) {
        const data = ResultExportAgent.formatFileResult(inspectionResult);
        const jsonStr = JSON.stringify(data, null, 2);
        const safeName = data.fileName.replace(/[^a-zA-Z0-9_\-]/g, '_');
        const blob = new Blob([jsonStr], { type: 'application/json;charset=utf-8' });
        ResultExportAgent._downloadBlob(blob, `${safeName}_${ResultExportAgent._timestamp()}.json`);
        return data;
    }

    /**
     * Xuất CSV cho 1 file duy nhất
     */
    static exportSingleCSV(inspectionResult) {
        const data = ResultExportAgent.formatFileResult(inspectionResult);
        let csv = '\uFEFF';
        csv += 'Tên trường,Giá trị,Trạng thái\n';

        data.fields.forEach(field => {
            const escapedValue = (field.value || '').replace(/"/g, '""');
            const statusText = field.status === 'filled' ? 'Đã điền' : 'Còn trống';
            csv += `"${field.name}","${escapedValue}","${statusText}"\n`;
        });

        const safeName = data.fileName.replace(/[^a-zA-Z0-9_\-]/g, '_');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8' });
        ResultExportAgent._downloadBlob(blob, `${safeName}_${ResultExportAgent._timestamp()}.csv`);
        return csv;
    }

    /**
     * Copy kết quả vào clipboard dạng text
     */
    static async copyToClipboard(allResults) {
        const data = ResultExportAgent.formatAllResults(allResults);
        let text = `KẾT QUẢ KIỂM TRA HỒ SƠ — ${data.exportedAt}\n`;
        text += `════════════════════════════════════\n`;
        text += `Tổng: ${data.summary.totalFiles} file | Đạt: ${data.summary.passedFiles} | Cần sửa: ${data.summary.failedFiles}\n`;
        text += `Trường: ${data.summary.filledFields}/${data.summary.totalFields} đã điền (${data.summary.overallScore}%)\n\n`;

        data.files.forEach(file => {
            text += `📄 ${file.fileName} — ${file.detectedType}\n`;
            text += `   Trạng thái: ${file.status === 'VALID' ? '✅ Đạt' : '⚠️ Cần bổ sung'}\n`;
            file.fields.forEach(field => {
                const icon = field.status === 'filled' ? '✅' : '❌';
                text += `   ${icon} ${field.name}: ${field.value || '(trống)'}\n`;
            });
            text += '\n';
        });

        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (err) {
            console.warn('Clipboard write failed:', err);
            // Fallback
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            return true;
        }
    }

    /**
     * Tạo timestamp string cho tên file
     */
    static _timestamp() {
        const d = new Date();
        return `${d.getFullYear()}${String(d.getMonth()+1).padStart(2,'0')}${String(d.getDate()).padStart(2,'0')}_${String(d.getHours()).padStart(2,'0')}${String(d.getMinutes()).padStart(2,'0')}`;
    }

    /**
     * Tải blob xuống dưới dạng file
     */
    static _downloadBlob(blob, fileName) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = fileName;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
}

// Export cho global scope
if (typeof window !== 'undefined') {
    window.ResultExportAgent = ResultExportAgent;
}
