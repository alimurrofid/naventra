<?php

namespace App\Modules\Accounting\Engine\DTOs;

class JournalDTO
{
    /**
     * @param  string              $source   Source table/module (e.g., 't_kbk')
     * @param  int                 $ref_id   Reference ID to the source record
     * @param  string|null         $description
     * @param  JournalEntryDTO[]   $entries  Array of journal entry lines
     */
    public function __construct(
        public readonly string  $source,
        public readonly int     $ref_id,
        public readonly ?string $description,
        public readonly array   $entries,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            source: $data['source'],
            ref_id: (int) $data['ref_id'],
            description: $data['description'] ?? null,
            entries: array_map(
                fn(array $entry) => JournalEntryDTO::fromArray($entry),
                $data['entries'] ?? []
            ),
        );
    }

    /**
     * Calculate total debit from all entries.
     */
    public function totalDebit(): float
    {
        return array_sum(array_map(fn(JournalEntryDTO $e) => $e->debit, $this->entries));
    }

    /**
     * Calculate total credit from all entries.
     */
    public function totalCredit(): float
    {
        return array_sum(array_map(fn(JournalEntryDTO $e) => $e->credit, $this->entries));
    }

    /**
     * Check if journal is balanced (debit == credit).
     */
    public function isBalanced(): bool
    {
        return bccomp(
            (string) $this->totalDebit(),
            (string) $this->totalCredit(),
            2
        ) === 0;
    }
}
