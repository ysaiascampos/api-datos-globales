#!/bin/bash
# Reconstruye imagenes y reinicia servicios
# NOTA: Solo es necesario si cambiaste Dockerfile.dev, composer.json
#       o archivos de docker/. El codigo PHP se refleja solo (volumenes).
# Uso: ./rebuild-local.sh [servicio]
#   ./rebuild-local.sh                    - Rebuild todos
#   ./rebuild-local.sh process-app        - Solo process
#   ./rebuild-local.sh export-system-app  - Solo export-system
#   ./rebuild-local.sh tx-system-app      - Solo tx-system

SERVICE=$1

echo ""
echo "========================================"
echo "  Reconstruyendo imagenes"
echo "========================================"
echo ""

if [ -z "$SERVICE" ]; then
    echo "Reconstruyendo TODOS los servicios..."
    docker compose -f docker-compose.local.yml build --no-cache
    docker compose -f docker-compose.local.yml up -d --force-recreate
else
    echo "Reconstruyendo: $SERVICE"
    docker compose -f docker-compose.local.yml build --no-cache "$SERVICE"
    docker compose -f docker-compose.local.yml up -d --force-recreate "$SERVICE"
fi

echo ""
echo "Rebuild completado."
echo ""
