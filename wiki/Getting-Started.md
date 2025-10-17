# Getting Started with OMDb API BT Platform

This guide will help you set up and run the OMDb API BT Platform on your local machine.

## Prerequisites

Before you begin, ensure you have the following installed:

- **PHP 8.3 or higher** with required extensions:
  - BCMath
  - Ctype
  - cURL
  - DOM
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PCRE
  - PDO
  - Tokenizer
  - XML

- **Composer** - PHP dependency manager
- **Node.js 20+** and **npm** - JavaScript runtime and package manager
- **MySQL** or **MariaDB** - Database server (SQLite for testing)
- **Redis** - Cache and queue backend

## Quick Setup

### 1. Clone the Repository

```bash
git clone https://github.com/goleaf/omdbapibt.prus.dev.git
cd omdbapibt.prus.dev
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 3. Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Environment Variables

Edit the `.env` file and configure the following:

```env
# Application
APP_NAME="OMDb API BT"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=omdbapibt
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cache & Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Stripe (for subscriptions)
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=your_webhook_secret

# OMDB API
OMDB_API_KEY=your_omdb_api_key

# TMDb API
TMDB_API_KEY=your_tmdb_api_key
```

### 5. Database Setup

```bash
# Run migrations
php artisan migrate

# Seed the database with baseline data
php artisan db:seed
```

The seeder will create:
- 10 common languages
- Production countries
- Movie genres from TMDb
- 1,000 demo users (first 2 are admins)

To find admin credentials:

```bash
php artisan tinker --execute="App\\Models\\User::where('role', App\\Enums\\UserRole::Admin->value)->pluck('email')"
```

All seeded users have password: `password`

### 6. Build Frontend Assets

```bash
# Development build with hot reload
npm run dev

# Or production build
npm run build
```

### 7. Start the Application

#### Option A: Use Composer Dev Script (Recommended)

```bash
composer run dev
```

This starts 4 concurrent processes:
- Laravel development server (port 8000)
- Queue listener
- Laravel Pail (log viewer)
- Vite dev server

#### Option B: Manual Start

In separate terminal windows:

```bash
# Terminal 1: Application server
php artisan serve

# Terminal 2: Queue worker
php artisan queue:work

# Terminal 3: Horizon (queue monitoring)
php artisan horizon

# Terminal 4: Frontend dev server
npm run dev
```

### 8. Access the Application

- **Application:** http://localhost:8000
- **Horizon Dashboard:** http://localhost:8000/horizon

## Initial Configuration

### Configure Subscription Plans

Edit `config/subscriptions.php`:

```php
return [
    'monthly_price_id' => env('STRIPE_MONTHLY_PRICE'),
    'yearly_price_id' => env('STRIPE_YEARLY_PRICE'),
    // ... additional settings
];
```

### Configure API Rate Limits

Edit `config/services.php` for OMDB API settings:

```php
'omdb' => [
    'key' => env('OMDB_API_KEY'),
    'base_url' => 'http://www.omdbapi.com',
    'max_requests_per_minute' => 60,
    // ... additional settings
],
```

## Running OMDB Key Discovery

To start the automated OMDB API key discovery system:

```bash
php artisan omdb:bruteforce
```

This command will:
1. Generate random API keys
2. Validate them asynchronously
3. Use valid keys to enrich movie metadata

See [OMDB Key Management](OMDB-Key-Management) for detailed information.

## Testing

### Run the Test Suite

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/Console/OmdbBruteforceCommandTest.php

# Run with parallel execution
php artisan test --parallel
```

### Code Formatting

```bash
# Format all PHP files
vendor/bin/pint

# Check formatting without fixing
vendor/bin/pint --test
```

## Common Issues

### Port Already in Use

If port 8000 is already in use:

```bash
php artisan serve --port=8001
```

### Permission Issues

Ensure storage and cache directories are writable:

```bash
chmod -R 775 storage bootstrap/cache
```

### Redis Connection Failed

Ensure Redis is running:

```bash
redis-cli ping
# Should return: PONG
```

### NPM Build Errors

Clear node_modules and reinstall:

```bash
rm -rf node_modules package-lock.json
npm install
```

## Next Steps

- **[User Guide](User-Guide)** - Learn how to use the platform
- **[Admin Guide](Admin-Guide)** - Administrative features
- **[API Documentation](API-Documentation)** - API endpoints
- **[Development Guide](Development-Guide)** - Contributing to the project

## Getting API Keys

### OMDB API Key

1. Visit https://www.omdbapi.com/apikey.aspx
2. Choose a plan (free tier available)
3. Verify your email
4. Add key to `.env` as `OMDB_API_KEY`

### TMDb API Key

1. Visit https://www.themoviedb.org/settings/api
2. Create an account if needed
3. Request an API key
4. Add key to `.env` as `TMDB_API_KEY`

### Stripe API Keys

1. Visit https://dashboard.stripe.com/test/apikeys
2. Create an account if needed
3. Copy publishable and secret keys
4. Add to `.env` as `STRIPE_KEY` and `STRIPE_SECRET`
5. Set up webhook endpoint for production

---

**Need help?** Check the [Troubleshooting](Troubleshooting) page or [open an issue](https://github.com/goleaf/omdbapibt.prus.dev/issues).

