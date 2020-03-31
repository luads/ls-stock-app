#!/usr/bin/env sh

BASEDIR=$(dirname $0)

# Run built-in server
serve -s "${BASEDIR}"/../build -l "${PORT}"
