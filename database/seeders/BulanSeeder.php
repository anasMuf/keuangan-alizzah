<?php

namespace Database\Seeders;

use App\Models\Bulan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BulanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bulans = [
            [
                'no_urut' => 1,
                'angka_bulan' => 7,
                'nama_bulan' => 'Juli',
            ],
            [
                'no_urut' => 2,
                'angka_bulan' => 8,
                'nama_bulan' => 'Agustus',
            ],
            [
                'no_urut' => 3,
                'angka_bulan' => 9,
                'nama_bulan' => 'September',
            ],
            [
                'no_urut' => 4,
                'angka_bulan' => 10,
                'nama_bulan' => 'Oktober',
            ],
            [
                'no_urut' => 5,
                'angka_bulan' => 11,
                'nama_bulan' => 'November',
            ],
            [
                'no_urut' => 6,
                'angka_bulan' => 12,
                'nama_bulan' => 'Desember',
            ],
            [
                'no_urut' => 7,
                'angka_bulan' => 1,
                'nama_bulan' => 'Januari',
            ],
            [
                'no_urut' => 8,
                'angka_bulan' => 2,
                'nama_bulan' => 'Februari',
            ],
            [
                'no_urut' => 9,
                'angka_bulan' => 3,
                'nama_bulan' => 'Maret',
            ],
            [
                'no_urut' => 10,
                'angka_bulan' => 4,
                'nama_bulan' => 'April',
            ],
            [
                'no_urut' => 11,
                'angka_bulan' => 5,
                'nama_bulan' => 'Mei',
            ],
            [
                'no_urut' => 12,
                'angka_bulan' => 6,
                'nama_bulan' => 'Juni',
            ],
        ];

        foreach ($bulans as $bulan) {
            Bulan::create([
                'no_urut' => $bulan['no_urut'],
                'angka_bulan' => $bulan['angka_bulan'],
                'nama_bulan' => $bulan['nama_bulan'],
            ]);
        }
    }
}
