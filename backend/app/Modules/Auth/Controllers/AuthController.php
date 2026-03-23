<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\DTOs\LoginDTO;
use App\Modules\Auth\DTOs\RefreshTokenDTO;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RefreshTokenRequest;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $dto = new LoginDTO(
                email: $request->validated('email'),
                password: $request->validated('password'),
                device_name: $request->header('User-Agent'), // Simplification
                ip_address: $request->ip(),
                user_agent: $request->userAgent()
            );

            $data = $this->authService->login($dto);
            $refreshToken = $data['refresh_token'];
            unset($data['refresh_token']);

            return response()->json($data)->cookie(
                'refresh_token', $refreshToken, 60 * 24 * 30, '/api/refresh', null, true, true, false, 'Strict'
            );
        } catch (Exception $e) {
            $status = $e->getCode() === 401 ? 401 : 500;
            $message = $status === 401 ? 'Invalid credentials' : 'An unexpected error occurred';
            report($e);
            return response()->json(['error' => $message], $status);
        }
    }

    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        try {
            $refreshTokenInput = $request->cookie('refresh_token') ?? $request->input('refresh_token');
            if (!$refreshTokenInput) {
                return response()->json(['error' => 'Refresh token required'], 401);
            }

            $dto = new RefreshTokenDTO(
                refresh_token: $refreshTokenInput,
                device_name: $request->header('User-Agent'),
                ip_address: $request->ip(),
                user_agent: $request->userAgent()
            );

            $data = $this->authService->refresh($dto);
            $refreshToken = $data['refresh_token'];
            unset($data['refresh_token']);

            return response()->json($data)->cookie(
                'refresh_token', $refreshToken, 60 * 24 * 30, '/api/refresh', null, true, true, false, 'Strict'
            );
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            // Optional refresh token in cookie or body to revoke
            $refreshToken = $request->cookie('refresh_token') ?? $request->input('refresh_token');
            $this->authService->logout($refreshToken);

            return response()->json(['message' => 'Successfully logged out'])
                ->withoutCookie('refresh_token', '/api/refresh');
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to log out'], 500);
        }
    }

    public function me(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth('api')->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($this->authService->getMe($user));
    }
}
