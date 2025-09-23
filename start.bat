@echo off
:: Caminho do seu projeto Laravel (altere para o caminho correto)
set "PROJECT_PATH=C:\xampp\htdocs\Framora"

:: Rodar o servidor Laravel em nova janela
echo Iniciando Laravel...
start "Laravel Server" cmd /k "cd /d %PROJECT_PATH% && php artisan serve"

:: Rodar o npm run dev em nova janela
echo Iniciando NPM...
start "NPM Dev" cmd /k "cd /d %PROJECT_PATH% && npm run dev"

pause
