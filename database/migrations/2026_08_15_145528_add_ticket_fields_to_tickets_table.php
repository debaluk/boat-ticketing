<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {

            $table->string('ticket_number', 30)
                ->unique()
                ->after('id');

            $table->foreignId('queue_id')
                ->unique()
                ->constrained('queues')
                ->restrictOnDelete();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->restrictOnDelete();

            $table->foreignId('service_id')
                ->constrained('services')
                ->restrictOnDelete();

            $table->unsignedInteger('passenger_count')
                ->default(1);

            $table->decimal('price', 15, 2)
                ->default(0);

            $table->decimal('subtotal', 15, 2)
                ->default(0);

            $table->decimal('discount', 15, 2)
                ->default(0);

            $table->decimal('total', 15, 2)
                ->default(0);

            $table->enum('status', [
                'pending',
                'paid',
                'cancelled',
                'completed',
            ])->default('pending');

            $table->softDeletes();

            $table->index('status');
            $table->index('customer_id');
            $table->index('service_id');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {

            $table->dropForeign([
                'queue_id',
            ]);

            $table->dropForeign([
                'customer_id',
            ]);

            $table->dropForeign([
                'service_id',
            ]);

            $table->dropUnique([
                'tickets_ticket_number_unique',
            ]);

            $table->dropUnique([
                'tickets_queue_id_unique',
            ]);

            $table->dropIndex([
                'tickets_status_index',
            ]);

            $table->dropIndex([
                'tickets_customer_id_index',
            ]);

            $table->dropIndex([
                'tickets_service_id_index',
            ]);

            $table->dropColumn([
                'ticket_number',
                'queue_id',
                'customer_id',
                'service_id',
                'passenger_count',
                'price',
                'subtotal',
                'discount',
                'total',
                'status',
                'deleted_at',
            ]);
        });
    }
};