@echo off
REM Levanta los 3 proyectos localmente
REM Uso: up-local.bat

echo.
echo ========================================
echo  Levantando ambiente local RSH
echo ========================================
echo.

REM Verificar que Docker este corriendo
docker info >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Docker no esta corriendo. Inicia Docker Desktop y vuelve a intentar.
    pause
    exit /b 1
)

REM Crear red si no existe
echo [1/4] Verificando red rsh-net...
docker network inspect rsh-net >nul 2>&1
if errorlevel 1 (
    echo       Creando red rsh-net...
    docker network create rsh-net
) else (
    echo       Red rsh-net ya existe.
)

REM Levantar nginx-proxy
echo [2/4] Levantando nginx-proxy...
docker compose -f docker-compose.local.yml up -d nginx-proxy

REM Levantar los 3 proyectos
echo [3/4] Levantando proyectos...
docker compose -f docker-compose.local.yml up -d

echo [4/4] Verificando estado...
docker compose -f docker-compose.local.yml ps

echo.
echo ========================================
echo  Ambiente listo!
echo ========================================
echo.
echo URLs disponibles:
echo   - http://api-rsh-datosglobales-process.local.com
echo   - http://api-rsh-datosglobalesexport-system.local.com
echo   - http://api-rsh-datosglobalestx-system.local.com
echo.
echo Comandos utiles:
echo   logs-local.bat    - Ver logs de todos los servicios
echo   down-local.bat    - Detener todo
echo.
pause
