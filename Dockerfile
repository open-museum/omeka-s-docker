FROM php:7.4-apache
ARG OMEKA_VERSION

RUN apt-get update
RUN apt-get install -y curl git libsodium-dev libxml2-dev software-properties-common unzip
RUN curl -sL https://deb.nodesource.com/setup_12.x | bash -
RUN apt-get install -y nodejs
RUN docker-php-ext-install intl pdo pdo_mysql sodium xml
RUN docker-php-ext-enable intl pdo pdo_mysql sodium xml

RUN git clone -b $OMEKA_VERSION https://github.com/omeka/omeka-s.git 

WORKDIR omeka-s

RUN npm install
RUN npm install --global gulp-cli
RUN gulp init

RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT /var/www/html/omeka-s/

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN chown -R www-data files

RUN curl -O EasyInstall-3.2.5.zip https://github.com/Daniel-KM/Omeka-S-module-EasyInstall/releases/download/3.2.5/EasyInstall-3.2.5.zip
RUN unzip EasyInstall-3.2.5.zip
RUN rm EasyInstall-3.2.5.zip