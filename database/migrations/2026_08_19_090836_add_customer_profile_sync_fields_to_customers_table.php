<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Add Customer Profile & Sync Fields
        |--------------------------------------------------------------------------
        */

        Schema::table('customers', function (Blueprint $table) {
            $table->uuid('uuid')
                ->nullable()
                ->unique()
                ->after('id');

            $table->string('email', 150)
                ->nullable()
                ->after('phone');

            $table->text('address')
                ->nullable()
                ->after('email');

            $table->string('country', 100)
                ->nullable()
                ->after('address');

            $table->string('sync_status', 20)
                ->default('pending')
                ->after('status');

            $table->timestamp('last_synced_at')
                ->nullable()
                ->after('sync_status');
        });

        /*
        |--------------------------------------------------------------------------
        | Generate UUID for Existing Customers
        |--------------------------------------------------------------------------
        */

        DB::table('customers')
            ->whereNull('uuid')
            ->orderBy('id')
            ->get()
            ->each(function ($customer) {
                DB::table('customers')
                    ->where('id', $customer->id)
                    ->update([
                        'uuid' => (string) Str::uuid(),
                    ]);
            });

        /*
        |--------------------------------------------------------------------------
        | UUID is Required
        |--------------------------------------------------------------------------
        */

        Schema::table('customers', function (Blueprint $table) {
            $table->uuid('uuid')
                ->nullable(false)
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'uuid',
                'email',
                'address',
                'country',
                'sync_status',
                'last_synced_at',
            ]);
        });
    }
};