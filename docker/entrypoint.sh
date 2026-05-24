#!/bin/bash
set -e

# Pastikan folder writable & uploads ada (jika di-mount sebagai volume kosong)
mkdir -p \
    /var/www/html/writable/cache \
    /var/www/html/writable/logs \
    /var/www/html/writable/session \
    /var/www/html/writable/uploads \
    /var/www/html/writable/debugbar \
    /var/www/html/uploads

# Fix permission setiap container start (volume seringkali milik root)
chown -R www-data:www-data \
    /var/www/html/writable \
    /var/www/html/uploads 2>/dev/null || true

chmod -R 775 \
    /var/www/html/writable \
    /var/www/html/uploads 2>/dev/null || true

# Jika .env tidak ada tapi env.example ada, biarkan CI4 baca dari $_ENV (Coolify inject env)
# Tidak perlu generate .env karena Coolify mengirim variabel via environment variables.

exec "$@"
