# OMDb AI Movie Platform

A Laravel 11 application that ingests data from OMDb and TMDb, offers multilingual metadata management, and powers subscription-based access for the omdbapibt.prus.dev movie catalogue.

## Production URL

- **Live application:** https://omdbapibt.prus.dev

## Local Development

1. Copy the example environment file and update the values to match your local stack:

   ```bash
   cp .env.example .env
   ```

2. Install dependencies and generate the application key:

   ```bash
   composer install
   npm install
   php artisan key:generate
   ```

3. Configure your database connection in `.env`, then run the migrations and seed the baseline reference data (languages, countries, genres) alongside the default local test user:

   ```bash
   php artisan migrate
   php artisan db:seed --class=BaselineDataSeeder
   php artisan db:seed
   ```

4. Start the application services:

   ```bash
   php artisan serve
   npm run dev
   php artisan horizon
   ```

## Baseline Reference Data

The following seeders keep the core lookup tables synchronized with TMDb identifiers:

- `LanguagesTableSeeder`
- `CountriesTableSeeder`
- `GenresTableSeeder`

They are aggregated inside `BaselineDataSeeder`, which is safe to run repeatedly thanks to `upsert` operations. The deployment tooling (documented below) executes this seeder automatically to guarantee consistency between environments.

## Production Configuration

Configure the production environment with the following keys:

| Variable | Description | Recommended value / notes |
| --- | --- | --- |
| `APP_ENV` | Laravel environment | `production` |
| `APP_URL` | Public application URL | `https://omdbapibt.prus.dev` |
| `APP_DEBUG` | Disable debug output | `false` |
| `QUEUE_CONNECTION` | Queue backend used by Horizon | `redis` |
| `HORIZON_PREFIX` | Prefix for Horizon Redis keys | `omdbapibt` (or another unique value) |
| `HORIZON_ENV` | Horizon environment flag | `production` |
| `CACHE_DRIVER` | Cache driver | `redis` |
| `SESSION_DRIVER` | Session driver | `redis` |
| `REDIS_HOST` / `REDIS_PASSWORD` | Redis connection | Values supplied by your managed Redis instance |
| `STRIPE_KEY` / `STRIPE_SECRET` | Stripe live keys | Copy from the Stripe **Live mode** dashboard |
| `STRIPE_WEBHOOK_SECRET` | Stripe webhook signing secret | Generated from the Stripe webhook endpoint targeting `/stripe/webhook` |
| `OMDB_API_KEY` | OMDb paid tier key | Purchase from [omdbapi.com/apikey.aspx](https://www.omdbapi.com/apikey.aspx) |
| `TMDB_API_KEY` | TMDb API key | Generate from the TMDb dashboard |
| `MIXPANEL_TOKEN` (optional) | Analytics token if enabled | Configure only if analytics are enabled |

Store these secrets in the server `.env` file as well as the GitHub repository secrets listed in the next section when using the automated deployment workflow.

### Queue & Horizon Notes

- Ensure Redis is reachable from the application host.
- The deployment script restarts Horizon with `horizon:terminate` followed by a background `php artisan horizon` invocation, so Supervisor or systemd should not simultaneously manage Horizon.
- If you rely on Supervisor, update `/etc/supervisor/conf.d/horizon.conf` to call the included `scripts/deploy-production.sh` logic or omit the final background start and allow Supervisor to handle the process lifecycle.

## Deployment

### Automated (GitHub Actions)

The workflow in `.github/workflows/deploy.yml` performs a zero-downtime deployment over SSH:

1. Configure the following GitHub secrets:
   - `PROD_SSH_HOST` – Server hostname or IP address.
   - `PROD_SSH_USER` – SSH user with permission to deploy the application.
   - `PROD_SSH_KEY` – Private key for the SSH user (in PEM format).
   - `PROD_SSH_PORT` – SSH port (use `22` if unspecified).
   - `PROD_APP_DIR` – Absolute path to the application directory on the server (for example `/var/www/omdbapibt.prus.dev`).

2. Optionally add repository variables or organization secrets for the production API keys if you prefer to template the `.env` file during deployment.

3. Trigger the workflow manually via **Run workflow** or push to the `main` branch. The job will:
   - Pull the latest code on the server.
   - Install optimized Composer dependencies.
   - Warm the config, route, and view caches.
   - Execute `scripts/deploy-production.sh`, which runs migrations with `--force`, seeds the baseline data, and restarts Horizon workers.

### Manual Server Deployment

To run the same steps directly on the production host:

```bash
export APP_DIR=/var/www/omdbapibt.prus.dev
bash scripts/deploy-production.sh
```

Override `PHP_BIN` if PHP is not available as `php` in your PATH.

The script performs the following operations inside `$APP_DIR`:

1. `php artisan migrate --force`
2. `php artisan db:seed --class=BaselineDataSeeder --force`
3. `php artisan horizon:terminate` (ignored if Horizon is not running)
4. `php artisan horizon` (backgrounded with `nohup`)

## Stripe Credentials (recap)

For completeness, obtain and configure the Stripe credentials as follows:

1. Switch to **Live mode** in the Stripe dashboard.
2. Copy the live publishable and secret keys to `STRIPE_KEY` and `STRIPE_SECRET`.
3. Create a webhook endpoint pointing to `https://omdbapibt.prus.dev/stripe/webhook` and copy the signing secret into `STRIPE_WEBHOOK_SECRET`.
4. Whenever the keys rotate, update the production `.env` file and redeploy so the cached configuration reflects the change.

## Testing

Run the application test suite locally before pushing changes:

```bash
php artisan test
```

This project inherits the MIT license from Laravel.
