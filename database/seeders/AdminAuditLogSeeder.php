<?php

namespace Database\Seeders;

use App\Enums\AdminAuditAction;
use App\Enums\UserRole;
use App\Models\AdminAuditLog;
use App\Models\ParserEntry;
use App\Models\User;
use Database\Seeders\Concerns\SeedsModelsInChunks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class AdminAuditLogSeeder extends Seeder
{
    use SeedsModelsInChunks;

    private const CHUNK_SIZE = 500;

    /**
     * Seed administrative audit logs tied to parser reviews.
     */
    public function run(): void
    {
        if (AdminAuditLog::query()->exists()) {
            return;
        }

        $admins = User::query()
            ->where('role', UserRole::Admin->value)
            ->get();

        if ($admins->isEmpty() || ! ParserEntry::query()->exists()) {
            return;
        }

        ParserEntry::query()
            ->orderBy('id')
            ->chunkById(self::CHUNK_SIZE, function (Collection $entries) use ($admins): void {
                $payloads = $entries->map(function (ParserEntry $entry) use ($admins): array {
                    $admin = $admins->random();
                    $status = $entry->status;

                    return [
                        'user_id' => $admin->getKey(),
                        'action' => AdminAuditAction::ParserEntryReviewed->value,
                        'details' => [
                            'parser_entry_id' => $entry->getKey(),
                            'status' => $status instanceof \BackedEnum ? $status->value : (is_object($status) && method_exists($status, 'value') ? $status->value : $status),
                            'notes' => fake()->sentence(),
                        ],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                });

                $normalized = $payloads->map(function (array $attributes): array {
                    $attributes['details'] = json_encode($attributes['details']);

                    return $attributes;
                });

                $this->chunkedInsert($normalized, 500, static fn (array $chunk): bool => AdminAuditLog::query()->insert($chunk));
            });
    }
}
