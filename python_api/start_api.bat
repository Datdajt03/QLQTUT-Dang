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

:: Install requirements
echo [..] Dang cai dat thu vien can thiet...
cd /d "%~dp0"
%PYTHON% -m pip install -r requirements.txt -q
if errorlevel 1 (
    echo [LOI] Khong the cai dat thu vien!
    pause
    exit /b 1
)
echo [OK] Thu vien da san sang
echo.

:: Start Flask
echo [..] Khoi dong Flask API tai http://localhost:5000
echo      Nhan Ctrl+C de dung
echo.
%PYTHON% app.py
pause
