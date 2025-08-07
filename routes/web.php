<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\JenjangController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\ReferensiController;
use App\Http\Controllers\SiswaKelasController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\SiswaMutasiController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\PosPemasukanController;
use App\Http\Controllers\TagihanSiswaController;
use App\Http\Controllers\TabunganSiswaController;
use App\Http\Controllers\PosPengeluaranController;
use App\Http\Controllers\SiswaKelulusanController;
use App\Http\Controllers\SiswaNaikKelasController;
use App\Http\Controllers\SiswaDispensasiController;
use App\Http\Controllers\SiswaPindahKelasController;
use App\Http\Controllers\KategoriDispensasiController;
use App\Http\Controllers\SiswaEkstrakulikulerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::middleware('auth')->group(function(){
    Route::prefix('/referensi')->as('referensi.')->controller(ReferensiController::class)->group(function(){
        Route::get('/siswa','siswa')->name('siswa');
        Route::get('/pos-pemasukan','posPemasukanByJenjang')->name('pos-pemasukan');
        // Route::get('/pos-pemasukan','posPemasukanNonTagihan')->name('pos-pemasukan');
        Route::get('/kategori-dispensasi','kategoriDispensasiById')->name('kategori-dispensasi');
    });

    Route::group(['prefix' => 'wilayah'], function () {
        Route::get('/provinsi', [WilayahController::class, 'getProvinsi']);
        Route::get('/kabupaten-kota', [WilayahController::class, 'getKabupatenKota']);
        Route::get('/kecamatan', [WilayahController::class, 'getKecamatan']);
        Route::get('/desa', [WilayahController::class, 'getDesa']);
        Route::post('/sync', [WilayahController::class, 'syncWilayah']);
    });

    Route::get('/', [HomeController::class, 'index'])->name('home');

    // MASTER DATA START
    Route::prefix('/siswa')->as('siswa')->controller(SiswaController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::get('/form','form')->name('.form');
        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');

        Route::get('/import', 'importForm')->name('.import.form');
        Route::post('/import', 'import')->name('.import');
        Route::get('/download-template', 'downloadTemplate')->name('.download.template');
    });

    Route::prefix('/jenjang')->as('jenjang')->controller(JenjangController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::get('/form','form')->name('.form');
        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');
    });

    Route::prefix('/kelas')->as('kelas')->controller(KelasController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::get('/form','form')->name('.form');
        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');
    });

    Route::prefix('/tahun-ajaran')->as('tahun_ajaran')->controller(TahunAjaranController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::get('/form','form')->name('.form');
        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');
    });

    Route::prefix('/kategori-dispensasi')->as('kategori_dispensasi')->controller(KategoriDispensasiController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::get('/form','form')->name('.form');
        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');
    });

    Route::prefix('/siswa-dispensasi')->as('siswa_dispensasi')->controller(SiswaDispensasiController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::get('/detail','detail')->name('.detail');
        Route::get('/form','form')->name('.form');
        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');
    });
    // MASTER DATA END

    // KESISWAAN START
    Route::prefix('/siswa-kelas')->as('siswa_kelas')->controller(SiswaKelasController::class)->group(function(){
        Route::get('/siswa','byKelas')->name('.byKelas');
        Route::get('/tanpa-kelas','getSiswaTanpaKelas')->name('.getSiswaTanpaKelas');
        Route::post('/store/from-kelas','storeFromKelas')->name('.storeFromKelas');
        Route::delete('/delete/{id}','delete')->name('.delete');
    });

    Route::prefix('/siswa-naik-kelas')->as('siswa_naik_kelas')->controller(SiswaNaikKelasController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::get('/form','form')->name('.form');
        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');
    });

    Route::prefix('/siswa-pindah-kelas')->as('siswa_pindah_kelas')->controller(SiswaPindahKelasController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::get('/form','form')->name('.form');
        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');
    });

    Route::prefix('/siswa-mutasi')->as('siswa_mutasi')->controller(SiswaMutasiController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::get('/form','form')->name('.form');
        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');
    });

    Route::prefix('/siswa-kelulusan')->as('siswa_kelulusan')->controller(SiswaKelulusanController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::get('/form','form')->name('.form');
        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');
    });

    Route::prefix('/siswa-ekstrakulikuler')->as('siswa_ekstrakulikuler')->controller(SiswaEkstrakulikulerController::class)->group(function(){
        Route::get('/', 'index')->name('.main');
        Route::get('/form','form')->name('.form');
        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');
    });
    // KESISWAAN END

    // POS START
    Route::prefix('/pos')->as('pos')->controller(PosController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::get('/form','form')->name('.form');
        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');
    });

    Route::prefix('/pos_pemasukan')->as('pos_pemasukan')->controller(PosPemasukanController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::get('/form','form')->name('.form');
        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');

        Route::get('/form-jenjang-pos-pemasukan-detail', 'formJenjangPosPemasukanDetail')->name('.formJenjangPosPemasukanDetail');
        Route::post('/store-jenjang-pos-pemasukan-detail', 'storeJenjangPosPemasukanDetail')->name('.storeJenjangPosPemasukanDetail');

        Route::get('/form-jenjang-pos-pemasukan-nominal', 'formJenjangPosPemasukanNominal')->name('.formJenjangPosPemasukanNominal');
        Route::put('/update-jenjang-pos-pemasukan-nominal', 'updateJenjangPosPemasukanNominal')->name('.updateJenjangPosPemasukanNominal');

        Route::put('/sync-tagihan', 'syncPosPemasukanTagihanSiswa')->name('.syncPosPemasukanTagihanSiswa');
    });

    Route::prefix('/pos_pengeluaran')->as('pos_pengeluaran')->controller(PosPengeluaranController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::get('/form','form')->name('.form');
        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');
    });
    // POS END

    // TRANSAKSI START
    Route::prefix('/pemasukan')->as('pemasukan')->controller(PemasukanController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::get('/form','form')->name('.form');

        Route::get('/tagihan-siswa/{siswaKelasId}', 'getTagihanSiswa')->name('.tagihan-siswa');

        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');
    });
    Route::prefix('/tagihan-siswa')->as('tagihan_siswa')->controller(TagihanSiswaController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::get('/form','form')->name('.form');
        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');

        Route::get('/generate', 'generateTagihanSiswa')->name('.generate');
        Route::put('/update-dispensasi', 'updateDispensasi')->name('.update_dispensasi');
    });
    Route::prefix('/pengeluaran')->as('pengeluaran')->controller(PengeluaranController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::get('/form','form')->name('.form');
        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');
    });
    Route::prefix('/tabungan-siswa')->as('tabungan_siswa')->controller(TabunganSiswaController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::get('/siswa/{siswa_id}','siswa')->name('.siswa');
        Route::get('/add','add')->name('.add');
        Route::get('/form','form')->name('.form');
        Route::post('/store','store')->name('.store');
        Route::delete('/delete/{id}','delete')->name('.delete');
    });
    // TRANSAKSI END

    // LAPORAN START
    Route::prefix('/laporan')->as('laporan')->controller(LaporanController::class)->group(function(){
        Route::get('/','index')->name('.main');
        Route::post('/generate','generate')->name('.generate');
    });
    // LAPORAN END
});
