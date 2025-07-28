FROM php:8.2-cli

# Instala extensões necessárias
RUN apt-get update && apt-get install -y \
    zip unzip curl git libonig-dev libzip-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Laravel installer global (opcional)
RUN composer global require laravel/installer

# Adiciona o caminho global do composer no PATH
ENV PATH="/root/.composer/vendor/bin:${PATH}"

# Cria diretório do projeto
RUN mkdir -p /var/www/backend

# Ajusta permissões de forma mais segura para evitar erro mkdir()
RUN chown -R www-data:www-data /var/www
RUN chmod -R 775 /var/www

# Usa usuário com permissão
USER www-data

WORKDIR /var/www/backend

# O CMD será sobrescrito pelo docker-compose (command:)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
