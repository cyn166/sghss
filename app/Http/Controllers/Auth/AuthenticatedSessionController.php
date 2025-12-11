<?php

namespace App\Http\Controllers\Auth;

// app/Http/Controllers/Auth/AuthenticatedSessionController.php

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request (API Token).
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
             throw ValidationException::withMessages([
                 'email' => [trans('auth.failed')],
             ]);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
             throw ValidationException::withMessages([
                 'email' => ['Usuário não encontrado após verificação.'],
             ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'message' => 'Login realizado com sucesso. Token gerado.'
        ], 200);
    }

   public function destroy(Request $request): JsonResponse
    {
        $currentAccessToken = $request->user()->currentAccessToken();

        if ($currentAccessToken instanceof PersonalAccessToken) {
            $currentAccessToken->delete();
            $message = 'Sessão encerrada com sucesso. Token revogado.';
        } else {
            $message = 'Sessão encerrada com sucesso.';
        }

        return response()->json([
            'message' => $message
        ], 200);
    }
}
