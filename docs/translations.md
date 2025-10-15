# Translation workflow

## Supported locales

The application ships with English (`en`), Spanish (`es`), and French (`fr`) locales defined in `config/translatable.php`. UI copy that is rendered from Blade or Livewire components should load strings from the locale files at `resources/lang/{locale}/ui.php`.

## Updating UI copy

1. **Add or edit translation keys** in the per-locale `ui.php` files. Keep keys consistent across locales so shared components can reference a single `__('ui...')` path.
2. **Reference the translation keys** from Blade templates and Livewire components instead of hard-coded strings. This repository already localizes the primary navigation, dashboard messaging, and discovery filters using the new keys.
3. **Format placeholders** inside translations (for example, `:date` or `:days`) and pass rendered HTML (wrapped with `e()` when necessary) through `__()` or `{!! !!}` just as the dashboard view demonstrates.

## Managing database-backed UI strings

The `UiTranslation` model (backed by Spatie's `HasTranslations` trait) allows administrators to manage copy that should be editable from the browser.

- Visit `/admin/ui-translations` to access the Livewire management screen.
- Create a translation by supplying a group, key, and values for every configured locale.
- Edit or delete existing entries as requirements change. The component enforces unique group/key pairs and validates that every locale is populated.

Downstream consumers can resolve these translations through the `UiTranslation` model, for example:

```php
UiTranslation::query()
    ->where('group', 'navigation')
    ->where('key', 'hero_cta')
    ->first()?->getTranslation('value', app()->getLocale());
```

## Verifying changes

Run the relevant automated tests after updating translations:

```bash
php artisan test
```

The CI pipeline also consumes `.env.testing`, which now exposes a `CI=true` flag so jobs run with the expected environment context.
