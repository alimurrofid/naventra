<?php

namespace App\Modules\Accounting\Master\MCoa\Services;

use App\Common\Services\BaseService;
use App\Modules\Accounting\Master\MCoa\DTOs\MCoaDTO;
use App\Modules\Accounting\Master\MCoa\Models\MCoa;
use App\Modules\Accounting\Master\MCoa\Repositories\MCoaRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MCoaService extends BaseService
{
    public function __construct(
        protected readonly MCoaRepository $repository,
    ) {}

    /**
     * Get paginated list of chart of accounts.
     */
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    /**
     * Get all active chart of accounts.
     */
    public function getActive(): Collection
    {
        return $this->repository->getActive();
    }

    /**
     * Get chart of accounts tree structure.
     */
    public function getTree(): Collection
    {
        return $this->repository->getTree();
    }

    /**
     * Create a new chart of account.
     */
    public function create(MCoaDTO $dto): MCoa
    {
        return $this->transaction(function () use ($dto) {
            return $this->repository->create($dto->toArray());
        });
    }

    /**
     * Find a chart of account by ID.
     */
    public function find(int $id): MCoa
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Update an existing chart of account.
     */
    public function update(int $id, MCoaDTO $dto): MCoa
    {
        return $this->transaction(function () use ($id, $dto) {
            $mcoa = $this->repository->findOrFail($id);

            $data = array_filter($dto->toArray(), fn($value) => $value !== null);
            unset($data['id']);

            $this->repository->update($mcoa, $data);

            return $mcoa->fresh();
        });
    }

    /**
     * Delete a chart of account (soft delete).
     */
    public function delete(int $id): void
    {
        $this->transaction(function () use ($id) {
            $mcoa = $this->repository->findOrFail($id);
            $this->repository->delete($mcoa);
        });
    }
}
