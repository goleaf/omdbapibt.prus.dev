# OMDB API Bruteforce - Quick Start Guide

## Single Command

```bash
php artisan omdb:bruteforce
```

**That's it!** The command automatically:
1. ✅ Generates API keys if needed
2. ✅ Validates keys asynchronously 
3. ✅ Parses movies with valid keys
4. ✅ Resumes if interrupted

## What Happens

### Automatic Workflow

```
┌─────────────────────────────────────┐
│  1. CHECK PENDING KEYS              │
│     < 10,000? Generate more         │
└─────────────────────────────────────┘
                 ↓
┌─────────────────────────────────────┐
│  2. VALIDATE KEYS (Async)           │
│     • 50 concurrent requests        │
│     • Test against OMDB API         │
│     • Update status (valid/invalid) │
│     • Save checkpoint               │
└─────────────────────────────────────┘
                 ↓
┌─────────────────────────────────────┐
│  3. PARSE MOVIES                    │
│     • Use valid keys                │
│     • Round-robin rotation          │
│     • Update metadata               │
│     • Process 1000 movies max       │
└─────────────────────────────────────┘
```

## Quick Commands

### Run Bruteforce
```bash
php artisan omdb:bruteforce
```

### Check Statistics
```bash
php artisan tinker --execute="
echo 'Valid keys: ' . App\Models\OmdbApiKey::valid()->count() . PHP_EOL;
echo 'Invalid keys: ' . App\Models\OmdbApiKey::where('status', 'invalid')->count() . PHP_EOL;
echo 'Pending keys: ' . App\Models\OmdbApiKey::pending()->count() . PHP_EOL;
"
```

### View Checkpoint
```bash
php artisan tinker --execute="echo Cache::get('omdb:checkpoint', 0);"
```

### Reset Checkpoint
```bash
php artisan tinker --execute="Cache::forget('omdb:checkpoint'); echo 'Checkpoint reset';"
```

### Clear All Keys (Fresh Start)
```bash
php artisan tinker --execute="
DB::table('omdb_api_keys')->truncate();
Cache::forget('omdb:checkpoint');
echo 'All cleared';
"
```

## Key Status Values

| Status | Description |
|--------|-------------|
| `pending` | Not yet validated |
| `valid` | ✅ Confirmed working |
| `invalid` | ❌ Rejected by OMDB |
| `unknown` | ⚠️ Network error/timeout |

## Configuration

Edit `config/services.php`:

```php
'omdb' => [
    'validation' => [
        'batch_size' => 50,        // Keys validated at once
        'timeout' => 10,           // Request timeout (seconds)
    ],
    'bruteforce' => [
        'min_pending_keys' => 10000,  // Minimum keys to maintain
        'generation_batch' => 1000,    // Keys generated per batch
    ],
],
```

## Scheduling (Optional)

Add to `bootstrap/app.php`:

```php
->withSchedule(function (Schedule $schedule) {
    // Run daily at 2 AM
    $schedule->command('omdb:bruteforce')->dailyAt('02:00');
    
    // Or run every 6 hours
    $schedule->command('omdb:bruteforce')->everySixHours();
})
```

## Resume Example

**Start command:**
```bash
php artisan omdb:bruteforce
```

**Output:**
```
Starting OMDB API Bruteforce System

✔ Checking pending keys
Generating 10000 new keys...
[████████████████] 100%

Resuming validation from checkpoint: 0
Validating batch of 50 keys (IDs 1 to 50)...
✓ Valid key: 42e12276
```

**Stop command (Ctrl+C):**
```
^C
Checkpoint saved at: 150
```

**Restart command:**
```bash
php artisan omdb:bruteforce
```

**Output:**
```
Resuming validation from checkpoint: 150  ← Continues from here!
Validating batch of 50 keys (IDs 151 to 200)...
```

## Monitoring

### Database Query
```sql
SELECT status, COUNT(*) as count 
FROM omdb_api_keys 
GROUP BY status;
```

### Via Tinker
```bash
php artisan tinker
>>> DB::table('omdb_api_keys')
    ->select('status', DB::raw('count(*) as count'))
    ->groupBy('status')
    ->get()
```

## Troubleshooting

### No valid keys found
**Cause:** All generated keys are invalid (expected with random keys)

**Solution:** The system will continue generating and testing until valid keys are found

### Command stops unexpectedly
**Solution:** Just run it again - it will resume from checkpoint

### Want to start fresh
```bash
php artisan tinker --execute="
    DB::table('omdb_api_keys')->truncate();
    Cache::forget('omdb:checkpoint');
"
php artisan omdb:bruteforce
```

## Performance

- **Key Generation:** ~10,000 keys in ~1 second
- **Validation:** 50 keys in ~10 seconds (concurrent)
- **Movie Parsing:** 50 movies in ~10 seconds (concurrent)
- **Total Throughput:** ~300 keys/minute validated

## Files Reference

| File | Purpose |
|------|---------|
| `app/Console/Commands/OmdbBruteforceCommand.php` | Main command |
| `app/Models/OmdbApiKey.php` | Database model |
| `config/services.php` | Configuration |
| `docs/bruteapi.md` | Full documentation |
| `tests/Feature/Console/OmdbBruteforceCommandTest.php` | Tests |

## Support

For detailed information, see:
- `docs/bruteapi.md` - Complete documentation
- `IMPLEMENTATION_SUMMARY.md` - Technical details

---

**Quick Start:** Just run `php artisan omdb:bruteforce` 🚀

