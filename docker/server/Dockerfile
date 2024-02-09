FROM lavoweb/php-8.3

# Mise à jour des paquets et installation des dépendances nécessaires
RUN apt-get update \
    && apt-get install -y --fix-missing \
        git \
        unzip \
        zip \
        nano \
        cron \
        libxslt-dev \
        librabbitmq-dev \
        libssl-dev \
        nodejs \
        npm

# Configuration de Apache et PHP
RUN a2enmod rewrite

# Installation et configuration des extensions PHP
RUN mkdir -p /usr/src/php/ext/apcu \
    && curl -fsSL https://pecl.php.net/get/apcu | tar xvz -C "/usr/src/php/ext/apcu" --strip 1 \
    && docker-php-ext-install apcu xsl

# Installation de Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
    && apt-get update \
    && apt-get install -y symfony-cli

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Installation de Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Installation de PHP extensions via le script install-php-extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions gd pdo_mysql zip opcache

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

EXPOSE 8585