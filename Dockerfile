# Usa una imagen base de PHP con Apache
FROM php:8.2-apache

# Instala las extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Instala Node.js, npm y sass
RUN apt-get update && \
    apt-get install -y nodejs npm && \
    npm install -g sass

# Copia los archivos de tu proyecto al contenedor
COPY . /var/www/html/

# Establece los permisos adecuados
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Habilita el módulo de reescritura de Apache
RUN a2enmod rewrite

# Establece la variable de entorno para las credenciales de Google
ENV GOOGLE_APPLICATION_CREDENTIALS=/var/www/html/config/client_secret_817642552550-grgavacspiedvqco6uu785u561bepi4o.apps.googleusercontent.com.json

# Expone el puerto 80
EXPOSE 80
