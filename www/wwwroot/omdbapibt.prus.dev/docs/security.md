# Security Hardening Notes

This project applies multiple layers of protection against cross-site scripting (XSS) and cross-site request forgery (CSRF) attacks.

## Livewire Form Safety

- All Livewire-driven forms explicitly include Laravel's `@csrf` directive to embed CSRF tokens in every payload.
- Components validate all user input using Laravel validation rules so that only strongly typed, length-limited data reaches the database.
- Automated Livewire tests cover the happy path and render assertions to guarantee the CSRF field is present in the DOM.

## HTML Sanitization Strategy

- User generated review content is sanitized with [`ezyang/htmlpurifier`](https://github.com/ezyang/htmlpurifier) via the `App\Support\HtmlSanitizer` helper before it is persisted or rendered.
- The sanitizer allows a small, readable HTML subset (paragraphs, emphasis, lists, code) and strips unsafe attributes or scripts.
- Display templates always render sanitized HTML (using the `sanitized_body` accessor) to provide a defence-in-depth layer, even if raw content reached storage by mistake.

## Regression Coverage

Automated tests in `tests/Feature/Livewire/ReviewSecurityTest.php` verify:

- CSRF tokens are rendered in the Livewire form markup.
- Malicious script tags are removed from stored and rendered review bodies.
- Safe markup such as `<strong>` remains intact after sanitation so that formatting is preserved without compromising safety.

These tests run as part of the standard `php artisan test` command and will fail if the security posture regresses.
