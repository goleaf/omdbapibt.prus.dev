# OMDB API Key Management System

The OMDb API BT Platform includes a sophisticated automated system for discovering, validating, and managing OMDB API keys.

## Overview

The OMDB Key Management System automatically:
1. **Generates** random 8-character alphanumeric API keys
2. **Validates** keys asynchronously using concurrent HTTP requests
3. **Tracks** key status and performance metrics
4. **Rotates** valid keys to distribute load
5. **Enriches** movie metadata using discovered keys

## Quick Start

Run the all-in-one command:

```bash
php artisan omdb:bruteforce
```

That's it! The system handles everything automatically.

## How It Works

### Three-Phase Workflow

#### Phase 1: Key Generation

The system maintains a minimum pool of pending keys:

- **Checks** if pending keys < threshold (default: 10,000)
- **Generates** random 8-character keys (0-9, a-z)
- **Batch inserts** with duplicate prevention
- **Progress tracking** with real-time progress bar

```
Generating 10000 new keys...
[████████████████████████████] 100%
✔ Generated 10000 keys
```

#### Phase 2: Asynchronous Validation

Keys are validated concurrently for maximum efficiency:

- **Loads checkpoint** from cache (for resume capability)
- **Validates** 50 keys simultaneously using Laravel's HTTP pool
- **Tests** against OMDB API with test movie (tt3896198)
- **Updates** status: valid, invalid, or unknown
- **Saves checkpoint** after each batch for resume
- **Logs** response codes for monitoring

```
Validating batch of 50 keys (IDs 1 to 50)...
✓ Valid key: 42e12276
Batch complete. Checkpoint: 50 | Valid: 1 | Invalid: 49
```

#### Phase 3: Movie Parsing

Valid keys are used to enrich movie metadata:

- **Retrieves** all valid keys from database
- **Rotates** through keys using round-robin
- **Fetches** OMDB data for up to 1,000 movies per run
- **Updates** movie metadata:
  - Title, year, runtime
  - Plot and tagline
  - Poster and backdrop URLs
  - IMDB ratings
  - Awards and additional metadata
- **Error handling** for network failures

```
Parsing movies with 15 valid key(s)...
Found 500 movies to update
[████████████████████████████] 100%
Movie parsing complete! Processed: 500 | Updated: 482
```

## Configuration

### Environment Variables

Configure the OMDB API settings in `config/services.php`:

```php
'omdb' => [
    // Primary OMDB API key
    'key' => env('OMDB_API_KEY'),
    
    // OMDB API base URL
    'base_url' => 'http://www.omdbapi.com',
    
    // Rate limit per minute
    'max_requests_per_minute' => 60,
    
    // Validation settings
    'validation' => [
        'test_imdb_id' => 'tt3896198',
        'batch_size' => 50,
        'timeout' => 10,
    ],
    
    // Bruteforce settings
    'bruteforce' => [
        'charset' => '0123456789abcdefghijklmnopqrstuvwxyz',
        'key_length' => 8,
        'min_pending_keys' => 10000,
        'generation_batch' => 1000,
    ],
],
```

### Tuning Parameters

#### Batch Sizes

Adjust concurrent validation batch size:

```php
'validation' => [
    'batch_size' => 50, // Number of concurrent HTTP requests
],
```

**Higher values** = faster validation, more server load  
**Lower values** = slower validation, less server load

#### Minimum Pending Keys

Control when new keys are generated:

```php
'bruteforce' => [
    'min_pending_keys' => 10000, // Generate when below this threshold
],
```

#### Validation Timeout

HTTP request timeout for key validation:

```php
'validation' => [
    'timeout' => 10, // Seconds to wait for OMDB API response
],
```

## Command Usage

### Basic Usage

```bash
php artisan omdb:bruteforce
```

The command runs all three phases automatically.

### Resume from Interruption

The command automatically resumes from the last checkpoint if interrupted:

```bash
php artisan omdb:bruteforce
```

Output will show:
```
Resuming validation from checkpoint: 5050
```

### Reset Checkpoint

To start validation from the beginning:

```bash
php artisan tinker
>>> Cache::forget('omdb:checkpoint')
>>> exit
```

Then run the command again:

```bash
php artisan omdb:bruteforce
```

## Monitoring

### Check Key Statistics

Using Tinker:

```bash
php artisan tinker
```

```php
// Count valid keys
>>> App\Models\OmdbApiKey::valid()->count()

// Count pending keys
>>> App\Models\OmdbApiKey::pending()->count()

// All keys by status
>>> DB::table('omdb_api_keys')->select('status', DB::raw('count(*) as count'))
    ->groupBy('status')->get()

// Recently validated keys
>>> App\Models\OmdbApiKey::latest('last_checked_at')->take(10)->get()

// Best performing keys (most recently confirmed)
>>> App\Models\OmdbApiKey::valid()->latest('last_confirmed_at')->take(10)->get()
```

### View Checkpoint Status

```bash
php artisan tinker
```

```php
// Get current checkpoint
>>> Cache::get('omdb:checkpoint')

// View checkpoint with expiry
>>> Cache::get('omdb:checkpoint', 'No checkpoint found')
```

### Database Queries

Check key distribution:

```sql
-- Count by status
SELECT status, COUNT(*) as count 
FROM omdb_api_keys 
GROUP BY status;

-- Valid keys
SELECT * FROM omdb_api_keys 
WHERE status = 'valid' 
ORDER BY last_confirmed_at DESC;

-- Recently checked keys
SELECT * FROM omdb_api_keys 
ORDER BY last_checked_at DESC 
LIMIT 20;

-- Invalid keys with response codes
SELECT key, status, last_response_code, last_checked_at 
FROM omdb_api_keys 
WHERE status = 'invalid';
```

## Scheduling

### Run as a Cron Job

Add to your scheduler in `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('omdb:bruteforce')
    ->daily()
    ->timezone('UTC')
    ->withoutOverlapping()
    ->runInBackground();
```

This will run the key discovery system once per day.

### Continuous Discovery

For aggressive key discovery:

```php
Schedule::command('omdb:bruteforce')
    ->hourly()
    ->timezone('UTC')
    ->withoutOverlapping()
    ->runInBackground();
```

## Performance Metrics

### Typical Performance

Based on default configuration:

- **Key Generation:** 10,000 keys in ~1 second
- **Validation:** 50 keys in ~10 seconds (concurrent)
- **Movie Parsing:** 50 movies in ~10 seconds (concurrent)
- **Throughput:** ~300 keys/minute validated
- **Speed Improvement:** 98% faster than sequential validation

### Optimization Tips

1. **Increase batch size** for faster validation (requires more resources)
2. **Use Redis** for checkpoint caching (faster than file cache)
3. **Run during off-peak** hours to avoid API rate limits
4. **Monitor valid key count** to avoid over-generation
5. **Schedule regular runs** to maintain key pool

## Database Schema

### omdb_api_keys Table

```sql
CREATE TABLE `omdb_api_keys` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `status` enum('pending','valid','invalid','unknown') DEFAULT NULL,
  `first_seen_at` timestamp NULL DEFAULT NULL,
  `last_checked_at` timestamp NULL DEFAULT NULL,
  `last_confirmed_at` timestamp NULL DEFAULT NULL,
  `last_response_code` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `omdb_api_keys_key_unique` (`key`),
  KEY `omdb_api_keys_status_index` (`status`)
);
```

### Key Fields

- **key** - The 8-character API key
- **status** - Current validation status
- **first_seen_at** - When key was first generated
- **last_checked_at** - Last validation attempt
- **last_confirmed_at** - Last successful use
- **last_response_code** - HTTP response code from OMDB

## Troubleshooting

### No Valid Keys Found

**Problem:** Validation completes but no valid keys discovered

**Solutions:**
- Increase the number of generated keys
- Check OMDB API rate limits
- Verify network connectivity
- Review `last_response_code` values

```bash
php artisan tinker
>>> DB::table('omdb_api_keys')
    ->select('last_response_code', DB::raw('count(*) as count'))
    ->groupBy('last_response_code')
    ->get()
```

### Validation Timeout

**Problem:** HTTP requests timing out

**Solutions:**
- Increase timeout in configuration
- Reduce batch size
- Check network latency
- Verify OMDB API availability

```php
'validation' => [
    'timeout' => 20, // Increase from default 10
    'batch_size' => 25, // Reduce from default 50
],
```

### Command Hangs or Stops

**Problem:** Command stops responding

**Solutions:**
- Check Redis connection
- Verify database connectivity
- Review Laravel logs
- Check system resources (memory, CPU)

```bash
# Check logs
tail -f storage/logs/laravel.log

# Monitor Redis
redis-cli monitor

# Check database connections
php artisan tinker
>>> DB::connection()->getPdo()
```

### Checkpoint Not Resuming

**Problem:** Command starts from beginning each time

**Solutions:**
- Verify Redis is running
- Check cache driver configuration
- Manually inspect checkpoint

```bash
php artisan tinker
>>> Cache::get('omdb:checkpoint')
>>> Cache::put('omdb:checkpoint', 0) // Reset to 0
```

### Too Many Pending Keys

**Problem:** Pending keys exceed reasonable amount

**Solutions:**
- Lower `min_pending_keys` threshold
- Increase validation frequency
- Check why validation isn't running

```php
'bruteforce' => [
    'min_pending_keys' => 5000, // Reduce from 10000
],
```

## Best Practices

1. **Monitor valid key count** regularly
2. **Run during off-peak hours** to respect API limits
3. **Keep validation batch sizes** reasonable (50-100)
4. **Use Redis for caching** for better performance
5. **Schedule regular runs** but avoid overlapping
6. **Review logs periodically** for errors
7. **Backup valid keys** before major updates
8. **Test in development** before production runs

## API Integration

### Using Valid Keys in Your Code

```php
use App\Models\OmdbApiKey;
use Illuminate\Support\Facades\Http;

// Get a random valid key
$apiKey = OmdbApiKey::valid()->inRandomOrder()->first();

if ($apiKey) {
    $response = Http::get('http://www.omdbapi.com/', [
        'apikey' => $apiKey->key,
        'i' => 'tt1234567', // IMDB ID
    ]);
    
    if ($response->successful()) {
        $data = $response->json();
        // Process movie data
    }
}
```

### Key Rotation

```php
use App\Models\OmdbApiKey;

// Round-robin key selection
$validKeys = OmdbApiKey::valid()->pluck('key')->toArray();
$keyIndex = 0;

foreach ($movies as $movie) {
    $key = $validKeys[$keyIndex % count($validKeys)];
    $keyIndex++;
    
    // Use $key for API request
}
```

## Related Documentation

- [API Documentation](API-Documentation) - Using the parser API
- [Development Guide](Development-Guide) - Testing and contributing
- [Deployment Guide](Deployment-Guide) - Production considerations

---

**Questions?** Open an issue on [GitHub](https://github.com/goleaf/omdbapibt.prus.dev/issues)

