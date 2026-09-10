<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{
Schema::create('roles',function(Blueprint $t){$t->id();$t->uuid('uuid')->unique();$t->string('code',50)->unique();$t->string('name',100);$t->text('description')->nullable();$t->boolean('is_active')->default(true);$t->timestamps();});
Schema::create('permissions',function(Blueprint $t){$t->id();$t->uuid('uuid')->unique();$t->string('code',100)->unique();$t->string('name',150);$t->string('module',80)->nullable();$t->timestamps();});
} public function down():void{Schema::dropIfExists('permissions');Schema::dropIfExists('roles');}};
