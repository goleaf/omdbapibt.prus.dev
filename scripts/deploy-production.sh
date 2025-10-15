#!/usr/bin/env bash

set -euo pipefail

if [ -f artisan ]; then
    php artisan config:cache
    php artisan migrate --force
    php artisan db:seed --force
    php artisan optimize
    php artisan horizon:terminate || true
else
    echo "Run this script from the project root where artisan is located." >&2
    exit 1
fi
