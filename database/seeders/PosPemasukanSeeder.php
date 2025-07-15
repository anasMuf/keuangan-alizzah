<?php

namespace Database\Seeders;

use App\Models\PosPemasukan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PosPemasukanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataPosPemasukan = [
            [// 1
                'pos_id' => 1,
                'nama_pos_pemasukan' => 'Biaya Awal',
                'pembayaran' => 'sekali',
                'wajib' => true,
                'nominal_valid' => 2410000,
            ],
            [// perlu detail 2
                'pos_id' => 1,
                'nama_pos_pemasukan' => 'Biaya Registrasi',
                'pembayaran' => 'tahunan',
                'wajib' => true,
                'nominal_valid' => 0,
            ],




            [// 3
                'pos_id' => 1,
                'nama_pos_pemasukan' => 'SPP',
                'pembayaran' => 'bulanan',
                'wajib' => true,
                'nominal_valid' => 150000,
            ],
            [// 4
                'pos_id' => 1,
                'nama_pos_pemasukan' => 'Infaq Harian',
                'pembayaran' => 'bulanan',
                'hari_aktif' => true,
                'wajib' => true,
                'nominal_valid' => 7000,
            ],
            [// 5
                'pos_id' => 1,
                'nama_pos_pemasukan' => 'Exkul Aslin',
                'pembayaran' => 'bulanan',
                'wajib' => true,
                'optional' => true,
                'nominal_valid' => 25000,
            ],
            [// 6
                'pos_id' => 1,
                'nama_pos_pemasukan' => 'Exkul Calisan KB',
                'pembayaran' => 'bulanan',
                'wajib' => true,
                'optional' => true,
                'nominal_valid' => 50000,
            ],
            [// 7
                'pos_id' => 1,
                'nama_pos_pemasukan' => 'Exkul Calisan TK',
                'pembayaran' => 'bulanan',
                'wajib' => true,
                'optional' => true,
                'nominal_valid' => 50000,
            ],
            [// 8
                'pos_id' => 1,
                'nama_pos_pemasukan' => 'Pasta Robotik',
                'pembayaran' => 'bulanan',
                'wajib' => true,
                'optional' => true,
                'nominal_valid' => 100000,
            ],
            [// 9
                'pos_id' => 1,
                'nama_pos_pemasukan' => 'Pasta Laptop Kids',
                'pembayaran' => 'bulanan',
                'wajib' => true,
                'optional' => true,
                'nominal_valid' => 100000,
            ],
            [// 10
                'pos_id' => 1,
                'nama_pos_pemasukan' => 'Pasta Taekwondo',
                'pembayaran' => 'bulanan',
                'wajib' => true,
                'optional' => true,
                'nominal_valid' => 50000,
            ],
            [// 11
                'pos_id' => 1,
                'nama_pos_pemasukan' => 'Pasta Menari',
                'pembayaran' => 'bulanan',
                'wajib' => true,
                'optional' => true,
                'nominal_valid' => 50000,
            ],
            [// 12
                'pos_id' => 1,
                'nama_pos_pemasukan' => 'Pasta Tilawah',
                'pembayaran' => 'bulanan',
                'wajib' => true,
                'optional' => true,
                'nominal_valid' => 50000,
            ],
            [// 13
                'pos_id' => 1,
                'nama_pos_pemasukan' => 'Pasta Menyanyi',
                'pembayaran' => 'bulanan',
                'wajib' => true,
                'optional' => true,
                'nominal_valid' => 50000,
            ],
            [// 14
                'pos_id' => 1,
                'nama_pos_pemasukan' => 'Pasta Melukis',
                'pembayaran' => 'bulanan',
                'wajib' => true,
                'optional' => true,
                'nominal_valid' => 50000,
            ],
            [// 15
                'pos_id' => 1,
                'nama_pos_pemasukan' => 'Tabungan Wajib Berlian',
                'pembayaran' => 'bulanan',
                'hari_aktif' => true,
                'tabungan' => true,
                'wajib' => true,
                'optional' => false,
                'nominal_valid' => 10000,
            ],
            [// 16
                'pos_id' => 1,
                'nama_pos_pemasukan' => 'Tabungan Umum',
                'pembayaran' => 'harian',
                'tabungan' => true,
                'wajib' => false,
                'optional' => true,
                'nominal_valid' => 0,
            ],
        ];
        foreach ($dataPosPemasukan as $item) {
            PosPemasukan::create($item);
        }

        $dataJenjangPosPemasukan = [
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 1,
                'jenjang_id' => 1, // KB

            ],
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 1,
                'jenjang_id' => 2, // TK A
            ],
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 1,
                'jenjang_id' => 3, // TK B
            ],


            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 2,
                'jenjang_id' => 1, // KB
            ],
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 2,
                'jenjang_id' => 2, // TK A
            ],
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 2,
                'jenjang_id' => 3, // TK B
            ],



            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 3,
                'jenjang_id' => 1, // KB
            ],
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 3,
                'jenjang_id' => 2, // TK A
            ],
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 3,
                'jenjang_id' => 3, // TK B
            ],


            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 4,
                'jenjang_id' => 1, // KB
            ],
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 4,
                'jenjang_id' => 2, // TK A
            ],
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 4,
                'jenjang_id' => 3, // TK B
            ],


            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 5,
                'jenjang_id' => 3, // TK B
            ],

            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 6,
                'jenjang_id' => 1, // KB
            ],

            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 7,
                'jenjang_id' => 2, // TK A
            ],
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 7,
                'jenjang_id' => 3, // TK B
            ],


            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 8,
                'jenjang_id' => 2, // TK A
            ],
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 8,
                'jenjang_id' => 3, // TK B
            ],


            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 9,
                'jenjang_id' => 2, // TK A
            ],
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 9,
                'jenjang_id' => 3, // TK B
            ],


            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 10,
                'jenjang_id' => 2, // TK A
            ],
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 10,
                'jenjang_id' => 3, // TK B
            ],


            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 11,
                'jenjang_id' => 2, // TK A
            ],
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 11,
                'jenjang_id' => 3, // TK B
            ],

            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 12,
                'jenjang_id' => 2, // TK A
            ],
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 12,
                'jenjang_id' => 3, // TK B
            ],


            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 13,
                'jenjang_id' => 2, // TK A
            ],
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 13,
                'jenjang_id' => 3, // TK B
            ],


            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 14,
                'jenjang_id' => 2, // TK A
            ],
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 14,
                'jenjang_id' => 3, // TK B
            ],



            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 15,
                'jenjang_id' => 3, // TK B
            ],



            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 16,
                'jenjang_id' => 1, // KB
            ],
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 16,
                'jenjang_id' => 2, // TK A
            ],
            [
                'tahun_ajaran_id' => 2,
                'pos_pemasukan_id' => 16,
                'jenjang_id' => 3, // TK B
            ],
        ];
        foreach ($dataJenjangPosPemasukan as $item) {
            \App\Models\JenjangPosPemasukan::create($item);
        }

        $dataJenjangPosPemasukanDetail = [
            // Registrasi
            [
                'jenjang_pos_pemasukan_id' => 4,
                'jenis_kelamin' => 'Laki-laki',
                'nominal' => 725000
            ],
            [
                'jenjang_pos_pemasukan_id' => 4,
                'jenis_kelamin' => 'Perempuan',
                'nominal' => 760000
            ],


            [
                'jenjang_pos_pemasukan_id' => 5,
                'jenis_kelamin' => 'Laki-laki',
                'nominal' => 825000
            ],
            [
                'jenjang_pos_pemasukan_id' => 5,
                'jenis_kelamin' => 'Perempuan',
                'nominal' => 860000
            ],


            [
                'jenjang_pos_pemasukan_id' => 6,
                'jenis_kelamin' => 'Laki-laki',
                'nominal' => 750000
            ],
            [
                'jenjang_pos_pemasukan_id' => 6,
                'jenis_kelamin' => 'Perempuan',
                'nominal' => 785000
            ],
        ];
        foreach ($dataJenjangPosPemasukanDetail as $item) {
            \App\Models\JenjangPosPemasukanDetail::create($item);
        }
    }
}
