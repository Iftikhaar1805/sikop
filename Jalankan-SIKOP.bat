@echo off
title SIKOP - Menyalakan aplikasi
setlocal
set "XAMPP=C:\xampp"
set "APP=%~dp0"
set "URL=http://127.0.0.1:8000"

if not exist "%XAMPP%\php\php.exe" (
    echo XAMPP tidak ditemukan di %XAMPP%.
    echo Pasang XAMPP dulu, atau ubah baris "set XAMPP=" di file ini.
    pause
    exit /b 1
)

echo.
echo  SIKOP - Sistem Informasi Penjualan Koperasi Sekolah
echo  ---------------------------------------------------
echo.

tasklist /FI "IMAGENAME eq mysqld.exe" 2>nul | find /I "mysqld.exe" >nul
if errorlevel 1 (
    echo  [1/3] Menyalakan MySQL...
    powershell -NoProfile -Command "Start-Process '%XAMPP%\mysql\bin\mysqld.exe' -ArgumentList '--defaults-file=%XAMPP%\mysql\bin\my.ini','--standalone' -WindowStyle Hidden"
) else (
    echo  [1/3] MySQL sudah menyala.
)

echo  [2/3] Menunggu MySQL siap...
set /a TRY=0
:waitdb
"%XAMPP%\mysql\bin\mysqladmin.exe" -u root ping >nul 2>&1
if not errorlevel 1 goto dbready
set /a TRY+=1
if %TRY% GEQ 30 (
    echo.
    echo  MySQL tidak mau menyala. Tutup program lain yang memakai port 3306,
    echo  lalu jalankan file ini lagi.
    pause
    exit /b 1
)
timeout /t 1 /nobreak >nul
goto waitdb
:dbready

curl -s -o nul "%URL%/login" 2>nul
if errorlevel 1 (
    echo  [3/3] Menyalakan aplikasi...
    powershell -NoProfile -Command "Start-Process '%XAMPP%\php\php.exe' -ArgumentList 'artisan','serve','--host=127.0.0.1','--port=8000' -WorkingDirectory '%APP%.' -WindowStyle Hidden"
) else (
    echo  [3/3] Aplikasi sudah menyala.
)

set /a TRY=0
:waitapp
curl -s -o nul "%URL%/login" 2>nul
if not errorlevel 1 goto appready
set /a TRY+=1
if %TRY% GEQ 30 (
    echo.
    echo  Aplikasi belum merespons. Coba jalankan file ini sekali lagi.
    pause
    exit /b 1
)
timeout /t 1 /nobreak >nul
goto waitapp
:appready

echo.
echo  SIKOP siap dipakai di %URL%
echo  Untuk mematikan, jalankan Matikan-SIKOP.bat
echo.
start "" "%URL%/login"
timeout /t 4 /nobreak >nul
endlocal
