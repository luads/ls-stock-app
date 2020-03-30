#!/usr/bin/env sh

BASEDIR=$(dirname $0)

php -S 0.0.0.0:"${PORT}" "${BASEDIR}"/../public/index.php
