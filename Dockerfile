FROM php:8.0-apache

# PHPのMySQL拡張をインストール
RUN docker-php-ext-install mysqli pdo pdo_mysql

# PHPの拡張をインストール
RUN docker-php-ext-install mysqli pdo pdo_mysql exif

# 警告がブラウザに出ないようにphp.iniの設定を変更
RUN sed -i 's/display_errors = On/display_errors = Off/' /usr/local/etc/php/php.ini-development && \
    sed -i 's/error_reporting = .*$/error_reporting = 24575/' /usr/local/etc/php/php.ini-development && \
    cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Webアプリケーションのコードをコピー
COPY ./app/ /var/www/html/