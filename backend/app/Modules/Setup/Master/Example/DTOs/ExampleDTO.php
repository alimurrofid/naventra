<?php

namespace App\Modules\Setup\Master\Example\DTOs;

use App\Common\DTOs\BaseDTO;
use Illuminate\Http\Request;

class ExampleDTO extends BaseDTO
{
    /**
     * @param ExampleDetailDTO[] $details
     */
    public function __construct(
        public string $code,
        public string $description,
        public string $transaction_date,
        public array $details,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return static::fromArray(method_exists($request, 'validated') ? $request->validated() : $request->all());
    }

    public static function fromArray(array $data): static
    {
        $details = array_map(
            fn(array $detail) => ExampleDetailDTO::fromArray($detail),
            $data['details'] ?? []
        );

        return new static(
            code: $data['code'],
            description: $data['description'],
            transaction_date: $data['transaction_date'],
            details: $details,
        );
    }
}
