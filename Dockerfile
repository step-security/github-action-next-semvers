FROM wyrihaximusnet/php:8.2-nts-alpine-slim-dev-root@sha256:88782862db4b194a45ecd55f57dc39a5dfff4bdf5629c0a163b1489eff5824e8 AS dependencies

RUN mkdir /workdir
COPY next.php /workdir
RUN mkdir /workdir/src
COPY src/ /workdir/src
COPY ./composer.json /workdir
COPY ./composer.lock /workdir
WORKDIR /workdir

RUN composer install --ansi --no-progress --no-interaction --prefer-dist --no-dev -o

FROM wyrihaximusnet/php:8.2-nts-alpine-slim-root@sha256:2d38a2d121af10a99d36567a3b794a0735d67e22503d621a8649adc3b4edc77d AS runtime

COPY --from=dependencies /workdir/ /workdir/

ENTRYPOINT ["php", "/workdir/next.php"]
