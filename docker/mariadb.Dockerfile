FROM mariadb:10.11

# Bake init SQL ke dalam image agar tidak bergantung pada bind-mount Coolify.
# Script di /docker-entrypoint-initdb.d/ otomatis dijalankan saat volume kosong.
COPY db_letter.sql /docker-entrypoint-initdb.d/01-init.sql
