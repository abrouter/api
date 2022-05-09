#!/usr/bin/env bash

set -e

TEMPLATE_FILE=/app/docker/config/env.ctmpl
TEMPLATE_OUTPUT_FILE=/app/.env
AFTER_CONSUL_FILE=/app/docker/build/consul-final.sh

touch $TEMPLATE_OUTPUT_FILE

printf "Generating .env.\n"
consul-template \
   -consul-addr=${CONSUL_HTTP_ADDR} \
   -template="${TEMPLATE_FILE}:${TEMPLATE_OUTPUT_FILE}:bash ${AFTER_CONSUL_FILE}"
