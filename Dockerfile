FROM php:8.2-apache

# Install extensions and dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip xml mbstring \
    && a2enmod rewrite

WORKDIR /var/www/html

COPY . .

COPY ./config/apache.conf /etc/apache2/sites-enabled/000-default.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN chmod +x -R /var/www/html/scripts

ENV PATH="$PATH:/var/www/html/scripts"

CMD ["start-server"]
