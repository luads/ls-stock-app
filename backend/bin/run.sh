#!/usr/bin/env sh

BASEDIR=$(dirname $0)

# Run DB migrations
php "${BASEDIR}"/console doctrine:schema:update --force

# Run built-in server
php -S 0.0.0.0:"${PORT}" "${BASEDIR}"/../public/index.php
