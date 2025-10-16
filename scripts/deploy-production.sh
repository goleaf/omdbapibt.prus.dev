#!/usr/bin/env bash

set -euo pipefail

if [ -f artisan ]; then
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

    npm prune --omit=dev

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
        # Only clear cache if database is healthy
        php artisan cache:clear
        php artisan migrate --force
        php artisan db:seed --force
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
    php artisan config:cache
    php artisan optimize
    php artisan horizon:terminate || true
else
    echo "Run this script from the project root where artisan is located." >&2
    exit 1
fi
