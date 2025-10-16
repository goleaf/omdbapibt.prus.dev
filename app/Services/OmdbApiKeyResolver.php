<?php

namespace App\Services;

use App\Models\OmdbApiKey;
use Throwable;

class OmdbApiKeyResolver
{
    public function __construct(
        protected ?string $fallbackKey = null
    ) {
        $this->fallbackKey ??= (string) config('services.omdb.key', '');
    }

    public function resolve(): string
    {
        try {
            $key = OmdbApiKey::query()
                ->where('is_working', true)
                ->orderByDesc('validated_at')
                ->orderByDesc('updated_at')
                ->value('key');
        } catch (Throwable $exception) {
            return $this->fallback();
        }

        return $key ?: $this->fallback();
    }

    public function __invoke(): string
    {
        return $this->resolve();
    }

    protected function fallback(): string
    {
        return $this->fallbackKey ?? '';
    }
}
