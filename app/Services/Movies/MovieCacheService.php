<?php

namespace App\Services\Movies;

use App\Models\Movie;
use Illuminate\Cache\CacheManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class MovieCacheService
{
    public function __construct(protected CacheManager $cache) {}

    /**
     * Retrieve trending movies with an hour-long cache window tagged for invalidation.
     */
    public function trending(int $limit = 20): Collection
    {
        $key = $this->cacheKey('trending', [$limit]);

        return $this->cache
            ->tags(['movies', 'trending'])
            ->remember(
                $key,
                Carbon::now()->addSeconds(config('cache_ttls.queries.trending')),
                fn () => Movie::query()
                    ->with(['tags:id,slug,name_i18n,type'])
                    ->orderByDesc('updated_at')
                    ->orderByDesc('popularity')
                    ->limit($limit)
                    ->get()
            );
    }

    /**
     * Retrieve popular movies with an hour-long cache window tagged for invalidation.
     */
    public function popular(int $limit = 20): Collection
    {
        $key = $this->cacheKey('popular', [$limit]);

        return $this->cache
            ->tags(['movies', 'popular'])
            ->remember(
                $key,
                Carbon::now()->addSeconds(config('cache_ttls.queries.popular')),
                fn () => Movie::query()
                    ->with(['tags:id,slug,name_i18n,type'])
                    ->orderByDesc('popularity')
                    ->orderByDesc('vote_count')
                    ->limit($limit)
                    ->get()
            );
    }

    /**
     * Flush the trending cache tag.
     */
    public function invalidateTrending(): void
    {
        $this->cache->tags(['movies', 'trending'])->flush();
    }

    /**
     * Flush the popular cache tag.
     */
    public function invalidatePopular(): void
    {
        $this->cache->tags(['movies', 'popular'])->flush();
    }

    protected function cacheKey(string $namespace, array $context = []): string
    {
        return sprintf('movies.%s.%s', $namespace, md5(json_encode($context)));
    }
}
