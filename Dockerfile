FROM wyrihaximusnet/php:8.6-nts-alpine-slim-dev-root@sha256:cbc835d808279d9ed91b0f02d722da411a799856e8f9088e1af5fb19eb18cef8 AS dependencies

RUN mkdir /workdir
COPY next.php /workdir
RUN mkdir /workdir/src
COPY src/ /workdir/src
COPY ./composer.json /workdir
COPY ./composer.lock /workdir
WORKDIR /workdir

RUN composer install --ansi --no-progress --no-interaction --prefer-dist --no-dev -o

FROM wyrihaximusnet/php:8.6-nts-alpine-slim-root@sha256:532697d140fbce2c6c87bfc33d35ad49a4b79c67bc3556086e6cebc7f93c2de7 AS runtime

COPY --from=dependencies /workdir/ /workdir/

ENTRYPOINT ["php", "/workdir/next.php"]
