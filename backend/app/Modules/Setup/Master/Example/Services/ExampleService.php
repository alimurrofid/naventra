<?php

namespace App\Modules\Setup\Master\Example\Services;

use App\Modules\Setup\Master\Example\DTOs\ExampleDTO;
use App\Modules\Setup\Master\Example\Models\Example;
use App\Modules\Setup\Master\Example\Models\ExampleDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExampleService
{
    public function store(ExampleDTO $dto): Example
    {
        return DB::transaction(function () use ($dto) {
            $userId = Auth::id();
            $now = now();

            // 1. Simpan Header
            $example = Example::create([
                'code' => $dto->code,
                'description' => $dto->description,
                'transaction_date' => $dto->transaction_date,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            // 2. Format list of array object untuk details
            $detailsData = [];
            foreach ($dto->details as $detail) {
                $detailsData[] = [
                    'example_id' => $example->id,
                    'item_name' => $detail->item_name,
                    'qty' => $detail->qty,
                    'price' => $detail->price,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // 3. Simpan menggunakan Bulk Insert untuk performa
            ExampleDetail::insert($detailsData);

            return $example->load('details');
        });
    }

    public function getAll()
    {
        return Example::orderBy('id', 'desc')->get();
    }

    public function delete(int $id): bool
    {
        $example = Example::findOrFail($id);
        return $example->delete();
    }
}
