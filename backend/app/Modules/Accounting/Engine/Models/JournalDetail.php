<?php

namespace App\Modules\Accounting\Engine\Models;

use App\Common\Traits\HasAuditFields;
use App\Modules\Accounting\Master\MCoa\Models\MCoa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalDetail extends Model
{
    use HasAuditFields;

    protected $table = 'journal_details';

    protected $fillable = [
        'journal_id',
        'coa_id',
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
     * Get the parent journal.
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    /**
     * Get the chart of account.
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(MCoa::class, 'coa_id');
    }
}
