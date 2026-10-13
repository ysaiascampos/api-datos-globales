#!/bin/sh
set -e

echo "Waiting for PHP-FPM..."

# espera a que php-fpm esté listo
until nc -z 127.0.0.1 9000; do
  sleep 0.5
done

echo "PHP-FPM listo ✅"

exec "$@"
