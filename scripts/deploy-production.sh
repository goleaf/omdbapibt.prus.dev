#!/usr/bin/env bash

# Ensure the script is always executed with Bash even if invoked via `sh`.
if [ -z "${BASH_VERSION:-}" ]; then
    exec bash "$0" "$@"
fi

set -euo pipefail

if [ -f artisan ]; then
    if command -v composer >/dev/null 2>&1; then
        COMPOSER_BIN="composer"
    elif [ -f composer.phar ]; then
        COMPOSER_BIN="php composer.phar"
    else
        echo "Composer is required but not installed." >&2
        exit 1
    fi

    if ! php -r 'exit(extension_loaded("redis") ? 0 : 1);'; then
        export REDIS_CLIENT="${REDIS_CLIENT:-predis}"
    fi

    if ! command -v npm >/dev/null 2>&1; then
        echo "npm must be installed to build frontend assets." >&2
        exit 1
    fi

    # Temporarily unset NODE_ENV to ensure devDependencies are installed
    NODE_ENV= npm ci
    npm run build

    if [ ! -d public/build ] || [ ! -f public/build/manifest.json ]; then
        echo "Vite build output not found. Ensure npm run build succeeded." >&2
        exit 1
    fi

    # Running the full PHP test suite requires additional dev dependencies and
    # infrastructure (Redis, large seeded datasets, etc.). Enable it explicitly
    # by exporting RUN_PHP_TESTS=1 before invoking the script.
    RUN_PHP_TESTS=${RUN_PHP_TESTS:-0}
    COMPOSER_DEV_INSTALLED=0

    if [ "$RUN_PHP_TESTS" -eq 1 ]; then
        if [ ! -f vendor/autoload.php ] || [ ! -x vendor/bin/phpunit ]; then
            $COMPOSER_BIN install --no-interaction --prefer-dist
            COMPOSER_DEV_INSTALLED=1
        fi
    elif [ ! -f vendor/autoload.php ]; then
        $COMPOSER_BIN install --no-dev --no-interaction --prefer-dist --optimize-autoloader
    fi

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

    if [ "$JS_TESTS_PRESENT" -eq 0 ]; then
        npm prune --omit=dev
    fi

    # Clear config cache before checking database
    php artisan config:clear
    
    # Check if database exists and is not corrupted
    DB_FILE="database/database.sqlite"
    if [ ! -f "$DB_FILE" ] || ! php artisan db:show >/dev/null 2>&1; then
        echo "Database missing or corrupted. Rebuilding..."
        rm -f database/database.sqlite database/database.sqlite-shm database/database.sqlite-wal
        touch database/database.sqlite
        chmod 664 database/database.sqlite
        php artisan migrate:fresh --seed --force
    else
        # Run migrations before clearing cache to ensure cache table exists
        php artisan migrate --force
        php artisan cache:clear
        php artisan db:seed --force
    fi

    echo "Generating API documentation with Scribe..."
    php artisan scribe:generate --ansi --force

    if [ ! -f storage/app/private/scribe/openapi.yaml ]; then
        echo "Scribe documentation output missing (storage/app/private/scribe/openapi.yaml)." >&2
        exit 1
    fi

    echo "Generating PHP documentation with phpDocumentor..."
    PHPDOC_BIN=""
    if [ -x vendor/bin/phpdoc ]; then
        PHPDOC_BIN="vendor/bin/phpdoc"
    fi

    PHPDOC_STORAGE_DIR="storage/app/private/phpdoc"
    PHPDOC_HTML_DIR="$PHPDOC_STORAGE_DIR/html"
    PHPDOC_CACHE_DIR="$PHPDOC_STORAGE_DIR/cache"
    PHPDOC_PHAR="$PHPDOC_STORAGE_DIR/phpDocumentor.phar"

    mkdir -p "$PHPDOC_STORAGE_DIR"

    if [ -z "$PHPDOC_BIN" ]; then
        if [ ! -f "$PHPDOC_PHAR" ]; then
            PHPDOC_RELEASE="v3.8.1"
            if command -v curl >/dev/null 2>&1; then
                curl -fsSL -o "$PHPDOC_PHAR" "https://github.com/phpDocumentor/phpDocumentor/releases/download/${PHPDOC_RELEASE}/phpDocumentor.phar"
            elif command -v wget >/dev/null 2>&1; then
                wget -q -O "$PHPDOC_PHAR" "https://github.com/phpDocumentor/phpDocumentor/releases/download/${PHPDOC_RELEASE}/phpDocumentor.phar"
            else
                echo "phpDocumentor is not installed and neither curl nor wget are available to download it." >&2
                exit 1
            fi
        fi

        if [ ! -f "$PHPDOC_PHAR" ]; then
            echo "phpDocumentor PHAR download failed." >&2
            exit 1
        fi

        chmod +x "$PHPDOC_PHAR"
        PHPDOC_BIN="php $PHPDOC_PHAR"
    fi

    rm -rf "$PHPDOC_HTML_DIR" "$PHPDOC_CACHE_DIR"

    if ! $PHPDOC_BIN --ansi --config=phpdoc.dist.xml; then
        echo "phpDocumentor generation failed." >&2
        exit 1
    fi

    if [ ! -f "$PHPDOC_HTML_DIR/index.html" ]; then
        echo "phpDocumentor output missing ($PHPDOC_HTML_DIR/index.html)." >&2
        exit 1
    fi

    # Set proper permissions for SQLite database and directory
    # Database directory needs write permission for SQLite WAL files
    chmod 775 database
    chmod 664 database/database.sqlite
    [ -f database/database.sqlite-shm ] && chmod 664 database/database.sqlite-shm
    [ -f database/database.sqlite-wal ] && chmod 664 database/database.sqlite-wal
    
    # Set proper permissions for Laravel storage directories
    chmod -R 775 storage bootstrap/cache
    
    # Ensure proper ownership (www user for web server)
    if command -v chown >/dev/null 2>&1; then
        chown -R www:www database/ storage/ bootstrap/cache/ 2>/dev/null || true
    fi
    
    # Cache config and optimize after database is ready
    if php -r 'exit(extension_loaded("redis") ? 0 : 1);'; then
        php artisan config:cache
        php artisan optimize
    else
        echo "Redis extension not available; skipping cache and optimize steps that require it." >&2
        echo "Config cache clear skipped because Redis is unavailable." >&2
    fi

    if php -r 'exit(extension_loaded("redis") ? 0 : 1);'; then
        php artisan horizon:terminate || true
    else
        echo "Skipping Horizon termination because Redis is unavailable." >&2
    fi

    # Run automated tests as a final verification step
    if [ "$RUN_PHP_TESTS" -eq 1 ]; then
        if ! $COMPOSER_BIN test --ansi; then
            echo "PHP test suite failed." >&2
            exit 1
        fi

        if [ "$COMPOSER_DEV_INSTALLED" -eq 1 ]; then
            $COMPOSER_BIN install --no-dev --no-interaction --prefer-dist --optimize-autoloader
        fi
    fi

    if [ "${JS_TESTS_PRESENT:-0}" -eq 1 ]; then
        npm run test
        npm prune --omit=dev
    fi
else
    echo "Run this script from the project root where artisan is located." >&2
    exit 1
fi
