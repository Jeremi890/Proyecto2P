FROM php:8.2-apache

# Habilitar mod_rewrite (esencial para que funcionen las rutas MVC)
RUN a2enmod rewrite

# Instalar conectores de base de datos MySQL para PHP
RUN docker-php-ext-install pdo pdo_mysql

# Configurar Apache para que la carpeta principal sea "public"
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copiar todo el código al servidor
COPY . /var/www/html/

# Dar permisos correctos a los archivos
RUN chown -R www-data:www-data /var/www/html
