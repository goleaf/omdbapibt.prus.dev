<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
use Laravel\Cashier\Subscription;

class CleanupExpiredTrials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trials:cleanup {--dry-run : List the users that would be removed without deleting them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete user accounts whose free trials expired without converting to a paid subscription.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $hasSubscriptionsTable = Schema::hasTable('subscriptions');
        } catch (QueryException $exception) {
            $this->warn('Unable to verify subscription tables: '.$exception->getMessage());

            return self::SUCCESS;
        }

        if (! $hasSubscriptionsTable) {
            $this->warn('Subscriptions table not found. Run Cashier migrations before scheduling trial cleanups.');

            return self::SUCCESS;
        }

        $subscriptions = Subscription::query()
            ->with(['user', 'user.subscriptions'])
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<=', now())
            ->get();

        $users = $subscriptions
            ->pluck('user')
            ->filter()
            ->unique(fn (User $user) => $user->getKey())
            ->filter(fn (User $user) => $this->shouldRemove($user))
            ->values();

        if ($users->isEmpty()) {
            $this->info('No expired trial users to clean up.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->table(['ID', 'Name', 'Email', 'Trial Ended'], $users->map(fn (User $user) => [
                $user->getKey(),
                $user->name,
                $user->email,
                optional($this->latestExpiredTrial($user))->trial_ends_at?->toDayDateTimeString() ?? 'N/A',
            ]));

            $this->info(sprintf('%d user account(s) would be removed.', $users->count()));

            return self::SUCCESS;
        }

        $users->each(function (User $user): void {
            $this->line(sprintf('Deleting user #%d (%s)', $user->getKey(), $user->email));
            $user->delete();
        });

        $this->info(sprintf('Removed %d user account(s) with expired trials.', $users->count()));

        return self::SUCCESS;
    }

    /**
     * Determine if the given user should be removed.
     */
    protected function shouldRemove(User $user): bool
    {
        $subscriptions = $user->subscriptions;

        if ($subscriptions->isEmpty()) {
            return false;
        }

        $hasExpiredTrial = $subscriptions->contains(function (Subscription $subscription): bool {
            return $subscription->trial_ends_at?->isPast() ?? false;
        });

        if (! $hasExpiredTrial) {
            return false;
        }

        $hasEverConverted = $subscriptions->contains(function (Subscription $subscription): bool {
            return in_array($subscription->stripe_status, ['active', 'past_due'], true);
        });

        if ($hasEverConverted) {
            return false;
        }

        $hasValidSubscription = $subscriptions->contains(function (Subscription $subscription): bool {
            return $subscription->valid();
        });

        return ! $hasValidSubscription;
    }

    /**
     * Get the most recent subscription that had an expired trial.
     */
    protected function latestExpiredTrial(User $user): ?Subscription
    {
        return $user->subscriptions
            ->filter(fn (Subscription $subscription) => $subscription->trial_ends_at)
            ->sortByDesc(fn (Subscription $subscription) => $subscription->trial_ends_at)
            ->first();
    }
}
