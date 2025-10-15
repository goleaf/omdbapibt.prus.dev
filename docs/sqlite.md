# Laravel + SQLite: Practical Guidance

This guide summarizes the key considerations when running SQLite inside a Laravel application. Use it as a checklist for development, migrations, and production operations.

## When SQLite Fits

- Small or medium services, CLI/edge workloads, prototypes, and apps with infrequent writes but heavy reads.
- Automated tests and local development where external dependencies are undesirable.
- Avoid for hot, highly concurrent write workloads or complex replication requirements.

## Baseline Configuration

- `.env`: `DB_CONNECTION=sqlite`, `DB_DATABASE=/absolute/path/storage/database.sqlite` (create the file ahead of time).
- Enable PRAGMA settings in `AppServiceProvider::boot()` (already configured in this project):
  - `foreign_keys=ON`, `journal_mode=WAL`, `synchronous=NORMAL`, `temp_store=MEMORY`, `cache_size≈20MB`, `busy_timeout=5000`.
- `config/database.php`: ensure `foreign_key_constraints=true` and set `PDO::ATTR_TIMEOUT=5`.
- Disable verbose query logging in production.

## Migrations and Schema Management

- For column changes, follow the pattern: add a new column → copy data → drop the old column. When using `renameColumn()` or `change()`, require `doctrine/dbal`.
- Add indexes for columns used in `WHERE`/`JOIN`/`ORDER BY` clauses. For partial indexes, call `DB::statement('CREATE INDEX ... WHERE ...')`.
- Always declare foreign keys; keep `PRAGMA foreign_keys=ON` enforced.

## Performance Practices

- Write-Ahead Logging (WAL) is essential to allow concurrent readers without heavy locking.
- Prefer batch inserts/updates (`Model::insert($bulk)`, `upsert`).
- Wrap multi-step changes inside transactions (`DB::transaction(fn () => ...)`).
- Maintenance tasks: run `PRAGMA optimize`, `VACUUM`, and `PRAGMA wal_checkpoint(TRUNCATE)` periodically.
- Do not rely on SQLite for cache/queue/session drivers in production—use Redis instead.

## JSON, CTE, and Full-Text Search

- JSON is stored as `TEXT`; expose it via casts on models (`array`). For indexing, create generated columns plus indexes.
- Common Table Expressions (including recursive queries) are supported; consider `staudenmeir/laravel-cte` for syntactic sugar.
- Full-text search: either use FTS5 (virtual table + triggers) or integrate Laravel Scout with TNTSearch.

## Testing Workflow

- For fast in-memory testing, configure `DB_DATABASE=:memory:` and apply the `RefreshDatabase` trait.

## Anti-Patterns

- Avoid storing large binaries in the database—use storage disks and keep only metadata in SQLite.
- Do not depend on data types that only exist in other RDBMS engines (enum/jsonb/array, etc.).

## Production Checklist

- PRAGMAs: enable WAL, set `synchronous` to `NORMAL` or `FULL`, enforce `foreign_keys=ON`, and define an appropriate `busy_timeout`.
- Confirm indexes on critical query columns.
- Use batch writes alongside transactions.
- Configure Redis for `cache`, `queue`, and `session` drivers.
- Schedule recurring `VACUUM`/`PRAGMA optimize` routines and maintain backups.
- Disable verbose query logging and limit Telescope to development environments.
- Adopt FTS only when the product requirements justify it.
