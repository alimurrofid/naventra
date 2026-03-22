<?php

namespace App\Modules\Accounting\Engine\Models;

use App\Common\Traits\HasAuditFields;
use App\Common\Traits\HasSoftDeleteWithUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    use HasAuditFields, HasSoftDeleteWithUser;

    protected $table = 'journals';

    protected $fillable = [
        'journal_number',
        'source',
        'ref_id',
        'journal_date',
        'description',
        'total_debit',
        'total_credit',
    ];

    protected function casts(): array
    {
        return [
            'journal_date' => 'date',
            'total_debit'  => 'decimal:2',
            'total_credit' => 'decimal:2',
        ];
    }

    /**
     * Get the journal detail lines.
     */
    public function details(): HasMany
    {
        return $this->hasMany(JournalDetail::class, 'journal_id');
    }
}
