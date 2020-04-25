FROM openmuseum/omeka-s

COPY ./config/.htaccess-development /usr/src/omeka-s/.htaccess
COPY ./config/database.ini /usr/src/omeka-s/config/database.ini
COPY ./config/local.config-development.php /usr/src/omeka-s/config/local.config.php
COPY ./config/php-development.ini /usr/local/etc/php/php.ini
RUN service apache2 restart