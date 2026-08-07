@echo off
title Flask API - He thong Ket nap Dang
color 4F
echo.
echo  ==========================================
echo    Flask API - He thong Ket nap Dang
echo  ==========================================
echo.

REM Tim kiem Python
set PYTHON=
for %%P in (py python python3 C:\Python312\python.exe C:\Python311\python.exe C:\Python310\python.exe C:\Users\%USERNAME%\AppData\Local\Programs\Python\Python312\python.exe C:\Users\%USERNAME%\AppData\Local\Programs\Python\Python311\python.exe) do (
    if not defined PYTHON (
        %%P --version >nul 2>&1 && set PYTHON=%%P
    )
)

if not defined PYTHON (
    echo [LOI] Khong tim thay Python!
    echo Vui long tai va cai dat Python tai: https://www.python.org/downloads/
    echo Nho tich chon "Add Python to PATH" khi cai dat.
    pause
    exit /b 1
)

echo [OK] Tim thay Python: %PYTHON%
echo.

REM Kiem tra thu vien da cai dat chua
cd /d "%~dp0"
%PYTHON% -c "import flask, flask_cors, pymysql, openpyxl, reportlab" >nul 2>&1
if %errorlevel% equ 0 (
    echo [OK] Cac thu vien Python da duoc cai dat day du.
    echo.
    goto START_FLASK
)

REM Cai dat thu vien neu thieu
echo [..] Phat hien thieu thu vien. Dang tu dong cai dat...
%PYTHON% -m pip install -r requirements.txt -q
if errorlevel 1 (
    echo [LOI] Khong the cai dat cac thu vien can thiet!
    pause
    exit /b 1
)
echo [OK] Da hoan thanh cai dat thu vien!
echo.

:START_FLASK
REM Khoi dong Flask
echo [..] Khoi dong Flask API tai http://localhost:5000
echo      Nhan Ctrl+C de dung
echo.
%PYTHON% app.py
pause
