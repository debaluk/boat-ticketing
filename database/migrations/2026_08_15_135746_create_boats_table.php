<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boats', function (Blueprint $table) {
            $table->id();

            $table->string('code', 30)->unique();
            $table->string('name', 100);
            $table->string('registration_number', 100)->nullable();

            $table->unsignedInteger('capacity')->default(0);

            $table->enum('status', [
                'active',
                'inactive',
                'maintenance'
            ])->default('active');

            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boats');
    }
};