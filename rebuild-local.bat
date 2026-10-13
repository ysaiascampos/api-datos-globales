@echo off
REM Reconstruye imagenes y reinicia servicios
REM NOTA: Solo es necesario si cambiaste Dockerfile.dev, composer.json
REM       o archivos de docker/. El codigo PHP se refleja solo (volumenes).
REM Uso: rebuild-local.bat [servicio]
REM   rebuild-local.bat                    - Rebuild todos
REM   rebuild-local.bat process-app        - Solo process
REM   rebuild-local.bat export-system-app  - Solo export-system
REM   rebuild-local.bat tx-system-app      - Solo tx-system

set SERVICE=%1

echo.
echo ========================================
echo  Reconstruyendo imagenes
echo ========================================
echo.

if "%SERVICE%"=="" (
    echo Reconstruyendo TODOS los servicios...
    docker compose -f docker-compose.local.yml build --no-cache
    docker compose -f docker-compose.local.yml up -d --force-recreate
) else (
    echo Reconstruyendo: %SERVICE%
    docker compose -f docker-compose.local.yml build --no-cache %SERVICE%
    docker compose -f docker-compose.local.yml up -d --force-recreate %SERVICE%
)

echo.
echo Rebuild completado.
echo.
pause
