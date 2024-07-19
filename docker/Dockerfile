FROM wordpress:latest

RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp \
    && wp --info

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
