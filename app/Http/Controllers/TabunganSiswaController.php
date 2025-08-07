<?php

namespace App\Http\Controllers;

use App\Models\SIAKAD\Siswa;
use Illuminate\Http\Request;
use App\Models\TabunganSiswa;
use App\Models\SIAKAD\SiswaKelas;
use App\Models\SIAKAD\TahunAjaran;

class TabunganSiswaController extends Controller
{
    public function index()
    {
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Nama Siswa',
            'Saldo',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $siswa = Siswa::with('tabungan_siswa')->get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [
                null,
                null,
                null,
                ['orderable' => false]
            ]
        ];

        $btnDelete = '';
        $btnDetails = '';
        $saldoTabungan = 0;
        $no = 1;

        foreach($siswa as $item){
            if($item->tabungan_siswa->isEmpty()){
                continue;
            }
            // $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id.',`'.$item->nama_tabungan_siswa.'`)">
            //     <i class="fa fa-lg fa-fw fa-trash"></i>
            // </button>';
            $btnDetails = '<a href="'.route('tabungan_siswa.siswa', ['siswa_id' => $item->id]).'" class="btn btn-info btn-xs mx-1" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
            </a>';

            $tabunganHelper = new \App\Services\TabunganSiswaService();
            $saldoTabungan = $tabunganHelper->tambahSaldoAkhir($item->tabungan_siswa);
            $countSaldo = $saldoTabungan->count();

            $data['config']['data'][] = [
                $no++,
                $item->nama_lengkap,
                'Rp '.number_format($saldoTabungan[$countSaldo - 1]->saldo_akhir, 0, ',', '.'),
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.tabungan_siswa.index', $data);
    }

    public function siswa($siswa_id)
    {
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Tanggal',
            'Keterangan',
            'Debit',
            'Kredit',
        ];

        $tabungan_siswa = TabunganSiswa::with('siswa')->where('siswa_id', $siswa_id)->orderBy('tanggal','asc')->get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [
                null,
                null,
                null,
                null,
                null,
            ]
        ];

        $no = 1;

        foreach($tabungan_siswa as $item){

            $data['config']['data'][] = [
                $no++,
                $item->tanggal,
                $item->keterangan,
                'Rp '.number_format($item->debit, 0, ',', '.'),
                'Rp '.number_format($item->kredit, 0, ',', '.')
            ];
        }

        $data['siswaKelas'] = SiswaKelas::with('siswa.tabungan_siswa','kelas')->where([
            'tahun_ajaran_id' => TahunAjaran::where('is_aktif', true)->first()->id,
            'siswa_id' => $siswa_id,
            'status' => 'aktif'
        ])->first();

        $tabunganSiswa = $data['siswaKelas']->siswa->tabungan_siswa ?? collect();

        $tabunganHelper = new \App\Services\TabunganSiswaService();
        $saldoTabungan = $tabunganHelper->tambahSaldoAkhir($tabunganSiswa);
        $countSaldo = $saldoTabungan->count();

        $data['totalSaldo'] = $saldoTabungan ? $saldoTabungan[$countSaldo - 1]->saldo_akhir : 0;

        return view('pages.tabungan_siswa.siswa', $data);
    }
}
