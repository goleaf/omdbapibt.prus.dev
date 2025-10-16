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
                $limits = collect($service->cachedLimits($user))
                    ->push(12)
                    ->map(fn ($value) => (int) $value)
                    ->filter(fn (int $limit) => $limit > 0)
                    ->unique()
                    ->values();

                $service->flush($user);

                foreach ($limits as $limit) {
                    $service->recommendFor($user, $limit);
                }

                $count++;
            }
        });

        $this->info(sprintf('Refreshed recommendations for %d users.', $count));

        return self::SUCCESS;
    }
}
