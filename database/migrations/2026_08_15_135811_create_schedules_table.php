<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('boat_id')
                ->constrained('boats')
                ->restrictOnDelete();

            $table->foreignId('route_id')
                ->constrained('routes')
                ->restrictOnDelete();

            $table->date('departure_date');

            $table->time('departure_time');
            $table->time('arrival_time')->nullable();

            $table->decimal('price', 15, 2)->default(0);

            $table->unsignedInteger('capacity')->default(0);
            $table->unsignedInteger('available_seats')->default(0);

            $table->enum('status', [
                'scheduled',
                'boarding',
                'departed',
                'completed',
                'cancelled'
            ])->default('scheduled');

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'departure_date',
                'status'
            ]);

            $table->index([
                'boat_id',
                'departure_date'
            ]);

            $table->index([
                'route_id',
                'departure_date'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};