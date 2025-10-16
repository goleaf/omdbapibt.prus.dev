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

        $admins = User::query()
            ->where('role', UserRole::Admin->value)
            ->get();

        $parserEntries = ParserEntry::query()->get();

        if ($admins->isEmpty() || $parserEntries->isEmpty()) {
            return;
        }

        $parserEntries->each(function (ParserEntry $entry) use ($admins): void {
            $admin = $admins->random();

            AdminAuditLog::query()->create([
                'user_id' => $admin->getKey(),
                'action' => AdminAuditAction::ParserEntryReviewed->value,
                'details' => [
                    'parser_entry_id' => $entry->getKey(),
                    'status' => $entry->status->value ?? $entry->status,
                    'notes' => fake()->sentence(),
                ],
            ]);
        });
    }
}
