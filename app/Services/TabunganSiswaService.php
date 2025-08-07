<?php

namespace App\Services;

use Illuminate\Support\Collection;

class TabunganSiswaService
{
    public static function tambahSaldoAkhir(Collection $collection): Collection
    {
        $saldo = 0;

        $data = $collection
            ->sortByDesc('tanggal')
            ->values()
            ->map(function ($item) use (&$saldo) {
                $saldo += (float) $item->debit - (float) $item->kredit;
                $item->saldo_akhir = number_format($saldo, 2, '.', '');
                return $item;
            });
        return $data;
    }
}
