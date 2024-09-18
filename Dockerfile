# Use the official PHP image as the base image
FROM php:8.1-apache

# Install necessary PHP extensions and tools
RUN docker-php-ext-install mysqli pdo pdo_mysql && \
    apt-get update && \
    apt-get install -y libzip-dev zip && \
    docker-php-ext-install zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy your application code to the container
COPY . /var/www/html/

# Set the working directory
WORKDIR /var/www/html/

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
