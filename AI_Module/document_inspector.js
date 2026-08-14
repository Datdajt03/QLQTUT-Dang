/**
 * AI_Module/document_inspector.js
 * Module Chuyên dụng: Thẩm định & Báo cáo Trường Thông tin Thiếu trong Mẫu Phiếu (Document Field Inspector Engine)
 */

class DocumentFieldInspector {
    constructor(documentModels = []) {
        this.models = documentModels;
    }

    /**
     * Nhận diện Mẫu Phiếu chuẩn dựa vào tên file và nội dung OCR
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

        return bestModel;
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
                    return match[1].trim();
                }
            }
        }
        for (let kw of keywords) {
            const regex = new RegExp(kw + '[\\s\\:\\-\\=]*([^\\n\\r;]{1,70})', 'i');
            const match = fullText.match(regex);
            if (match && match[1] && match[1].trim().length > 0) {
                return match[1].trim();
            }
        }
        return null;
    }

    /**
     * Soi chi tiết từng trường thông tin trong TỆP THỰC TẾ theo Mẫu Phiếu
     */
    inspectDocumentFile(fileName, extractedText, modelOverride = null) {
        const model = modelOverride || this.classifyDocument(fileName, extractedText);
        const textLower = ((extractedText || '') + ' ' + (fileName || '')).toLowerCase();

        if (!model) {
            return {
                fileName: fileName,
                model: null,
                isRecognized: false,
                foundFields: [],
                missingFields: [],
                scorePercent: 0,
                status: 'UNRECOGNIZED',
                summary: `Tệp "${fileName}" chưa thể phân loại chính xác vào Mẫu phiếu nào.`
            };
        }

        const foundFields = [];
        const missingFields = [];

        model.requiredFields.forEach(field => {
            const found = field.keywords.some(kw => textLower.includes(kw.toLowerCase()));
            const valSnippet = found ? this.extractValueSnippet(extractedText, field.keywords) : null;

            const item = {
                fieldKey: field.fieldKey,
                fieldName: field.fieldName,
                found: found,
                extractedValue: valSnippet
            };

            if (found) {
                foundFields.push(item);
            } else {
                missingFields.push(item);
            }
        });

        const total = model.requiredFields.length;
        const foundCount = foundFields.length;
        const scorePercent = total > 0 ? Math.round((foundCount / total) * 100) : 0;
        const status = missingFields.length === 0 ? 'VALID' : 'INCOMPLETE';

        return {
            fileName: fileName,
            model: model,
            isRecognized: true,
            foundFields: foundFields,
            missingFields: missingFields,
            totalFields: total,
            foundCount: foundCount,
            scorePercent: scorePercent,
            status: status,
            summary: status === 'VALID'
                ? `Tệp phiếu "${model.name}" đạt ĐẦY ĐỦ 100% (${foundCount}/${total} trường).`
                : `Tệp phiếu "${model.name}" bị THIẾU ${missingFields.length}/${total} trường thông tin chi tiết: [${missingFields.map(f => f.fieldName).join(', ')}].`
        };
    }

    /**
     * Tổng hợp kết quả thẩm định toàn bộ tệp nộp cho 5 Mẫu Phiếu bắt buộc
     */
    inspectPortfolio(uploadedFileList) {
        let modelStatusMap = {};
        this.models.forEach(m => {
            modelStatusMap[m.key] = {
                model: m,
                uploadedFiles: [],
                inspections: [],
                status: 'MISSING', // 'MISSING', 'INCOMPLETE', 'VALID'
                missingFields: [],
                foundFields: []
            };
        });

        let unclassifiedFiles = [];

        // Nạp các tệp vào Model tương ứng
        uploadedFileList.forEach(fileItem => {
            const inspection = this.inspectDocumentFile(fileItem.name, fileItem.extractedText);
            fileItem.inspectionResult = inspection;

            if (inspection.isRecognized && inspection.model) {
                fileItem.matchedModel = inspection.model;
                const mKey = inspection.model.key;
                modelStatusMap[mKey].uploadedFiles.push(fileItem);
                modelStatusMap[mKey].inspections.push(inspection);
            } else {
                unclassifiedFiles.push(fileItem);
            }
        });

        // Tổng hợp trạng thái từng Mẫu Phiếu bắt buộc
        let missingModelCount = 0;
        let incompleteModelCount = 0;
        let validModelCount = 0;

        this.models.forEach(m => {
            const entry = modelStatusMap[m.key];
            if (entry.uploadedFiles.length === 0) {
                entry.status = 'MISSING';
                missingModelCount++;
                entry.missingFields = m.requiredFields.map(f => ({ fieldKey: f.fieldKey, fieldName: f.fieldName }));
            } else {
                // Đã có tệp nộp cho Mẫu phiếu này, hợp nhất kết quả soi
                let mergedFoundMap = {};
                let mergedSnippetMap = {};

                m.requiredFields.forEach(f => {
                    mergedFoundMap[f.fieldKey] = false;
                    mergedSnippetMap[f.fieldKey] = null;
                });

                entry.inspections.forEach(insp => {
                    insp.foundFields.forEach(ff => {
                        mergedFoundMap[ff.fieldKey] = true;
                        if (ff.extractedValue) mergedSnippetMap[ff.fieldKey] = ff.extractedValue;
                    });
                });

                const foundList = [];
                const missingList = [];

                m.requiredFields.forEach(f => {
                    if (mergedFoundMap[f.fieldKey]) {
                        foundList.push({
                            fieldKey: f.fieldKey,
                            fieldName: f.fieldName,
                            extractedValue: mergedSnippetMap[f.fieldKey]
                        });
                    } else {
                        missingList.push({
                            fieldKey: f.fieldKey,
                            fieldName: f.fieldName
                        });
                    }
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

        return {
            modelStatusMap: modelStatusMap,
            unclassifiedFiles: unclassifiedFiles,
            missingModelCount: missingModelCount,
            incompleteModelCount: incompleteModelCount,
            validModelCount: validModelCount,
            isFullyValid: (missingModelCount === 0 && incompleteModelCount === 0)
        };
    }
}

if (typeof window !== 'undefined') {
    window.DocumentFieldInspector = DocumentFieldInspector;
}
