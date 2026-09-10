<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->id();

            $table->string('queue_number', 30);

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->restrictOnDelete();

            $table->foreignId('service_id')
                ->constrained('services')
                ->restrictOnDelete();

            $table->foreignId('boat_id')
                ->nullable()
                ->constrained('boats')
                ->nullOnDelete();

            $table->unsignedInteger('passenger_count')->default(1);

            $table->date('queue_date');
            $table->time('queue_time');

            $table->enum('status', [
                'waiting',
                'called',
                'boarding',
                'on_trip',
                'completed',
                'cancelled'
            ])->default('waiting');

            $table->timestamps();

            $table->index([
                'queue_date',
                'status'
            ]);

            $table->index([
                'queue_date',
                'queue_number'
            ]);

            $table->index([
                'status',
                'created_at'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};