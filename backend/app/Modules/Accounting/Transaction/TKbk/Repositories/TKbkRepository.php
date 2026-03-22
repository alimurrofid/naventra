<?php

namespace App\Modules\Accounting\Transaction\TKbk\Repositories;

use App\Common\Repositories\BaseRepository;
use App\Modules\Accounting\Transaction\TKbk\Models\TKbk;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TKbkRepository extends BaseRepository
{
    public function __construct(TKbk $model)
    {
        parent::__construct($model);
    }

    /**
     * Get paginated transactions with details.
     */
    public function paginateWithDetails(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->with('details.coa')
            ->orderByDesc('transaction_date')
            ->paginate($perPage);
    }

    /**
     * Find transaction with details loaded.
     */
    public function findWithDetails(int $id): TKbk
    {
        return $this->query()
            ->with('details.coa')
            ->findOrFail($id);
    }

    /**
     * Generate the next transaction number.
     */
    public function generateTransactionNumber(string $prefix = 'KBK'): string
    {
        $lastTransaction = $this->query()
            ->where('transaction_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->transaction_number, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
