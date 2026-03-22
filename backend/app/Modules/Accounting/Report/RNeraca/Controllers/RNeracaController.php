<?php

namespace App\Modules\Accounting\Report\RNeraca\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Report\RNeraca\RNeracaReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RNeracaController extends Controller
{
    public function __construct(
        protected readonly RNeracaReport $report,
    ) {}

    /**
     * Generate and return the Balance Sheet report.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $params = [
            'end_date' => $request->input('end_date', now()->toDateString()),
        ];

        return $this->report->toJson($params);
    }
}
