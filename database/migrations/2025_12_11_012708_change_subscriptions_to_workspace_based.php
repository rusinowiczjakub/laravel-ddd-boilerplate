<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Drop old index
            $table->dropIndex('subscriptions_user_id_stripe_status_index');

            // Change user_id to workspace_id (UUID)
            $table->dropColumn('user_id');
            $table->char('workspace_id', 36)->after('id');

            // Add new index
            $table->index(['workspace_id', 'stripe_status'], 'subscriptions_workspace_id_stripe_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Drop new index
            $table->dropIndex('subscriptions_workspace_id_stripe_status_index');

            // Change back to user_id
            $table->dropColumn('workspace_id');
            $table->unsignedBigInteger('user_id')->after('id');

            // Restore old index
            $table->index(['user_id', 'stripe_status'], 'subscriptions_user_id_stripe_status_index');
        });
    }
};
