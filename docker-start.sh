#!/bin/sh
set -e
# Render/Railway memberi port lewat $PORT
PORT="${PORT:-10000}"
sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

cd /var/www/html
php artisan config:cache
php artisan view:cache
php artisan migrate --force

# Isi data demo hanya jika RUN_SEED=1 (sekali saja untuk uji coba)
if [ "${RUN_SEED:-0}" = "1" ]; then
  php artisan db:seed --force || true
fi

exec apache2-foreground
