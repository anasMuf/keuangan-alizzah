<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        if(\App\Models\User::count() < 1) {
            \App\Models\User::factory()->create([
                'name' => 'Admin',
                'username' => 'admin',
            ]);
        }
        if(\App\Models\Pos::count() < 1) {
            \App\Models\Pos::create([
                'nama_pos' => 'PEMASUKAN',
                'tipe' => 'in',
            ]);
            \App\Models\Pos::create([
                'nama_pos' => 'PENGELUARAN',
                'tipe' => 'out',
            ]);
        }

        $this->call([
            BulanSeeder::class,
            PosPemasukanSeeder::class,
            PosPengeluaranSeeder::class,
            // Seeders lainnya
        ]);
    }
}
