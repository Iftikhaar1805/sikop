@echo off
title SIKOP - Mematikan aplikasi
setlocal
set "XAMPP=C:\xampp"

echo.
echo  Mematikan SIKOP...
powershell -NoProfile -Command "Get-CimInstance Win32_Process -Filter \"Name='php.exe'\" | Where-Object { $_.CommandLine -match 'artisan' -or $_.CommandLine -match 'server.php' } | ForEach-Object { Stop-Process -Id $_.ProcessId -Force }" >nul 2>&1
"%XAMPP%\mysql\bin\mysqladmin.exe" -u root shutdown >nul 2>&1

rem Wait for MySQL to exit. A process left behind blocks the next Start in the XAMPP panel.
set /a TRY=0
:waitstop
tasklist /FI "IMAGENAME eq mysqld.exe" 2>nul | find /I "mysqld.exe" >nul
if errorlevel 1 goto stopped
set /a TRY+=1
if %TRY% GEQ 20 (
    taskkill /F /IM mysqld.exe >nul 2>&1
    goto stopped
)
timeout /t 1 /nobreak >nul
goto waitstop
:stopped

echo  Aplikasi dan MySQL sudah dimatikan. Data tersimpan aman.
echo.
timeout /t 3 /nobreak >nul
endlocal
