<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar usuário autenticado e outro usuário para testes de isolamento
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    /**
     * Checa se usuário autenticado consegue listar apenas suas notificações.
     * Confirma que notificações de outros usuários não aparecem.
     */
    #[Test]
    public function authenticated_user_can_list_their_notifications()
    {
        Notification::factory()->create([
            'user_id' => $this->user->id,
            'notification_message' => 'Notificação 1',
            'viewed' => false,
        ]);
        Notification::factory()->create([
            'user_id' => $this->user->id,
            'notification_message' => 'Notificação 2',
            'viewed' => true,
        ]);
        // Notificação de outro usuário, que não deve aparecer na listagem
        Notification::factory()->create([
            'user_id' => $this->otherUser->id,
            'notification_message' => 'Notificação de outro usuário',
        ]);

        $response = $this->actingAs($this->user, 'api')->getJson('/api/notifications');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['notification_message' => 'Notificação 1']);
        $response->assertJsonFragment(['notification_message' => 'Notificação 2']);
        $response->assertJsonMissing(['notification_message' => 'Notificação de outro usuário']);
    }

    /**
     * Checa se o usuário autenticado pode marcar sua notificação como vista.
     * Confirma o update no banco de dados.
     */
    #[Test]
    public function user_can_mark_notification_as_viewed()
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'viewed' => false,
        ]);

        $response = $this->actingAs($this->user, 'api')->patchJson("/api/notifications/{$notification->id}/viewed");

        $response->assertStatus(200)
            ->assertJsonFragment(['viewed' => true]);

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'viewed' => true,
        ]);
    }

    /**
     * Garante que o usuário não pode marcar como vista uma notificação que pertence a outro usuário.
     * Deve retornar 404 (não encontrado) para impedir acesso não autorizado.
     */
    #[Test]
    public function user_cannot_mark_notification_of_other_user()
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->otherUser->id,
            'viewed' => false,
        ]);

        $response = $this->actingAs($this->user, 'api')->patchJson("/api/notifications/{$notification->id}/viewed");

        $response->assertStatus(404);
    }

    /**
     * Checa se um usuário não autenticado não pode listar notificações.
     */
    #[Test]
    public function guest_cannot_list_notifications()
    {
        $response = $this->getJson('/api/notifications');

        $response->assertStatus(401);
    }

    /**
     * Checa que um usuário não autenticado não pode marcar notificações como vistas.
     */
    #[Test]
    public function guest_cannot_mark_notification_as_viewed()
    {
        $notification = Notification::factory()->create();

        $response = $this->patchJson("/api/notifications/{$notification->id}/viewed");

        $response->assertStatus(401);
    }
}
