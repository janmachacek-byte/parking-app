# ---- build stage ----
  FROM composer:2 AS vendor
  WORKDIR /app
  COPY composer.json composer.lock ./
  RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader
  COPY . .
  
  # ---- runtime stage ----
  FROM php:8.3-fpm-alpine
  
  RUN apk add --no-cache nginx supervisor bash icu-dev oniguruma-dev libzip-dev postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql intl mbstring zip opcache
  
  RUN mkdir -p /run/nginx
  COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
  
  WORKDIR /var/www/html
  COPY --from=vendor /app /var/www/html
  
  RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
  
  COPY docker/supervisord.conf /etc/supervisord.conf
  COPY docker/start.sh /start.sh
  RUN chmod +x /start.sh
  
  EXPOSE 8080
  CMD ["/start.sh"]
  