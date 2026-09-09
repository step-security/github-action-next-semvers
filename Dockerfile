FROM wyrihaximusnet/php:8.5-nts-alpine-slim-dev-root@sha256:b9c44dedd74b249e501571362f98289734f178f15f737ffc1e21c9613e654f9b AS dependencies

RUN mkdir /workdir
COPY next.php /workdir
RUN mkdir /workdir/src
COPY src/ /workdir/src
COPY ./composer.json /workdir
COPY ./composer.lock /workdir
WORKDIR /workdir

RUN composer install --ansi --no-progress --no-interaction --prefer-dist --no-dev -o

FROM wyrihaximusnet/php:8.5-nts-alpine-slim-root@sha256:3153d8bdcec4ba19bc5333177519e4a1edce2cb1ad5d5bca68a43fcc45e96779 AS runtime

COPY --from=dependencies /workdir/ /workdir/

ENTRYPOINT ["php", "/workdir/next.php"]
