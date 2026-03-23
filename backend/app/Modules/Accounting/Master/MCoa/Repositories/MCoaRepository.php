<?php

namespace App\Modules\Accounting\Master\MCoa\Repositories;

use App\Common\Repositories\BaseRepository;
use App\Modules\Accounting\Master\MCoa\Models\MCoa;
use Illuminate\Database\Eloquent\Collection;

class MCoaRepository extends BaseRepository
{
    public function __construct(MCoa $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all active chart of accounts.
     */
    public function getActive(): Collection
    {
        return $this->query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get();
    }

    /**
     * Get chart of accounts tree (with children loaded).
     */
    public function getTree(): Collection
    {
        return $this->query()
            ->whereNull('parent_id')
            ->with('children.children.children.children') // Eager load up to 4 levels deep
            ->orderBy('code')
            ->get();
    }

    /**
     * Find by account code.
     */
    public function findByCode(string $code): ?MCoa
    {
        return $this->query()->where('code', $code)->first();
    }
}
