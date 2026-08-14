/**
 * AI_Module/document_inspector.js
 * Module Chuyên dụng: Thẩm định & Báo cáo Trường Thông tin Thiếu Tổng quát cho MỌI Loại Phiếu
 * (Universal Dynamic Document Field Inspector Engine)
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
        // Nếu chỉ toàn dấu chấm, gạch dưới hoặc quá ngắn rác
        if (s.length === 0 || /^[\.\_\-\s]+$/.test(s)) return null;
        return s;
    }

    /**
     * Trích xuất Tiêu đề Phiếu và TỰ ĐỘNG SO LƯỢC MỌI TRƯỜNG THÔNG TIN DYNAMIC (Universal Form Extraction)
     * Giúp soi MỌI LOẠI PHIẾU bất kỳ do người dùng tải lên
     */
    extractUniversalFormStructure(fileName, extractedText) {
        const text = extractedText || '';
        const lines = text.split(/\r?\n/).map(l => l.trim()).filter(l => l.length > 0);

        // 1. Trích xuất Tiêu đề Phiếu từ các dòng đầu
        let docTitle = '';
        for (let i = 0; i < Math.min(lines.length, 8); i++) {
            const lineUpper = lines[i].toUpperCase();
            if (lineUpper.includes('PHỦ') || lineUpper.includes('ĐẢNG') || lineUpper.includes('CỘNG HÒA') || lineUpper.includes('ĐỘC LẬP')) {
                continue;
            }
            if (lineUpper.includes('PHIẾU') || lineUpper.includes('BẢN') || lineUpper.includes('GIẤY') || lineUpper.includes('SƠ YẾU') || lineUpper.includes('ĐƠN') || lineUpper.includes('TỜ TRÌNH') || lineUpper.includes('BÁO CÁO')) {
                docTitle = lines[i];
                break;
            }
        }
        if (!docTitle) {
            docTitle = fileName.replace(/\.[^/.]+$/, '').replace(/[\_\-]/g, ' ');
        }

        // 2. Thẩm định Mọi Nhãn Trường Dạng [Nhãn]: [Giá trị / Chấm lửng]
        const dynamicFields = [];
        const labelRegex = /(?:^|[\n\r])\s*([A-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚÝĐa-zàáâãèéêìíòóôõùúýđ0-9\s\/\(\)\.\,]{2,45})[\:\=]\s*([^\n\r]*)/g;
        
        let match;
        const seenLabels = new Set();

        while ((match = labelRegex.exec(text)) !== null) {
            const labelRaw = match[1].trim();
            const valRaw = match[2] ? match[2].trim() : '';

            // Loại bỏ các nhãn tiêu đề quốc hiệu rác
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
     * Soi chi tiết từng trường thông tin trong TỆP THỰC TẾ theo Model hoặc Mẫu Phiếu Tùy Chỉnh
     */
    inspectDocumentFile(fileName, extractedText, modelOverride = null) {
        const model = modelOverride || this.classifyDocument(fileName, extractedText);
        const universalStruct = this.extractUniversalFormStructure(fileName, extractedText);
        const textLower = ((extractedText || '') + ' ' + (fileName || '')).toLowerCase();

        // TRƯỜNG HỢP A: PHIẾU TÙY CHỈNH KHÔNG THUỘC 5 MODEL CỐ ĐỊNH (UNIVERSAL CUSTOM FORM)
        if (!model) {
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
            const status = (missingFields.length === 0 && total > 0) ? 'VALID' : 'INCOMPLETE';

            return {
                fileName: fileName,
                model: {
                    key: 'custom_form',
                    name: `Mẫu Phiếu Tùy Chỉnh: ${universalStruct.docTitle}`,
                    label: '[Phiếu tùy chỉnh]'
                },
                isRecognized: false, // Dạng phiếu tự do tùy chỉnh
                foundFields: foundFields,
                missingFields: missingFields,
                totalFields: total,
                foundCount: foundCount,
                scorePercent: scorePercent,
                status: status,
                summary: total === 0
                    ? `Tệp "${fileName}" chưa phát hiện được nhãn trường thông tin.`
                    : (status === 'VALID'
                        ? `Tệp phiếu "${universalStruct.docTitle}" đã điền ĐẦY ĐỦ (${foundCount}/${total} trường).`
                        : `Tệp phiếu "${universalStruct.docTitle}" đang THIẾU/TRỐNG ${missingFields.length}/${total} trường thông tin.`)
            };
        }

        // TRƯỜNG HỢP B: PHIẾU KHỚP VỚI MODEL ĐẢNG VỤ TIÊU CHUẨN
        const foundFields = [];
        const missingFields = [];

        model.requiredFields.forEach(field => {
            const found = field.keywords.some(kw => textLower.includes(kw.toLowerCase()));
            const valSnippet = found ? this.extractValueSnippet(extractedText, field.keywords) : null;

            const item = {
                fieldKey: field.fieldKey,
                fieldName: field.fieldName,
                found: (found && valSnippet !== null),
                extractedValue: valSnippet
            };

            if (item.found) {
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
     * Tổng hợp kết quả thẩm định toàn bộ tệp nộp (Hỗ trợ cả 5 Mẫu Bắt buộc & Các Mẫu Phiếu Tùy Chỉnh)
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

        let customFormInspections = [];

        // Nạp các tệp vào Model tương ứng hoặc Mẫu tùy chỉnh
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
            customFormInspections: customFormInspections,
            missingModelCount: missingModelCount,
            incompleteModelCount: incompleteModelCount,
            validModelCount: validModelCount,
            isFullyValid: (missingModelCount === 0 && incompleteModelCount === 0 && customFormInspections.every(c => c.status === 'VALID'))
        };
    }
}

if (typeof window !== 'undefined') {
    window.DocumentFieldInspector = DocumentFieldInspector;
}
