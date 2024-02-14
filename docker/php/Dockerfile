FROM php:8.1.0-apache

# Enable Mod Rewrite
RUN a2enmod rewrite

# Install Linux Libraries
RUN apt-get update -y && apt-get install -y \
	nano \
	curl \
	zip \
	unzip \
	openssl \
	libssl-dev

# Install PHP Extensions
RUN docker-php-ext-install \
	bcmath \
	gettext \
	pdo_mysql

# Install mongodb extension
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node 20 LTS
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash -- \
	&& apt-get install -y nodejs \
	&& apt-get autoremove -y

# Copy Virtualhost configuration
COPY  ./000-default.conf /etc/apache2/sites-available/000-default.conf

# COPY  app /var/www/html/

# WORKDIR /var/www/html/

# RUN chown -R www-data:www-data /var/www/html

