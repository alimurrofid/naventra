<?php

namespace App\Modules\Accounting\Engine;

use App\Modules\Accounting\Engine\DTOs\JournalDTO;
use App\Modules\Accounting\Engine\Models\Journal;
use App\Modules\Accounting\Engine\Models\JournalDetail;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * JournalEngine — Core ERP Accounting Engine
 *
 * This engine is responsible for creating journal entries across
 * the entire ERP system. It is called DIRECTLY (not via events)
 * to ensure core accounting integrity is never bypassed.
 *
 * All modules that need to create journal entries must use
 * this engine, passing a structured JournalDTO.
 */
class JournalEngine
{
    /**
     * Create a journal entry from a DTO.
     *
     * Validates that total debit == total credit before persisting.
     * Creates both the journal header and all detail lines atomically.
     *
     * @throws InvalidArgumentException If journal is unbalanced or has no entries
     */
    public function createJournal(JournalDTO $dto): Journal
    {
        $this->validate($dto);

        return DB::transaction(function () use ($dto) {
            // Create journal header
            $journal = Journal::create([
                'journal_number' => $this->generateJournalNumber(),
                'source'         => $dto->source,
                'ref_id'         => $dto->ref_id,
                'journal_date'   => now(),
                'description'    => $dto->description,
                'total_debit'    => $dto->totalDebit(),
                'total_credit'   => $dto->totalCredit(),
            ]);

            // Create journal detail lines
            foreach ($dto->entries as $entry) {
                JournalDetail::create([
                    'journal_id' => $journal->id,
                    'coa_id'     => $entry->coa_id,
                    'debit'      => $entry->debit,
                    'credit'     => $entry->credit,
                ]);
            }

            return $journal->load('details');
        });
    }

    /**
     * Reverse a journal entry (create contra entries).
     *
     * @throws InvalidArgumentException
     */
    public function reverseJournal(Journal $journal, ?string $description = null): Journal
    {
        $entries = $journal->details->map(fn(JournalDetail $detail) => [
            'coa_id' => $detail->coa_id,
            'debit'  => $detail->credit,  // Swap debit and credit
            'credit' => $detail->debit,
        ])->toArray();

        $dto = JournalDTO::fromArray([
            'source'      => $journal->source,
            'ref_id'      => $journal->ref_id,
            'description' => $description ?? "Reversal of Journal #{$journal->journal_number}",
            'entries'     => $entries,
        ]);

        return $this->createJournal($dto);
    }

    /**
     * Validate the journal DTO before processing.
     *
     * @throws InvalidArgumentException
     */
    protected function validate(JournalDTO $dto): void
    {
        if (empty($dto->entries)) {
            throw new InvalidArgumentException(
                'Journal must have at least one entry.'
            );
        }

        if (!$dto->isBalanced()) {
            throw new InvalidArgumentException(
                sprintf(
                    'Journal is not balanced. Total debit (%s) must equal total credit (%s).',
                    number_format($dto->totalDebit(), 2),
                    number_format($dto->totalCredit(), 2),
                )
            );
        }

        // Validate each entry has either debit or credit (not both zero)
        foreach ($dto->entries as $index => $entry) {
            if ($entry->debit == 0 && $entry->credit == 0) {
                throw new InvalidArgumentException(
                    "Entry at index {$index} has both debit and credit as zero."
                );
            }
        }
    }

    /**
     * Generate the next journal number.
     */
    protected function generateJournalNumber(): string
    {
        $prefix = 'JRN' . now()->format('Ym');

        $lastJournal = Journal::where('journal_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        if ($lastJournal) {
            $lastNumber = (int) substr($lastJournal->journal_number, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
