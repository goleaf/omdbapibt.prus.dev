# SQLite Operations Guide

This guide summarizes practical recommendations for using SQLite within the Laravel stack that powers omdbapibt.prus.dev.
It expands on the database section of the agent handbook with environment-specific tips, migration strategies, and
performance advice.

## When SQLite Fits

- Small to medium services, CLI/edge workloads, prototypes, and applications with infrequent writes and heavy reads.
- Automated tests and local development environments that benefit from a self-contained database.
- Avoid for high-concurrency write workloads or complex replication requirements.

## Baseline Configuration

- `.env`: set `DB_CONNECTION=sqlite` and `DB_DATABASE=/absolute/path/storage/database.sqlite` (create the file beforehand).
- Enable core PRAGMAs (configured in `AppServiceProvider::boot()`):
  - `foreign_keys = ON`
  - `journal_mode = WAL`
  - `synchronous = NORMAL`
  - `temp_store = MEMORY`
  - `cache_size` ≈ 20 MB
  - `busy_timeout = 5000`
- `config/database.php`: ensure `foreign_key_constraints=true` and `PDO::ATTR_TIMEOUT=5`.
- Disable query logging in production to keep memory usage predictable.

## Migrations & Schema Changes

- Column alterations should follow the recreate-copy-drop pattern: create the new column/table, migrate data, then drop the
  original. Use `doctrine/dbal` when you must rely on `renameColumn()` or `change()`.
- Add indexes to columns used in `WHERE`, `JOIN`, or `ORDER BY` clauses. Partial indexes are possible via `DB::statement('CREATE INDEX ... WHERE ...')`.
- Always declare foreign keys and keep `PRAGMA foreign_keys=ON` enabled.

## Performance Considerations

- **Write-Ahead Logging:** Keep WAL mode enabled for concurrent readers.
- **Bulk operations:** Prefer `Model::insert($bulk)` or `upsert()` for batch writes.
- **Transactions:** Wrap multi-step changes inside `DB::transaction(fn () => ...)`.
- **Maintenance:** Schedule `PRAGMA optimize`, `VACUUM`, and `PRAGMA wal_checkpoint(TRUNCATE)` for long-lived databases.
- **Driver selection:** Use Redis (not SQLite) for cache, queue, and session drivers in production.

## JSON, CTE, and Full-Text Search

- JSON is stored as `TEXT`; cast attributes to arrays on your models and consider generated columns plus indexes for hot fields.
- Common table expressions (including recursive CTEs) are available; pair with packages like `staudenmeir/laravel-cte` if needed.
- Full-text search can be implemented via FTS5 virtual tables with triggers or external tooling such as Laravel Scout + TNTSearch.

## Testing Tips

- For ultra-fast tests use `DB_DATABASE=:memory:` with the `RefreshDatabase` trait.

## Anti-Patterns to Avoid

- Do not store large binaries in the database—use storage disks and persist metadata only.
- Avoid SQLite-specific column types if you plan to migrate to other database engines later.

## Production Checklist

- Confirm WAL mode, `synchronous=NORMAL|FULL`, `foreign_keys=ON`, and a sensible `busy_timeout`.
- Ensure indexes exist on critical lookup columns.
- Batch writes and wrap them in transactions.
- Keep Redis configured for cache/queue/session responsibilities.
- Run regular `VACUUM`/`PRAGMA optimize` cycles and maintain backups.
- Disable verbose query logging and keep Telescope limited to development.
- Introduce full-text search (FTS) only when necessary.
