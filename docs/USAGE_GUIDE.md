# OMDB API Bruteforce - Complete Usage Guide

## 🚀 Getting Started

### Run the Command

```bash
php artisan omdb:bruteforce
```

That's it! No parameters, no configuration needed.

---

## 📋 What Happens When You Run It

### Step 1: Key Generation
```
✔ Checking pending keys
Generating 10000 new keys...
[████████████████████████████] 100%
✔ Generated 10000 keys
```

### Step 2: Async Validation
```
Resuming validation from checkpoint: 0
Validating batch of 50 keys (IDs 1 to 50)...
✓ Valid key: 42e12276
Batch complete. Checkpoint: 50 | Valid: 1 | Invalid: 49 | Total: 50
...
All keys validated!
Validation complete! Processed: 10000 | Valid: 15 | Invalid: 9985
```

### Step 3: Movie Parsing
```
Parsing movies with 15 valid key(s)...
Found 500 movies to update
[████████████████████████████] 100%
Movie parsing complete! Processed: 500 | Updated: 482
```

---

## 💡 Common Use Cases

### 1. First Run (Generate & Validate Keys)
```bash
php artisan omdb:bruteforce
```
- Generates 10,000 random API keys
- Validates them asynchronously
- Saves checkpoint for later

### 2. Continue Validation (Resume)
```bash
php artisan omdb:bruteforce
```
- Automatically resumes from last checkpoint
- No need to specify --resume flag
- Just run the same command again

### 3. Parse Movies After Finding Valid Keys
```bash
php artisan omdb:bruteforce
```
- If valid keys exist, automatically parses movies
- Updates metadata: title, year, plot, poster, rating
- Processes up to 1,000 movies per run

### 4. Daily Automated Run
Add to `bootstrap/app.php`:
```php
->withSchedule(function (Schedule $schedule) {
    $schedule->command('omdb:bruteforce')->daily();
})
```

---

## 🔍 Monitoring & Statistics

### Check Key Statistics
```bash
php artisan tinker --execute="
echo 'Total Keys: ' . App\Models\OmdbApiKey::count() . PHP_EOL;
echo 'Valid Keys: ' . App\Models\OmdbApiKey::valid()->count() . PHP_EOL;
echo 'Invalid: ' . App\Models\OmdbApiKey::where('status', 'invalid')->count() . PHP_EOL;
echo 'Pending: ' . App\Models\OmdbApiKey::pending()->count() . PHP_EOL;
"
```

### View Status Distribution
```bash
php artisan tinker --execute="
DB::table('omdb_api_keys')
    ->select('status', DB::raw('count(*) as count'))
    ->groupBy('status')
    ->get()
    ->each(fn(\$r) => print_r(\$r));
"
```

### Check Current Checkpoint
```bash
php artisan tinker --execute="
echo 'Checkpoint: ' . Cache::get('omdb:checkpoint', 0);
"
```

---

## 🛠️ Troubleshooting

### Problem: Command Stops Unexpectedly

**Solution:** Just run it again
```bash
php artisan omdb:bruteforce
```
It automatically resumes from the last checkpoint.

### Problem: No Valid Keys Found

**Expected Behavior:** Random keys rarely match real API keys. This is normal.

**What to Do:** 
- System continues generating and testing
- Eventually valid keys may be found (very rare)
- Or use a known valid key in `.env` as fallback

### Problem: Movies Not Updating

**Check:**
1. Do valid keys exist?
   ```bash
   php artisan tinker --execute="echo App\Models\OmdbApiKey::valid()->count();"
   ```

2. Do movies need updates?
   ```bash
   php artisan tinker --execute="echo App\Models\Movie::whereNull('plot')->count();"
   ```

**Solution:** If both are > 0, run the command again.

### Problem: Want to Start Fresh

**Reset Everything:**
```bash
php artisan tinker --execute="
DB::table('omdb_api_keys')->truncate();
Cache::forget('omdb:checkpoint');
echo 'Reset complete';
"
```

Then run:
```bash
php artisan omdb:bruteforce
```

---

## ⚙️ Configuration

Edit `config/services.php`:

### Validation Settings
```php
'validation' => [
    'test_imdb_id' => 'tt3896198',  // Movie to test against
    'batch_size' => 50,              // Keys validated at once
    'timeout' => 10,                 // Request timeout (seconds)
],
```

### Bruteforce Settings
```php
'bruteforce' => [
    'charset' => '0123456789abcdefghijklmnopqrstuvwxyz',
    'key_length' => 8,
    'min_pending_keys' => 10000,     // Minimum to maintain
    'generation_batch' => 1000,      // Keys per batch
],
```

**Apply Changes:**
```bash
php artisan config:cache
```

---

## 📊 Performance Tuning

### Increase Validation Speed
```php
// In config/services.php
'validation' => [
    'batch_size' => 100,  // Default: 50
],
```

### Generate More Keys
```php
'bruteforce' => [
    'min_pending_keys' => 50000,  // Default: 10000
],
```

### Parse More Movies Per Run
```php
// Edit OmdbBruteforceCommand.php line 216
->limit(5000)  // Default: 1000
```

---

## 🔄 Resume Examples

### Scenario 1: Validation Interrupted

**Run 1:**
```bash
php artisan omdb:bruteforce
```
Output:
```
Validating batch of 50 keys (IDs 1 to 50)...
Checkpoint: 50
^C (Stopped)
```

**Run 2:**
```bash
php artisan omdb:bruteforce
```
Output:
```
Resuming validation from checkpoint: 50  ← Continues here!
Validating batch of 50 keys (IDs 51 to 100)...
```

### Scenario 2: Manual Checkpoint Reset

```bash
# Reset to start from beginning
php artisan tinker --execute="Cache::forget('omdb:checkpoint');"

# Run again (starts at 0)
php artisan omdb:bruteforce
```

### Scenario 3: Reset to Specific Position

```bash
# Set checkpoint to 500
php artisan tinker --execute="Cache::put('omdb:checkpoint', 500);"

# Run (starts at 501)
php artisan omdb:bruteforce
```

---

## 📈 Understanding Status Values

| Status | Meaning | Action |
|--------|---------|--------|
| `pending` | Not yet tested | Will be validated next run |
| `valid` | ✅ Working key | Used for movie parsing |
| `invalid` | ❌ Rejected | Skipped in future |
| `unknown` | ⚠️ Network error | Could retry later |

---

## 🎯 Best Practices

### 1. Run During Off-Peak Hours
```bash
# Via cron at 2 AM
0 2 * * * cd /www/wwwroot/omdbapibt.prus.dev && php artisan omdb:bruteforce
```

### 2. Monitor Valid Key Count
```bash
# Alert if valid keys < 5
VALID_COUNT=$(php artisan tinker --execute="echo App\Models\OmdbApiKey::valid()->count();")
if [ $VALID_COUNT -lt 5 ]; then
    echo "Warning: Only $VALID_COUNT valid keys!"
fi
```

### 3. Regular Checkpoint Backups
```bash
# Save checkpoint
php artisan tinker --execute="
file_put_contents('checkpoint.txt', Cache::get('omdb:checkpoint', 0));
"

# Restore checkpoint
php artisan tinker --execute="
Cache::put('omdb:checkpoint', file_get_contents('checkpoint.txt'));
"
```

---

## 🧹 Maintenance

### Weekly: Clean Unknown Keys
```bash
php artisan tinker --execute="
DB::table('omdb_api_keys')
    ->where('status', 'unknown')
    ->where('created_at', '<', now()->subWeek())
    ->delete();
echo 'Old unknown keys removed';
"
```

### Monthly: Validate Stats
```bash
php artisan tinker --execute="
\$stats = DB::table('omdb_api_keys')
    ->selectRaw('status, count(*) as count, max(created_at) as latest')
    ->groupBy('status')
    ->get();
print_r(\$stats->toArray());
"
```

---

## 📚 Quick Reference

| Task | Command |
|------|---------|
| Run bruteforce | `php artisan omdb:bruteforce` |
| Check stats | `php artisan tinker --execute="App\Models\OmdbApiKey::valid()->count()"` |
| Reset checkpoint | `php artisan tinker --execute="Cache::forget('omdb:checkpoint')"` |
| Clear all keys | `php artisan tinker --execute="DB::table('omdb_api_keys')->truncate()"` |
| Run tests | `php artisan test --filter=OmdbBruteforceCommandTest` |
| View logs | `tail -f storage/logs/laravel.log` |

---

## 🆘 Support

- **Quick Start:** `docs/bruteapi-quickstart.md`
- **Full Documentation:** `docs/bruteapi.md`
- **Technical Details:** `IMPLEMENTATION_SUMMARY.md`
- **Main README:** `OMDB_BRUTEFORCE_README.md`

---

## ✅ Quick Start Checklist

- [ ] Database migrated: `php artisan migrate`
- [ ] Config cached: `php artisan config:cache`
- [ ] Run command: `php artisan omdb:bruteforce`
- [ ] Monitor progress in terminal
- [ ] Check valid keys: `php artisan tinker --execute="App\Models\OmdbApiKey::valid()->count()"`
- [ ] Schedule if needed: Add to `bootstrap/app.php`

**You're ready to go! 🚀**

