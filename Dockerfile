# Sử dụng hình ảnh chính thức của PHP 8.3 với Apache
FROM php:8.3-apache

# Cài đặt các tiện ích mở rộng cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Thiết lập thư mục làm việc
WORKDIR /var/www

# Copy toàn bộ mã nguồn Laravel vào container
COPY . .

# Cài đặt các phụ thuộc của dự án
RUN #composer install

# Thiết lập quyền cho thư mục storage và bootstrap/cache
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Copy file cấu hình Apache cho Laravel
COPY ./apache/laravel.conf /etc/apache2/sites-available/000-default.conf

# Bật mod_rewrite của Apache
RUN a2enmod rewrite

# Expose cổng 80
EXPOSE 80

# Khởi động Apache
CMD ["apache2-foreground"]