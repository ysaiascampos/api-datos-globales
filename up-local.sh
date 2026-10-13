#!/bin/bash
# Levanta los 3 proyectos localmente
# Uso: ./up-local.sh

set -e

echo ""
echo "========================================"
echo "  Levantando ambiente local RSH"
echo "========================================"
echo ""

# Verificar que Docker este corriendo
if ! docker info > /dev/null 2>&1; then
    echo "[ERROR] Docker no esta corriendo. Inicia Docker y vuelve a intentar."
    exit 1
fi

# Crear red si no existe
echo "[1/4] Verificando red rsh-net..."
if ! docker network inspect rsh-net > /dev/null 2>&1; then
    echo "      Creando red rsh-net..."
    docker network create rsh-net
else
    echo "      Red rsh-net ya existe."
fi

# Levantar nginx-proxy
echo "[2/4] Levantando nginx-proxy..."
docker compose -f docker-compose.local.yml up -d nginx-proxy

# Levantar los 3 proyectos
echo "[3/4] Levantando proyectos..."
docker compose -f docker-compose.local.yml up -d

echo "[4/4] Verificando estado..."
docker compose -f docker-compose.local.yml ps

echo ""
echo "========================================"
echo "  Ambiente listo!"
echo "========================================"
echo ""
echo "URLs disponibles:"
echo "  - http://api-rsh-datosglobales-process.local.com"
echo "  - http://api-rsh-datosglobalesexport-system.local.com"
echo "  - http://api-rsh-datosglobalestx-system.local.com"
echo ""
echo "Comandos utiles:"
echo "  ./logs-local.sh    - Ver logs de todos los servicios"
echo "  ./down-local.sh    - Detener todo"
echo ""
