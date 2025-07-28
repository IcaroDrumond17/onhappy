<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthController extends Controller
{
    /**
     * Realiza o login e retorna o token JWT.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = auth()->attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'E-mail ou senha inválidas!',
                ], 401);
            }

            return $this->respondWithToken($token);
        } catch (Exception $e) {
            Log::error('Erro no login: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno ao tentar autenticar. Tente novamente mais tarde.',
            ], 500);
        }
    }

    /**
     * Retorna os dados do usuário autenticado.
     */
    public function me()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado.',
                ], 401);
            }

            return response()->json([
                'success' => true,
                'user' => $user,
            ]);
        } catch (Exception $e) {
            Log::error('Erro ao obter usuário autenticado: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno ao recuperar dados do usuário.',
            ], 500);
        }
    }

    /**
     * Faz logout (invalida o token).
     */
    public function logout()
    {
        try {
            Auth::logout();

            return response()->json([
                'success' => true,
                'message' => 'Logout realizado com sucesso.',
            ]);
        } catch (Exception $e) {
            Log::error('Erro no logout: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao realizar logout. Tente novamente.',
            ], 500);
        }
    }

    /**
     * Atualiza o token JWT.
     */
    public function refresh()
    {
        try {
            $token = Auth::refresh();

            return $this->respondWithToken($token);
        } catch (Exception $e) {
            Log::error('Erro ao atualizar token: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Não foi possível renovar o token. Faça login novamente.',
            ], 401);
        }
    }

    /**
     * Estrutura de resposta com o token JWT.
     */
    protected function respondWithToken(string $token)
    {
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ]);
    }
}
