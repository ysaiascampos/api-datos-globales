@echo off
REM Muestra logs de los servicios
REM Uso: logs-local.bat [servicio]
REM   logs-local.bat                    - Todos los logs
REM   logs-local.bat process-app        - Solo process
REM   logs-local.bat export-system-app  - Solo export-system
REM   logs-local.bat tx-system-app      - Solo tx-system
REM   logs-local.bat nginx-proxy        - Solo el proxy

set SERVICE=%1

if "%SERVICE%"=="" (
    echo Mostrando logs de TODOS los servicios... (Ctrl+C para salir)
    docker compose -f docker-compose.local.yml logs -f
) else (
    echo Mostrando logs de: %SERVICE% (Ctrl+C para salir)
    docker compose -f docker-compose.local.yml logs -f %SERVICE%
)
