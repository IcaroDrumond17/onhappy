<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Aplica filtros na query das orders.
     */
   private function applyFilters($query, Request $request)
    {
        if ($request->filled('requestor_name')) {
            $query->where('requestor_name', 'like', '%' . $request->requestor_name . '%');
        }

        if ($request->filled('status')) {
            $status = $request->input('status', []);
            if (is_array($status) && count($status) > 0) {
                $query->whereIn('status', $status);
            }
        }

        if ($request->filled('destination')) {
            $destinations = $request->input('destination', []);
            if (is_array($destinations) && count($destinations) > 0) {
                $query->where(function ($q) use ($destinations) {
                    foreach ($destinations as $dest) {
                        $q->orWhereRaw('LOWER(destination) LIKE ?', ['%' . strtolower($dest) . '%']);
                    }
                });
            }
        }

        if ($request->filled('departure_date') && $request->filled('return_date')) {
            $query->whereDate('departure_date', '=', $request->departure_date)
            ->whereDate('return_date', '=', $request->return_date);
        } elseif ($request->filled('departure_date')) {
            $query->whereDate('departure_date', '=', $request->departure_date);
        } elseif ($request->filled('return_date')) {
            $query->whereDate('return_date', '=', $request->return_date);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        return $query;
    }



    /**
     * Lista orders do usuário autenticado com filtros opcionais.
     */
    public function ordersByUser(Request $request)
    {
        try {
            $query = Order::where('user_id', auth()->id());
            $query = $this->applyFilters($query, $request);
            $orders = $query->get();

            Log::info('Listagem de pedidos do usuário realizada', ['user_id' => auth()->id()]);

            return response()->json([
                'message' => 'Pedidos do usuário listados com sucesso.',
                'data' => $orders,
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao listar pedidos do usuário', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Erro ao listar pedidos do usuário.'], 500);
        }
    }

    /**
     * Lista todas as orders com filtros opcionais.
     */
    public function allOrders(Request $request)
    {
        try {
            $query = Order::query();

            // Se não for admin, restringe para os pedidos do usuário autenticado
            if (auth()->user()->type_user !== 'admin') {
                $query->where('user_id', auth()->id());
            }

            $query = $this->applyFilters($query, $request);
            $orders = $query->get();

            Log::info('Listagem de pedidos realizada', [
                'user_id' => auth()->id(),
                'is_admin' => auth()->user()->type_user === 'admin',
            ]);

            return response()->json([
                'message' => 'Pedidos listados com sucesso.',
                'data' => $orders,
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao listar pedidos', ['error' => $e->getMessage(), 'user_id' => auth()->id()]);
            return response()->json(['message' => 'Erro ao listar os pedidos.'], 500);
        }
    }

    /**
     * Adicionar novo pedido.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'requestor_name' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
            'status' => ['sometimes', Rule::in(['requested', 'approved', 'canceled'])],
        ]);

        try {
            $order = Order::create([
                ...$validated,
                'status' => $validated['status'] ?? 'requested',
                'user_id' => auth()->id(),
            ]);

            Log::info('Pedido criado', [
                'order_id' => $order->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'message' => 'Pedido criado com sucesso.',
                'data' => $order,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Erro ao criar pedido', [
                'error' => $e->getMessage()
            ]);

            return response()->json(['message' => 'Erro ao criar pedido.'], 500);
        }
    }

    /**
     * Buscar informações de um determinado pedido pelo seu ID.
     */
    public function show(string $id)
    {
        try {
            $order = Order::findOrFail($id);
            $user = auth()->user();

            if ($user->type_user !== 'admin' && $order->user_id !== $user->id) {
                Log::warning('Acesso negado ao visualizar pedido', [
                    'order_id' => $id,
                    'user_id' => $user->id,
                ]);

                return response()->json([
                    'message' => 'Você não tem permissão para visualizar este pedido.',
                ], 403);
            }

            Log::info('Pedido exibido com sucesso', [
                'order_id' => $id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'message' => 'Pedido encontrado.',
                'data' => $order,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Pedido não encontrado', [
                'order_id' => $id,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Pedido não encontrado.',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Erro ao exibir pedido', [
                'order_id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erro ao exibir pedido.',
            ], 500);
        }
    }

    /**
     * Atualizar pedido.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'requestor_name' => 'sometimes|required|string|max:255',
            'destination' => 'sometimes|required|string|max:255',
            'departure_date' => 'sometimes|required|date',
            'return_date' => 'sometimes|required|date|after_or_equal:departure_date',
            'status' => ['sometimes', Rule::in(['requested', 'approved', 'canceled'])],
            // Remove user_id da validação para evitar mudanças não autorizadas
        ]);

        try {
            $order = Order::findOrFail($id);
            $user = auth()->user();

            if ($user->type_user !== 'admin' && $order->user_id !== $user->id) {
                Log::warning('Acesso negado para atualização de pedido', [
                    'order_id' => $id,
                    'user_id' => $user->id,
                ]);

                return response()->json([
                    'message' => 'Você não tem permissão para atualizar este pedido.',
                ], 403);
            }

            $order->update($validated);

            Log::info('Pedido atualizado com sucesso', [
                'order_id' => $id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'message' => 'Pedido atualizado com sucesso.',
                'data' => $order,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Pedido não encontrado para atualização', [
                'order_id' => $id,
                'user_id' => auth()->id(),
            ]);

            return response()->json(['message' => 'Pedido não encontrado.'], 404);
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar pedido', [
                'order_id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Erro ao atualizar pedido.'], 500);
        }
    }

    /**
     * Atualiza somente o status do pedido.
     */
    public function updateStatus(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'canceled'])],
        ]);

        try {
            $order = Order::findOrFail($id);

            $user = auth()->user();
            $isAdmin = $user->type_user === 'admin';
            $isOwner = $user->id === $order->user_id;

            if (!$isAdmin && !$isOwner) {
                return response()->json(['message' => 'Você não tem permissão para editar este pedido.'], 403);
            }

            if (
                $validated['status'] === 'canceled'
                && $order->status === 'approved'
            ) {
                return response()->json(['message' => 'Não é possível cancelar um pedido já aprovado.'], 403);
            }

            $order->status = $validated['status'];
            $order->save();

            \App\Models\Notification::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'notification_message' => "Seu pedido #{$order->id} foi {$order->status}.",
                'viewed' => false,
            ]);

            Log::info('Status do pedido atualizado', [
                'order_id' => $id,
                'status' => $validated['status'],
                'user_id' => $user->id,
            ]);

            return response()->json([
                'message' => 'Status atualizado com sucesso.',
                'data' => $order,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Pedido não encontrado para atualizar status', [
                'order_id' => $id,
                'user_id' => auth()->id(),
            ]);
            return response()->json(['message' => 'Pedido não encontrado.'], 404);
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar status do pedido', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Erro ao atualizar status.'], 500);
        }
    }

    /**
     * Remover pedido pelo seu ID.
     */
    public function destroy(string $id)
    {
        try {
            $order = Order::findOrFail($id);
            $user = auth()->user();

            if ($user->type_user !== 'admin' && $order->user_id !== $user->id) {
                return response()->json(['message' => 'Você não tem permissão para deletar este pedido.'], 403);
            }

            $order->delete();

            Log::info('Pedido deletado', ['order_id' => $id, 'user_id' => $user->id]);

            return response()->json(['message' => 'Pedido deletado com sucesso.']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Pedido não encontrado para deleção', ['order_id' => $id, 'user_id' => auth()->id()]);
            return response()->json(['message' => 'Pedido não encontrado.'], 404);
        } catch (\Throwable $e) {
            Log::error('Erro ao deletar pedido', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Erro ao deletar pedido.'], 500);
        }
    }
}
