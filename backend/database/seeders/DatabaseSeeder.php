<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\OrderSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(UserSeeder::class);
        $this->call(OrderSeeder::class);
    }
}
