<?php

namespace App\Http\Controllers;

use App\Models\KategoriDispensasi;
use App\Models\SIAKAD\SiswaKelas;
use App\Models\PosPemasukan;
use Illuminate\Http\Request;

class ReferensiController extends Controller
{
    public function siswa(Request $request)
    {
        $search = $request->q;
        $siswa = SiswaKelas::with(['siswa', 'kelas.jenjang','tahun_ajaran'])
            ->whereHas('siswa', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%$search%")
                ->orWhere('nama_panggilan', 'like', "%$search%");
            })
            // ->whereHas('tahun_ajaran', function($q) {
            //     $q->where('is_aktif', true);
            // })
            ->where('status','aktif')
            ->limit(10)
            ->get();

        return response()->json($siswa);
    }

    public function posPemasukanByJenjang(Request $request)
    {
        $jenjangId = $request->jenjang_id;
        // $pos = PosPemasukan::with('pemasukan_detail')
        // ->whereHas('jenjang_pos_pemasukan', function($q) use ($jenjangId) {
        //     $q->where('jenjang_id', $jenjangId);
        // })
        // ->get();

        $siswa_kelas_id = $request->siswa_kelas_id; // Tambahkan parameter siswa_id

        $pos = PosPemasukan::with([
            'pemasukan_detail' => function($query) use ($siswa_kelas_id) {
                // Filter pemasukan_detail berdasarkan siswa
                $query->whereHas('pemasukan', function($q) use ($siswa_kelas_id) {
                    $q->where('siswa_kelas_id', $siswa_kelas_id);
                });
            }])
            ->whereHas('jenjang_pos_pemasukan', function($q) use ($jenjangId) {
                $q->where('jenjang_id', $jenjangId);
            })
            ->where('wajib', false) // Filter hanya pos pemasukan yang wajib
            ->get()
            // ->filter(function($posPemasukan) {
            //     // Jika wajib = true dan tipe pembayaran adalah 'sekali'
            //     if ($posPemasukan->wajib && $posPemasukan->pembayaran === 'sekali') {
            //         // Cek apakah sudah ada transaksi untuk pos ini, jika sudah ada maka tidak tampil
            //         return $posPemasukan->pemasukan_detail->isEmpty();
            //     }

            //     // Jika wajib = false dan tipe pembayaran adalah 'sekali', tetap tampil
            //     if (!$posPemasukan->wajib && $posPemasukan->pembayaran === 'sekali') {
            //         return true;
            //     }

            //     // Untuk tipe 'bulanan' dan 'tahunan', selalu tampilkan
            //     if (in_array($posPemasukan->pembayaran, ['bulanan', 'tahunan'])) {
            //         return true;
            //     }

            //     return false;
            // })
            // ->values()
            ; // Reset array keys setelah filter

        return response()->json($pos);
    }

    public function posPemasukanNonTagihan(Request $request)
    {
        $data = PosPemasukan::where('wajib', false)->get();

        return response()->json($data);
    }

    public function kategoriDispensasiById(Request $request)
    {
        $id = $request->id;
        $data = KategoriDispensasi::find($id);

        return response()->json($data);
    }
}
