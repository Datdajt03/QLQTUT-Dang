# Mẫu Báo Cáo / Bài Tập Lớn / Khóa Luận LaTeX — Trường Đại Học Tây Bắc (TBU)

Bộ công cụ và gói Style LaTeX (`tbu_report.sty`) giúp sinh viên và giảng viên Trường Đại học Tây Bắc dễ dàng tạo các báo cáo bài tập lớn, báo cáo nghiên cứu khoa học, đồ án và khóa luận tốt nghiệp đạt chuẩn trình trình bày chuyên nghiệp.

---

## 📁 Danh Mục Tệp Trong Thư Mục

- **`tbu_report.sty`**: Gói style định dạng chuẩn dành riêng cho Trường ĐH Tây Bắc (căn lề chuẩn: Trên 2.0cm, Dưới 2.0cm, Trái 3.0cm, Phải 1.5cm).
- **`main.tex`**: File mẫu hoàn chỉnh (phông 12pt, tiêu đề căn trái sát lề chuẩn).
- **`main.pdf`**: File PDF đã được biên dịch hoàn chỉnh.
- **`tbu_logo.png`**: Logo chính thức của Trường Đại học Tây Bắc.

---

## 🚀 Cách Sử Dụng Cực Kỳ Đơn Giản

Sao chép 2 tệp **`tbu_report.sty`** và **`tbu_logo.png`** vào thư mục bài viết của bạn, sau đó sử dụng:

```latex
\documentclass[12pt,a4paper]{report}
\usepackage{tbu_report}

% --- KHAI BÁO THÔNG TIN BÁO CÁO ---
\ministry{BỘ GIÁO DỤC VÀ ĐÀO TẠO}
\university{TRƯỜNG ĐẠI HỌC TÂY BẮC}
\faculty{KHOA CÔNG NGHỆ THÔNG TIN}
\reporttype{BÁO CÁO BÀI TẬP LỚN}
\title{XÂY DỰNG HỆ THỐNG GỢI Ý SẢN PHẨM}
\courseclass{K64 ĐẠI HỌC CNTT}
\groupnumber{Nhóm 3}
\advisor{TS. Phạm Quốc Thắng}
\location{SƠN LA}
\reportdate{THÁNG 05/2026}
\logopath{tbu_logo.png}

% --- DANH SÁCH SINH VIÊN THỰC HIỆN (TỰ ĐỘNG CHIA 2 CỘT) ---
\addstudent[NT]{\textbf{Hoàng Trung Hiếu}}
\addstudent{Nguyễn Huy Hoàng}
\addstudent{Hà Việt Cường}
\addstudent{Tòng Thị Huyền Diệu}
\addstudent{Điêu Chính Doanh}
\addstudent{Lò Khánh Linh}
\addstudent{Dên Sạ Mon Súc Pha Sắc}
\addstudent{\textbf{Lò Mạnh Đạt}}
\addstudent{Phạm Thị Thanh Hảo}
\addstudent{Tòng Văn Hiến}
\addstudent{Sổm Chăn Chăn Mạ Ni Khăm}
\addstudent{Đăm Lắt Chay Vang Manh}
\addstudent{Su Pha Thong Na Khon Súc}
\addstudent{Tòng Lưu Anh Tú}

\begin{document}

% 1. Trang bìa ngoài và bìa phụ
\makeoutercover
\makeinnercover

% 2. Lời cảm ơn
\begin{acknowledgements}
Nội dung lời cảm ơn...
\end{acknowledgements}

% 3. Mục lục (Chỉ gồm Mở đầu, Nội dung các Chương, Kết luận, Tài liệu tham khảo)
\makecontentlists

% 4. Danh mục từ viết tắt
\begin{abbreviations}
  CNTT & Công nghệ Thông tin \\ \hline
  TBU & Trường Đại học Tây Bắc \\
\end{abbreviations}

% 5. Phần Mở đầu
\unchapter{MỞ ĐẦU}
\unsection{1.1}{Tổng quan về hệ thống}
Trình bày tổng quan...

% 6. Các Chương nội dung
\chapter{CƠ SỞ LÝ THUYẾT}
\section{Tổng quan}
Nội dung lý thuyết...

\chapter{PHÂN TÍCH THIẾT KẾ HỆ THỐNG}
\section{Mô tả kiến trúc}
Nội dung phân tích...

\chapter{XÂY DỰNG VÀ KIỂM THỬ HỆ THỐNG}
\section{Kết quả thực nghiệm}
Nội dung thực nghiệm...

% 7. Kết luận
\unchapter{KẾT LUẬN VÀ KIẾN NGHỊ}
Nêu kết luận...

% 8. Tài liệu tham khảo
\cleardoublepage
\addcontentsline{toc}{chapter}{TÀI LIỆU THAM KHẢO}
\begin{thebibliography}{99}
  \bibitem{ref1} Tên tác giả (2026), \textit{Tên sách/bài báo}, NXB.
\end{thebibliography}

\end{document}
```

---

## 🎨 Quy Chuẩn Căn Lề Chuẩn Xác

- **Lề Trên (Top)**: `2.0 cm` (mép trên đỉnh chữ tiêu đề nằm đúng vị trí 2.0 cm).
- **Lề Dưới (Bottom)**: `2.0 cm` (số trang chân trang nằm đúng vị trí 2.0 cm).
- **Lề Trái (Left)**: `3.0 cm` (30mm).
- **Lề Phải (Right)**: `1.5 cm` (15mm).
