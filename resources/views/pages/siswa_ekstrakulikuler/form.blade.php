@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Ekstrakulikuler Siswa')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Ekstrakulikuler Siswa')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Ekstrakulikuler Siswa">
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_ekstrakulikuler" id="id_ekstrakulikuler" value="{{ $data ? old('id_ekstrakulikuler',$data->id) : old('id_ekstrakulikuler') }}">
        <input type="hidden" name="siswa_kelas_id" id="siswa_kelas_id" value="{{ old('siswa_kelas_id',$siswaKelas->id) }}">
        <div class="row">
            <x-adminlte-select name="pos_pemasukan_id" label="Pos Pemasukan" fgroup-class="col-md-6">
                <x-adminlte-options :options="$pos_pemasukan"
                :selected="$data ? old('pos_pemasukan_id',$posPemaukanSelected) : old('pos_pemasukan_id')" empty-option=".:: Pilih Pos Pemasukan ::."/>
            </x-adminlte-select>

            <x-adminlte-textarea name="keterangan" label="Keterangan" fgroup-class="col-md-6" rows=3>
                {{ old('keterangan', $data ? $data->keterangan : '') }}
            </x-adminlte-textarea>

            <x-adminlte-input type="text" name="nominalFormated" id="nominal" label="Nominal Tagihan (isi jika berbeda)" value="{{ $tagihanSiswa ? old('nominal',number_format($tagihanSiswa->nominal,0,',','.')) : old('nominal',0) }}" fgroup-class="col-md-6"/>
            <input type="hidden" name="nominal" id="nominalHidden" value="{{ $tagihanSiswa ? old('nominal',number_format($tagihanSiswa->nominal,0,',','.')) : old('nominal',0) }}">
        </div>
    </form>

    <a href="{{ route('siswa_ekstrakulikuler.main',['siswa_id' => $siswaKelas->siswa_id]) }}" class="btn btn-default btn-flat">Kembali</a>

    @if(!$data)
    <x-adminlte-button form="formData" class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" id="submitButton"/>
    @endif
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
        $(function () {
            if($('#id_ekstrakulikuler').val()) {
                $('#formData input,textarea,option').prop('disabled', true);
                $('#formData select').prop('disabled', true);
            }
        })
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
            $.post("{{ route('siswa_ekstrakulikuler.store') }}", data)
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
                        location.href = "/siswa-ekstrakulikuler?siswa_id="+$('#siswa_kelas_id').val()
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
