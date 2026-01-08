<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, upgrade any 'business' plans to 'enterprise'
        DB::table('workspaces')
            ->where('plan', 'business')
            ->update(['plan' => 'enterprise']);

        // Then modify the enum to remove 'business'
        DB::statement("ALTER TABLE workspaces MODIFY COLUMN plan ENUM('free', 'starter', 'pro', 'enterprise') NOT NULL DEFAULT 'free'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add 'business' back to the enum
        DB::statement("ALTER TABLE workspaces MODIFY COLUMN plan ENUM('free', 'starter', 'pro', 'business', 'enterprise') NOT NULL DEFAULT 'free'");
    }
};
