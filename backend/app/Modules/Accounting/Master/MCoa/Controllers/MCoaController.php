<?php

namespace App\Modules\Accounting\Master\MCoa\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Master\MCoa\DTOs\MCoaDTO;
use App\Modules\Accounting\Master\MCoa\Requests\StoreMCoaRequest;
use App\Modules\Accounting\Master\MCoa\Requests\UpdateMCoaRequest;
use App\Modules\Accounting\Master\MCoa\Services\MCoaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MCoaController extends Controller
{
    public function __construct(
        protected readonly MCoaService $service,
    ) {}

    /**
     * Display a paginated listing of chart of accounts.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 15), 100);

        return response()->json([
            'status'  => 'success',
            'data'    => $this->service->list((int) $perPage),
        ]);
    }

    /**
     * Store a newly created chart of account.
     */
    public function store(StoreMCoaRequest $request): JsonResponse
    {
        $dto = MCoaDTO::fromRequest($request);
        $mcoa = $this->service->create($dto);

        return response()->json([
            'status'  => 'success',
            'message' => 'Chart of Account created successfully.',
            'data'    => $mcoa,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified chart of account.
     */
    public function show(int $mcoa): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->service->find($mcoa),
        ]);
    }

    /**
     * Update the specified chart of account.
     */
    public function update(UpdateMCoaRequest $request, int $mcoa): JsonResponse
    {
        $dto = MCoaDTO::fromRequest($request);
        $updated = $this->service->update($mcoa, $dto);

        return response()->json([
            'status'  => 'success',
            'message' => 'Chart of Account updated successfully.',
            'data'    => $updated,
        ]);
    }

    /**
     * Remove the specified chart of account (soft delete).
     */
    public function destroy(int $mcoa): JsonResponse
    {
        $this->service->delete($mcoa);

        return response()->json([
            'status'  => 'success',
            'message' => 'Chart of Account deleted successfully.',
        ]);
    }

    /**
     * Get the chart of accounts tree structure.
     */
    public function tree(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->service->getTree(),
        ]);
    }
}
