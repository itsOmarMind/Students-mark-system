# Use an official PHP image with Apache
FROM php:8.2-apache

# Install necessary PHP extensions (e.g., for MySQL)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy your project files to the web server directory
COPY . /var/www/html/

# Set permissions for Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80