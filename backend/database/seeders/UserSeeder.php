<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'default@teste.com'],
            [
                'name' => 'Icaro Default',
                'password' => bcrypt('1234'),
                'type_user' => 'default',
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@teste.com'],
            [
                'name' => 'Icaro Admin',
                'password' => bcrypt('1234'),
                'type_user' => 'admin',
            ]
        );
    }
}
