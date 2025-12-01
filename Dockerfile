FROM yiisoftware/yii2-php:8.1-apache

# Install and enable the PHP memcached and intl extensions
RUN set -eux; \
    apt-get update && \
    apt-get install -y --no-install-recommends \
            libicu-dev \
            libmemcached-dev \
            libssl-dev \
            libzip-dev \
            zlib1g-dev && \
    docker-php-ext-install intl && \
    printf "" | pecl install -D 'with-libmemcached-dir="/usr"' memcached && \
    docker-php-ext-enable memcached && \
    rm -rf /var/lib/apt/lists/*

# Копируем файлы приложения
COPY . /var/www/html/

# Устанавливаем рабочую директорию
WORKDIR /var/www/html/

EXPOSE 80