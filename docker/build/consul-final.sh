#!/usr/bin/env bash

set -e

cd /app
php artisan cache:clear
php artisan route:cache
exit 0
