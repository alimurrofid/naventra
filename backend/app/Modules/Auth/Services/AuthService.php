<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Auth\DTOs\LoginDTO;
use App\Modules\Auth\DTOs\RefreshTokenDTO;
use App\Modules\Auth\Models\RefreshToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
use Exception;

class AuthService
{
    /**
     * Handle user login, returning access and refresh tokens.
     */
    public function login(LoginDTO $dto): array
    {
        $credentials = [
            'email' => $dto->email,
            'password' => $dto->password,
        ];

        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('api');

        if (! $token = $guard->attempt($credentials)) {
            throw new Exception('Invalid credentials', 401);
        }

        /** @var User $user */
        $user = $guard->user();

        $refreshTokenPlain = $this->generateRefreshToken();
        $this->storeRefreshToken($user->id, $refreshTokenPlain, $dto);

        return $this->formatTokenResponse($token, $refreshTokenPlain, $user);
    }

    /**
     * Refresh the access and refresh tokens.
     */
    public function refresh(RefreshTokenDTO $dto): array
    {
        $plainToken = $dto->refresh_token;

        // Find a matching refresh token in DB
        // Because the token is hashed, we have to find all active tokens for the user and verify, 
        // OR better yet: store a hash format that allows direct lookups (e.g., hash('sha256', $plainToken)).
        // Using SHA-256 for fast lookup is better than bcrypt for tokens.
        $hashedToken = hash('sha256', $plainToken);

        $refreshTokenDb = RefreshToken::where('token', $hashedToken)->first();

        if (! $refreshTokenDb) {
            throw new Exception('Invalid refresh token', 401);
        }

        if ($refreshTokenDb->isRevoked()) {
            throw new Exception('Refresh token has been revoked', 401);
        }

        if ($refreshTokenDb->isExpired()) {
            throw new Exception('Refresh token has expired', 401);
        }

        // Revoke the old token
        $this->revokeRefreshToken($refreshTokenDb);

        // Generate new tokens (Rotation)
        $user = $refreshTokenDb->user;

        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('api');
        $newAccessToken = $guard->login($user);
        $newRefreshTokenPlain = $this->generateRefreshToken();
        
        $this->storeRefreshToken($user->id, $newRefreshTokenPlain, $dto);

        return $this->formatTokenResponse($newAccessToken, $newRefreshTokenPlain, $user);
    }

    /**
     * Handle user logout (revoke refresh token and invalidate access token).
     */
    public function logout(?string $plainRefreshToken = null): void
    {
        if ($plainRefreshToken) {
            $hashedToken = hash('sha256', $plainRefreshToken);
            $refreshTokenDb = RefreshToken::where('token', $hashedToken)->first();
            
            if ($refreshTokenDb) {
                $this->revokeRefreshToken($refreshTokenDb);
            }
        }

        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('api');
        $guard->logout();
    }

    /**
     * Get the authenticated user with roles and permissions.
     */
    public function getMe(User $user): array
    {
        // Mocking RBAC formatting. If using spatie/laravel-permission:
        // $roles = $user->getRoleNames();
        // $permissions = $user->getAllPermissions()->pluck('name');
        
        // Return dummy roles/permissions array for structural compliance
        return [
            'user' => $user,
            'roles' => $user->roles ?? ['admin'], 
            'permissions' => $user->permissions ?? [
                'dashboard.view',
                'accounting.m_coa.read',
                'accounting.t_kbk.read',
                'accounting.t_kbk.create', 
                'accounting.t_kbk.post',
                'accounting.t_kbk.request_approval',
                'accounting.t_kbk.approve',
                'accounting.t_kbk.reject',
            ], 
        ];
    }

    /**
     * Store a new refresh token in the database.
     */
    private function storeRefreshToken(int $userId, string $plainToken, object $dto): RefreshToken
    {
        return RefreshToken::create([
            'user_id' => $userId,
            'token' => hash('sha256', $plainToken), // Hashed before storing
            'expires_at' => Carbon::now()->addDays(30),
            'device_name' => $dto->device_name ?? null,
            'ip_address' => $dto->ip_address ?? null,
            'user_agent' => $dto->user_agent ?? null,
        ]);
    }

    /**
     * Mark a refresh token as revoked.
     */
    private function revokeRefreshToken(RefreshToken $token): void
    {
        $token->update([
            'revoked_at' => Carbon::now(),
        ]);
    }

    /**
     * Generate a new random refresh token string.
     */
    private function generateRefreshToken(): string
    {
        return Str::random(64);
    }

    /**
     * Format the final token response payload.
     */
    private function formatTokenResponse(string $accessToken, string $refreshTokenPlain, User $user): array
    {
        /** @var \Tymon\JWTAuth\JWTGuard $guard */
        $guard = auth('api');
        
        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshTokenPlain,
            'expires_in' => $guard->factory()->getTTL() * 60,
        ];
    }
}
