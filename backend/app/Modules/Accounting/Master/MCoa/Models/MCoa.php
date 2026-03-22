<?php

namespace App\Modules\Accounting\Master\MCoa\Models;

use App\Common\Traits\HasAuditFields;
use App\Common\Traits\HasSoftDeleteWithUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MCoa extends Model
{
    use HasFactory, HasAuditFields, HasSoftDeleteWithUser;

    protected $table = 'm_coa';

    protected $fillable = [
        'code',
        'name',
        'parent_id',
        'level',
        'type',
        'is_active',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'level' => 'integer',
            'parent_id' => 'integer',
        ];
    }

    /**
     * Parent chart of account.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Child chart of accounts.
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
