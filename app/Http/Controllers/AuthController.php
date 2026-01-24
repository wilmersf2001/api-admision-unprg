<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\HandlesValidation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse, HandlesValidation;

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $this->throwUserNotFound();
        }

        if (!Hash::check($request->password, $user->password)) {
            $this->throwInvalidPassword();
        }

        // Revocar tokens existentes del dispositivo (opcional)
        $deviceName = $request->device_name ?? 'API Token';
        $user->tokens()->where('name', $deviceName)->delete();

        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->successResponse(
            new LoginResource($user, $token),
            'Login exitoso'
        );
    }

    /**
     * Obtener información del usuario autenticado
     */
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            new UserResource($request->user()),
            'Usuario autenticado'
        );
    }

    /**
     * Cerrar sesión actual (revocar token actual)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(
            null,
            'Sesión cerrada exitosamente'
        );
    }

    /**
     * Cerrar todas las sesiones (revocar todos los tokens)
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return $this->successResponse(
            null,
            'Todas las sesiones han sido cerradas'
        );
    }

    /**
     * Listar todos los tokens activos del usuario
     */
    public function tokens(Request $request): JsonResponse
    {
        $tokens = $request->user()->tokens()->get()->map(function ($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'abilities' => $token->abilities,
                'last_used_at' => $token->last_used_at,
                'created_at' => $token->created_at,
                'expires_at' => $token->expires_at,
            ];
        });

        return $this->successResponse(
            $tokens,
            'Tokens del usuario'
        );
    }
}
