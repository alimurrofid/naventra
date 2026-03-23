<?php

namespace App\Modules\Setup\Master\Example\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Example extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function details(): HasMany
    {
        return $this->hasMany(ExampleDetail::class);
    }
}
