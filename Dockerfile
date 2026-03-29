FROM php:8.2-apache

# Enable Apache mod_rewrite (if needed later)
RUN a2enmod rewrite

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy project files to the container
COPY . /var/www/html/

# Create telemetry.json if it doesn't exist and set permissions
RUN touch /var/www/html/telemetry.json && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html

# Expose port 80
EXPOSE 80
