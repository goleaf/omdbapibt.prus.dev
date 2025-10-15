#!/usr/bin/env bash
set -euo pipefail

APP_DIR=${APP_DIR:-/var/www/omdbapibt.prus.dev}
PHP_BIN=${PHP_BIN:-php}

cd "$APP_DIR"

if [ ! -f artisan ]; then
  echo "Unable to locate artisan in $APP_DIR" >&2
  exit 1
fi

echo "Running database migrations..."
$PHP_BIN artisan migrate --force

echo "Seeding baseline reference data..."
$PHP_BIN artisan db:seed --class=BaselineDataSeeder --force

echo "Restarting Horizon workers..."
$PHP_BIN artisan horizon:terminate >/dev/null 2>&1 || true
nohup $PHP_BIN artisan horizon >/dev/null 2>&1 &

echo "Deployment tasks complete."
