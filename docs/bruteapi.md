# OMDB API Bruteforce System

## Overview

The OMDB API Bruteforce System is a comprehensive solution for generating, validating, and managing OMDB API keys, then using them to automatically parse movie metadata. The system is designed to be resilient, with resume capability and async processing for maximum efficiency.

## Architecture

### Components

1. **OmdbBruteforceCommand** - Main command that orchestrates the entire process
2. **OmdbApiKey Model** - Manages API key records in the database
3. **OmdbApiKeyResolver** - Resolves and rotates valid API keys for use
4. **Configuration** - Centralized configuration in `config/services.php`

### Workflow

The system operates in three distinct phases:

#### Phase 1: Key Generation
- Checks if sufficient pending keys exist (minimum 10,000 by default)
- Generates random 8-character alphanumeric keys
- Inserts keys in batches with duplicate prevention
- Uses transactions for data integrity

#### Phase 2: Async Validation
- Loads checkpoint from cache to support resume
- Fetches pending keys in configurable batches (default: 50)
- Uses Laravel's `Http::pool()` for concurrent HTTP requests
- Tests each key against OMDB API
- Updates key status based on response
- Saves checkpoint after each batch

#### Phase 3: Movie Parsing
- Retrieves all valid API keys
- Queries movies needing OMDB data
- Rotates through keys (round-robin) for load balancing
- Fetches movie metadata using async HTTP requests
- Updates movie records with OMDB data
- Implements rate limiting

## API Key Structure

### Format
- **Length**: 8 characters
- **Character Set**: Alphanumeric (0-9, a-z)
- **Example**: `42e12276`
- **Total Possibilities**: 36^8 = 2,821,109,907,456 combinations

### Status Values

| Status | Description |
|--------|-------------|
| `pending` | Key generated but not yet validated |
| `valid` | Key confirmed working with OMDB API |
| `invalid` | Key rejected by OMDB API |
| `unknown` | Validation inconclusive (network error, timeout, etc.) |

## Database Schema

### omdb_api_keys Table

```sql
CREATE TABLE omdb_api_keys (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    key VARCHAR(8) UNIQUE NOT NULL,
    first_seen_at TIMESTAMP NULL,
    last_checked_at TIMESTAMP NULL,
    last_confirmed_at TIMESTAMP NULL,
    last_response_code SMALLINT NULL,
    status ENUM('pending', 'valid', 'invalid', 'unknown') NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX(status)
);
```

### Key Fields

- `key` - The 8-character API key
- `first_seen_at` - When the key was first generated
- `last_checked_at` - Last validation attempt timestamp
- `last_confirmed_at` - Last successful validation timestamp
- `last_response_code` - HTTP status code from last request
- `status` - Current key status (indexed for performance)

## Configuration

### Environment Variables

Add to your `.env` file:

```env
OMDB_API_KEY=your_fallback_key
OMDB_BASE_URL=http://www.omdbapi.com
OMDB_MAX_REQUESTS_PER_MINUTE=60
```

### Config File

Located in `config/services.php`:

```php
'omdb' => [
    'key' => env('OMDB_API_KEY'),
    'base_url' => 'http://www.omdbapi.com',
    'max_requests_per_minute' => 60,
    'validation' => [
        'test_imdb_id' => 'tt3896198',  // Test movie for validation
        'batch_size' => 50,              // Keys per validation batch
        'timeout' => 10,                 // Request timeout (seconds)
    ],
    'bruteforce' => [
        'charset' => '0123456789abcdefghijklmnopqrstuvwxyz',
        'key_length' => 8,
        'min_pending_keys' => 10000,     // Minimum pending keys to maintain
        'generation_batch' => 1000,      // Keys generated per batch
    ],
],
```

## Usage

### Running the Command

The system requires only a single command with no parameters:

```bash
php artisan omdb:bruteforce
```

### What Happens

1. **Key Generation**: If pending keys < 10,000, generates new keys
2. **Validation**: Validates all pending keys using async HTTP requests
3. **Parsing**: Updates movies with OMDB metadata using valid keys

### Resume Capability

The system automatically saves progress. If interrupted:
- Checkpoint is stored in cache: `omdb:checkpoint`
- Next run automatically resumes from last position
- No duplicate validation work is performed

### Example Output

```
Starting OMDB API Bruteforce System

✔ Checking pending keys
Generating 8500 new keys...
Generated 1000 keys...
Generated 2000 keys...
...
Generated 8500 keys

Resuming validation from checkpoint: 0
Validating batch of 50 keys (IDs 1 to 50)...
✓ Valid key: 42e12276
Batch complete. Checkpoint: 50 | Valid: 1 | Invalid: 49 | Total: 50
...
All keys validated!
Validation complete! Processed: 10000 | Valid: 15 | Invalid: 9985

Parsing movies with 15 valid key(s)...
Found 500 movies to update
50/500 [==>-------------------------]  10%
...
Movie parsing complete! Processed: 500 | Updated: 482

✔ OMDB Bruteforce System completed successfully!
```

## Rate Limiting

### Validation Phase
- Batch size: 50 concurrent requests
- Pause between batches: 1 second
- Timeout per request: 10 seconds

### Parsing Phase
- Chunk size: 50 movies per batch
- Pause between batches: 1 second
- Key rotation: Round-robin through all valid keys

### OMDB API Limits
- Standard limit: 1,000 requests per day
- With multiple valid keys, throughput scales linearly
- Example: 10 valid keys = 10,000 requests/day

## Async HTTP Processing

The system leverages Laravel's `Http::pool()` for concurrent requests:

```php
$responses = Http::pool(function (Pool $pool) use ($keys) {
    foreach ($keys as $key) {
        $pool->as($key->id)
            ->timeout(10)
            ->get('http://www.omdbapi.com/', [
                'i' => 'tt3896198',
                'apikey' => $key->key,
            ]);
    }
});
```

### Benefits
- **Performance**: Process 50 keys in ~10 seconds instead of 500 seconds
- **Efficiency**: Network I/O happens in parallel
- **Scalability**: Easily adjust batch sizes

## Key Validation Logic

### Successful Response
```json
{
  "Response": "True",
  "Title": "Guardians of the Galaxy Vol. 2",
  ...
}
```
→ Status: `valid`

### Invalid API Key
```json
{
  "Response": "False",
  "Error": "Invalid API key!"
}
```
→ Status: `invalid`

### Network Error
- Timeout, connection refused, etc.
→ Status: `unknown`

## Movie Parsing

### Selection Criteria

Movies are selected for parsing if:
1. `plot` field is NULL, OR
2. `updated_at` is older than 30 days

Limit: 1,000 movies per run

### Updated Fields

From OMDB API to Movie model:
- `Title` → `title`
- `Year` → `year`
- `Plot` → `plot`
- `Poster` → `poster_path`
- `imdbRating` → `vote_average`

### Key Rotation

Valid keys are rotated in round-robin fashion:
```
Key 1 → Movie 1
Key 2 → Movie 2
Key 3 → Movie 3
Key 1 → Movie 4  (rotation)
...
```

## Monitoring

### Check Valid Keys

```bash
php artisan tinker
>>> App\Models\OmdbApiKey::valid()->count()
```

### View Key Statistics

```bash
php artisan tinker
>>> DB::table('omdb_api_keys')->select('status', DB::raw('count(*) as count'))->groupBy('status')->get()
```

### Check Checkpoint

```bash
php artisan tinker
>>> Cache::get('omdb:checkpoint')
```

### Reset Checkpoint

```bash
php artisan tinker
>>> Cache::forget('omdb:checkpoint')
```

## Troubleshooting

### Issue: No keys being validated

**Check**: Ensure pending keys exist
```bash
php artisan tinker
>>> App\Models\OmdbApiKey::pending()->count()
```

**Solution**: Lower `min_pending_keys` in config or wait for generation

### Issue: All keys invalid

**Possible Causes**:
- OMDB API down
- Network connectivity issues
- Invalid test IMDB ID

**Solution**: Check OMDB API status, verify network, update `test_imdb_id`

### Issue: Movies not updating

**Check**: Verify valid keys exist
```bash
php artisan tinker
>>> App\Models\OmdbApiKey::valid()->exists()
```

**Check**: Verify movies have IMDB IDs
```bash
php artisan tinker
>>> App\Models\Movie::whereNotNull('imdb_id')->count()
```

## Performance Optimization

### Database Indexes
- `status` column is indexed for fast filtering
- `key` column is unique for duplicate prevention
- `id` for checkpoint-based pagination

### Batch Processing
- Generate: 1,000 keys per batch
- Validate: 50 keys concurrently
- Parse: 50 movies concurrently

### Caching
- Checkpoint stored in cache (persistent)
- Valid keys can be cached in `OmdbApiKeyResolver`

## Security Considerations

### API Key Storage
- Keys stored in database (not environment)
- Status tracking prevents repeated invalid attempts
- Automatic rotation distributes load

### Rate Limiting
- Built-in delays between batches
- Respects OMDB API limits
- Prevents IP blacklisting

### Error Handling
- Graceful degradation on network failures
- Transaction-safe batch operations
- Resume capability prevents data loss

## Best Practices

1. **Run during off-peak hours** - Lower network contention
2. **Monitor valid key count** - Ensure adequate keys for parsing
3. **Regular execution** - Schedule as cron job for continuous operation
4. **Checkpoint management** - Reset if validation behavior changes
5. **Configuration tuning** - Adjust batch sizes based on server capacity

## Scheduled Automation

Add to Laravel scheduler in `bootstrap/app.php`:

```php
->withSchedule(function (Schedule $schedule) {
    // Run bruteforce daily at 2 AM
    $schedule->command('omdb:bruteforce')->dailyAt('02:00');
})
```

## Advanced Usage

### Manual Key Insertion

```php
OmdbApiKey::create([
    'key' => 'abc12345',
    'status' => OmdbApiKey::STATUS_PENDING,
    'first_seen_at' => now(),
]);
```

### Bulk Key Import

```php
$keys = ['key1', 'key2', 'key3'];

foreach (array_chunk($keys, 1000) as $chunk) {
    $data = collect($chunk)->map(fn($key) => [
        'key' => $key,
        'status' => 'pending',
        'first_seen_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ])->toArray();
    
    DB::table('omdb_api_keys')->insertOrIgnore($data);
}
```

### Revalidate Expired Keys

```php
// Mark old keys as pending for revalidation
OmdbApiKey::where('last_checked_at', '<', now()->subDays(7))
    ->update(['status' => OmdbApiKey::STATUS_PENDING]);
```

## API Reference

### OmdbApiKey Model

#### Constants
- `STATUS_PENDING = 'pending'`
- `STATUS_VALID = 'valid'`
- `STATUS_INVALID = 'invalid'`
- `STATUS_UNKNOWN = 'unknown'`

#### Scopes
- `pending()` - Get pending/unvalidated keys
- `valid()` - Get confirmed working keys

#### Fillable Attributes
- `key`, `status`, `first_seen_at`, `last_checked_at`, `last_confirmed_at`, `last_response_code`

### Command Methods

#### Public
- `handle(): int` - Main execution method

#### Protected
- `ensureKeysAvailable(): void` - Generates keys if needed
- `validatePendingKeys(): void` - Validates keys asynchronously
- `parseMovies(): void` - Updates movies with OMDB data
- `generateRandomKey(string $charset, int $length): string` - Creates random key
- `processValidationResponse(OmdbApiKey $key, $response): string` - Handles validation result
- `updateMovieFromOmdb(Movie $movie, $response): bool` - Updates movie record

## Conclusion

The OMDB API Bruteforce System provides a complete solution for API key management and movie metadata enrichment. Its async processing, resume capability, and intelligent rotation make it efficient and reliable for large-scale operations.

For questions or issues, refer to the codebase or submit an issue in the project repository.

