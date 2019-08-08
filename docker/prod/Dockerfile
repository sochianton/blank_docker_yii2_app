FROM gitlab.icerockdev.com:4567/docker/prod-alpine-php-7.3:latest

COPY . /app
WORKDIR /app
RUN cp docker/prod/php-override.ini /usr/local/etc/php/conf.d/php-override.ini

VOLUME ["/runtime", "/storage"]
ENTRYPOINT ["/app/docker/deploy/entrypoint.sh"]
CMD ["php-fpm"]
