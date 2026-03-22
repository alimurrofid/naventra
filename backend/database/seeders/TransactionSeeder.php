<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $transactions = [];
        $details = [];

        // Generate 20 random transactions over the last 30 days
        $types = ['cash_in', 'bank_in', 'cash_out', 'bank_out'];
        $statuses = ['draft', 'posted', 'posted', 'posted', 'posted']; // weight towards posted

        $coas = DB::table('m_coa')->pluck('id', 'code')->toArray();

        for ($i = 1; $i <= 30; $i++) {
            $type = $types[array_rand($types)];
            $amount = rand(5, 50) * 100000; // Between 500,000 and 5,000,000
            $date = Carbon::now()->subDays(rand(0, 30));
            $isIncome = in_array($type, ['cash_in', 'bank_in']);

            $kbkId = $i;
            $transactions[] = [
                'id' => $kbkId,
                'transaction_number' => 'TRX-' . $date->format('Ym') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'transaction_date' => $date->format('Y-m-d'),
                'transaction_type' => $type,
                'description' => $isIncome ? 'Payment from Corporate Client ABC' : 'Operational Expenses and Restocking',
                'total_amount' => $amount,
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Setup double entry logic depending on type
            $mainCoaId = in_array($type, ['cash_in', 'cash_out']) ? $coas['1001'] : $coas['1002'];
            $offsetCoaId = $isIncome ? $coas['4001'] : $coas['5003']; 

            // Debit Entry
            $details[] = [
                'kbk_id' => $kbkId,
                'coa_id' => $isIncome ? $mainCoaId : $offsetCoaId,
                'description' => 'Main entry',
                'debit' => $amount,
                'credit' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Credit Entry
            $details[] = [
                'kbk_id' => $kbkId,
                'coa_id' => $isIncome ? $offsetCoaId : $mainCoaId,
                'description' => 'Offset entry',
                'debit' => 0,
                'credit' => $amount,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('t_kbk')->insert($transactions);
        DB::table('t_kbk_details')->insert($details);
    }
}
