# UI Translation Workflow

The UI copy is delivered through a combination of version-controlled language files and dynamic overrides managed in the admin panel.

## Language files

- Static strings live in `lang/{locale}/ui.php`. The repo ships with English (`en`), Spanish (`es`), and French (`fr`).
- Group keys (for example `nav.links.home`) mirror the view segments that consume them. Views should always reference copy with the `__()` helper so DB overrides can layer on top of the defaults.
- When adding a new UI string, create the key in each locale file and update the view or component to reference it.

## Database overrides

- The `ui_translations` table stores localized overrides for group/key pairs.
- Each record is translatable through Spatie's `HasTranslations`; at minimum the fallback locale (default `en`) must be populated.
- Admins can manage entries through the Livewire interface at `/admin/ui-translations`. The tool supports creating, editing, deleting, and refreshing the cache.

## Caching strategy

- The `App\Support\UiTranslationRepository` loads translations into the translator and caches results forever using the store defined in `config/ui-translations.php` (`UI_TRANSLATIONS_CACHE_STORE`).
- Production defaults to Redis; when Redis is unavailable (or in CI) the repository falls back to the configured `fallback_store` (array by default).
- A lightweight `redis_stub` cache driver is registered for test environments so the repository can behave like Redis without a server. `.env.testing` pins `UI_TRANSLATIONS_CACHE_STORE=redis_stub`.
- The `redis_stub` cache store is configured in `config/cache.php`; it mirrors the Redis connection options so `Cache::store('redis_stub')` resolves without falling back to the default driver.
- Call `refreshCache` from the admin panel (or the repository) after manual database changes to flush the cache and repopulate the translator.

## Testing and CI

- `.env.testing` exposes `CI=true` for checks that need to branch on automation runs.
- Feature tests should seed any required translation rows and, when necessary, call `UiTranslationRepository::refreshAndRegister()` so the translator sees database updates.
