@echo off
setlocal
cd /d "%~dp0"

title Inventory & Sales Management System (Enterprise POS)

echo ===================================================================
echo   Inventory & Sales Management System (Enterprise POS & Stock)
echo ===================================================================
echo.

:: 1. Tentukan executable PHP 8.5+ secara presisi
set "PHP_BIN=C:\Program Files\php-8.5.7\php.exe"
if not exist "%PHP_BIN%" set "PHP_BIN=php.exe"

set "PHPRC=%USERPROFILE%\php"

:: 2. Cek apakah MySQL sudah berjalan di port 3306
echo [1/3] Memeriksa database MySQL (port 3306)...
netstat -ano | findstr :3306 | findstr LISTENING >nul
if %errorlevel% neq 0 (
    echo [INFO] Menyalakan layanan MySQL XAMPP...
    start "MySQL XAMPP" /d "C:\xampp" cmd.exe /c "mysql_start.bat"
    timeout /t 3 /nobreak >nul
) else (
    echo [OK] MySQL sudah aktif di port 3306.
)

:: 3. Buka browser otomatis ke halaman login
echo [2/3] Membuka browser ke http://127.0.0.1:8000/login ...
start http://127.0.0.1:8000/login

:: 4. Jalankan Laravel Development Server dengan PHP 8.5+
echo [3/3] Menjalankan web server Laravel...
echo.
echo ===================================================================
echo   Aplikasi aktif di : http://127.0.0.1:8000/login
echo   Status Server     : Berjalan normal
echo   Kredensial Login  :
echo     - Admin / Owner        : admin / admin123
echo     - Staff / Kasir        : staff / staff123
echo     - Customer / Pelanggan : customer / customer123
echo.
echo   PENTING: JANGAN TUTUP JENDELA INI SELAMA MENGGUNAKAN APLIKASI.
echo ===================================================================
echo.

"%PHP_BIN%" -c "%PHPRC%\php.ini" artisan serve --host=127.0.0.1 --port=8000

pause
