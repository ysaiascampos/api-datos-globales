#!/bin/bash
# Muestra logs de los servicios
# Uso: ./logs-local.sh [servicio]
#   ./logs-local.sh                    - Todos los logs
#   ./logs-local.sh process-app        - Solo process
#   ./logs-local.sh export-system-app  - Solo export-system
#   ./logs-local.sh tx-system-app      - Solo tx-system
#   ./logs-local.sh nginx-proxy        - Solo el proxy

SERVICE=$1

if [ -z "$SERVICE" ]; then
    echo "Mostrando logs de TODOS los servicios... (Ctrl+C para salir)"
    docker compose -f docker-compose.local.yml logs -f
else
    echo "Mostrando logs de: $SERVICE (Ctrl+C para salir)"
    docker compose -f docker-compose.local.yml logs -f "$SERVICE"
fi
