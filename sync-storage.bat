@echo off
REM Storage Sync Script for Yakan E-Commerce
REM This script copies files from storage/app/public to public/storage
REM Run this after file uploads to make them accessible via web

echo ============================================
echo Yakan E-Commerce Storage Sync
echo ============================================
echo.

cd /d "%~dp0"

echo Syncing custom_orders...
xcopy /E /I /Y storage\app\public\custom_orders\* public\storage\custom_orders\ >nul 2>&1

echo Syncing payment_receipts...
xcopy /E /I /Y storage\app\public\payment_receipts\* public\storage\payment_receipts\ >nul 2>&1

echo Syncing products...
xcopy /E /I /Y storage\app\public\products\* public\storage\products\ >nul 2>&1

echo.
echo ============================================
echo Sync completed successfully!
echo ============================================
echo.
echo Files are now accessible at:
echo - http://127.0.0.1:8000/storage/payment_receipts/
echo - http://127.0.0.1:8000/storage/custom_orders/
echo - http://127.0.0.1:8000/storage/products/
echo.

pause
