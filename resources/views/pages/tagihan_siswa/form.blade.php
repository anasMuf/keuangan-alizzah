@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Tagihan Siswa')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Tagihan Siswa')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Tagihan Siswa">
    <div class="detail-siswa">
        @if($siswaKelas)
            <h5>Detail Siswa</h5>
            <p><strong>Nama:</strong> {{ $siswaKelas->siswa->nama_lengkap }}</p>
            <p><strong>Kelas:</strong> {{ $siswaKelas->kelas->nama_kelas }}</p>
            <p><strong>Tahun Ajaran:</strong> {{ $siswaKelas->tahun_ajaran->nama_tahun_ajaran }}</p>
        @endif
    </div>
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_tagihan" id="id_tagihan" value="{{ $data ? old('id_tagihan',$data->id) : old('id_tagihan') }}">
        <input type="hidden" name="siswa_kelas_id" id="siswa_kelas_id" value="{{ old('siswa_kelas_id',$siswaKelas->id) }}">
        <div class="row">
            <x-adminlte-select name="pos_pemasukan_id" label="Pos Pemasukan" fgroup-class="col-md-6">
                <x-adminlte-options :options="$pos_pemasukan"
                :selected="$data ? old('pos_pemasukan_id',$posPemaukanSelected) : old('pos_pemasukan_id')" empty-option=".:: Pilih Pos Pemasukan ::."/>
            </x-adminlte-select>
        </div>

        <x-adminlte-input type="text" name="nominalFormated" id="nominal" label="Nominal Tagihan (isi jika berbeda)" value="{{ old('nominal',0) }}" fgroup-class="col-md-6"/>
            <input type="hidden" name="nominal" id="nominalHidden" value="{{ old('nominal',0) }}">
    </form>

    <a href="{{ route('tagihan_siswa.main',['siswa_id' => $siswaKelas->siswa->id]) }}" class="btn btn-default btn-flat">Kembali</a>

    <x-adminlte-button form="formData" class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" id="submitButton"/>
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
            $.post("{{ route('tagihan_siswa.store') }}", data)
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
                        location.href = "/tagihan-siswa?siswa_id={{ $siswaKelas->siswa->id }}";
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
