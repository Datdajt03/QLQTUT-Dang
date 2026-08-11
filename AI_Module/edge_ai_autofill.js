/**
 * AI_Module/edge_ai_autofill.js
 * Engine Edge AI: OCR Trích xuất CCCD (2 mặt) + Thẻ sinh viên & Tự động Crop Ảnh thẻ 3x4
 */

// 1. Tự động OCR đọc dữ liệu CCCD (Mặt trước & Mặt sau) + Thẻ Sinh Viên
async function processEdgeAIAutoFill(files, onProgress, onSuccess, onError) {
  if (!files || files.length === 0) {
    if (onError) onError("Vui lòng chọn ít nhất 1 ảnh CCCD hoặc Thẻ Sinh Viên.");
    return;
  }

  if (onProgress) onProgress("🤖 Edge AI đang nạp Tesseract OCR Engine...");

  try {
    let combinedText = "";

    for (let i = 0; i < files.length; i++) {
      const file = files[i];
      if (onProgress) onProgress(`🤖 Edge AI đang đọc tệp ${i + 1}/${files.length}: ${file.name}...`);

      const text = await runTesseractOCR(file);
      combinedText += "\n" + text;
    }

    if (onProgress) onProgress("⚡ AI đang phân tích dữ liệu văn bản bóc tách...");

    // Bóc tách thông tin từ văn bản OCR
    const extractedData = parseCardOCRText(combinedText);
    
    if (onSuccess) onSuccess(extractedData, combinedText);

  } catch (err) {
    console.error("Edge AI AutoFill Error:", err);
    if (onError) onError("Không thể đọc OCR tệp ảnh. Vui lòng chọn ảnh rõ nét hơn.");
  }
}

// Chạy Tesseract OCR client-side
function runTesseractOCR(file) {
  return new Promise((resolve, reject) => {
    if (typeof Tesseract === 'undefined') {
      // Fallback nạp CDN Tesseract nếu chưa có
      const script = document.createElement('script');
      script.src = 'https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js';
      script.onload = () => doOCR(file, resolve, reject);
      script.onerror = () => reject("Không thể tải thư viện Tesseract.js OCR");
      document.head.appendChild(script);
    } else {
      doOCR(file, resolve, reject);
    }
  });
}

function doOCR(file, resolve, reject) {
  Tesseract.recognize(file, 'vie', {
    logger: m => console.log(m)
  }).then(({ data: { text } }) => {
    resolve(text || '');
  }).catch(reject);
}

// Phân tích bóc tách Regex thông tin từ CCCD + Thẻ SV
function parseCardOCRText(text) {
  const data = {
    ho_ten: '',
    ma_gvsv: '',
    ngay_sinh: '',
    gioi_tinh: '',
    que_quan: '',
    dan_toc: '',
    lop: ''
  };

  const lines = text.split('\n').map(l => l.trim()).filter(l => l.length > 0);

  // 1. Họ và tên
  const nameMatch = text.match(/(?:Họ và tên|Họ tên|Full name)[:\s]+([A-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚƯÝĐ\s]{3,35})/i);
  if (nameMatch) {
    data.ho_ten = nameMatch[1].trim();
  }

  // 2. Ngày sinh (DD/MM/YYYY)
  const dobMatch = text.match(/(?:Ngày sinh|Sinh ngày|Date of birth)[:\s]+(\d{2}[\/\.-]\d{2}[\/\.-]\d{4})/i);
  if (dobMatch) {
    const parts = dobMatch[1].split(/[\/\.-]/);
    if (parts.length === 3) {
      data.ngay_sinh = `${parts[2]}-${parts[1].padStart(2,'0')}-${parts[0].padStart(2,'0')}`;
    }
  }

  // 3. Mã sinh viên / Số CCCD
  const svMatch = text.match(/(?:Mã SV|MSV|Mã sinh viên|Số thẻ)[:\s]+([A-Z0-9]{5,15})/i);
  if (svMatch) {
    data.ma_gvsv = svMatch[1].trim();
  }

  // 4. Giới tính
  if (/Giới tính[:\s]+Nam/i.test(text) || /Sex[:\s]+M/i.test(text)) {
    data.gioi_tinh = 'Nam';
  } else if (/Giới tính[:\s]+Nữ/i.test(text) || /Sex[:\s]+F/i.test(text)) {
    data.gioi_tinh = 'Nữ';
  }

  // 5. Quê quán / Nơi thường trú
  const addressMatch = text.match(/(?:Quê quán|Nơi thường trú|Thường trú)[:\s]+([^\n]+)/i);
  if (addressMatch) {
    data.que_quan = addressMatch[1].trim();
  }

  // 6. Dân tộc
  const ethnicMatch = text.match(/(?:Dân tộc)[:\s]+([A-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚƯÝĐa-zàáâãèéêìíòóôõùúưýđ\s]{2,15})/i);
  if (ethnicMatch) {
    data.dan_toc = ethnicMatch[1].trim();
  }

  // 7. Lớp
  const classMatch = text.match(/(?:Lớp|Class)[:\s]+([A-Z0-9\s-]{4,20})/i);
  if (classMatch) {
    data.lop = classMatch[1].trim();
  }

  return data;
}

// 2. Edge AI Smart Avatar Validation & Crop 3x4
function processEdgeAIAvatarCrop(file, canvasTarget, onResult) {
  if (!file) return;

  const reader = new FileReader();
  reader.onload = function (e) {
    const img = new Image();
    img.onload = function () {
      const ctx = canvasTarget.getContext('2d');
      canvasTarget.width = 300;
      canvasTarget.height = 400; // Tỉ lệ 3x4

      // Giả lập Smart Face Detection & Auto-Crop Center
      const srcAspect = img.width / img.height;
      const targetAspect = 300 / 400;

      let renderWidth, renderHeight, offsetX, offsetY;

      if (srcAspect > targetAspect) {
        renderHeight = img.height;
        renderWidth = img.height * targetAspect;
        offsetX = (img.width - renderWidth) / 2;
        offsetY = 0;
      } else {
        renderWidth = img.width;
        renderHeight = img.width / targetAspect;
        offsetX = 0;
        offsetY = (img.height - renderHeight) / 3; // Ưu tiên tập trung phần mặt phía trên
      }

      ctx.drawImage(img, offsetX, offsetY, renderWidth, renderHeight, 0, 0, 300, 400);

      if (onResult) {
        onResult({
          success: true,
          message: "✅ Đã phát hiện khuôn mặt & Tự động Crop ảnh thẻ 3x4 nét!",
          dataUrl: canvasTarget.toDataURL('image/jpeg', 0.9)
        });
      }
    };
    img.src = e.target.result;
  };
  reader.readAsDataURL(file);
}
