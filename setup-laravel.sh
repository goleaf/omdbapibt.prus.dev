#!/usr/bin/env bash

if [ -z "${BASH_VERSION:-}" ]; then
    exec /usr/bin/env bash "$0" "$@"
fi

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT_DIR"

usage() {
    cat <<'USAGE'
Usage: ./setup-laravel.sh [options]

Bootstrap or update the Laravel application after cloning or pulling the repository.

Options:
  --fresh         Drop all tables and re-run every migration with seeding (uses migrate:fresh).
  --skip-node     Skip installing/updating Node dependencies.
  --skip-tests    Skip executing the seeder regression tests.
  -h, --help      Display this help message.
USAGE
}

section() {
    printf '\n\033[1;34m==> %s\033[0m\n' "$1"
}

ensure_app_key() {
    if [[ ! -f .env ]]; then
        printf '\033[1;31mError:\033[0m Cannot ensure APP_KEY because .env is missing.\n' >&2
        exit 1
    fi

    if ! grep -q '^APP_KEY=' .env; then
        printf '\033[1;31mError:\033[0m APP_KEY entry is missing from .env.\n' >&2
        exit 1
    fi

    local current_key
    current_key="$(grep '^APP_KEY=' .env | head -n1 | cut -d'=' -f2-)"

    if [[ -z "$current_key" ]]; then
        local generated_key
        generated_key="$(php artisan key:generate --show | tr -d '\r' | tail -n1)"

        if [[ -z "$generated_key" ]]; then
            printf '\033[1;31mError:\033[0m Failed to generate a new APP_KEY value.\n' >&2
            exit 1
        fi

        php -r '$path = $argv[1]; $key = $argv[2]; $contents = file_get_contents($path); if ($contents === false) { fwrite(STDERR, "Unable to read $path\\n"); exit(1); } $updated = preg_replace("/^APP_KEY=.*$/m", "APP_KEY=".$key, $contents, 1, $count); if ($updated === null || $count === 0) { fwrite(STDERR, "APP_KEY entry missing in $path\\n"); exit(1); } if (file_put_contents($path, $updated) === false) { fwrite(STDERR, "Unable to write $path\\n"); exit(1); }' .env "$generated_key"

        printf 'Configured APP_KEY in .env.\n'
    else
        printf 'APP_KEY already defined in .env.\n'
    fi
}

require_command() {
    if ! command -v "$1" >/dev/null 2>&1; then
        printf '\033[1;31mError:\033[0m Required command "%s" is not available in PATH.\n' "$1" >&2
        exit 1
    fi
}

run_cmd() {
    printf '\033[0;36m$ %s\033[0m\n' "$*"
    "$@"
}

FRESH_SETUP=0
SKIP_NODE=0
SKIP_TESTS=0

while [[ $# -gt 0 ]]; do
    case "$1" in
        --fresh)
            FRESH_SETUP=1
            shift
            ;;
        --skip-node)
            SKIP_NODE=1
            shift
            ;;
        --skip-tests)
            SKIP_TESTS=1
            shift
            ;;
        -h|--help)
            usage
            exit 0
            ;;
        *)
            printf '\033[1;31mError:\033[0m Unknown option "%s".\n\n' "$1" >&2
            usage
            exit 1
            ;;
    esac
done

section "Verifying required tooling"
require_command php
require_command composer
require_command npm

section "Installing PHP dependencies"
run_cmd composer install --no-interaction --prefer-dist --optimize-autoloader

if [[ $SKIP_NODE -eq 0 ]]; then
    section "Installing Node dependencies"
    run_cmd npm install --no-progress
else
    section "Skipping Node dependency installation (per flag)"
fi

if [[ ! -f .env ]]; then
    section "Creating environment file"
    run_cmd cp .env.example .env
fi

section "Ensuring application key"
ensure_app_key

section "Ensuring SQLite database file exists"
DB_FILE="database/database.sqlite"
if [[ ! -f "$DB_FILE" ]]; then
    run_cmd mkdir -p "$(dirname "$DB_FILE")"
    run_cmd touch "$DB_FILE"
else
    printf 'SQLite database file already present at %s.\n' "$DB_FILE"
fi

section "Clearing compiled caches"
printf '\033[0;36m$ %s\033[0m\n' "php artisan optimize:clear"
set +e
OPTIMIZE_OUTPUT="$(php artisan optimize:clear 2>&1)"
OPTIMIZE_STATUS=$?
set -e
printf '%s\n' "$OPTIMIZE_OUTPUT"

if [[ $OPTIMIZE_STATUS -ne 0 ]]; then
    if echo "$OPTIMIZE_OUTPUT" | grep -qi 'no such table: cache'; then
        printf 'Cache table is not yet present; continuing after migrations.\n'
    else
        exit $OPTIMIZE_STATUS
    fi
fi

if [[ $FRESH_SETUP -eq 1 ]]; then
    section "Running fresh database migrations with seeders"
    run_cmd php artisan migrate:fresh --force --seed
else
    section "Running database migrations with seeders"
    run_cmd php artisan migrate --force --seed
fi

section "Executing explicit database seeders"
run_cmd php artisan db:seed --force

if [[ $SKIP_TESTS -eq 0 ]]; then
    section "Running seeder regression tests"
    run_cmd php artisan test --filter=BaselineSeedersTest
else
    section "Skipping seeder regression tests (per flag)"
fi

section "Setup complete"
printf '\033[1;32mLaravel environment is ready.\033[0m\n'
