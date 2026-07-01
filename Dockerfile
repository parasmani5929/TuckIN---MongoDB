FROM php:8.3-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libssl-dev libcurl4-openssl-dev ca-certificates \
    && docker-php-ext-install zip \
    && pecl install --configureoptions "with-mongodb-ssl=openssl" mongodb \
    && docker-php-ext-enable mongodb \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader --ignore-platform-req=ext-mongodb

# Koyeb default port is 8000; Render uses 10000. PORT env var overrides at runtime.
ENV PORT=8000
EXPOSE 8000

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT} -t ."]
