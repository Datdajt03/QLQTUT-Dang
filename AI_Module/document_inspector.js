/**
 * AI_Module/document_inspector.js
 * Cooperating AI Agent Suite: Edge AI OCR + Document Inspector Agent + Gap Diagnostic Agent
 * Phối hợp Agent phân tích, đưa ra Kết luận Thông minh & Khuyến nghị Khắc phục cụ thể cho từng tệp.
 */

class AIDocumentInspectorAgent {
    constructor(models = []) {
        this.models = models;
    }

    /**
     * Phân loại Mẫu Phiếu dựa vào tên file và nội dung OCR
     */
    classifyDocument(fileName, extractedText) {
        const textLower = ((extractedText || '') + ' ' + (fileName || '')).toLowerCase();
        let bestModel = null;
        let maxScore = 0;

        this.models.forEach(model => {
            let score = 0;
            model.typeKeywords.forEach(kw => {
                if (textLower.includes(kw.toLowerCase())) score += 2;
            });
            if (fileName.toLowerCase().includes(model.key) || fileName.toLowerCase().includes(model.name.toLowerCase())) {
                score += 3;
            }

            if (score > maxScore) {
                maxScore = score;
                bestModel = model;
            }
        });

        // Yêu cầu tối thiểu 2 điểm để gán vào Mẫu phiếu tiêu chuẩn
        return maxScore >= 2 ? bestModel : null;
    }

    /**
     * Trích xuất chuỗi văn bản dữ liệu ngay sau từ khóa
     */
    extractValueSnippet(fullText, keywords) {
        if (!fullText) return null;
        const lines = fullText.split(/\r?\n/);
        for (let kw of keywords) {
            const regex = new RegExp(kw + '[\\s\\:\\-\\=]*([^\\n\\r;]{1,70})', 'i');
            for (let line of lines) {
                const match = line.match(regex);
                if (match && match[1] && match[1].trim().length > 0) {
                    const cleaned = this.cleanFieldValue(match[1]);
                    if (cleaned) return cleaned;
                }
            }
        }
        for (let kw of keywords) {
            const regex = new RegExp(kw + '[\\s\\:\\-\\=]*([^\\n\\r;]{1,70})', 'i');
            const match = fullText.match(regex);
            if (match && match[1] && match[1].trim().length > 0) {
                const cleaned = this.cleanFieldValue(match[1]);
                if (cleaned) return cleaned;
            }
        }
        return null;
    }

    /**
     * Lọc bỏ chấm lửng (.....), gạch dưới (_____) và ký tự rác của ô trống
     */
    cleanFieldValue(val) {
        if (!val) return null;
        let s = val.replace(/^[\s\:\=\-\_\.]+|[\s\_\.\-\:]+$/g, '').trim();
        s = s.replace(/^[\.\_]{2,}$/g, '').trim();
        if (s.length === 0 || /^[\.\_\-\s]+$/.test(s)) return null;
        return s;
    }

    /**
     * Agent 1: Semantic Document Synopsis (Nhận diện loại Văn bản & Trích xuất Tiêu đề)
     */
    generateDocumentSynopsis(fileName, text) {
        const lines = (text || '').split(/\r?\n/).map(l => l.trim()).filter(l => l.length > 0);
        let titleLine = '';
        for (let i = 0; i < Math.min(lines.length, 10); i++) {
            const lUpper = lines[i].toUpperCase();
            if (lUpper.includes('CỘNG HÒA') || lUpper.includes('ĐỘC LẬP') || lUpper.includes('ĐẢNG CỘNG SẢN')) continue;
            if (lUpper.length >= 4 && (lUpper.includes('BẢN') || lUpper.includes('GIẤY') || lUpper.includes('PHIẾU') || lUpper.includes('SƠ YẾU') || lUpper.includes('ĐƠN') || lUpper.includes('TỜ TRÌNH') || lUpper.includes('BÁO CÁO') || lUpper.includes('CHỨNG NHẬN') || lUpper.includes('QUYẾT ĐỊNH'))) {
                titleLine = lines[i];
                break;
            }
        }
        if (!titleLine && lines.length > 0) {
            titleLine = lines[0];
        }
        if (!titleLine) {
            titleLine = fileName.replace(/\.[^/.]+$/, '').replace(/[\_\-]/g, ' ');
        }
        return titleLine;
    }

    /**
     * Agent 2: Dynamic Form Field Extractor (Trích xuất các cặp Nhãn-Giá trị trong tệp)
     */
    extractUniversalFormStructure(fileName, extractedText) {
        const text = extractedText || '';
        const docTitle = this.generateDocumentSynopsis(fileName, text);
        const dynamicFields = [];
        const labelRegex = /(?:^|[\n\r])\s*([A-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚÝĐa-zàáâãèéêìíòóôõùúýđ0-9\s\/\(\)\.\,]{2,45})[\:\=]\s*([^\n\r]*)/g;

        let match;
        const seenLabels = new Set();

        while ((match = labelRegex.exec(text)) !== null) {
            const labelRaw = match[1].trim();
            const valRaw = match[2] ? match[2].trim() : '';

            if (labelRaw.toLowerCase().includes('cộng hòa') || labelRaw.toLowerCase().includes('độc lập') || labelRaw.length < 2) {
                continue;
            }

            const labelKey = labelRaw.toLowerCase();
            if (seenLabels.has(labelKey)) continue;
            seenLabels.add(labelKey);

            const cleanedVal = this.cleanFieldValue(valRaw);
            const isFilled = cleanedVal !== null && cleanedVal.length > 0;

            dynamicFields.push({
                fieldName: labelRaw,
                found: isFilled,
                extractedValue: cleanedVal
            });
        }

        return {
            docTitle: docTitle,
            dynamicFields: dynamicFields
        };
    }

    /**
     * Agent 3: Gap Diagnostic & Executive AI Verdict Agent (Agent Đánh giá & Kết luận Thông minh)
     */
    inspectDocumentFile(fileName, extractedText, modelOverride = null) {
        const model = modelOverride || this.classifyDocument(fileName, extractedText);
        const universalStruct = this.extractUniversalFormStructure(fileName, extractedText);
        const textLower = ((extractedText || '') + ' ' + (fileName || '')).toLowerCase();

        // 1. Trường hợp tệp thuộc Mô hình Bắt buộc của Đảng vụ
        if (model) {
            const foundFields = [];     // Keyword tìm thấy + có giá trị
            const missingFields = [];   // Keyword tìm thấy nhưng giá trị trống
            // Keyword không tìm thấy → bỏ qua (không báo thiếu)

            model.requiredFields.forEach(field => {
                const keywordDetected = field.keywords.some(kw => textLower.includes(kw.toLowerCase()));
                const valSnippet = keywordDetected ? this.extractValueSnippet(extractedText, field.keywords) : null;

                if (keywordDetected && valSnippet !== null) {
                    // Agent phát hiện trường trên phiếu VÀ có dữ liệu
                    foundFields.push({
                        fieldKey: field.fieldKey,
                        fieldName: field.fieldName,
                        found: true,
                        extractedValue: valSnippet,
                        detectedOnForm: true
                    });
                } else if (keywordDetected && valSnippet === null) {
                    // Agent phát hiện trường trên phiếu NHƯNG ô trống / chưa điền
                    missingFields.push({
                        fieldKey: field.fieldKey,
                        fieldName: field.fieldName,
                        found: false,
                        extractedValue: null,
                        detectedOnForm: true
                    });
                }
                // Nếu keyword không xuất hiện → trường này không có trên phiếu → bỏ qua
            });

            const detectedCount = foundFields.length + missingFields.length;
            const foundCount = foundFields.length;
            const scorePercent = detectedCount > 0 ? Math.round((foundCount / detectedCount) * 100) : 100;
            let status = missingFields.length === 0 ? 'VALID' : 'INCOMPLETE';

            // AI Agent Reasoning: Đưa ra Kết luận Nhận xét & Khuyến nghị tự động
            let agentVerdict = '';
            let actionAdvice = '';

            if (status === 'VALID') {
                agentVerdict = `AI Inspector Agent Kết luận: Tệp "${fileName}" khớp chuẩn Mẫu "${model.name}". Đã quét và xác nhận ${foundCount} trường thông tin đầy đủ (${scorePercent}%).`;
                actionAdvice = `Tệp hợp lệ. Đã sẵn sàng gửi duyệt chính thức.`;
            } else {
                agentVerdict = `AI Inspector Agent Cảnh báo: Tệp "${fileName}" thuộc Mẫu "${model.name}" — phát hiện ${missingFields.length} ô thông tin còn trống.`;
                actionAdvice = `Vui lòng điền bổ sung: [${missingFields.map(f => f.fieldName).join(', ')}].`;
            }

            return {
                fileName: fileName,
                model: model,
                isRecognized: true,
                foundFields: foundFields,
                missingFields: missingFields,
                totalFields: detectedCount,
                foundCount: foundCount,
                scorePercent: scorePercent,
                status: status,
                agentVerdict: agentVerdict,
                actionAdvice: actionAdvice,
                summary: agentVerdict
            };
        }

        // 2. Trường hợp tệp tùy chỉnh / Không khớp 5 mẫu mặc định
        const foundFields = [];
        const missingFields = [];

        universalStruct.dynamicFields.forEach(f => {
            if (f.found) {
                foundFields.push(f);
            } else {
                missingFields.push(f);
            }
        });

        const total = universalStruct.dynamicFields.length;
        const foundCount = foundFields.length;
        const scorePercent = total > 0 ? Math.round((foundCount / total) * 100) : 0;
        let status = (missingFields.length === 0 && total > 0) ? 'VALID' : 'INCOMPLETE';

        let agentVerdict = '';
        let actionAdvice = '';

        if (total === 0) {
            status = 'UNRECOGNIZED';
            agentVerdict = `AI Inspector Agent Cảnh báo: Tệp "${fileName}" (Tiêu đề: "${universalStruct.docTitle}") chưa phát hiện được ô điền thông tin dạng [Nhãn]: [Nội dung].`;
            actionAdvice = `Kiểm tra lại chất lượng tệp scan/ảnh chụp hoặc đảm bảo văn bản có ô nhãn rõ ràng.`;
        } else if (status === 'VALID') {
            agentVerdict = `AI Inspector Agent Đánh giá: Tệp phiếu tùy chỉnh "${universalStruct.docTitle}" đã được điền đủ ${foundCount}/${total} nhãn thông tin.`;
            actionAdvice = `Tệp tùy chỉnh hợp lệ.`;
        } else {
            agentVerdict = `AI Inspector Agent Cảnh báo: Tệp phiếu "${universalStruct.docTitle}" bị trống ${missingFields.length}/${total} nhãn thông tin.`;
            actionAdvice = `Yêu cầu điền bổ sung nội dung cho các nhãn đang trống: [${missingFields.map(f => f.fieldName).join(', ')}].`;
        }

        return {
            fileName: fileName,
            model: {
                key: 'custom_form',
                name: `Tệp Phiếu: ${universalStruct.docTitle}`,
                label: '[Phiếu tùy chỉnh]'
            },
            isRecognized: false,
            foundFields: foundFields,
            missingFields: missingFields,
            totalFields: total,
            foundCount: foundCount,
            scorePercent: scorePercent,
            status: status,
            agentVerdict: agentVerdict,
            actionAdvice: actionAdvice,
            summary: agentVerdict
        };
    }

    /**
     * Agent 4: Portfolio Executive Synthesis Agent (Agent Tổng hợp Báo cáo Toàn bộ Bộ Hồ sơ)
     */
    inspectPortfolio(uploadedFileList) {
        let modelStatusMap = {};
        this.models.forEach(m => {
            modelStatusMap[m.key] = {
                model: m,
                uploadedFiles: [],
                inspections: [],
                status: 'MISSING',
                missingFields: [],
                foundFields: []
            };
        });

        let customFormInspections = [];

        uploadedFileList.forEach(fileItem => {
            const inspection = this.inspectDocumentFile(fileItem.name, fileItem.extractedText);
            fileItem.inspectionResult = inspection;

            if (inspection.isRecognized && inspection.model) {
                fileItem.matchedModel = inspection.model;
                const mKey = inspection.model.key;
                modelStatusMap[mKey].uploadedFiles.push(fileItem);
                modelStatusMap[mKey].inspections.push(inspection);
            } else {
                customFormInspections.push(inspection);
            }
        });

        let missingModelCount = 0;
        let incompleteModelCount = 0;
        let validModelCount = 0;

        this.models.forEach(m => {
            const entry = modelStatusMap[m.key];
            if (entry.uploadedFiles.length === 0) {
                // Chưa nộp phiếu → chỉ đánh dấu MISSING, không liệt kê trường
                entry.status = 'MISSING';
                missingModelCount++;
                entry.missingFields = [];
                entry.foundFields = [];
            } else {
                // Merge kết quả từ tất cả inspections với 3 trạng thái:
                // 'found' = có keyword + có giá trị
                // 'empty' = có keyword + trống giá trị  
                // 'not_detected' = không tìm thấy keyword
                let fieldStateMap = {};
                let fieldSnippetMap = {};

                m.requiredFields.forEach(f => {
                    fieldStateMap[f.fieldKey] = 'not_detected';
                    fieldSnippetMap[f.fieldKey] = null;
                });

                entry.inspections.forEach(insp => {
                    insp.foundFields.forEach(ff => {
                        fieldStateMap[ff.fieldKey] = 'found';
                        if (ff.extractedValue) fieldSnippetMap[ff.fieldKey] = ff.extractedValue;
                    });
                    insp.missingFields.forEach(mf => {
                        // Chỉ đánh dấu 'empty' nếu chưa được 'found' ở inspection khác
                        if (fieldStateMap[mf.fieldKey] !== 'found') {
                            fieldStateMap[mf.fieldKey] = 'empty';
                        }
                    });
                });

                const foundList = [];
                const missingList = [];

                m.requiredFields.forEach(f => {
                    if (fieldStateMap[f.fieldKey] === 'found') {
                        foundList.push({
                            fieldKey: f.fieldKey,
                            fieldName: f.fieldName,
                            extractedValue: fieldSnippetMap[f.fieldKey]
                        });
                    } else if (fieldStateMap[f.fieldKey] === 'empty') {
                        // Chỉ báo thiếu khi keyword CÓ trên phiếu nhưng giá trị trống
                        missingList.push({
                            fieldKey: f.fieldKey,
                            fieldName: f.fieldName
                        });
                    }
                    // 'not_detected' → bỏ qua, không báo thiếu
                });

                entry.foundFields = foundList;
                entry.missingFields = missingList;

                if (missingList.length > 0) {
                    entry.status = 'INCOMPLETE';
                    incompleteModelCount++;
                } else {
                    entry.status = 'VALID';
                    validModelCount++;
                }
            }
        });

        // AI Agent Synthesis Executive Summary
        const totalUploaded = uploadedFileList.length;
        let executiveSummary = '';
        let systemStatus = 'READY';

        if (missingModelCount === 0 && incompleteModelCount === 0) {
            executiveSummary = `🤖 AI Inspector Agent Kết luận: Bộ hồ sơ hoàn toàn ĐẠT CHUẨN 100%. Đã nộp đầy đủ 5/5 Mẫu phiếu bắt buộc với 100% các trường dữ liệu chi tiết.`;
            systemStatus = 'PASSED';
        } else {
            executiveSummary = `🤖 AI Inspector Agent Kết luận: Bộ hồ sơ CHƯA ĐẠT CHUẨN. Còn thiếu ${missingModelCount} loại phiếu bắt buộc và ${incompleteModelCount} phiếu bị thiếu trường thông tin chi tiết.`;
            systemStatus = 'NEEDS_FIX';
        }

        return {
            modelStatusMap: modelStatusMap,
            customFormInspections: customFormInspections,
            missingModelCount: missingModelCount,
            incompleteModelCount: incompleteModelCount,
            validModelCount: validModelCount,
            totalUploaded: totalUploaded,
            executiveSummary: executiveSummary,
            systemStatus: systemStatus,
            isFullyValid: (missingModelCount === 0 && incompleteModelCount === 0 && customFormInspections.every(c => c.status === 'VALID'))
        };
    }
}

// Bảo tồn tương thích
const DocumentFieldInspector = AIDocumentInspectorAgent;

if (typeof window !== 'undefined') {
    window.DocumentFieldInspector = DocumentFieldInspector;
    window.AIDocumentInspectorAgent = AIDocumentInspectorAgent;
}
