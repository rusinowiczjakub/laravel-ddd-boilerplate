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
        Schema::table('workspaces', function (Blueprint $table) {
            $table->string('pending_plan')->nullable()->after('plan');
            $table->string('pending_billing_period')->nullable()->after('pending_plan');
            $table->timestamp('plan_changes_at')->nullable()->after('pending_billing_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn(['pending_plan', 'pending_billing_period', 'plan_changes_at']);
        });
    }
};
