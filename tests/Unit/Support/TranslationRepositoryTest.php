<?php

namespace Tests\Unit\Support;

use App\Support\TranslationRepository;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

class TranslationRepositoryTest extends TestCase
{
    public function test_locales_are_discovered(): void
    {
        $repository = app(TranslationRepository::class);

        $this->assertSame(['en', 'es', 'fr'], $repository->locales());
    }

    public function test_keys_are_flattened_with_group_prefixes(): void
    {
        $repository = app(TranslationRepository::class);
        $keys = $repository->keysFor('en');

        $this->assertContains('ui.nav.links.home', $keys);
        $this->assertContains('admin.ui_translations.status.updated', $keys);
        $this->assertContains('validation.custom.query.required', $keys);
    }

    public function test_missing_and_extra_keys_are_detected(): void
    {
        $files = app(Filesystem::class);
        $repository = app(TranslationRepository::class);
        $temporaryLocale = 'zz-test';
        $localePath = lang_path($temporaryLocale);

        $files->ensureDirectoryExists($localePath);
        $files->put($localePath.'/sample.php', <<<'PHP'
<?php

return [
    'foo' => 'bar',
];
PHP);

        try {
            $missing = $repository->missingKeys('en', $temporaryLocale);
            $extra = $repository->extraKeys('en', $temporaryLocale);

            $this->assertContains('ui.nav.links.home', $missing);
            $this->assertSame(['sample.foo'], $extra);
        } finally {
            $files->deleteDirectory($localePath);
        }
    }

    public function test_exception_is_thrown_for_missing_locale(): void
    {
        $repository = app(TranslationRepository::class);

        $this->expectException(FileNotFoundException::class);
        $repository->entriesFor('missing-locale');
    }
}
