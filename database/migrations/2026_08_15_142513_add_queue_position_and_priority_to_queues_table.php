<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->unsignedInteger('queue_position')
                ->default(0)
                ->after('queue_number');

            $table->unsignedInteger('priority')
                ->default(0)
                ->after('queue_position');

            $table->index([
                'queue_date',
                'queue_position',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->dropIndex([
                'queues_queue_date_queue_position_index'
            ]);

            $table->dropColumn([
                'queue_position',
                'priority',
            ]);
        });
    }
};