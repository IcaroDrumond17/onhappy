<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(), // cria um usuário automaticamente e vincula
            'requestor_name' => $this->faker->name,
            'destination' => $this->faker->city,
            'departure_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'return_date' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'status' => 'requested',
        ];
    }
}