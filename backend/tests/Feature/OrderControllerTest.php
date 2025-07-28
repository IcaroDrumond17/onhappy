<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['type_user' => 'default']);
        $this->admin = User::factory()->create(['type_user' => 'admin']);
    }

    /**
     * Checa se o admin pode listar todos os pedidos.
     */
    #[Test]
    public function admin_can_list_all_orders()
    {
        Order::factory()->count(5)->create();

        $response = $this->actingAs($this->admin, 'api')->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'requestor_name', 'destination', 'departure_date', 'return_date', 'status', 'user_id']],
            ])
            ->assertJsonCount(5, 'data');
    }

     /**
     * Garante que o usuário que não for admin só possa acessar pedidos próprios.
     */
    #[Test]
    public function user_cannot_access_admin_order_list()
    {
        // Cria pedidos de vários usuários, incluindo o usuário testado
        Order::factory()->count(3)->create(['user_id' => $this->user->id]);
        Order::factory()->count(2)->create();

        $response = $this->actingAs($this->user, 'api')->getJson('/api/orders');

        $response->assertStatus(200);
        // Deve retornar só pedidos do usuário autenticado
        foreach ($response->json('data') as $order) {
            $this->assertEquals($this->user->id, $order['user_id']);
        }
    }

    /**
     * Garante que o usuário possa listar apenas pedidos por ele criado.
     */
    #[Test]
    public function user_can_list_their_own_orders()
    {
        Order::factory()->count(3)->create(['user_id' => $this->user->id]);
        Order::factory()->count(2)->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->user, 'api')->getJson('/api/orders/user');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /**
     * Checa se o usuario pode criar um novo pedido (user_id não enviado no payload, é preenchido pelo backend).
     */
    #[Test]
    public function user_can_create_order()
    {
        $orderData = [
            'requestor_name' => 'Icaro',
            'destination' => 'Ipatinga',
            'departure_date' => '2025-08-01',
            'return_date' => '2025-08-10',
            'status' => 'requested', // opcional
        ];

        $response = $this->actingAs($this->user, 'api')->postJson('/api/orders', $orderData);

        $response->assertStatus(201)
            ->assertJsonFragment(['requestor_name' => 'Icaro'])
            ->assertJsonPath('data.user_id', $this->user->id);
    }

    /**
     * Checa se o usuário pode ver um pedido que ele mesmo criou.
     */
    #[Test]
    public function user_can_view_own_order()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'api')->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $order->id]);
    }

    /**
     * Checa se o usuário não consegue ver pedidos de outros usuários.
     */
    #[Test]
    public function user_cannot_view_others_orders()
    {
        $order = Order::factory()->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->user, 'api')->getJson("/api/orders/{$order->id}");

        $response->assertStatus(403)
            ->assertJson(['message' => 'Você não tem permissão para visualizar este pedido.']);
    }

    /**
     * Checa se o usuário pode modificar pedidos criados por ele mesmo.
     */
    #[Test]
    public function user_can_update_own_order()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $updateData = ['destination' => 'Belo Horizonte'];

        $response = $this->actingAs($this->user, 'api')->putJson("/api/orders/{$order->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment(['destination' => 'Belo Horizonte']);
    }

    /**
     * Checa se o usuário é proibido de modificar pedidos de outros usuários.
     */
    #[Test]
    public function user_cannot_update_others_orders()
    {
        $order = Order::factory()->create(['user_id' => $this->admin->id]);

        $updateData = ['destination' => 'Recife'];

        $response = $this->actingAs($this->user, 'api')->putJson("/api/orders/{$order->id}", $updateData);

        $response->assertStatus(403)
            ->assertJson(['message' => 'Você não tem permissão para atualizar este pedido.']);
    }

    /**
     * Checa se o usuário pode remover um pedido por ele criado.
     */
    #[Test]
    public function user_can_delete_own_order()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'api')->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Pedido deletado com sucesso.']);
    }

    /**
     * Checa se o usuário é proibido de remover pedidos de outros usuários.
     */
    #[Test]
    public function user_cannot_delete_others_orders()
    {
        $order = Order::factory()->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->user, 'api')->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(403)
            ->assertJson(['message' => 'Você não tem permissão para deletar este pedido.']);
    }

    /**
     * Checa se o usuário pode filtrar seus pedidos por status e destino.
     */
    #[Test]
    public function user_can_filter_orders_by_status_and_destination()
    {
        Order::factory()->create([
            'status' => 'requested',
            'destination' => 'Ipatinga',
            'user_id' => $this->user->id,
        ]);

        Order::factory()->create([
            'status' => 'approved',
            'destination' => 'Rio de Janeiro',
            'user_id' => $this->user->id,
        ]);

        $filters = [
            'status' => ['requested'],
            'destination' => ['Ipatinga'],
        ];

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/orders/user?' . http_build_query($filters));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['destination' => 'Ipatinga']);
    }

    /**
     * Checa se o usuário pode filtrar seus pedidos por data de ida e volta.
     */
    #[Test]
    public function user_can_filter_orders_by_date_range(): void
    {

        Order::factory()->create([
            'departure_date' => '2025-08-01',
            'return_date' => '2025-08-10',
            'user_id' => $this->user->id,
        ]);

        
        Order::factory()->create([
            'departure_date' => '2025-09-01',
            'return_date' => '2025-09-10',
            'user_id' => $this->user->id,
        ]);


        $filters = [
            'departure_date' => '2025-08-01',
            'return_date' => '2025-08-10',
        ];


        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/orders/user?' . http_build_query($filters));


        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $this->assertEquals('2025-08-01', substr($response->json('data')[0]['departure_date'], 0, 10));
        $this->assertEquals('2025-08-10', substr($response->json('data')[0]['return_date'], 0, 10));
    }

    /**
     * Checa se o admin pode visualizar qualquer pedido, independentemente de quem criou.
     */
    #[Test]
    public function admin_can_view_any_order()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->admin, 'api')->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $order->id]);
    }

    /**
     * Testa se admin pode atualizar o status do pedido.
     */
    #[Test]
    public function admin_can_update_order_status()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id, 'status' => 'requested']);

        $response = $this->actingAs($this->admin, 'api')->patchJson("/api/orders/{$order->id}/status", [
            'status' => 'approved',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'approved']);
    }

    /**
     * Testa se o dono do pedido pode atualizar o status.
     */
    #[Test]
    public function owner_can_update_order_status()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id, 'status' => 'requested']);

        $response = $this->actingAs($this->user, 'api')->patchJson("/api/orders/{$order->id}/status", [
            'status' => 'approved',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'approved']);
    }

    /**
     * Testa se usuário que não é admin nem dono não pode atualizar status.
     */
    #[Test]
    public function other_user_cannot_update_order_status()
    {
        $otherUser = User::factory()->create(['type_user' => 'default']);
        $order = Order::factory()->create(['user_id' => $this->user->id, 'status' => 'requested']);

        $response = $this->actingAs($otherUser, 'api')->patchJson("/api/orders/{$order->id}/status", [
            'status' => 'approved',
        ]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'Você não tem permissão para editar este pedido.']);
    }

    /**
     * Testa se não é permitido cancelar pedido já aprovado.
     */
    #[Test]
    public function cannot_cancel_approved_order()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id, 'status' => 'approved']);

        $response = $this->actingAs($this->user, 'api')->patchJson("/api/orders/{$order->id}/status", [
            'status' => 'canceled',
        ]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'Não é possível cancelar um pedido já aprovado.']);
    }

    /**
     * Testa validação para status inválido.
     */
    #[Test]
    public function invalid_status_returns_validation_error()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id, 'status' => 'requested']);

        $response = $this->actingAs($this->user, 'api')->patchJson("/api/orders/{$order->id}/status", [
            'status' => 'invalid_status',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('status');
    }

    /**
     * Testa filtro pelo campo departure_date (data de saída).
     */
    #[Test]
    public function user_can_filter_orders_by_departure_date()
    {
        Order::factory()->create([
            'user_id' => $this->user->id,
            'departure_date' => '2025-07-27',
        ]);
        Order::factory()->create([
            'user_id' => $this->user->id,
            'departure_date' => '2025-07-28',
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/orders/user?departure_date=2025-07-27');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('2025-07-27', substr($response->json('data')[0]['departure_date'], 0, 10));
    }

    /**
     * Testa filtro pelo campo return_date (data de retorno).
     */
    #[Test]
    public function user_can_filter_orders_by_return_date()
    {
        Order::factory()->create([
            'user_id' => $this->user->id,
            'return_date' => '2025-08-05',
        ]);
        Order::factory()->create([
            'user_id' => $this->user->id,
            'return_date' => '2025-08-06',
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/orders/user?return_date=2025-08-05');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('2025-08-05', substr($response->json('data')[0]['return_date'], 0, 10));
    }

    /**
     * Testa filtro pelo campo start_date (data inicial de criação).
     */
    #[Test]
    public function user_can_filter_orders_by_start_date()
    {
        Order::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => Carbon::parse('2025-07-01 10:00:00'),
        ]);
        Order::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => Carbon::parse('2025-06-20 10:00:00'),
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/orders/user?start_date=2025-07-01');

        $response->assertStatus(200);
        foreach ($response->json('data') as $order) {
            $this->assertTrue($order['created_at'] >= '2025-07-01');
        }
    }

    /**
     * Testa filtro pelo campo end_date (data final de criação).
     */
    #[Test]
    public function user_can_filter_orders_by_end_date()
    {
        Order::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => Carbon::parse('2025-07-01 10:00:00'),
        ]);
        Order::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => Carbon::parse('2025-07-15 10:00:00'),
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/orders/user?end_date=2025-07-10');

        $response->assertStatus(200);
        foreach ($response->json('data') as $order) {
            $this->assertTrue($order['created_at'] <= '2025-07-10');
        }
    }

    /**
     * Testa filtro combinado start_date e end_date.
     */
    #[Test]
    public function user_can_filter_orders_between_start_and_end_date()
    {
        Order::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => Carbon::parse('2025-07-05 10:00:00'),
        ]);
        Order::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => Carbon::parse('2025-07-20 10:00:00'),
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/orders/user?start_date=2025-07-01&end_date=2025-07-10');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertTrue($response->json('data')[0]['created_at'] >= '2025-07-01');
        $this->assertTrue($response->json('data')[0]['created_at'] <= '2025-07-10');
    }

    /**
     * Testa filtro combinado start_date e departure_date.
     */
    #[Test]
    public function user_can_filter_orders_by_start_date_and_departure_date()
    {
        Order::factory()->create([
            'user_id' => $this->user->id,
            'departure_date' => '2025-07-15',
            'created_at' => Carbon::parse('2025-07-01 10:00:00'),
        ]);
        Order::factory()->create([
            'user_id' => $this->user->id,
            'departure_date' => '2025-07-15',
            'created_at' => Carbon::parse('2025-06-20 10:00:00'),
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/orders/user?start_date=2025-07-01&departure_date=2025-07-15');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('2025-07-15', substr($response->json('data')[0]['departure_date'], 0, 10));
        $this->assertTrue($response->json('data')[0]['created_at'] >= '2025-07-01');
    }

    /**
     * Testa filtro combinado end_date e return_date.
     */
    #[Test]
    public function user_can_filter_orders_by_end_date_and_return_date()
    {
        Order::factory()->create([
            'user_id' => $this->user->id,
            'return_date' => '2025-07-20',
            'created_at' => Carbon::parse('2025-07-10 10:00:00'),
        ]);
        
        Order::factory()->create([
            'user_id' => $this->user->id,
            'return_date' => '2025-07-20',
            'created_at' => Carbon::parse('2025-07-16 10:00:00'), // Fora do filtro
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/orders/user?end_date=2025-07-15&return_date=2025-07-20');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('2025-07-20', substr($response->json('data')[0]['return_date'], 0, 10));
        $this->assertTrue($response->json('data')[0]['created_at'] <= '2025-07-15');
    }
}
