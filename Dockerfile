# Используем официальный образ PHP с FPM
FROM php:8.2-fpm

# Устанавливаем зависимости
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl gd

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Настраиваем рабочую директорию
WORKDIR /var/www/html

# Копируем проект в контейнер (обязательно ДО composer install)
COPY . .

# Права доступа
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Указываем CMD
CMD ["php-fpm"]
