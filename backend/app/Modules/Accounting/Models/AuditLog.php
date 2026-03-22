<?php

namespace App\Modules\Accounting\Models;

use App\Common\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasAuditFields;

    protected $table = 'audit_logs';

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'action',
        'module',
        'description',
        'old_values',
        'new_values',
        'user_id',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }
}
