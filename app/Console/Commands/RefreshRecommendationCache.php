<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Movies\RecommendationService;
use Illuminate\Console\Command;

class RefreshRecommendationCache extends Command
{
    protected $signature = 'recommendations:refresh {--user=* : Limit to specific user IDs}';

    protected $description = 'Rebuild cached movie recommendations for users.';

    public function handle(RecommendationService $service): int
    {
        $userIds = collect((array) $this->option('user'))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->all();

        $query = User::query();

        if ($userIds !== []) {
            $query->whereIn('id', $userIds);
        }

        $count = 0;

        $query->chunkById(100, function ($users) use ($service, &$count) {
            foreach ($users as $user) {
                $limits = $service->cachedLimits($user);

                $service->flush($user);
                $service->recommendFor($user);

                foreach ($limits as $limit) {
                    if ($limit === 12) {
                        continue;
                    }

                    $service->recommendFor($user, $limit);
                }
                $count++;
            }
        });

        $this->info("Refreshed recommendations for {$count} users.");

        return self::SUCCESS;
    }
}
