@echo off
REM Start Laravel development server from workspace root
cd /d "%~dp0"
echo Starting Laravel development server...
"C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe" artisan serve
pause
