@echo off
chcp 65001 >nul
title Flask API - He thong Ket nap Dang
color 4F
echo.
echo  ==========================================
echo    Flask API - He thong Ket nap Dang
echo  ==========================================
echo.

:: Find Python
set PYTHON=
for %%P in (py python python3 C:\Python312\python.exe C:\Python311\python.exe C:\Python310\python.exe C:\Users\%USERNAME%\AppData\Local\Programs\Python\Python312\python.exe C:\Users\%USERNAME%\AppData\Local\Programs\Python\Python311\python.exe) do (
    if not defined PYTHON (
        %%P --version >nul 2>&1 && set PYTHON=%%P
    )
)

if not defined PYTHON (
    echo [LOI] Khong tim thay Python!
    echo Vui long cai dat Python tu: https://www.python.org/downloads/
    echo Nho check "Add Python to PATH" khi cai dat.
    pause
    exit /b 1
)

echo [OK] Tim thay Python: %PYTHON%
echo.

:: Check if libraries are already installed to skip pip install
cd /d "%~dp0"
%PYTHON% -c "import flask, flask_cors, pymysql, openpyxl, reportlab" >nul 2>&1
if %errorlevel% equ 0 (
    echo [OK] Các thư viện Python đã cài đặt đầy đủ. Bỏ qua bước cài đặt lại.
    echo.
    goto START_FLASK
)

:: Install requirements if missing
echo [..] Phát hiện thiếu thư viện. Đang tiến hành cài đặt tự động...
%PYTHON% -m pip install -r requirements.txt -q
if errorlevel 1 (
    echo [LOI] Không thể cài đặt các thư viện cần thiết!
    pause
    exit /b 1
)
echo [OK] Đã hoàn thành cài đặt thư viện thành công!
echo.

:START_FLASK
:: Start Flask
echo [..] Khởi động Flask API tại http://localhost:5000
echo      Nhấn Ctrl+C để dừng
echo.
%PYTHON% app.py
pause
