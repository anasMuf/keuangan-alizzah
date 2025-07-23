<?php

namespace App\Http\Controllers;

use App\Models\KategoriDispensasi;
use App\Models\SIAKAD\SiswaKelas;
use App\Models\PosPemasukan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->where('wajib', false) // Filter hanya pos pemasukan lainnya atau tidak wajib
            ->get();

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

    public function subtotalTabungan(Request $request)
    {
        // Hitung subtotal pembayaran untuk pos tabungan
        $totals = DB::table('pemasukan_detail as pd')
            ->join('pos_pemasukan as p', 'pd.pos_pemasukan_id', '=', 'p.id')
            ->join('pemasukan_pembayaran as pp', 'pd.id', '=', 'pp.pemasukan_detail_id')
            ->where('p.tabungannya', true)
            ->groupBy('pd.pos_pemasukan_id')
            ->select('pd.pos_pemasukan_id', DB::raw('SUM(pp.nominal) as subtotal'))
            ->get();

        return response()->json($totals);
    }
}
