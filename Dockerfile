FROM php:8.2-fpm-alpine

# Instala o Nginx e outras dependências
RUN apk update && apk add --no-cache \
    nginx \
    supervisor

# Instala as extensões do PHP
RUN docker-php-ext-install pdo pdo_mysql

# Copia a configuração do PHP-FPM
COPY ./docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

# Copia a configuração do Nginx
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copia a configuração do Supervisor
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copia o código da aplicação
COPY . /var/www/html

# Define o diretório de trabalho
WORKDIR /var/www/html

# Expõe a porta 80
EXPOSE 80

# Comando para iniciar o Nginx e o PHP-FPM
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
