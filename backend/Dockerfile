FROM ricc/composer-prestissimo as vendor
FROM php:7.4.3-fpm-alpine

ARG DEV
ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /var/www

RUN apk --no-cache --update add \
    $PHPIZE_DEPS \
    icu-dev

# Run composer from builder image
COPY --from=vendor /usr/bin/composer /usr/bin/composer
COPY --from=vendor /tmp /root/.composer
COPY composer.json composer.lock ./

RUN if [ "$DEV" = true ] ; \
    then composer install --no-scripts --no-autoloader; \
    else composer install --no-dev --no-scripts --no-autoloader \
        && composer dump-autoload --optimize ; \
    fi

COPY . ./

ENTRYPOINT ["sh", "/var/www/bin/run.sh"]
