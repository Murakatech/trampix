    FROM php:8.2-apache

    WORKDIR /var/www/html

    # Instala as dependências do sistema operacional para as extensões PHP
    RUN apt-get update && apt-get install -y \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        libpng-dev \
        git \
        unzip \
        && rm -rf /var/lib/apt/lists/*

    # Instala o Composer globalmente no container
    COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

    # Instala as extensões PHP necessárias para o Laravel e MySQL (REMOVIDO xdebug daqui)
    RUN docker-php-ext-install mysqli pdo pdo_mysql mbstring xml bcmath gd

    # Instala o Xdebug via PECL e o habilita
    RUN pecl install xdebug \
        && docker-php-ext-enable xdebug

    # Copia o arquivo de configuração do Apache personalizado
    COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

    # Copia o arquivo de configuração do Xdebug personalizado
    COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

    # Habilita o módulo rewrite do Apache (essencial para URLs amigáveis do Laravel)
    RUN a2enmod rewrite

    EXPOSE 80
    