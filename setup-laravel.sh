#!/usr/bin/env bash

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
    run_cmd php artisan key:generate --force
fi

section "Clearing compiled caches"
run_cmd php artisan optimize:clear

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
