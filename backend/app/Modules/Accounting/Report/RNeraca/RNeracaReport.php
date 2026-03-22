<?php

namespace App\Modules\Accounting\Report\RNeraca;

use App\Modules\Accounting\Engine\Models\JournalDetail;
use App\Modules\Accounting\Master\MCoa\Models\MCoa;
use App\Modules\Accounting\Report\ReportEngine;
use Illuminate\Support\Facades\DB;

/**
 * RNeraca — Balance Sheet (Neraca) Report
 *
 * Generates a structured balance sheet report from journal data.
 */
class RNeracaReport extends ReportEngine
{
    public function title(): string
    {
        return 'Balance Sheet (Neraca)';
    }

    /**
     * Generate balance sheet data.
     *
     * @param array $params ['start_date' => ..., 'end_date' => ...]
     */
    public function generate(array $params = []): array
    {
        $endDate = $params['end_date'] ?? now()->toDateString();

        // Get account balances from journal details
        $balances = JournalDetail::query()
            ->join('journals', 'journal_details.journal_id', '=', 'journals.id')
            ->join('m_coa', 'journal_details.coa_id', '=', 'm_coa.id')
            ->whereNull('journals.deleted_at')
            ->where('journals.journal_date', '<=', $endDate)
            ->select(
                'm_coa.id as coa_id',
                'm_coa.code',
                'm_coa.name',
                'm_coa.type',
                DB::raw('SUM(journal_details.debit) as total_debit'),
                DB::raw('SUM(journal_details.credit) as total_credit'),
                DB::raw('SUM(journal_details.debit) - SUM(journal_details.credit) as balance'),
            )
            ->groupBy('m_coa.id', 'm_coa.code', 'm_coa.name', 'm_coa.type')
            ->orderBy('m_coa.code')
            ->get();

        // Group by account type
        $grouped = $balances->groupBy('type');

        return [
            'as_of_date' => $endDate,
            'assets'      => $grouped->get('asset', collect())->toArray(),
            'liabilities' => $grouped->get('liability', collect())->toArray(),
            'equity'      => $grouped->get('equity', collect())->toArray(),
            'summary'     => [
                'total_assets'      => $grouped->get('asset', collect())->sum('balance'),
                'total_liabilities' => abs($grouped->get('liability', collect())->sum('balance')),
                'total_equity'      => abs($grouped->get('equity', collect())->sum('balance')),
            ],
        ];
    }
}
