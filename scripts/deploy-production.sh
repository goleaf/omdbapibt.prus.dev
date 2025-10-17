#!/usr/bin/env bash

# Ensure the script is always executed with Bash even if invoked via `sh`.
if [ -z "${BASH_VERSION:-}" ]; then
    exec /usr/bin/env bash "$0" "$@"
fi

set -euo pipefail
set -o errtrace

on_error() {
    local exit_code=$?
    local line_no=${BASH_LINENO[0]:-unknown}
    printf '\n\033[1;31m✖ Deployment failed\033[0m (exit code %s at line %s).\n' "$exit_code" "$line_no" >&2
    printf 'Review the log above for details.\n' >&2
}

trap on_error ERR

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")"/.. && pwd)"
cd "$ROOT_DIR"

banner() {
    printf '\033[1;35m'
    cat <<'BANNER'
 ____        _           _                      _   _             _   _             _          
|  _ \  ___ | |__  _   _| |__   ___  _ __   ___| |_(_)_ __   __ _| |_(_) ___  _ __ (_) ___ ___ 
| | | |/ _ \| '_ \| | | | '_ \ / _ \| '_ \ / __| __| | '_ \ / _` | __| |/ _ \| '_ \| |/ __/ __|
| |_| | (_) | |_) | |_| | |_) | (_) | | | | (__| |_| | | | | (_| | |_| | (_) | | | | | (__\__ \
|____/ \___/|_.__/ \__,_|_.__/ \___/|_| |_|\___|\__|_|_| |_|\__,_|\__|_|\___/|_| |_|_|\___|___/
BANNER
    printf '\033[0m\n'
}

usage() {
    cat <<'USAGE'
Usage: ./scripts/deploy-production.sh [options]

Prepare and deploy the Laravel application. Combines the original setup and
production deployment workflows.

Options:
  --fresh         Drop all tables and re-run every migration with seeding.
  --install-only  Use Composer install instead of update (recommended for CI/prod).
  --skip-composer Skip Composer dependency installation/update.
  --skip-node     Skip Node dependency installation/update and asset build.
  --skip-tests    Skip running database seeder regression tests.
  -h, --help      Display this help message.

Environment variables:
  RUN_PHP_TESTS   Set to 1 to execute the Composer test suite after deployment.
USAGE
}

section() {
    printf '\n\033[1;34m➡ %s\033[0m\n' "$1"
}

info() {
    printf '\033[0;36m• %s\033[0m\n' "$1"
}

success() {
    printf '\033[1;32m✓ %s\033[0m\n' "$1"
}

warning() {
    printf '\033[1;33m! %s\033[0m\n' "$1"
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

        php -r '$path = $argv[1]; $key = $argv[2]; $contents = file_get_contents($path); if ($contents === false) { fwrite(STDERR, "Unable to read $path\n"); exit(1); } $updated = preg_replace("/^APP_KEY=.*$/m", "APP_KEY=".$key, $contents, 1, $count); if ($updated === null || $count === 0) { fwrite(STDERR, "APP_KEY entry missing in $path\n"); exit(1); } if (file_put_contents($path, $updated) === false) { fwrite(STDERR, "Unable to write $path\n"); exit(1); }' .env "$generated_key"

        success 'Configured APP_KEY in .env.'
    else
        info 'APP_KEY already defined in .env.'
    fi
}

FRESH_SETUP=0
SKIP_COMPOSER=0
COMPOSER_INSTALL_ONLY=0
SKIP_NODE=0
SKIP_TESTS=0

while [[ $# -gt 0 ]]; do
    case "$1" in
        --fresh)
            FRESH_SETUP=1
            shift
            ;;
        --install-only)
            COMPOSER_INSTALL_ONLY=1
            shift
            ;;
        --skip-composer)
            SKIP_COMPOSER=1
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

if [[ ! -f artisan ]]; then
    printf '\033[1;31mError:\033[0m Run this script from the project root where artisan is located.\n' >&2
    exit 1
fi

banner

section "Verifying required tooling"
require_command php

COMPOSER_BIN=""
if [[ $SKIP_COMPOSER -eq 0 ]]; then
    if command -v composer >/dev/null 2>&1; then
        COMPOSER_BIN="composer"
    elif [[ -f composer.phar ]]; then
        COMPOSER_BIN="php composer.phar"
    else
        printf '\033[1;31mError:\033[0m Composer is required but not installed.\n' >&2
        exit 1
    fi
fi

if [[ $SKIP_NODE -eq 0 ]]; then
    require_command npm
fi

if ! php -r 'exit(extension_loaded("redis") ? 0 : 1);'; then
    export REDIS_CLIENT="${REDIS_CLIENT:-predis}"
fi

RUN_PHP_TESTS=${RUN_PHP_TESTS:-0}
if [[ $SKIP_TESTS -eq 1 ]]; then
    RUN_PHP_TESTS=0
fi

RUN_SEEDER_TESTS=$((SKIP_TESTS == 0 ? 1 : 0))
NEEDS_DEV_DEPENDENCIES=0
if [[ $RUN_SEEDER_TESTS -eq 1 || $RUN_PHP_TESTS -eq 1 ]]; then
    NEEDS_DEV_DEPENDENCIES=1
fi

if [[ $SKIP_COMPOSER -eq 1 && $RUN_PHP_TESTS -eq 1 ]]; then
    warning 'Skipping PHP tests because Composer steps are disabled.'
    RUN_PHP_TESTS=0
    NEEDS_DEV_DEPENDENCIES=$((RUN_SEEDER_TESTS == 1 ? 1 : 0))
fi

section "Ensuring environment file"
if [[ ! -f .env ]]; then
    run_cmd cp .env.example .env
else
    info '.env file already present.'
fi

section "Ensuring application key"
ensure_app_key

section "Ensuring SQLite database file exists"
DB_FILE="database/database.sqlite"
if [[ ! -f "$DB_FILE" ]]; then
    run_cmd mkdir -p "$(dirname "$DB_FILE")"
    run_cmd touch "$DB_FILE"
else
    info "SQLite database file already present at $DB_FILE."
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
        info 'Cache table is not yet present; continuing after migrations.'
    else
        exit $OPTIMIZE_STATUS
    fi
fi

COMPOSER_UPDATE_ARGS=(update --no-interaction --prefer-dist --optimize-autoloader)
if [[ $NEEDS_DEV_DEPENDENCIES -eq 0 ]]; then
    COMPOSER_UPDATE_ARGS+=(--no-dev)
fi

COMPOSER_DEV_INSTALLED=$NEEDS_DEV_DEPENDENCIES

if [[ $SKIP_COMPOSER -eq 0 ]]; then
    if [[ $COMPOSER_INSTALL_ONLY -eq 1 ]]; then
        section "Installing PHP dependencies"
        run_cmd $COMPOSER_BIN install --no-interaction --prefer-dist --optimize-autoloader $([[ $NEEDS_DEV_DEPENDENCIES -eq 0 ]] && printf '%s' '--no-dev')
    else
        section "Updating PHP dependencies"
        run_cmd $COMPOSER_BIN "${COMPOSER_UPDATE_ARGS[@]}"
    fi
else
    section "Skipping Composer dependency steps"
    COMPOSER_DEV_INSTALLED=0
fi

if [[ $SKIP_NODE -eq 0 ]]; then
    section "Updating Node dependencies"
    NODE_ENV= run_cmd npm update

    section "Installing Node dependencies"
    if [[ -f package-lock.json ]]; then
        NODE_ENV= run_cmd npm ci --no-progress
    else
        NODE_ENV= run_cmd npm install --no-progress
    fi

    section "Building frontend assets"
    run_cmd npm run build

    section "Validating Vite manifest"
    php <<'PHP'
<?php
declare(strict_types=1);

$manifestPath = getcwd() . '/public/build/manifest.json';
$manifestContents = file_get_contents($manifestPath);

if ($manifestContents === false) {
    fwrite(STDERR, 'Unable to read Vite manifest.' . PHP_EOL);
    exit(1);
}

$manifest = json_decode($manifestContents, true, 512, JSON_THROW_ON_ERROR);
$missing = [];

foreach ($manifest as $entry) {
    if (isset($entry['file']) && !is_file('public/build/' . $entry['file'])) {
        $missing[] = $entry['file'];
    }

    if (!empty($entry['css'])) {
        foreach ($entry['css'] as $css) {
            if (!is_file('public/build/' . $css)) {
                $missing[] = $css;
            }
        }
    }

    if (!empty($entry['assets'])) {
        foreach ($entry['assets'] as $asset) {
            if (!is_file('public/build/' . $asset)) {
                $missing[] = $asset;
            }
        }
    }
}

if ($missing !== []) {
    fwrite(STDERR, 'Missing assets referenced by manifest: ' . implode(', ', $missing) . PHP_EOL);
    exit(1);
}
PHP

    JS_TESTS_PRESENT=0
    if command -v node >/dev/null 2>&1; then
        if [ "$(node -pe "(()=>{try{return require('./package.json').scripts?.test ? '1' : ''; } catch (error) { return ''; }})()")" = "1" ]; then
            JS_TESTS_PRESENT=1
        fi
    fi
else
    section "Skipping Node dependency and build steps (per flag)"
    JS_TESTS_PRESENT=0
fi

section "Clearing configuration cache"
run_cmd php artisan config:clear

section "Preparing database"
if [[ $FRESH_SETUP -eq 1 ]]; then
    run_cmd rm -f database/database.sqlite database/database.sqlite-shm database/database.sqlite-wal
    run_cmd touch database/database.sqlite
    run_cmd chmod 664 database/database.sqlite
    run_cmd php artisan migrate:fresh --seed --force
else
    if [[ ! -f "$DB_FILE" ]] || ! php artisan db:show >/dev/null 2>&1; then
        warning 'Database missing or corrupted. Rebuilding...'
        run_cmd rm -f database/database.sqlite database/database.sqlite-shm database/database.sqlite-wal
        run_cmd touch database/database.sqlite
        run_cmd chmod 664 database/database.sqlite
        run_cmd php artisan migrate:fresh --seed --force
    else
        run_cmd php artisan migrate --force
        run_cmd php artisan cache:clear
    fi
fi

run_cmd php artisan db:seed --force

section "Generating API documentation with Scribe"
run_cmd php artisan scribe:generate --ansi --force

if [[ ! -f storage/app/private/scribe/openapi.yaml ]]; then
    printf '\033[1;31mError:\033[0m Scribe documentation output missing (storage/app/private/scribe/openapi.yaml).\n' >&2
    exit 1
fi

section "Generating PHP documentation with phpDocumentor"
if [[ -f phpdoc.dist.xml ]]; then
    PHPDOC_BIN=""
    if [[ -x vendor/bin/phpdoc ]]; then
        PHPDOC_BIN="vendor/bin/phpdoc"
    fi

    PHPDOC_STORAGE_DIR="storage/app/private/phpdoc"
    PHPDOC_HTML_DIR="$PHPDOC_STORAGE_DIR/html"
    PHPDOC_CACHE_DIR="$PHPDOC_STORAGE_DIR/cache"
    PHPDOC_PHAR="$PHPDOC_STORAGE_DIR/phpDocumentor.phar"

    run_cmd mkdir -p "$PHPDOC_STORAGE_DIR"

    if [[ -z "$PHPDOC_BIN" ]]; then
        if [[ ! -f "$PHPDOC_PHAR" ]]; then
            PHPDOC_RELEASE="v3.8.1"
            if command -v curl >/dev/null 2>&1; then
                run_cmd curl -fsSL -o "$PHPDOC_PHAR" "https://github.com/phpDocumentor/phpDocumentor/releases/download/${PHPDOC_RELEASE}/phpDocumentor.phar"
            elif command -v wget >/dev/null 2>&1; then
                run_cmd wget -q -O "$PHPDOC_PHAR" "https://github.com/phpDocumentor/phpDocumentor/releases/download/${PHPDOC_RELEASE}/phpDocumentor.phar"
            else
                printf '\033[1;31mError:\033[0m phpDocumentor is not installed and neither curl nor wget are available to download it.\n' >&2
                exit 1
            fi
        fi

        if [[ ! -f "$PHPDOC_PHAR" ]]; then
            printf '\033[1;31mError:\033[0m phpDocumentor PHAR download failed.\n' >&2
            exit 1
        fi

        run_cmd chmod +x "$PHPDOC_PHAR"
        PHPDOC_BIN="php $PHPDOC_PHAR"
    fi

    run_cmd rm -rf "$PHPDOC_HTML_DIR" "$PHPDOC_CACHE_DIR"

    if ! $PHPDOC_BIN --ansi --config=phpdoc.dist.xml; then
        printf '\033[1;31mError:\033[0m phpDocumentor generation failed.\n' >&2
        exit 1
    fi

    if [[ ! -f "$PHPDOC_HTML_DIR/index.html" ]]; then
        printf '\033[1;31mError:\033[0m phpDocumentor output missing (%s/index.html).\n' "$PHPDOC_HTML_DIR" >&2
        exit 1
    fi
else
    warning 'phpdoc.dist.xml not found; skipping phpDocumentor generation.'
fi

section "Setting filesystem permissions"
run_cmd chmod 775 database
run_cmd chmod 664 database/database.sqlite
[[ -f database/database.sqlite-shm ]] && run_cmd chmod 664 database/database.sqlite-shm
[[ -f database/database.sqlite-wal ]] && run_cmd chmod 664 database/database.sqlite-wal
run_cmd chmod -R 775 storage bootstrap/cache

if command -v chown >/dev/null 2>&1; then
    chown -R www:www database/ storage/ bootstrap/cache/ 2>/dev/null || true
fi

if php -r 'exit(extension_loaded("redis") ? 0 : 1);'; then
    section "Caching configuration and optimizing application"
    run_cmd php artisan config:cache
    run_cmd php artisan optimize
else
    warning 'Redis extension not available; skipping cache and optimize steps that require it.'
    warning 'Config cache clear skipped because Redis is unavailable.'
fi

if php -r 'exit(extension_loaded("redis") ? 0 : 1);'; then
    section "Terminating Horizon"
    php artisan horizon:terminate || true
else
    warning 'Skipping Horizon termination because Redis is unavailable.'
fi

if [[ $RUN_SEEDER_TESTS -eq 1 ]]; then
    section "Running seeder regression tests"
    run_cmd php artisan test --filter=BaselineSeedersTest
else
    section "Skipping seeder regression tests (per flag)"
fi

if [[ $RUN_PHP_TESTS -eq 1 ]]; then
    section "Running PHP test suite"
    if ! $COMPOSER_BIN test --ansi; then
        printf '\033[1;31mError:\033[0m PHP test suite failed.\n' >&2
        exit 1
    fi
fi

if [[ $COMPOSER_DEV_INSTALLED -eq 1 ]]; then
    section "Installing production Composer dependencies"
    run_cmd $COMPOSER_BIN install --no-dev --no-interaction --prefer-dist --optimize-autoloader
    COMPOSER_DEV_INSTALLED=0
fi

if [[ $SKIP_NODE -eq 0 ]]; then
    if [[ ${JS_TESTS_PRESENT:-0} -eq 1 ]]; then
        section "Running JavaScript tests"
        run_cmd npm run test
        section "Pruning Node dev dependencies"
        run_cmd npm prune --omit=dev
    else
        section "Pruning Node dev dependencies"
        run_cmd npm prune --omit=dev
    fi
fi

if [[ $SKIP_TESTS -eq 0 ]]; then
    section "Setup complete"
    success 'Laravel environment is ready.'
else
    section "Deployment complete"
    success 'Laravel environment prepared (tests skipped).'
fi
