FROM php:7.4-apache

# Install ekstensi yang dibutuhkan
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Aktifkan mod_rewrite
RUN a2enmod rewrite

# Ubah DocumentRoot Apache agar ke folder CodeIgniter
ENV APACHE_DOCUMENT_ROOT /var/www/html

# Update config agar pakai folder yang benar
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf

# Copy file ke /var/www/html
COPY . /var/www/html/

# Ubah permission jika perlu
RUN chown -R www-data:www-data /var/www/html
