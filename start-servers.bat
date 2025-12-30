@echo off
start "Laravel Server" cmd /k "cd YAKAN-WEB-main && php artisan serve --host=0.0.0.0 --port=8000"
timeout /t 3
start "Expo Server" cmd /k "npm start"
