<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder { 
	public function run(): void
	{
		$this->call([
			BoatSeeder::class,
			CustomerSeeder::class,
			ServiceSeeder::class,
			AgentSeeder::class,
		]);
	}

}

