<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Notification;
use App\Models\User;
use App\Models\Order;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        // Pegar id aleatorio de usuarios ja na base de dados
        $userId = User::inRandomOrder()->value('id') ?? User::factory()->create()->id;

        // Pegar id aleatorio de um pedido ja na base de dados
        $orderId = Order::inRandomOrder()->value('id') ?? Order::factory()->create()->id;

        return [
            'order_id' => $orderId,
            'user_id' => $userId,
            'notification_message' => $this->faker->sentence(),
            'viewed' => $this->faker->boolean(20), // 20% true
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
