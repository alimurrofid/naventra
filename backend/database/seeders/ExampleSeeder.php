<?php

namespace Database\Seeders;

use App\Modules\Setup\Master\Example\Models\Example;
use App\Modules\Setup\Master\Example\Models\ExampleDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExampleSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            ExampleDetail::query()->delete();
            Example::query()->delete();

            $words = ['Pengadaan', 'Pembelian', 'Sewa', 'Lisensi', 'Maintenance', 'Perbaikan', 'Instalasi'];
            $items = ['ATK Kantor', 'Aset TI Baru', 'Layanan Cloud', 'Kendaraan Operasional', 'Alat Berat', 'Mesin Pabrik', 'Seragam Karyawan'];

            for ($i = 1; $i <= 100; $i++) {
                $code = 'EX-' . str_pad($i, 4, '0', STR_PAD_LEFT);
                $descWord1 = $words[array_rand($words)];
                $descWord2 = $items[array_rand($items)];
                
                $example = Example::create([
                    'code' => $code,
                    'description' => "$descWord1 $descWord2",
                    'transaction_date' => date('Y-m-d', strtotime('-' . rand(0, 365) . ' days')),
                ]);

                // Generate 1 to 3 random details per example transaction
                $numDetails = rand(1, 3);
                for ($d = 1; $d <= $numDetails; $d++) {
                    ExampleDetail::create([
                        'example_id' => $example->id,
                        'item_name' => 'Barang / Jasa tipe ' . chr(64 + $d) . '-' . rand(100, 999),
                        'qty' => rand(1, 10),
                        'price' => rand(10, 1000) * 1000,
                    ]);
                }
            }
        });
    }
}
