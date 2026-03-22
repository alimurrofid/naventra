<?php

namespace App\Common\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    /**
     * The Eloquent model instance.
     */
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get a new query builder for the model.
     */
    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Get all records.
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->all($columns);
    }

    /**
     * Find a record by its primary key.
     */
    public function find(int|string $id, array $columns = ['*']): ?Model
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Find a record by its primary key or throw an exception.
     */
    public function findOrFail(int|string $id, array $columns = ['*']): Model
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * Create a new record.
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record.
     */
    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    /**
     * Delete a record (soft delete if trait is used).
     */
    public function delete(Model $model): ?bool
    {
        return $model->delete();
    }

    /**
     * Paginate results.
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * Find records by a specific column value.
     */
    public function findWhere(string $column, mixed $value, array $columns = ['*']): Collection
    {
        return $this->model->where($column, $value)->get($columns);
    }
}
