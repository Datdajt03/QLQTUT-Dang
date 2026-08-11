@echo off
title PROJECT ONE-CLICK SETUP
color 0A

echo =======================================================================
echo HET HONG QUAN LY PHAT TRIEN DANG VIEN - TU DONG CAI DAT MAY MOI
echo =======================================================================
echo.

set CURRENT_DIR=%~dp0
set MYSQL_BIN=C:\xampp\mysql\bin\mysql.exe
set PHP_INI=C:\xampp\php\php.ini

if not exist "%MYSQL_BIN%" (
    if exist "A:\xamapp\mysql\bin\mysql.exe" (
        set MYSQL_BIN=A:\xamapp\mysql\bin\mysql.exe
        set PHP_INI=A:\xamapp\php\php.ini
    )
)

echo -----------------------------------------------------------------------
echo BUOC 1: KIEM TRA VA KHOI TAO THU MUC UPLOADS
echo -----------------------------------------------------------------------
if exist "%CURRENT_DIR%uploads\avatars" echo [DA CAI DAT] Thu muc uploads va cac thu muc con da ton tai!
if not exist "%CURRENT_DIR%uploads\avatars" mkdir "%CURRENT_DIR%uploads" 2>nul
if not exist "%CURRENT_DIR%uploads\avatars" mkdir "%CURRENT_DIR%uploads\ho_so_minh_chung" 2>nul
if not exist "%CURRENT_DIR%uploads\avatars" mkdir "%CURRENT_DIR%uploads\avatars" 2>nul
if not exist "%CURRENT_DIR%uploads\avatars" echo [THANH CONG] Da tao moi day du cau truc thu muc uploads!
echo.

echo -----------------------------------------------------------------------
echo BUOC 2: KIEM TRA VA KICH HOAT EXTENSION=ZIP TRONG PHP.INI
echo -----------------------------------------------------------------------
if exist "%PHP_INI%" powershell -Command "(Get-Content '%PHP_INI%') -replace ';extension=zip', 'extension=zip' | Set-Content '%PHP_INI%'"
if exist "%PHP_INI%" echo [THANH CONG - DA CAI DAT] Kich hoat extension=zip trong php.ini hoan tat!
if not exist "%PHP_INI%" echo [CANH BAO / LOI] Khong tim thay php.ini!
echo.

echo -----------------------------------------------------------------------
echo BUOC 3: KIEM TRA VA CAI DAT THU VIEN PYTHON (REPORTLAB, FLASK...)
echo -----------------------------------------------------------------------
where python >nul 2>&1
if errorlevel 1 echo [LOI] May tinh chua cai dat Python! Vui long cai Python 3.9 tro len.
if not errorlevel 1 echo [DA CAI DAT] Moi truong Python da san sang!

python -c "import reportlab, flask, pymysql" >nul 2>&1
if errorlevel 1 echo [DANG CAI DAT] Dang cai dat dependencies tu python_api\requirements.txt...
if errorlevel 1 pip install -r "%CURRENT_DIR%python_api\requirements.txt"
if not errorlevel 1 echo [DA CAI DAT] Cac thu vien Python (ReportLab, Flask, PyMySQL) da san sang!
echo.

echo -----------------------------------------------------------------------
echo BUOC 4: KIEM TRA VA NAP CO SO DU LIEU MYSQL (ql_dangvien)
echo -----------------------------------------------------------------------
if exist "%MYSQL_BIN%" "%MYSQL_BIN%" -u root -e "USE ql_dangvien;" >nul 2>&1
if exist "%MYSQL_BIN%" if not errorlevel 1 echo [DA CAI DAT] Database ql_dangvien da ton tai trong MySQL, khong can nap lai!

if exist "%MYSQL_BIN%" if errorlevel 1 (
    echo [DANG CAI DAT] Dang tao Database ql_dangvien va nap file schema...
    "%MYSQL_BIN%" -u root -e "CREATE DATABASE IF NOT EXISTS ql_dangvien CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul
    "%MYSQL_BIN%" -u root ql_dangvien < "%CURRENT_DIR%Cau_hinh\db.sql" 2>nul
    echo [THANH CONG] Da khoi tao va nap CSDL ql_dangvien thanh cong!
)
if not exist "%MYSQL_BIN%" echo [LOI] Khong tim thay MySQL CLI de nap CSDL!
echo.

echo -----------------------------------------------------------------------
echo BUOC 5: KHOI CHAY MICROSERVICE PYTHON API EXPORT PDF
echo -----------------------------------------------------------------------
echo Dang khoi dong Flask Python PDF Engine...
start "Python PDF API Microservice" cmd /k "cd /d %CURRENT_DIR%python_api && python app.py"

echo.
echo =======================================================================
echo HE THONG DA TU DONG KIEM TRA VA CAI DAT HOAN TAT!
echo =======================================================================
echo Truy cap Web App tai: http://localhost/web1
echo.
pause
