<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Movies\RecommendationService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class RefreshRecommendationsCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recommendations:refresh {--limit=12 : Number of results to cache for each user} {--user=* : Limit the refresh to specific user IDs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild cached movie recommendations for users.';

    public function __construct(protected RecommendationService $recommendations)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $userIds = collect($this->option('user'))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->values();

        $query = User::query();

        if ($userIds->isNotEmpty()) {
            $query->whereIn('id', $userIds);
        }

        $count = 0;

        $query->chunkById(100, function (Collection $users) use (&$count, $limit): void {
            foreach ($users as $user) {
                $this->recommendations->refresh($user, $limit);
                $count++;
            }
        });

        if ($count === 0) {
            $this->info('No users found to refresh.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Refreshed recommendations for %d user(s).', $count));

        return self::SUCCESS;
    }
}
