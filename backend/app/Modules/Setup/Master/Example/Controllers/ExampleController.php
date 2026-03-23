<?php

namespace App\Modules\Setup\Master\Example\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Setup\Master\Example\DTOs\ExampleDTO;
use App\Modules\Setup\Master\Example\Requests\StoreExampleRequest;
use App\Modules\Setup\Master\Example\Services\ExampleService;
use Illuminate\Http\JsonResponse;

class ExampleController extends Controller
{
    public function __construct(
        protected readonly ExampleService $service
    ) {}

    public function index(): JsonResponse
    {
        $examples = $this->service->getAll();
        return response()->json([
            'message' => 'Available examples fetched.',
            'data' => $examples
        ]);
    }

    public function store(StoreExampleRequest $request): JsonResponse
    {
        $dto = ExampleDTO::fromRequest($request);
        $example = $this->service->store($dto);

        return response()->json([
            'message' => 'Example transaction created successfully.',
            'data' => $example
        ], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);
        
        return response()->json([
            'message' => 'Example transaction deleted successfully.'
        ]);
    }
}
