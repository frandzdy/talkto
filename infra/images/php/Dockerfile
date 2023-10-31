FROM php:8.2.12-fpm-bullseye

ENV DEBIAN_FRONTEND noninteractive

## Installation des utilitaires
RUN apt-get update && apt-get install -y curl vim nano default-mysql-client git zip

## Config Timezone
RUN rm /etc/localtime && ln -s /usr/share/zoneinfo/Europe/Paris /etc/localtime

## PHP Extensions & Composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions gd pdo_mysql intl zip bcmath sockets redis xsl && install-php-extensions @composer

## PHPCS
RUN curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar && mv phpcs.phar /usr/local/bin/phpcs && chmod u+x /usr/local/bin/phpcs

## WHTMLTOPDF
RUN curl -LO https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6-1/wkhtmltox_0.12.6-1.buster_amd64.deb && \
    apt-get update && apt-get install -y --no-install-recommends ./wkhtmltox_0.12.6-1.buster_amd64.deb && \
    rm wkhtmltox_0.12.6-1.buster_amd64.deb

WORKDIR /var/www/app
