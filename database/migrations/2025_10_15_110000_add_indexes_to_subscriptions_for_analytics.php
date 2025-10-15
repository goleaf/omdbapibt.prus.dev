<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table): void {
            $table->index('created_at', 'subscriptions_created_at_index');
            $table->index('ends_at', 'subscriptions_ends_at_index');
            $table->index('stripe_status', 'subscriptions_stripe_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table): void {
            $table->dropIndex('subscriptions_created_at_index');
            $table->dropIndex('subscriptions_ends_at_index');
            $table->dropIndex('subscriptions_stripe_status_index');
        });
    }
};
