# Caching Strategy

## API clients

- **Static resources** exposed by OMDb and TMDb are cached for 24 hours via `config/cache_ttls.php`.
- TMDb trending and popular endpoints are cached for one hour with cache tags so specific segments can be invalidated without flushing unrelated data.
- The bindings for `App\Services\Clients\OmdbClient` and `App\Services\Clients\TmdbClient` live in `App\Providers\AppServiceProvider`.

## Application queries

- `App\Services\Movies\MovieCacheService` caches trending and popular movie lookups for one hour using the `movies` tag combined with the query type.
- The service exposes explicit invalidation helpers so downstream processes (for example, the parser) can clear affected caches.

## Persistence layer

Parsed entities are persisted through `App\Services\Movies\ParsedMoviePersister`. The service intentionally **avoids writing parsed payloads to cache** and instead flushes the affected tags after database commits. This keeps the database as the source of truth while still allowing cached query layers to repopulate with fresh data on demand.
