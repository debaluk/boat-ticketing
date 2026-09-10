<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->string('payment_number', 30)->unique();

            $table->foreignId('ticket_id')
                ->unique()
                ->constrained('tickets')
                ->restrictOnDelete();

            $table->decimal('amount', 15, 2);

            $table->enum('method', [
                'cash',
                'transfer',
                'qris',
                'debit',
                'credit_card',
            ]);

            $table->enum('status', [
                'pending',
                'paid',
                'cancelled',
                'refunded',
            ])->default('pending');

            $table->timestamp('paid_at')->nullable();

            $table->string('reference_number', 100)->nullable();

            $table->foreignId('cashier_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('method');
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};