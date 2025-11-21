<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan Bulanan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; font-size: 10px; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .item-name { text-align: left; font-weight: bold; }
        .amount { text-align: right; }
        .total-row { background-color: #e0e0e0; font-weight: bold; }
        .print-btn { margin: 20px 0; text-align: center; }
        @media print { .print-btn { display: none; } }
        .negative { color: red; }
        .pemasukan-section { background-color: #e8f5e8; }
        .pengeluaran-section { background-color: #ffe8e8; }
    </style>
</head>
<body>
    <div class="print-btn">
        <button onclick="window.print()" style="padding: 10px 20px; margin: 5px;">Print Laporan</button>
        <button onclick="window.close()" style="padding: 10px 20px; margin: 5px;">Tutup</button>
    </div>

    <div class="header">
        <h2>laporan keuangan al izzah</h2>
        <h3>periode {{ $tanggal_awal }} s/d {{ $tanggal_akhir }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">pos pemasukan<br>(item pos pemasukan)</th>
                @foreach($months as $month)
                <th>{{ $month['name'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <!-- Pos Pemasukan -->
            @php $pemasukanItems = $data['items']->where('type', 'pemasukan'); @endphp
            @if($pemasukanItems->count() > 0)
                @foreach($pemasukanItems as $item)
                <tr class="pemasukan-section">
                    <td class="item-name">(item pos pemasukan) {{ $item['pos_item']->nama }}</td>
                    @foreach($item['periods'] as $period)
                    <td class="amount">{{ $period['formatted'] }}</td>
                    @endforeach
                </tr>
                @endforeach
            @else
                <tr class="pemasukan-section">
                    <td class="item-name">(item pos pemasukan)</td>
                    @foreach($months as $month)
                    <td class="amount">Rp0</td>
                    @endforeach
                </tr>
            @endif

            <!-- Pos Pengeluaran -->
            @php $pengeluaranItems = $data['items']->where('type', 'pengeluaran'); @endphp
            @if($pengeluaranItems->count() > 0)
                @foreach($pengeluaranItems as $item)
                <tr class="pengeluaran-section">
                    <td class="item-name">(item pos pengeluaran) {{ $item['pos_item']->nama }}</td>
                    @foreach($item['periods'] as $period)
                    <td class="amount negative">
                        @if($period['total'] > 0)
                        ({{ $period['formatted'] }})
                        @else
                        {{ $period['formatted'] }}
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            @else
                <tr class="pengeluaran-section">
                    <td class="item-name">(item pos pengeluaran)</td>
                    @foreach($months as $month)
                    <td class="amount">Rp0</td>
                    @endforeach
                </tr>
            @endif

            <!-- Total Row -->
            <tr class="total-row">
                <td>total</td>
                @foreach($data['totals'] as $total)
                <td class="amount">{{ $total['saldo_formatted'] }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <p><strong>Keterangan:</strong></p>
        <ul>
            <li>Angka dalam tanda kurung ( ) menunjukkan pengeluaran</li>
            <li>Total merupakan selisih antara pemasukan dan pengeluaran</li>
            <li>Laporan ini dibuat pada {{ now()->format('d/m/Y H:i:s') }}</li>
        </ul>
    </div>
</body>
</html>
