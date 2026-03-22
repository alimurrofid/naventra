<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $accounts = [
            // Assets
            ['id' => 1, 'code' => '1001', 'name' => 'Cash in Hand', 'type' => 'asset', 'level' => 1],
            ['id' => 2, 'code' => '1002', 'name' => 'Bank BCA', 'type' => 'asset', 'level' => 1],
            ['id' => 3, 'code' => '1003', 'name' => 'Inventory', 'type' => 'asset', 'level' => 1],
            ['id' => 4, 'code' => '1004', 'name' => 'Accounts Receivable', 'type' => 'asset', 'level' => 1],
            
            // Liabilities
            ['id' => 5, 'code' => '2001', 'name' => 'Accounts Payable', 'type' => 'liability', 'level' => 1],
            ['id' => 6, 'code' => '2002', 'name' => 'Tax Payable', 'type' => 'liability', 'level' => 1],
            
            // Equity
            ['id' => 7, 'code' => '3001', 'name' => 'Owner Equity', 'type' => 'equity', 'level' => 1],
            ['id' => 8, 'code' => '3002', 'name' => 'Retained Earnings', 'type' => 'equity', 'level' => 1],
            
            // Revenue
            ['id' => 9, 'code' => '4001', 'name' => 'Sales Revenue', 'type' => 'revenue', 'level' => 1],
            ['id' => 10, 'code' => '4002', 'name' => 'Service Revenue', 'type' => 'revenue', 'level' => 1],
            
            // Expense
            ['id' => 11, 'code' => '5001', 'name' => 'Rent Expense', 'type' => 'expense', 'level' => 1],
            ['id' => 12, 'code' => '5002', 'name' => 'Salary Expense', 'type' => 'expense', 'level' => 1],
            ['id' => 13, 'code' => '5003', 'name' => 'Utilities Expense', 'type' => 'expense', 'level' => 1],
        ];

        foreach ($accounts as $account) {
            DB::table('m_coa')->insert([
                'id' => $account['id'],
                'code' => $account['code'],
                'name' => $account['name'],
                'type' => $account['type'],
                'level' => $account['level'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
