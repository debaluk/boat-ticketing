<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sync_transactions', function (Blueprint $table) {
            $table->unique(
                ['entity_type', 'entity_id'],
                'sync_transactions_entity_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('sync_transactions', function (Blueprint $table) {
            $table->dropUnique('sync_transactions_entity_unique');
        });
    }
};