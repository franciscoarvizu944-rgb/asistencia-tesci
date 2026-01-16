FROM php:8.2-apache
RUN docker-php-ext-install mysqli
# Esta línea copia TODO tu código al lugar correcto del servidor
COPY . /var/www/html/
EXPOSE 80
