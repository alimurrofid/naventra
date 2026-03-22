<?php

namespace App\Modules\Accounting\Engine\DTOs;

class JournalEntryDTO
{
    public function __construct(
        public readonly int   $coa_id,
        public readonly float $debit,
        public readonly float $credit,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            coa_id: (int) $data['coa_id'],
            debit: (float) ($data['debit'] ?? 0),
            credit: (float) ($data['credit'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return [
            'coa_id' => $this->coa_id,
            'debit'  => $this->debit,
            'credit' => $this->credit,
        ];
    }
}
