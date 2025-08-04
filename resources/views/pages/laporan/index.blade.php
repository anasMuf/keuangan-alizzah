@extends('layouts.app')

@section('subtitle', 'Laporan Keuangan')
@section('content_header_title', 'Laporan Keuangan')
@section('content_header_subtitle', 'Laporan')

@section('content_body')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pilih Jenis Laporan</h3>
                </div>
                <div class="card-body">
                    <form id="form-laporan">
                        <div class="form-group">
                            <label for="tipe_laporan">Tipe Laporan</label>
                            <select class="form-control" id="tipe_laporan" name="tipe_laporan">
                                <option value="harian">Harian</option>
                                <option value="mingguan">Mingguan</option>
                                <option value="bulanan">Bulanan</option>
                                <option value="tahunan">Tahunan</option>
                            </select>
                        </div>
                        <div class="form-group" id="filter-tanggal">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal">
                        </div>
                        <div class="form-group d-none" id="filter-mingguan">
                            <label for="minggu">Minggu ke-</label>
                            <select class="form-control" id="minggu" name="minggu">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                        <div class="form-group d-none" id="filter-bulanan">
                            <label for="bulan">Bulan</label>
                            <select class="form-control" id="bulan" name="bulan">
                                @for($i=1;$i<=12;$i++)
                                    <option value="{{ $i }}">{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group d-none" id="filter-tahunan">
                            <label for="tahun">Tahun</label>
                            <input type="number" class="form-control" id="tahun" name="tahun" value="{{ date('Y') }}">
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">
                            <i class="fas fa-print"></i> Cetak Laporan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <!-- Tempat preview laporan atau info summary -->
            <div id="laporan-preview"></div>
        </div>
    </div>
</div>
@stop

@push('js')
<script>
$(function() {
    $('#tipe_laporan').change(function() {
        const tipe = $(this).val();
        $('#filter-tanggal, #filter-mingguan, #filter-bulanan, #filter-tahunan').addClass('d-none');
        if (tipe === 'harian') {
            $('#filter-tanggal').removeClass('d-none');
        } else if (tipe === 'mingguan') {
            $('#filter-mingguan').removeClass('d-none');
            $('#filter-bulanan').removeClass('d-none');
            $('#filter-tahunan').removeClass('d-none');
        } else if (tipe === 'bulanan') {
            $('#filter-bulanan').removeClass('d-none');
            $('#filter-tahunan').removeClass('d-none');
        } else if (tipe === 'tahunan') {
            $('#filter-tahunan').removeClass('d-none');
        }
    }).trigger('change');

    $('#form-laporan').submit(function(e) {
        e.preventDefault();
        // TODO: AJAX request untuk generate/cetak laporan sesuai filter
        $.ajax({
            url: '{{ route("laporan.generate") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#laporan-preview').html(response);
            },
            error: function(xhr) {
                $('#laporan-preview').html('<div class="alert alert-danger">Terjadi kesalahan saat generate laporan.</div>');
            }
        });
    });
});
</script>
@endpush
