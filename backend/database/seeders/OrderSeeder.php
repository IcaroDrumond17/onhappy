<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        // Se não houver usuários, cria um
        if ($users->isEmpty()) {
            $users = collect([User::factory()->create()]);
        }

        foreach ($users as $user) {
            Order::factory()
                ->count(10) // Define quantos pedidos criar por usuário
                ->create([
                    'user_id' => $user->id,
                ]);
        }
    }
}