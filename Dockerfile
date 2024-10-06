# Stage 1: Build stage
FROM bitnami/php-fpm:7.4 as builder

# Install required packages
RUN install_packages git autoconf build-essential

# Set the working directory
WORKDIR /app

# Install Xdebug
RUN wget https://github.com/xdebug/xdebug/archive/3.1.5.tar.gz && \
    tar xzf 3.1.5.tar.gz && \
    cd xdebug-3.1.5 && \
    phpize && \
    ./configure --enable-xdebug && \
    make && make install

# Install additional PHP extensions and tools
RUN apt-get update && apt-get install -y libpq-dev postgresql-client php-pgsql curl php-imagick

# Stage 2: Final stage
FROM bitnami/php-fpm:7.4

# Copy the Xdebug extension from the builder stage
COPY --from=builder /opt/bitnami/php/lib/php/extensions/xdebug.so /opt/bitnami/php/lib/php/extensions/

# Configure PHP settings
RUN echo 'zend_extension="/opt/bitnami/php/lib/php/extensions/xdebug.so"' >> /opt/bitnami/php/etc/php.ini && \
    echo 'extension=pdo_pgsql.so' >> /opt/bitnami/php/etc/php.ini && \
    echo 'extension=pgsql.so' >> /opt/bitnami/php/etc/php.ini && \
    echo 'extension=imagick.so' >> /opt/bitnami/php/etc/php.ini && \
    echo 'max_execution_time = 180' >> /opt/bitnami/php/etc/php.ini && \
    echo 'opcache.enable = Off' >> /opt/bitnami/php/etc/php.ini && \
    echo 'php_admin_value[disable_functions] = passthru, system' >> /opt/bitnami/php/etc/php-fpm.conf

# Configure Xdebug settings
RUN echo "xdebug.mode=debug" >> /opt/bitnami/php/etc/php.ini \
    && echo "xdebug.client_port=9000" >> /opt/bitnami/php/etc/php.ini \
    && echo "xdebug.client_host=192.168.122.1" >> /opt/bitnami/php/etc/php.ini \
    && echo "xdebug.start_with_request=yes" >> /opt/bitnami/php/etc/php.ini \
    && echo "xdebug.log=/tmp/xdebug.log" >> /opt/bitnami/php/etc/php.ini \
    && echo "clear_env = no" >> /opt/bitnami/php/etc/php-fpm.conf

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set up www user
RUN groupadd -g 1500 www
RUN useradd -u 1500 -ms /bin/bash -g www www

# Set permissions for logs and temporary files
RUN chown -R www:www /opt/bitnami/php/logs
RUN chown -R www:www /opt/bitnami/php/tmp

# Switch to www user
USER www
