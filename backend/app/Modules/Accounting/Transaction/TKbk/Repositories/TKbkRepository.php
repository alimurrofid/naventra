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
        $result = \Illuminate\Support\Facades\DB::selectOne(
            "SELECT COALESCE(MAX(CAST(SUBSTRING(transaction_number FROM ?) AS INTEGER)), 0) + 1 AS next_num
             FROM t_kbk
             WHERE transaction_number LIKE ?
             FOR UPDATE",
            [strlen($prefix) + 1, $prefix . '%']
        );

        return $prefix . str_pad($result->next_num, 6, '0', STR_PAD_LEFT);
    }
}
