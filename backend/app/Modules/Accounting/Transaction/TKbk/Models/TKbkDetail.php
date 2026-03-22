<?php

namespace App\Modules\Accounting\Transaction\TKbk\Models;

use App\Common\Traits\HasAuditFields;
use App\Modules\Accounting\Master\MCoa\Models\MCoa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TKbkDetail extends Model
{
    use HasAuditFields;

    protected $table = 't_kbk_details';

    protected $fillable = [
        'kbk_id',
        'coa_id',
        'description',
        'debit',
        'credit',
    ];

    protected function casts(): array
    {
        return [
            'debit'  => 'decimal:2',
            'credit' => 'decimal:2',
        ];
    }

    /**
     * Get the parent transaction.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(TKbk::class, 'kbk_id');
    }

    /**
     * Get the chart of account.
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(MCoa::class, 'coa_id');
    }
}
