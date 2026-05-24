FROM php:8.2-apache

# --- System deps & PHP extensions yang dibutuhkan CodeIgniter 4 ---
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        mysqli \
        pdo_mysql \
        gd \
        zip \
        mbstring \
        exif \
        bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# --- Apache: aktifkan mod_rewrite & headers, arahkan DocumentRoot ke public/ ---
RUN a2enmod rewrite headers
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

# --- PHP production config + tuning upload ---
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php-custom.ini /usr/local/etc/php/conf.d/zz-custom.ini

# --- Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# --- Install dependencies dulu (memanfaatkan layer cache) ---
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader

# --- Salin source code ---
COPY . .

# --- Permission writable & uploads ---
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chmod -R 775 /var/www/html/writable /var/www/html/public/uploads /var/www/html/uploads || true

# --- Entrypoint untuk fix permission volume saat container start ---
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -fsS http://127.0.0.1/ >/dev/null || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
