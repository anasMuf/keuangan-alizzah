<?php

namespace Database\Seeders;

use App\Models\PosPengeluaran;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PosPengeluaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataPosPengeluaran = [
            [// 1
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Biaya Awal',
                'pos_pemasukan_id' => 1,
                'nominal_valid' => 0,
            ],
            [// perlu detail 2
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Biaya Registrasi',
                'pos_pemasukan_id' => 2,
                'nominal_valid' => 0,
            ],
            [// 3
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'SPP',
                'pos_pemasukan_id' => 3,
                'nominal_valid' => 0,
            ],
            [// 4
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Infaq Harian',
                'pos_pemasukan_id' => 4,
                'nominal_valid' => 0,
            ],
            [// 5
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Exkul Aslin',
                'pos_pemasukan_id' => 5,
                'nominal_valid' => 0,
            ],
            [// 6
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Exkul Calisan KB',
                'pos_pemasukan_id' => 6,
                'nominal_valid' => 0,
            ],
            [// 7
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Exkul Calisan TK',
                'pos_pemasukan_id' => 7,
                'nominal_valid' => 0,
            ],
            [// 8
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Pasta Robotik',
                'pos_pemasukan_id' => 8,
                'nominal_valid' => 0,
            ],
            [// 9
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Pasta Laptop Kids',
                'pos_pemasukan_id' => 9,
                'nominal_valid' => 0,
            ],
            [// 10
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Pasta Taekwondo',
                'pos_pemasukan_id' => 10,
                'nominal_valid' => 0,
            ],
            [// 11
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Pasta Menari',
                'pos_pemasukan_id' => 11,
                'nominal_valid' => 0,
            ],
            [// 12
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Pasta Tilawah',
                'pos_pemasukan_id' => 12,
                'nominal_valid' => 0,
            ],
            [// 13
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Pasta Menyanyi',
                'pos_pemasukan_id' => 13,
                'nominal_valid' => 0,
            ],
            [// 14
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Pasta Melukis',
                'pos_pemasukan_id' => 14,
                'nominal_valid' => 0,
            ],
            [// 15
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Tabungan Wajib Berlian',
                'pos_pemasukan_id' => 15,
                'nominal_valid' => 0,
            ],
            [// 16
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Tabungan Umum',
                'pos_pemasukan_id' => 16,
                'nominal_valid' => 0,
            ],
        ];
        foreach ($dataPosPengeluaran as $item) {
            PosPengeluaran::create($item);
        }
    }
}
