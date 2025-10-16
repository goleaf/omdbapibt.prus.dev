<?php

namespace Database\Seeders;

use App\Enums\AdminAuditAction;
use App\Enums\UserRole;
use App\Models\AdminAuditLog;
use App\Models\ParserEntry;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AdminAuditLogSeeder extends Seeder
{
    /**
     * Seed administrative audit logs tied to parser reviews.
     */
    public function run(): void
    {
        if (! Schema::hasTable('admin_audit_logs')
            || ! Schema::hasTable('users')
            || ! Schema::hasTable('parser_entries')) {
            return;
        }

        if (AdminAuditLog::query()->exists()) {
            return;
        }

        $adminIds = User::query()
            ->where('role', UserRole::Admin->value)
            ->pluck('id');

        if ($adminIds->isEmpty() || ParserEntry::query()->doesntExist()) {
            return;
        }

        ParserEntry::query()
            ->orderBy('id')
            ->chunkById(200, function ($entries) use ($adminIds): void {
                $entries->each(function (ParserEntry $entry) use ($adminIds): void {
                    AdminAuditLog::query()->create([
                        'user_id' => $adminIds->random(),
                        'action' => AdminAuditAction::ParserEntryReviewed->value,
                        'details' => [
                            'parser_entry_id' => $entry->getKey(),
                            'status' => $entry->status->value ?? $entry->status,
                            'notes' => fake()->sentence(),
                        ],
                    ]);
                });
            });
    }
}
