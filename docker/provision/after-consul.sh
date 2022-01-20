#!/usr/bin/env bash

# Exit the script if any statement returns a non-true return value
set -e

cd /app
php artisan cache:clear
php artisan migrate --force
