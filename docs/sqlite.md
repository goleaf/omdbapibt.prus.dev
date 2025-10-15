# Laravel + SQLite: практичные рекомендации

Коротко о важном для проектов на SQLite в Laravel. Эта заметка служит ориентиром для разработки, миграций и эксплуатации.

## Когда SQLite уместен

- Малые/средние сервисы, CLI/edge, прототипы, приложения с редкими записями и активными чтениями.
- Тесты и локальная разработка без внешних зависимостей.
- Не для горячей высококонкурентной записи и сложной репликации.

## Базовая настройка

- `.env`: `DB_CONNECTION=sqlite`, `DB_DATABASE=/absolute/path/storage/database.sqlite` (создайте файл).
- Включаем PRAGMA в `AppServiceProvider::boot()` (уже включено в проект):
  - `foreign_keys=ON`, `journal_mode=WAL`, `synchronous=NORMAL`, `temp_store=MEMORY`, `cache_size≈20MB`, `busy_timeout=5000`.
- `config/database.php`: `foreign_key_constraints=true`, `PDO::ATTR_TIMEOUT=5`.
- Отключайте лог запросов в проде.

## Миграции и схема

- Изменения колонок в SQLite делайте через стратегию: новая колонка → перенос → удаление старой. Для `renameColumn()`/`change()` используйте `doctrine/dbal`.
- Индексы: добавляйте на поля `WHERE/JOIN/ORDER BY`. Частичные индексы через `DB::statement('CREATE INDEX ... WHERE ...')`.
- Внешние ключи объявляйте всегда; PRAGMA `foreign_keys=ON` обязательно.

## Производительность

- WAL — must-have (множественные читатели без блокировки).
- Пакетные вставки/обновления (`Model::insert($bulk)`, `upsert`).
- Транзакции (`DB::transaction(fn () => ...)`).
- Обслуживание: периодически `PRAGMA optimize`, `VACUUM`, `PRAGMA wal_checkpoint(TRUNCATE)`.
- Не используйте SQLite как драйвер `cache/queue/session` в проде — используйте Redis.

## JSON, CTE, FTS

- JSON хранится как TEXT; используйте JSON1 и касты в моделях (`array`). Для индексации — сгенерированные колонки + индекс.
- CTE/рекурсивные запросы поддерживаются; удобно с пакетом `staudenmeir/laravel-cte`.
- FTS: либо FTS5 (виртуальная таблица + триггеры), либо Laravel Scout + TNTSearch.

## Тестирование

- Быстрый in-memory: `DB_DATABASE=:memory:` + `RefreshDatabase` в тестах.

## Что не делать

- Не храните большие бинарники в БД — используйте хранилище, а в БД только метаданные.
- Не полагайтесь на типы, специфичные для других СУБД (enum/jsonb/array).

## Чек-лист продакшена

- PRAGMA: WAL, `synchronous=NORMAL|FULL`, `foreign_keys=ON`, `busy_timeout`.
- Индексы на ключевые поля.
- Пакетные записи + транзакции.
- Redis для `cache/queue/session`.
- Регулярные `VACUUM`/`optimize` + бэкапы.
- Запросы не логируются, Telescope — только в dev.
- FTS при необходимости.
