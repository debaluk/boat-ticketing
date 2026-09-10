<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('devices',function(Blueprint $t){$t->id();$t->uuid('uuid')->unique();$t->string('device_code',80)->unique();$t->string('device_name',120);$t->string('device_type',50);$t->string('location',150)->nullable();$t->string('status',30)->default('ACTIVE');$t->timestamp('last_seen_at')->nullable();$t->timestamps();});} public function down():void{Schema::dropIfExists('devices');}};
