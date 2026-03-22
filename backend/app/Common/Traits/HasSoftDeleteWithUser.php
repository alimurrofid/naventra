<?php

namespace App\Common\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * Trait HasSoftDeleteWithUser
 *
 * Extends Laravel's SoftDeletes to also track which user
 * performed the soft delete via a deleted_by column.
 */
trait HasSoftDeleteWithUser
{
    use SoftDeletes;

    public static function bootHasSoftDeleteWithUser(): void
    {
        static::deleting(function ($model) {
            if (!$model->isForceDeleting() && Auth::check()) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly();
            }
        });

        static::restoring(function ($model) {
            $model->deleted_by = null;
        });
    }

    /**
     * Initialize the trait for an instance.
     */
    public function initializeHasSoftDeleteWithUser(): void
    {
        $this->mergeFillable(['deleted_by']);
    }
}
