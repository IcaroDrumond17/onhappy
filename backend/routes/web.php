<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Rota web padrão. Use /api para API.',
    ]);
});
