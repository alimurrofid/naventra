<?php

namespace App\Common\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * Trait HasAuditFields
 *
 * Automatically populates created_by and updated_by fields
 * on model creation and update events.
 */
trait HasAuditFields
{
    public static function bootHasAuditFields(): void
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    /**
     * Initialize the trait for an instance.
     */
    public function initializeHasAuditFields(): void
    {
        $this->mergeFillable(['created_by', 'updated_by']);
    }
}
