<?php

namespace App\Http\Controllers;

use App\Helpers\LogPretty;
use App\Models\SIAKAD\SiswaKelas;
use App\Models\SIAKAD\TahunAjaran;
use Illuminate\Http\Request;
use App\Services\TagihanSiswaService;

class TagihanSiswaController extends Controller
{
    public function index()
    {
        // Logic to display tagihan siswa
        // This is a placeholder for the actual implementation
        return view('pages.tagihan.index');
    }

    public function form(Request $request)
    {
        // Logic to display the form for creating or editing tagihan siswa
        // This is a placeholder for the actual implementation
        return view('pages.tagihan.form', ['id' => $request->id]);
    }

    public function store(Request $request)
    {
        // Logic to store tagihan siswa
        // This is a placeholder for the actual implementation
        // You can validate the request and save the data to the database

        return response()->json(['message' => 'Tagihan Siswa saved successfully']);
    }

    public function delete($id)
    {
        // Logic to delete tagihan siswa
        // This is a placeholder for the actual implementation
        // You can find the tagihan by ID and delete it from the database

        return response()->json(['message' => 'Tagihan Siswa deleted successfully']);
    }

    public function generateTagihanSiswa()
    {
        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAjaran) {
            return response()->json(['message' => 'Tahun Ajaran aktif tidak ditemukan'], 404);
        }

        $siswaKelas = SiswaKelas::where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('status', 'aktif')
            ->pluck('id');
        if ($siswaKelas->isEmpty()) {
            return response()->json(['message' => 'Tidak ada siswa kelas aktif untuk tahun ajaran ini'], 404);
        }

        $dataTagihan = [];
        foreach($siswaKelas as $siswaKelasId) {
            $tagihanSiswaService = new TagihanSiswaService();
            $dataTagihan = [
                'tahun_ajaran_id' => $tahunAjaran->id,
                'siswa_kelas' => SiswaKelas::with('kelas.jenjang','siswa.siswa_dispensasi')->find($siswaKelasId)->toArray(),
                'siswa_dispensasi' => $siswaKelas['siswa']['siswa_dispensasi'] ?? [],
            ];
            // return $dataTagihan;
            $resultTagihan = $tagihanSiswaService->create($dataTagihan);
            LogPretty::info('Hasil pembuatan tagihan siswa: ' . json_encode($resultTagihan, JSON_PRETTY_PRINT));
            if(!$resultTagihan['success']) {
                LogPretty::error('Gagal membuat tagihan siswa, id: ' . $siswaKelasId . ': ' . $resultTagihan['message']);
            }
        }

        return redirect()->route('siswa.main')->with([
            'message' => 'Tagihan siswa berhasil dibuat.',
            'success' => true,
        ]);
    }
}
