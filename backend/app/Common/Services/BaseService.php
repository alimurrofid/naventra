<?php

namespace App\Common\Services;

use Closure;
use Illuminate\Support\Facades\DB;

abstract class BaseService
{
    /**
     * Execute a callback within a database transaction.
     *
     * @template T
     * @param  Closure(): T  $callback
     * @return T
     *
     * @throws \Throwable
     */
    protected function transaction(Closure $callback): mixed
    {
        return DB::transaction($callback);
    }
}
