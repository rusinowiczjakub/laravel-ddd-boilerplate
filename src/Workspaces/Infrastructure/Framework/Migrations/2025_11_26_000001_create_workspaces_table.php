<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->enum('plan', ['free', 'starter', 'pro', 'business', 'enterprise'])->default('free');
            $table->enum('status', ['active', 'suspended', 'cancelled'])->default('active');
            $table->uuid('owner_id');
            $table->timestamp('created_at');

            $table->unique(['owner_id', 'slug'], 'workspaces_owner_slug_unique');
            $table->index('owner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspaces');
    }
};
