# Translation Workflow

This project combines classic Laravel language files for interface chrome and Spatie Translatable JSON columns for dynamic copy managed through the admin panel. The sections below describe how contributors can propose translation updates safely.

## Supported locales

The application currently ships with three locales:

- `en` – English (fallback)
- `es` – Spanish
- `fr` – French

The list of supported locales is defined once in [`config/translatable.php`](../config/translatable.php). Update that file if you introduce a new language so the value is shared by both Blade helpers and translatable models.

## Updating language files (`resources/lang`)

Static interface text (navigation labels, filter names, dashboard copy, etc.) lives inside locale-specific directories:

```
resources/lang/
    en/
    es/
    fr/
```

Each directory mirrors the same PHP files (`navigation.php`, `dashboard.php`, `filters.php`, `admin.php`, `auth.php`). When you add a new key, be sure to create it in every locale file so translators know what needs attention. Strings are referenced in Blade views using Laravel's `__()` helper, e.g. `__('navigation.dashboard')`.

> **Note:** `auth.php` powers the placeholder login screen that protects the admin translation manager in non-interactive environments.

### Recommended workflow

1. Edit the English source file first and commit the new keys.
2. Add or update the corresponding strings in the other locales.
3. When opening a pull request, mention any strings that still need translation so native speakers can help.

## Managing model translations (Spatie Translatable)

Dynamic copy that belongs to database records is stored in the `ui_translations` table using [Spatie Translatable](https://github.com/spatie/laravel-translatable). An authenticated admin can browse and edit these entries by visiting **Dashboard → UI Translations**.

### Creating or editing entries

1. Sign in and open the dashboard.
2. Click **UI Translations** to access the manager.
3. Use **Create translation entry** to register a new key. Provide content for any locales you have available. Empty fields are ignored.
4. Use the **Edit** action beside an existing key to update its localized values.
5. Delete an entry if it is no longer referenced in the codebase.

Entries are validated so that only configured locales can be saved. The admin views expose the configured languages automatically, so adding a new locale to `config/translatable.php` is all that is required for the UI to display a new column.

### Importing or syncing content

If you add new `UiTranslation` records through seeders, factories, or migrations, run the admin interface afterwards to verify everything renders correctly. Editors can use the search bar to locate specific keys when syncing content from an external source.

## Submitting translation pull requests

- Include screenshots whenever you modify user-facing copy.
- Reference this document or `config/translatable.php` to highlight new locales.
- Run `php artisan test` (or the relevant test suite) so reviewers know the build is green.
- Describe whether your change affects language files, model translations, or both to help reviewers route it to the right translators.
