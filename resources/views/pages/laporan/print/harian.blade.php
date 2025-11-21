<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan Harian</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .date-column { text-align: left; font-weight: bold; }
        .amount { text-align: right; }
        .total-row { background-color: #e0e0e0; font-weight: bold; }
        .print-btn { margin: 20px 0; text-align: center; }
        @media print { .print-btn { display: none; } }
        .transaction-detail { font-size: 10px; text-align: left; }
    </style>
</head>
<body>
    <div class="print-btn">
        <button onclick="window.print()" style="padding: 10px 20px; margin: 5px;">Print Laporan</button>
        <button onclick="window.close()" style="padding: 10px 20px; margin: 5px;">Tutup</button>
    </div>

    <div class="header">
        <h2>Laporan Keuangan Harian Al Izzah</h2>
        <h3>Periode {{ $tanggal_awal }} s/d {{ $tanggal_akhir }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Pemasukan</th>
                <th>Pengeluaran</th>
                <th>Saldo Harian</th>
                <th>Detail Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['daily'] as $daily)
            <tr>
                <td class="date-column">
                    {{ $daily['date']['formatted'] }}<br>
                    <small>{{ $daily['date']['day_name'] }}</small>
                </td>
                <td class="amount">{{ $daily['pemasukan_formatted'] }}</td>
                <td class="amount">{{ $daily['pengeluaran_formatted'] }}</td>
                <td class="amount">{{ $daily['saldo_formatted'] }}</td>
                <td class="transaction-detail">
                    @if($daily['ledgers']->count() > 0)
                        @foreach($daily['ledgers'] as $ledger)
                        <div>
                            {{ $ledger->posItem->nama }}:
                            {{ 'Rp' . number_format($ledger->nominal, 0, ',', '.') }}
                            @if($ledger->keterangan)
                            <br><small>{{ $ledger->keterangan }}</small>
                            @endif
                        </div>
                        @endforeach
                    @else
                        <em>Tidak ada transaksi</em>
                    @endif
                </td>
            </tr>
            @endforeach

            <!-- Grand Total Row -->
            <tr class="total-row">
                <td><strong>TOTAL</strong></td>
                <td class="amount"><strong>{{ $data['grand_total']['pemasukan_formatted'] }}</strong></td>
                <td class="amount"><strong>{{ $data['grand_total']['pengeluaran_formatted'] }}</strong></td>
                <td class="amount"><strong>{{ $data['grand_total']['saldo_formatted'] }}</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <p><strong>Keterangan:</strong></p>
        <ul>
            <li>Saldo harian = Pemasukan - Pengeluaran per hari</li>
            <li>Total saldo = Total pemasukan - Total pengeluaran selama periode</li>
            <li>Laporan ini dibuat pada {{ now()->format('d/m/Y H:i:s') }}</li>
        </ul>
    </div>
</body>
</html>
