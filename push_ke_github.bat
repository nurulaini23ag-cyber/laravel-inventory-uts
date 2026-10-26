@echo off
title Push Codingan Laravel ke GitHub
cd /d "C:\xampp\htdocs\inventory-laravel"
echo ========================================================
echo    MENGHUBUNGKAN & PUSH CODINGAN KE GITHUB
echo ========================================================
echo.
echo Akun GitHub : nurulaini23ag-cyber
echo Repository  : https://github.com/nurulaini23ag-cyber/laravel-inventory-uts.git
echo.
echo Sedang melakukan git push...
echo Jika muncul jendela GitHub Login atau tab Browser,
echo silakan klik 'Sign in with your browser' / 'Authorize'.
echo.
git push -u origin main
echo.
echo ========================================================
echo Selesai! Jika berhasil, codingan Anda sudah di GitHub.
echo ========================================================
pause
