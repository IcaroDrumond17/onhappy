<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\NotificationController;

# Teste
Route::get('/', function () {
    return response()->json([
        'message' => 'API Laravel funcionando!',
        'version' => app()->version(),
        'status' => 'ok'
    ]);
});


# Login

// Logar e retornar o token
Route::post('login', [AuthController::class, 'login']);

// Retornar dados do usuário autenticado
Route::middleware('auth:api')->get('/me', [AuthController::class, 'me']);

# Pedidos

Route::middleware('auth:api')->prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'allOrders']);          
    Route::get('/user', [OrderController::class, 'ordersByUser']);   
    Route::post('/', [OrderController::class, 'store']);             
    Route::get('/{id}', [OrderController::class, 'show']);           
    Route::put('/{id}', [OrderController::class, 'update']);     
    Route::patch('/{id}/status', [OrderController::class, 'updateStatus']); 
    Route::delete('/{id}', [OrderController::class, 'destroy']);    
});


# Notificações

Route::middleware('auth:api')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{id}/viewed', [NotificationController::class, 'markAsViewed']);
});