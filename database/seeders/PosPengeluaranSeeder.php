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
                'nama_pos_pengeluaran' => 'Infaq Sarpras',
                'pos_pemasukan_id' => 1,
                'nominal_valid' => 0,
            ],
            [// 2
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Infaq APE',
                'pos_pemasukan_id' => 1,
                'nominal_valid' => 0,
            ],
            [// 3
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Buku DDTK',
                'pos_pemasukan_id' => 1,
                'nominal_valid' => 0,
            ],
            [// 4
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Biaya Psikotest IQ',
                'pos_pemasukan_id' => 1,
                'nominal_valid' => 0,
            ],
            [// 5
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Koperasi',
                'pos_pemasukan_id' => 1,
                'nominal_valid' => 0,
            ],


            [// 6
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Biaya MPLS',
                'pos_pemasukan_id' => 2,
                'nominal_valid' => 0,
            ],
            [// 7
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Buku Bayar',
                'pos_pemasukan_id' => 2,
                'nominal_valid' => 0,
            ],
            [// 8
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Infaq Awal Tabungan',
                'pos_pemasukan_id' => 2,
                'nominal_valid' => 0,
            ],
            [// 9
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Buku PK Karakter',
                'pos_pemasukan_id' => 2,
                'nominal_valid' => 0,
            ],
            [// 10
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Kaos Field Trip',
                'pos_pemasukan_id' => 2,
                'nominal_valid' => 0,
            ],
            [// 11
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Map Hasil Karya',
                'pos_pemasukan_id' => 2,
                'nominal_valid' => 0,
            ],
            [// 12
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Map Raport dan Foto Raport',
                'pos_pemasukan_id' => 2,
                'nominal_valid' => 0,
            ],
            [// 13
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Alat Belajar',
                'pos_pemasukan_id' => 2,
                'nominal_valid' => 0,
            ],
            [// 14
                'pos_id' => 2,
                'nama_pos_pengeluaran' => '1 Seri Buku Asik Membaca',
                'pos_pemasukan_id' => 2,
                'nominal_valid' => 0,
            ],
            [// 15
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Buku Kreatifitas',
                'pos_pemasukan_id' => 2,
                'nominal_valid' => 0,
            ],
            [// 16
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Iuran Kegiatan Kecamatan - Kabupaten',
                'pos_pemasukan_id' => 2,
                'nominal_valid' => 0,
            ],
            [// 17
                'pos_id' => 2,
                'nama_pos_pengeluaran' => '2 Pcs Buku Jurnal',
                'pos_pemasukan_id' => 2,
                'nominal_valid' => 0,
            ],
            [// 18
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Administrasi LPP (4 Trimester)',
                'pos_pemasukan_id' => 2,
                'nominal_valid' => 0,
            ],
            [// 19
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Kalender',
                'pos_pemasukan_id' => 2,
                'nominal_valid' => 0,
            ],
            [// 20
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Buku Kotak',
                'pos_pemasukan_id' => 2,
                'nominal_valid' => 0,
            ],
            [// 21
                'pos_id' => 2,
                'nama_pos_pengeluaran' => 'Jilbab Field Trip',
                'pos_pemasukan_id' => 2,
                'nominal_valid' => 0,
            ],
        ];
        foreach ($dataPosPengeluaran as $item) {
            PosPengeluaran::create($item);
        }
    }
}
