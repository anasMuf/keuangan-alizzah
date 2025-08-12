@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Tabungan Siswa')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Tabungan Siswa')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Tabungan Siswa">
    <div class="row details">
        <div class="col-md-6">
            <h5>Nama Siswa: {{ $siswaKelas->siswa->nama_lengkap }}</h5>
            <h5>Kelas: {{ $siswaKelas->kelas->nama_kelas }}</h5>
        </div>
        <div class="col-md-6 text-right">
            <h5>Total Tabungan: Rp {{ number_format($totalSaldo, 0,',','.') }}</h5>
        </div>
    </div>
    <hr>
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="siswa_kelas_id" id="siswa_kelas_id" value="{{ old('siswa_kelas_id',$siswaKelas->id) }}">
        <input type="hidden" name="transaksi" id="transaksi" value="{{ old('transaksi',$transaksi) }}">

        <div class="form-group">
            <label for="tanggal">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" placeholder="Tanggal...">
        </div>

        <x-adminlte-input type="text" name="nominalFormated" id="nominal" label="Nominal {{ Str::ucfirst($transaksi) }} Tunai" value="{{ old('nominal',0) }}" fgroup-class="col-md-6"/>
            <input type="hidden" name="nominal" id="nominalHidden" value="{{ old('nominal',0) }}">
    </form>

    <a href="{{ route('tabungan_siswa.detail', ['siswa_id' => $siswaKelas->siswa->id]) }}" class="btn btn-default btn-flat">Kembali</a>

    <x-adminlte-button form="formData" class="btn-flat" type="submit" label="Submit" theme="{{ $transaksi === 'setor' ? 'success' : 'danger' }}" icon="fas fa-lg fa-save" id="submitButton"/>
</x-adminlte-card>
@stop

{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">
@endpush

{{-- Push extra scripts --}}

@push('js')
    <script>
        $('#nominal').on('input', function() {
            let nominal = parseRupiah(this.value);
            $('#nominalHidden').val(nominal);
            // Format the input value as Rupiah currency
            const formattedValue = formatRupiah(nominal);
            this.value = formattedValue;
        });

        // Helper function to format currency
        function formatRupiah(number) {
            const num = parseFloat(number);
            return !isNaN(num) ?
                num.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }) : '0';
        }

        // Helper function to parse currency string to number
        function parseRupiah(rupiahString) {
            if (!rupiahString) return 0;
            return parseFloat(rupiahString.replace(/[^0-9-]/g, '')) || 0;
        }
    </script>
    <script>
        $('#formData').submit(function (e) {
            e.preventDefault();
            const data = $('#formData').serialize()
            $.post("{{ route('tabungan_siswa.store') }}", data)
            .done(function (response) {
                if(response.success){
                    let timer = 1500
                    Swal.fire({
                        title: "Sukses",
                        text: response.message,
                        icon: "success",
                        showConfirmButton: false,
                        timer: timer
                    })
                    setTimeout(() => {
                        location.href = "/tabungan-siswa/siswa/"+$('#siswa_kelas_id').val()
                    }, timer);
                }else{
                    let message = response.message;
                        $.each(response.message_validation, function (i,msg) {
                            message += msg[0]+', <br>';
                        })
                    Swal.fire('Peringatan', message, 'warning')
                }
            })
            .fail(function(xhr,status,error){
                Swal.fire('Terjadi Kesalahan', error, 'error')
            });
        });
    </script>
@endpush
