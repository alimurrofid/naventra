<?php

namespace App\Modules\Accounting\Transaction\TKbk\DTOs;

class TKbkDetailDTO
{
    public function __construct(
        public readonly int     $coa_id,
        public readonly ?string $description,
        public readonly float   $debit,
        public readonly float   $credit,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            coa_id: (int) $data['coa_id'],
            description: $data['description'] ?? null,
            debit: (float) ($data['debit'] ?? 0),
            credit: (float) ($data['credit'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'coa_id'      => $this->coa_id,
            'description' => $this->description,
            'debit'       => $this->debit,
            'credit'      => $this->credit,
        ];
    }
}
