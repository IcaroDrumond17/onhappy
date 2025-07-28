<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Listar notificações do usuário
     */
    public function index()
    {
        $user = auth()->user();

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('viewed', 'asc')    
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $notifications,
        ]);
    }

    /**
     * Marca uma notificação como visualizada
     */
    public function markAsViewed($id)
    {
        $user = auth()->user();

        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$notification) {
            return response()->json(['message' => 'Notificação não encontrada.'], 404);
        }

        $notification->viewed = true;
        $notification->save();

        return response()->json([
            'message' => 'Notificação marcada como visualizada.',
            'data' => $notification,
        ]);
    }
}
