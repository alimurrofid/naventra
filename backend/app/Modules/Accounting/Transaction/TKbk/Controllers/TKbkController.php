<?php

namespace App\Modules\Accounting\Transaction\TKbk\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Transaction\TKbk\DTOs\TKbkDTO;
use App\Modules\Accounting\Transaction\TKbk\Requests\StoreTKbkRequest;
use App\Modules\Accounting\Transaction\TKbk\Services\TKbkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TKbkController extends Controller
{
    public function __construct(
        protected readonly TKbkService $service,
    ) {}

    /**
     * Display a paginated listing of transactions.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 15), 100);

        return response()->json([
            'status' => 'success',
            'data'   => $this->service->list((int) $perPage),
        ]);
    }

    /**
     * Store a newly created transaction.
     */
    public function store(StoreTKbkRequest $request): JsonResponse
    {
        $dto = TKbkDTO::fromRequest($request);
        $transaction = $this->service->create($dto);

        return response()->json([
            'status'  => 'success',
            'message' => 'Transaction created successfully.',
            'data'    => $transaction,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified transaction.
     */
    public function show(int $tkbk): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->service->find($tkbk),
        ]);
    }

    /**
     * Remove the specified transaction (soft delete).
     */
    public function destroy(int $tkbk): JsonResponse
    {
        $this->service->delete($tkbk);

        return response()->json([
            'status'  => 'success',
            'message' => 'Transaction deleted successfully.',
        ]);
    }
}
