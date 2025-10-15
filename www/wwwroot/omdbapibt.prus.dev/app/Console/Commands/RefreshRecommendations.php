<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\Console\Command;

class RefreshRecommendations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recommendations:refresh {--user= : Only refresh recommendations for the given user id} {--limit=10 : Number of items to cache per user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate cached recommendations for one or all users.';

    public function handle(RecommendationService $service): int
    {
        $limit = (int) $this->option('limit');
        $userId = $this->option('user');

        if (! empty($userId)) {
            $user = User::query()->find($userId);

            if ($user === null) {
                $this->error("User [{$userId}] was not found.");

                return self::FAILURE;
            }

            $service->refreshRecommendations($user, $limit);
            $this->info("Recommendations refreshed for user {$user->id}.");

            return self::SUCCESS;
        }

        $count = 0;

        User::query()
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($users) use ($service, $limit, &$count) {
                foreach ($users as $user) {
                    $service->refreshRecommendations($user, $limit);
                    $count++;
                    $this->output->writeln("<info>Cached recommendations for user {$user->id}</info>");
                }
            });

        $this->info("Recommendation cache refresh complete for {$count} users.");

        return self::SUCCESS;
    }
}
