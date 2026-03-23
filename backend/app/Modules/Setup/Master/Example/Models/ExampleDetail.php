<?php

namespace App\Modules\Setup\Master\Example\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExampleDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function example(): BelongsTo
    {
        return $this->belongsTo(Example::class);
    }
}
