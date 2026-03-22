<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Revenue (Sum of 'cash_in' and 'bank_in')
        $revenue = DB::table('t_kbk')
            ->whereIn('transaction_type', ['cash_in', 'bank_in'])
            ->whereNull('deleted_at')
            ->sum('total_amount');
            
        // Number of transactions
        $transactionsCount = DB::table('t_kbk')
            ->whereNull('deleted_at')
            ->count();
            
        // Receivables Mocked via Bank Out
        $receivables = DB::table('t_kbk')
            ->where('transaction_type', 'bank_out')
            ->whereNull('deleted_at')
            ->sum('total_amount');

        // Recent 5 transactions
        $recentTransactions = DB::table('t_kbk')
            ->select('id', 'transaction_number', 'transaction_date', 'description', 'total_amount', 'status')
            ->whereNull('deleted_at')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // Chart Data
        $chartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => [12000000, 19000000, 15000000, 22000000, 18000000, $revenue > 0 ? $revenue : 25000000],
                    'borderColor' => '#3b82f6',
                    'tension' => 0.4
                ],
                [
                    'label' => 'Expenses',
                    'data' => [8000000, 12000000, 10000000, 14000000, 11000000, $receivables > 0 ? $receivables : 13000000],
                    'borderColor' => '#ef4444',
                    'tension' => 0.4
                ]
            ]
        ];

        return response()->json([
            'stats' => [
                'revenue' => $revenue,
                'transactions' => $transactionsCount,
                'inventory' => 856, // Static metric for inventory until module is built
                'receivables' => $receivables
            ],
            'charts' => [
                'revenue' => $chartData
            ],
            'recent_transactions' => $recentTransactions
        ]);
    }
}
