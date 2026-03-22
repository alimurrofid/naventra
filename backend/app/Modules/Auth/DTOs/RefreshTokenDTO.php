<?php

namespace App\Modules\Auth\DTOs;

use App\Common\DTOs\BaseDTO;

class RefreshTokenDTO extends BaseDTO
{
    public function __construct(
        public string $refresh_token,
        public ?string $device_name = null,
        public ?string $ip_address = null,
        public ?string $user_agent = null
    ) {
    }

    public static function fromRequest(\Illuminate\Http\Request $request): static
    {
        return static::fromArray($request->all());
    }

    public static function fromArray(array $data): static
    {
        return new static(
            refresh_token: $data['refresh_token'] ?? '',
            device_name: $data['device_name'] ?? null,
            ip_address: $data['ip_address'] ?? null,
            user_agent: $data['user_agent'] ?? null
        );
    }
}
