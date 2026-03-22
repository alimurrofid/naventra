<?php

namespace App\Modules\Auth\DTOs;

use App\Common\DTOs\BaseDTO;

class LoginDTO extends BaseDTO
{
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
        // Safe arrays from FormRequests should be passed manually or handled via all()
        return static::fromArray($request->all());
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
