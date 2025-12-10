@echo off
REM Clear Laravel caches after fixing session driver

cd /d "c:\Users\HP\Desktop\yakan-ecommerce-main\yakan-ecommerce-main"

echo Clearing configuration cache...
php artisan config:clear

echo Clearing application cache...
php artisan cache:clear

echo Clearing view cache...
php artisan view:clear

echo Clearing route cache...
php artisan route:clear

echo.
echo ✅ All caches cleared successfully!
echo.
echo Next steps:
echo 1. Clear your browser cookies/cache
echo 2. Try the operation again
echo.
pause
