<?php

namespace App\Modules\Auth\DTOs;

use App\Common\DTOs\BaseDTO;

class LoginDTO extends BaseDTO
{
    protected array $hidden = ['password'];

    public function __construct(
        public string $email,
        public string $password,
        public ?string $device_name = null,
        public ?string $ip_address = null,
        public ?string $user_agent = null
    ) {
    }

    public static function fromRequest(\Illuminate\Http\Request $request): static
    {
        // Enforce usage of validated data to prevent mass assignment (M-01)
        return static::fromArray(method_exists($request, 'validated') ? $request->validated() : $request->all());
    }

    public static function fromArray(array $data): static
    {
        return new static(
            email: $data['email'] ?? '',
            password: $data['password'] ?? '',
            device_name: $data['device_name'] ?? null,
            ip_address: $data['ip_address'] ?? null,
            user_agent: $data['user_agent'] ?? null
        );
    }
}
