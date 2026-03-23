<?php

namespace App\Modules\Accounting\Transaction\TKbk\Services;

use App\Common\Services\BaseService;
use App\Modules\Accounting\Engine\DTOs\JournalDTO;
use App\Modules\Accounting\Engine\DTOs\JournalEntryDTO;
use App\Modules\Accounting\Engine\JournalEngine;
use App\Modules\Accounting\Events\TransactionCreated;
use App\Modules\Accounting\Transaction\TKbk\DTOs\TKbkDTO;
use App\Modules\Accounting\Transaction\TKbk\DTOs\TKbkDetailDTO;
use App\Modules\Accounting\Transaction\TKbk\Models\TKbk;
use App\Modules\Accounting\Transaction\TKbk\Repositories\TKbkRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TKbkService extends BaseService
{
    public function __construct(
        protected readonly TKbkRepository $repository,
        protected readonly JournalEngine  $journalEngine,
    ) {}

    /**
     * Get paginated transactions with details.
     */
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateWithDetails($perPage);
    }

    /**
     * Find a transaction by ID with details loaded.
     */
    public function find(int $id): TKbk
    {
        return $this->repository->findWithDetails($id);
    }

    /**
     * Create a new bank/cash transaction.
     *
     * Flow:
     * 1. Create transaction header + details
     * 2. Call JournalEngine DIRECTLY (core accounting)
     * 3. Fire TransactionCreated event (extensibility)
     */
    public function create(TKbkDTO $dto): TKbk
    {
        return $this->transaction(function () use ($dto) {
            // 1. Generate transaction number and create header
            $transactionNumber = $this->repository->generateTransactionNumber();

            $transaction = $this->repository->create([
                'transaction_number' => $transactionNumber,
                'transaction_date'   => $dto->transaction_date,
                'transaction_type'   => $dto->transaction_type,
                'description'        => $dto->description,
                'total_amount'       => $dto->total_amount,
                'status'             => 'posted',
            ]);

            // 2. Create detail lines using Bulk Insert (P-02)
            $detailData = [];
            $now = now();
            $userId = \Illuminate\Support\Facades\Auth::id();

            foreach ($dto->details as $detail) {
                /** @var TKbkDetailDTO $detail */
                $detailData[] = array_merge($detail->toArray(), [
                    'kbk_id'     => $transaction->id,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
            \App\Modules\Accounting\Transaction\TKbk\Models\TKbkDetail::insert($detailData);

            // 3. DIRECT CALL to JournalEngine (core accounting logic)
            $journalDto = new JournalDTO(
                source: 't_kbk',
                ref_id: $transaction->id,
                description: $dto->description,
                entries: array_map(
                    fn(TKbkDetailDTO $d) => new JournalEntryDTO(
                        coa_id: $d->coa_id,
                        debit: $d->debit,
                        credit: $d->credit,
                    ),
                    $dto->details
                ),
            );

            $this->journalEngine->createJournal($journalDto);

            // 4. Fire event for extensibility (audit log, notifications, etc.)
            event(new TransactionCreated($transaction));

            return $transaction->load('details');
        });
    }

    /**
     * Delete a transaction (soft delete).
     */
    public function delete(int $id): void
    {
        $this->transaction(function () use ($id) {
            $transaction = $this->repository->findOrFail($id);
            $this->repository->delete($transaction);
        });
    }
}
