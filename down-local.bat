@echo off
REM Detiene todos los servicios locales
REM Uso: down-local.bat

echo.
echo ========================================
echo  Deteniendo ambiente local RSH
echo ========================================
echo.

docker compose -f docker-compose.local.yml down

echo.
echo Ambiente detenido.
echo.
pause
