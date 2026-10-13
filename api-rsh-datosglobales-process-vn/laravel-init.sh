# docker/laravel-init.sh (lo que quedó)
php artisan optimize:clear || true

echo "📘 Generando documentación Swagger..."
php artisan l5-swagger:generate || true     # ← NUEVO

php artisan config:cache
php artisan route:cache
php artisan view:cache
