<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Ledger;
use App\Models\PosPemasukan;
use Illuminate\Http\Request;
use App\Models\SIAKAD\TahunAjaran;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        $posPemasukan = PosPemasukan::get();
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        return view('pages.laporan.index', compact('posPemasukan', 'tahunAjarans'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'jenis_laporan' => 'required|string|in:tahunan,bulanan,harian',
            'tahun_ajaran' => 'nullable|string',
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'pos_items' => 'nullable|array',
            'pos_items.*' => 'exists:pos_pemasukans,id',
        ]);

        $jenis = $request->jenis_laporan;

        switch ($jenis) {
            case 'tahunan':
                if (!$request->tahun_ajaran) {
                    return response()->json(['error' => 'Tahun ajaran wajib diisi untuk laporan tahunan'], 400);
                }
                [$tahunMulai, $tahunAkhir] = explode('/', $request->tahun_ajaran);
                return $this->generateLaporanTahunan($tahunMulai, $tahunAkhir, $request->pos_items);

            case 'bulanan':
                if (!$request->tanggal_awal || !$request->tanggal_akhir) {
                    return response()->json(['error' => 'Tanggal awal dan akhir wajib diisi untuk laporan bulanan'], 400);
                }
                return $this->generateLaporanBulanan($request->tanggal_awal, $request->tanggal_akhir, $request->pos_items);

            case 'harian':
                if (!$request->tanggal_awal || !$request->tanggal_akhir) {
                    return response()->json(['error' => 'Tanggal awal dan akhir wajib diisi untuk laporan harian'], 400);
                }
                return $this->generateLaporanHarian($request->tanggal_awal, $request->tanggal_akhir, $request->pos_items);

            default:
                return response()->json(['error' => 'Jenis laporan tidak valid'], 400);
        }
    }

    private function generateLaporanTahunan($tahunMulai, $tahunAkhir, $posItems = null)
    {
        // Tahun ajaran: Juli tahun_mulai - Juni tahun_akhir
        $startDate = Carbon::create($tahunMulai, 7, 1); // 1 Juli
        $endDate = Carbon::create($tahunAkhir, 6, 30);   // 30 Juni

        $months = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $months[] = [
                'name' => $this->getIndonesianMonth($current->month),
                'year' => $current->year,
                'month' => $current->month,
                'date_start' => $current->copy()->startOfMonth(),
                'date_end' => $current->copy()->endOfMonth(),
            ];
            $current->addMonth();
        }

        $data = $this->getReportData($startDate, $endDate, $posItems, 'monthly', $months);

        return view('pages.laporan.print.tahunan', [
            'data' => $data,
            'months' => $months,
            'tahun_ajaran' => "$tahunMulai/$tahunAkhir",
            'periode' => 'tahunan'
        ]);
    }

    private function generateLaporanBulanan($tanggalAwal, $tanggalAkhir, $posItems = null)
    {
        $startDate = Carbon::parse($tanggalAwal);
        $endDate = Carbon::parse($tanggalAkhir);

        $months = [];
        $current = $startDate->copy()->startOfMonth();

        while ($current <= $endDate) {
            if ($current >= $startDate->startOfMonth()) {
                $months[] = [
                    'name' => $this->getIndonesianMonth($current->month),
                    'year' => $current->year,
                    'month' => $current->month,
                    'date_start' => $current->copy()->startOfMonth(),
                    'date_end' => $current->copy()->endOfMonth(),
                ];
            }
            $current->addMonth();
        }

        $data = $this->getReportData($startDate, $endDate, $posItems, 'monthly', $months);

        return view('pages.laporan.print.bulanan', [
            'data' => $data,
            'months' => $months,
            'tanggal_awal' => $startDate->format('d/m/Y'),
            'tanggal_akhir' => $endDate->format('d/m/Y'),
            'periode' => 'bulanan'
        ]);
    }

    private function generateLaporanHarian($tanggalAwal, $tanggalAkhir, $posItems = null)
    {
        $startDate = Carbon::parse($tanggalAwal);
        $endDate = Carbon::parse($tanggalAkhir);

        // Untuk laporan harian, kita group per hari
        $days = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $days[] = [
                'date' => $current->copy(),
                'formatted' => $current->format('d/m/Y'),
                'day_name' => $this->getIndonesianDay($current->dayOfWeek),
                'date_start' => $current->copy()->startOfDay(),
                'date_end' => $current->copy()->endOfDay(),
            ];
            $current->addDay();
        }

        $data = $this->getReportDataHarian($startDate, $endDate, $posItems, $days);

        return view('pages.laporan.print.harian', [
            'data' => $data,
            'days' => $days,
            'tanggal_awal' => $startDate->format('d/m/Y'),
            'tanggal_akhir' => $endDate->format('d/m/Y'),
            'periode' => 'harian'
        ]);
    }

    private function getReportData($startDate, $endDate, $posItems, $groupBy, $periods)
    {
        // Query ledger berdasarkan struktur tabel yang benar dari contoh data
        $query = Ledger::whereBetween('trx_date', [$startDate, $endDate]);

        // Filter berdasarkan pos pemasukan yang dipilih
        if ($posItems) {
            $query->where(function($q) use ($posItems) {
                // Join dengan tabel pemasukan dan pemasukan_detail untuk mendapatkan pos_pemasukan_id
                $q->whereExists(function($subquery) use ($posItems) {
                    $subquery->select(DB::raw(1))
                            ->from('pemasukan')
                            ->join('pemasukan_detail', 'pemasukan.id', '=', 'pemasukan_detail.pemasukan_id')
                            ->whereColumn('pemasukan.id', 'ledgers.referensi_id')
                            ->where('ledgers.sumber_tabel', 'pemasukan')
                            ->whereIn('pemasukan_detail.pos_pemasukan_id', $posItems);
                });
            });
        }

        $ledgers = $query->get();

        // Ambil semua pos pemasukan untuk struktur laporan
        $posItemsQuery = PosPemasukan::with('pos_pengeluaran');
        if ($posItems) {
            $posItemsQuery->whereIn('id', $posItems);
        }
        $allPosItems = $posItemsQuery->get();

        $data = [];

        foreach ($allPosItems as $posItem) {
            $itemData = [
                'pos_item' => $posItem,
                'type' => $this->getPosItemType($posItem),
                'periods' => []
            ];

            foreach ($periods as $period) {
                $periodStart = $period['date_start'];
                $periodEnd = $period['date_end'];

                // Filter ledger untuk pos item dan periode tertentu
                // Berdasarkan struktur: ledgers -> pemasukan -> pemasukan_detail -> pos_pemasukan
                $periodTotal = Ledger::whereBetween('trx_date', [$periodStart, $periodEnd])
                    ->where('sumber_tabel', 'pemasukan')
                    ->whereExists(function($query) use ($posItem) {
                        $query->select(DB::raw(1))
                              ->from('pemasukan')
                              ->join('pemasukan_detail', 'pemasukan.id', '=', 'pemasukan_detail.pemasukan_id')
                              ->whereColumn('pemasukan.id', 'ledgers.referensi_id')
                              ->where('pemasukan_detail.pos_pemasukan_id', $posItem->id);
                    })
                    ->sum(DB::raw('debit - kredit'));

                $itemData['periods'][] = [
                    'period' => $period,
                    'total' => $periodTotal,
                    'formatted' => 'Rp' . number_format($periodTotal, 0, ',', '.'),
                ];
            }

            $data[] = $itemData;
        }

        // Hitung total per periode
        $totals = [];
        foreach ($periods as $key => $period) {
            $totalPemasukan = 0;
            $totalPengeluaran = 0;

            foreach ($data as $item) {
                if ($item['type'] === 'pemasukan') {
                    $totalPemasukan += $item['periods'][$key]['total'];
                } else {
                    $totalPengeluaran += $item['periods'][$key]['total'];
                }
            }

            $totals[] = [
                'pemasukan' => $totalPemasukan,
                'pengeluaran' => $totalPengeluaran,
                'saldo' => $totalPemasukan - $totalPengeluaran,
                'pemasukan_formatted' => 'Rp' . number_format($totalPemasukan, 0, ',', '.'),
                'pengeluaran_formatted' => 'Rp' . number_format($totalPengeluaran, 0, ',', '.'),
                'saldo_formatted' => 'Rp' . number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.')
            ];
        }

        return [
            'items' => collect($data),
            'totals' => $totals
        ];
    }

    private function getReportDataHarian($startDate, $endDate, $posItems, $days)
    {
        $query = Ledger::whereBetween('trx_date', [$startDate, $endDate]);

        if ($posItems) {
            $query->where(function($q) use ($posItems) {
                $q->whereExists(function($subquery) use ($posItems) {
                    $subquery->select(DB::raw(1))
                            ->from('pemasukan')
                            ->join('pemasukan_detail', 'pemasukan.id', '=', 'pemasukan_detail.pemasukan_id')
                            ->whereColumn('pemasukan.id', 'ledgers.referensi_id')
                            ->where('ledgers.sumber_tabel', 'pemasukan')
                            ->whereIn('pemasukan_detail.pos_pemasukan_id', $posItems);
                });
            });
        }

        $ledgers = $query->orderBy('trx_date', 'asc')->get();

        // Group ledgers by date
        $dailyData = [];
        $grandTotalPemasukan = 0;
        $grandTotalPengeluaran = 0;

        foreach ($days as $day) {
            $dayLedgers = $ledgers->filter(function ($ledger) use ($day) {
                $trxDate = Carbon::parse($ledger->trx_date);
                return $trxDate->isSameDay($day['date']);
            });

            // Hitung berdasarkan tipe transaksi
            // Pemasukan: tipe='in' dan jenis_akun='pendapatan'
            $dailyPemasukan = $dayLedgers->where('tipe', 'in')
                                        ->where('jenis_akun', 'pendapatan')
                                        ->sum('debit');

            // Pengeluaran: tipe='out' atau jenis_akun='beban'
            $dailyPengeluaran = $dayLedgers->where('tipe', 'out')
                                          ->sum('kredit');

            $dailySaldo = $dailyPemasukan - $dailyPengeluaran;

            $grandTotalPemasukan += $dailyPemasukan;
            $grandTotalPengeluaran += $dailyPengeluaran;

            $dailyData[] = [
                'date' => $day,
                'ledgers' => $dayLedgers,
                'pemasukan' => $dailyPemasukan,
                'pengeluaran' => $dailyPengeluaran,
                'saldo' => $dailySaldo,
                'pemasukan_formatted' => 'Rp' . number_format($dailyPemasukan, 0, ',', '.'),
                'pengeluaran_formatted' => 'Rp' . number_format($dailyPengeluaran, 0, ',', '.'),
                'saldo_formatted' => 'Rp' . number_format($dailySaldo, 0, ',', '.')
            ];
        }

        return [
            'daily' => $dailyData,
            'grand_total' => [
                'pemasukan' => $grandTotalPemasukan,
                'pengeluaran' => $grandTotalPengeluaran,
                'saldo' => $grandTotalPemasukan - $grandTotalPengeluaran,
                'pemasukan_formatted' => 'Rp' . number_format($grandTotalPemasukan, 0, ',', '.'),
                'pengeluaran_formatted' => 'Rp' . number_format($grandTotalPengeluaran, 0, ',', '.'),
                'saldo_formatted' => 'Rp' . number_format($grandTotalPemasukan - $grandTotalPengeluaran, 0, ',', '.')
            ]
        ];
    }

    private function getPosItemType($posItem)
    {
        // Karena ini dari PosPemasukan, defaultnya adalah pemasukan
        // Sesuaikan dengan field yang ada di model Anda
        if (isset($posItem->type)) {
            return $posItem->type;
        }

        // Default untuk pos pemasukan
        return 'pemasukan';
    }

    private function getIndonesianMonth($month)
    {
        $months = [
            1 => 'januari', 2 => 'februari', 3 => 'maret', 4 => 'april',
            5 => 'mei', 6 => 'juni', 7 => 'juli', 8 => 'agustus',
            9 => 'september', 10 => 'oktober', 11 => 'november', 12 => 'desember'
        ];
        return $months[$month];
    }

    private function getIndonesianDay($day)
    {
        $days = [
            0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu',
            4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'
        ];
        return $days[$day];
    }
}
