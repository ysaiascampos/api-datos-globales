#!/bin/bash
# Detiene todos los servicios locales
# Uso: ./down-local.sh

echo ""
echo "========================================"
echo "  Deteniendo ambiente local RSH"
echo "========================================"
echo ""

docker compose -f docker-compose.local.yml down

echo ""
echo "Ambiente detenido."
echo ""
