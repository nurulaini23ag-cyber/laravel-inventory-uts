@echo off
setlocal
cd /d "%~dp0"

title Inventory & Sales Management System (Enterprise POS)

echo ===================================================================
echo   Inventory & Sales Management System (Enterprise POS & Stock)
echo ===================================================================
echo.

:: 1. Daftarkan path PHP dan konfigurasi PHPRC secara portabel
if exist "%USERPROFILE%\php\php.ini" set "PHPRC=%USERPROFILE%\php"
if exist "C:\xampp\php\php.ini" if not defined PHPRC set "PHPRC=C:\xampp\php"
if exist "%~dp0php.ini" if not defined PHPRC set "PHPRC=%~dp0"

if exist "C:\Program Files\php-8.5.7" set "PATH=C:\Program Files\php-8.5.7;%PATH%"
if exist "%USERPROFILE%\php" set "PATH=%USERPROFILE%\php;%PATH%"
if exist "C:\xampp\php" set "PATH=C:\xampp\php;%PATH%"

:: 2. Cek apakah MySQL sudah berjalan, jika belum jalankan otomatis
echo [1/3] Memeriksa database MySQL (port 3306)...
netstat -ano | findstr :3306 | findstr LISTENING >nul
if %errorlevel% neq 0 (
    echo [INFO] Menyalakan layanan MySQL XAMPP...
    start "" /d "C:\xampp" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone
    timeout /t 3 /nobreak >nul
) else (
    echo [OK] MySQL sudah aktif di port 3306.
)

:: 3. Buka browser otomatis ke aplikasi
echo [2/3] Membuka browser ke http://127.0.0.1:8000 ...
start http://127.0.0.1:8000

:: 4. Jalankan Laravel Development Server
echo [3/3] Menjalankan web server Laravel...
echo.
echo ===================================================================
echo   Aplikasi aktif di : http://127.0.0.1:8000
echo   Status Server     : Berjalan normal
echo   Kredensial Login  :
echo     - Admin / Owner : admin / admin123
echo     - Staff / Kasir : staff / staff123
echo.
echo   PENTING: Jangan tutup jendela ini selama menggunakan aplikasi.
echo   (Tekan CTRL+C jika ingin mematikan server)
echo ===================================================================
echo.

php artisan serve --host=127.0.0.1 --port=8000 --no-reload

pause
