# Cursor Rules

- Use SQLite as the only supported relational database.
- Prefer schema constructs that are compatible with SQLite (no stored generated columns or MySQL-only features).
- Queue, cache and session drivers must continue to use the default SQLite connection unless explicitly overridden.
- When adding seeders or tests, rely on the SQLite database stored at `database/database.sqlite`.
- Update documentation to reflect the SQLite-only stack whenever database changes are introduced.
