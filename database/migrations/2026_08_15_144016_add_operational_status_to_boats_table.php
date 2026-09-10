<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boats', function (Blueprint $table) {
            $table->enum('operational_status', [
                'available',
                'boarding',
                'on_trip',
                'maintenance',
            ])->default('available')->after('status');

            $table->index('operational_status');
        });
    }

    public function down(): void
    {
        Schema::table('boats', function (Blueprint $table) {
            $table->dropIndex([
                'boats_operational_status_index'
            ]);

            $table->dropColumn('operational_status');
        });
    }
};