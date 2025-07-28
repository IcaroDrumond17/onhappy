<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderStatusTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $defaultUser;
    protected $orderAdmin;
    protected $orderDefaultUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar usuário administrador (admin) e usuário comum (default)
        $this->adminUser = User::factory()->create(['type_user' => 'admin']);
        $this->defaultUser = User::factory()->create(['type_user' => 'default']);

        // Criar uma ordem vinculada ao admin
        $this->orderAdmin = Order::factory()->create([
            'status' => 'requested',
            'user_id' => $this->adminUser->id,
        ]);

        // Criar uma ordem vinculada ao usuário comum
        $this->orderDefaultUser = Order::factory()->create([
            'status' => 'requested',
            'user_id' => $this->defaultUser->id,
        ]);
    }

    /**
     * Admin pode atualizar o status do pedido para 'approved'.
     */
    #[Test]
    public function admin_can_update_status_to_approved()
    {
        $response = $this->actingAs($this->adminUser, 'api')
            ->patchJson("/api/orders/{$this->orderAdmin->id}/status", [
                'status' => 'approved',
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'approved']);
        $this->assertDatabaseHas('orders', [
            'id' => $this->orderAdmin->id,
            'status' => 'approved',
        ]);
    }

    /**
     * Admin pode atualizar o status do pedido para 'canceled'.
     */
    #[Test]
    public function admin_can_update_status_to_canceled()
    {
        $response = $this->actingAs($this->adminUser, 'api')
            ->patchJson("/api/orders/{$this->orderAdmin->id}/status", [
                'status' => 'canceled',
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'canceled']);
        $this->assertDatabaseHas('orders', [
            'id' => $this->orderAdmin->id,
            'status' => 'canceled',
        ]);
    }

    /**
     * Admin não pode atualizar status para valor inválido.
     */
    #[Test]
    public function admin_cannot_update_status_to_invalid_value()
    {
        $response = $this->actingAs($this->adminUser, 'api')
            ->patchJson("/api/orders/{$this->orderAdmin->id}/status", [
                'status' => 'invalid_status',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('status');
    }

    /**
     * Usuário comum pode atualizar o status de seu próprio pedido.
     */
    #[Test]
    public function user_can_update_own_order_status()
    {
        $response = $this->actingAs($this->defaultUser, 'api')
            ->patchJson("/api/orders/{$this->orderDefaultUser->id}/status", [
                'status' => 'approved',
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'approved']);
        $this->assertDatabaseHas('orders', [
            'id' => $this->orderDefaultUser->id,
            'status' => 'approved',
        ]);
    }

    /**
     * Usuário comum NÃO pode atualizar status de pedido que não é dele.
     */
    #[Test]
    public function default_user_cannot_update_status_of_others_order()
    {
        $response = $this->actingAs($this->defaultUser, 'api')
            ->patchJson("/api/orders/{$this->orderAdmin->id}/status", [
                'status' => 'approved',
            ]);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Você não tem permissão para editar este pedido.']);
    }

    /**
     * Usuário não autenticado não pode atualizar status do pedido.
     */
    #[Test]
    public function guest_cannot_update_status()
    {
        $response = $this->patchJson("/api/orders/{$this->orderAdmin->id}/status", [
            'status' => 'approved',
        ]);

        $response->assertStatus(401);
    }
}
