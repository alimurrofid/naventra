<?php

namespace App\Modules\Accounting\Report;

use Illuminate\Http\JsonResponse;

/**
 * ReportEngine — Base Report Engine
 *
 * Provides structured JSON output for reports,
 * ready for rendering to HTML → PDF → Excel.
 */
abstract class ReportEngine
{
    /**
     * Generate the report data.
     *
     * @return array<string, mixed>
     */
    abstract public function generate(array $params = []): array;

    /**
     * Return the report as a JSON response.
     */
    public function toJson(array $params = []): JsonResponse
    {
        $data = $this->generate($params);

        return response()->json([
            'status' => 'success',
            'report' => [
                'title'        => $this->title(),
                'generated_at' => now()->toIso8601String(),
                'params'       => $params,
                'data'         => $data,
            ],
        ]);
    }

    /**
     * Get the report title.
     */
    abstract public function title(): string;
}
