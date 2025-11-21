@extends('layouts.app')

@section('subtitle', 'Laporan Keuangan')
@section('content_header_title', 'Laporan Keuangan')
@section('content_header_subtitle', 'Laporan')

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Generate Laporan Keuangan</h3>
                </div>
                <div class="card-body">
                    <form id="laporanForm" method="POST" action="{{ route('laporan.generate') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenis_laporan">Jenis Laporan</label>
                                    <select class="form-control" name="jenis_laporan" id="jenis_laporan" required>
                                        <option value="">Pilih Jenis Laporan</option>
                                        <option value="tahunan">Laporan Tahunan</option>
                                        <option value="bulanan">Laporan Bulanan</option>
                                        <option value="harian">Laporan Harian</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6" id="tahun_ajaran_group" style="display: none;">
                                <div class="form-group">
                                    <label for="tahun_ajaran">Tahun Ajaran</label>
                                    <select class="form-control" name="tahun_ajaran" id="tahun_ajaran">
                                        <option value="">Pilih Tahun Ajaran</option>
                                        @foreach($tahunAjarans as $tahunAjaran)
                                        <option value="{{ $tahunAjaran->nama_tahun_ajaran }}">
                                            {{ $tahunAjaran->nama_tahun_ajaran }}
                                            @if($tahunAjaran->is_aktif)
                                            (Aktif)
                                            @endif
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="date_range_group" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_awal">Tanggal Awal</label>
                                    <input type="date" class="form-control" name="tanggal_awal" id="tanggal_awal">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_akhir">Tanggal Akhir</label>
                                    <input type="date" class="form-control" name="tanggal_akhir" id="tanggal_akhir">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Filter Pos Item (Opsional)</label>
                            <div class="row">
                                @foreach($posPemasukan as $item)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="pos_items[]" value="{{ $item->id }}" id="pos_{{ $item->id }}">
                                        <label class="form-check-label" for="pos_{{ $item->id }}">
                                            {{ $item->nama_pos_pemasukan }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-print"></i> Generate Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisLaporan = document.getElementById('jenis_laporan');
    const tahunAjaranGroup = document.getElementById('tahun_ajaran_group');
    const dateRangeGroup = document.getElementById('date_range_group');

    jenisLaporan.addEventListener('change', function() {
        const jenis = this.value;

        if (jenis === 'tahunan') {
            tahunAjaranGroup.style.display = 'block';
            dateRangeGroup.style.display = 'none';
        } else if (jenis === 'bulanan' || jenis === 'harian') {
            tahunAjaranGroup.style.display = 'none';
            dateRangeGroup.style.display = 'block';
        } else {
            tahunAjaranGroup.style.display = 'none';
            dateRangeGroup.style.display = 'none';
        }
    });

    document.getElementById('laporanForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const url = this.action;

        // Open in new window for printing
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.target = '_blank';

        for (let [key, value] of formData.entries()) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    });
});
</script>
@endpush
