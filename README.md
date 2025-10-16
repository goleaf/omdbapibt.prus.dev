# OMDb API BT Platform

The OMDb API BT platform is a Laravel 12 application that aggregates metadata from OMDb and TMDb while offering subscription-gated access to premium content. The project couples Laravel Horizon powered queues with Livewire-driven browsing experiences.

- **Production**: https://omdbapibt.prus.dev
- **Stack**: Laravel 12, PHP 8.3, MySQL/MariaDB, Redis, Horizon, Livewire 3, Vite, Tailwind CSS

## Local development

### Requirements

- PHP 8.3 with the extensions defined in `composer.json`
- Node.js 20+ and npm
- Composer
- SQLite (default testing driver) or MySQL/MariaDB for local development
- Redis (queues, cache, Horizon)

### Bootstrap the project

1. Install PHP dependencies:
   ```bash
   composer install
   ```
2. Install frontend dependencies:
   ```bash
   npm install
   ```
3. Copy the environment file and generate an application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Populate environment values (see [Configuration](#configuration)).
5. Run database migrations and seed the baseline catalogues:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
6. Compile frontend assets:
   ```bash
   npm run dev
   ```
7. Start the queue worker and Horizon dashboard when working with background jobs:
   ```bash
   php artisan horizon
   ```

## Configuration

### Core environment variables

| Variable | Purpose |
| --- | --- |
| `DB_CONNECTION` / `DB_DATABASE` / `DB_HOST` / `DB_PORT` / `DB_USERNAME` / `DB_PASSWORD` | Database connection for application and queue tables. |
| `CACHE_DRIVER` / `QUEUE_CONNECTION` | Set to `redis` for production so Horizon can supervise queues. |
| `SESSION_DRIVER` | Use `redis` or `database` in production for horizontal scaling. |
| `STRIPE_KEY` / `STRIPE_SECRET` | Stripe credentials consumed by Laravel Cashier. |
| `STRIPE_WEBHOOK_SECRET` | Verifies incoming Stripe webhook signatures. |
| `OMDB_API_KEY` | Authenticates requests to the OMDb API. |
| `TMDB_API_KEY` | Authenticates requests to the TMDb API. |
| `HORIZON_PREFIX` | Optional Redis prefix if sharing infrastructure with other apps. |

> Keep production secrets exclusively in the environment. Never commit them to version control.

### Baseline data seeders

`php artisan db:seed` now orchestrates the core catalogue data and demo accounts in one run:

- `LanguageSeeder` — loads ten common ISO 639-1 languages used for localisation workflows.
- `CountrySeeder` — provisions the initial ISO 3166-1 alpha-2 production countries surfaced in filters.
- `GenreSeeder` — syncs the canonical TMDb movie genres used by the parser service.
- `UserSeeder` — generates 1,000 accounts and promotes the first two to administrators so the back office is available immediately after seeding. All generated users share the factory default `password`, which means you can sign in with any seeded address before tailoring credentials locally.

To locate the seeded admins quickly, run:

```bash
php artisan tinker --execute="App\\Models\\User::where('role', App\\Enums\\UserRole::Admin->value)->pluck('email')"
```

> **Need deterministic demo credentials?** Update the returned admin record(s) with your preferred email/password combination via `tinker` before handing credentials to teammates.

When your checkout includes the optional billing seeders (`SubscriptionSeeder`, `PaymentHistorySeeder`), run them after the baseline call to populate Cashier's tables with a demo subscription and synthetic invoice history. Configure the Stripe plan identifiers exposed in `config/subscriptions.php` (`STRIPE_MONTHLY_PRICE`, `STRIPE_YEARLY_PRICE`, and related amount/currency overrides) first so the seeded billing data matches your environment.

Each seeder is written to be re-runnable, either by upserting rows or short-circuiting when data already exists, so you can refresh demo content without creating duplicates.

## Deployment

### Production deployment script

The repository ships with `scripts/deploy-production.sh`, which performs the idempotent deployment steps:

1. Install Node dependencies with `npm ci` (dev dependencies are required for Vite).
2. Build the Vite assets with `npm run build` so `public/build/manifest.json` exists.
3. Prune dev dependencies with `npm prune --omit=dev` to keep the runtime lean.
4. Verify every manifest entry points to an on-disk asset to avoid hashed-asset 404s.
5. Cache configuration for fast bootstrap.
6. Run database migrations with `--force`.
7. Seed the baseline catalogues.
8. Optimise the framework caches.
9. Signal Horizon to restart so the latest code is loaded.

Execute the script from the project root on the production server after pulling the latest code:

```bash
./scripts/deploy-production.sh
```

### GitHub Actions workflow

`.github/workflows/deploy-production.yml` defines a manual **Deploy Production** workflow. It connects to the server over SSH using [`appleboy/ssh-action`](https://github.com/appleboy/ssh-action) and performs the following:

1. Fetch and reset the server checkout to the requested ref (default `main`).
2. Install PHP dependencies with optimisations.
3. Install Node dependencies and build frontend assets.
4. Call `scripts/deploy-production.sh` to migrate, seed, optimise caches, and restart Horizon.

Provision these secrets in the repository before triggering a deployment:

| Secret | Description |
| --- | --- |
| `PRODUCTION_SSH_HOST` | SSH host of the production server. |
| `PRODUCTION_SSH_USER` | SSH username with deploy permissions. |
| `PRODUCTION_SSH_KEY` | Private key for authentication. |
| `PRODUCTION_SSH_PORT` | Optional SSH port (defaults to 22 if omitted). |
| `PRODUCTION_APP_PATH` | Absolute path to the deployed application on the server. |

After the workflow completes, Horizon will be restarted automatically and the catalogue seeders will ensure languages, countries, and genres remain up to date.

> **Manual deployments:** When performing the workflow steps outside GitHub Actions, ensure the server has Node.js 20+ available. Run `npm ci` followed by `npm run build` (and optionally `npm prune --omit=dev`) from the project root before invoking `./scripts/deploy-production.sh` so the Vite manifest and hashed assets are in place.

## Documentation

Additional design notes are available in [`docs/`](docs/).
