<?php

namespace App\Modules\Accounting\Transaction\TKbk\DTOs;

use App\Common\DTOs\BaseDTO;
use Illuminate\Http\Request;

class TKbkDTO extends BaseDTO
{
    public function __construct(
        public readonly ?int    $id,
        public readonly string  $transaction_date,
        public readonly string  $transaction_type,
        public readonly ?string $description,
        public readonly float   $total_amount,
        public readonly array   $details,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new static(
            id: $request->route('tkbk'),
            transaction_date: $request->input('transaction_date'),
            transaction_type: $request->input('transaction_type'),
            description: $request->input('description'),
            total_amount: (float) $request->input('total_amount', 0),
            details: array_map(
                fn(array $detail) => TKbkDetailDTO::fromArray($detail),
                $request->input('details', [])
            ),
        );
    }

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'] ?? null,
            transaction_date: $data['transaction_date'],
            transaction_type: $data['transaction_type'],
            description: $data['description'] ?? null,
            total_amount: (float) ($data['total_amount'] ?? 0),
            details: array_map(
                fn(array $detail) => TKbkDetailDTO::fromArray($detail),
                $data['details'] ?? []
            ),
        );
    }
}
