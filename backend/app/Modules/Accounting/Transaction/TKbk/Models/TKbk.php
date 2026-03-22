<?php

namespace App\Modules\Accounting\Transaction\TKbk\Models;

use App\Common\Traits\HasAuditFields;
use App\Common\Traits\HasSoftDeleteWithUser;
use App\Modules\Accounting\Transaction\TKbk\Models\TKbkDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TKbk extends Model
{
    use HasFactory, HasAuditFields, HasSoftDeleteWithUser;

    protected $table = 't_kbk';

    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'transaction_type',
        'description',
        'total_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'total_amount'     => 'decimal:2',
        ];
    }

    /**
     * Get the transaction detail lines.
     */
    public function details(): HasMany
    {
        return $this->hasMany(TKbkDetail::class, 'kbk_id');
    }
}
