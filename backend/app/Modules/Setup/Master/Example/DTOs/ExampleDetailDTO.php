<?php

namespace App\Modules\Setup\Master\Example\DTOs;

use App\Common\DTOs\BaseDTO;
use Illuminate\Http\Request;

class ExampleDetailDTO extends BaseDTO
{
    public function __construct(
        public string $item_name,
        public int $qty,
        public float $price,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return static::fromArray(method_exists($request, 'validated') ? $request->validated() : $request->all());
    }

    public static function fromArray(array $data): static
    {
        return new static(
            item_name: $data['item_name'],
            qty: (int) $data['qty'],
            price: (float) $data['price'],
        );
    }
}
