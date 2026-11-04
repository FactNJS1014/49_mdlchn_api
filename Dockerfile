FROM php:8.2-apache

WORKDIR /var/www/html

# ติดตั้ง dependencies พื้นฐาน
RUN apt-get update && apt-get install -y \
    apt-transport-https \
    ca-certificates \
    curl \
    gnupg2 \
    unixodbc-dev \
    libzip-dev \
    libssl-dev \
    build-essential \
    lsb-release \
    unzip \
    git \
    && docker-php-ext-install pdo zip

# เพิ่ม Microsoft SQL Server repository สำหรับ Debian 12 (bookworm)
RUN curl -sSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft.gpg \
    && echo "deb [signed-by=/usr/share/keyrings/microsoft.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18 mssql-tools18 \
    && ln -sfn /opt/mssql-tools18/bin/sqlcmd /usr/bin/sqlcmd \
    && ln -sfn /opt/mssql-tools18/bin/bcp /usr/bin/bcp

# ติดตั้ง SQLSRV extensions ผ่าน PECL
RUN pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# เปิด mod_rewrite
RUN a2enmod rewrite

# Copy source code
COPY . /var/www/html

# ตั้งสิทธิ์
RUN chown -R www-data:www-data /var/www/html

# ติดตั้ง composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

EXPOSE 80
