<?php

namespace App\Common\DTOs;

use Illuminate\Http\Request;

abstract class BaseDTO
{
    /**
     * Create a DTO instance from a request.
     */
    abstract public static function fromRequest(Request $request): static;

    /**
     * Create a DTO instance from an array.
     */
    abstract public static function fromArray(array $data): static;

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
