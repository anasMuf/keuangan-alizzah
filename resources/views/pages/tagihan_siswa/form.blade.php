@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Tagihan Siswa')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Tagihan Siswa')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Tagihan Siswa">
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
    </form>

    <a href="{{ route('tagihan_siswa.main') }}" class="btn btn-default btn-flat">Kembali</a>

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
                        location.href = "{{ route('tagihan_siswa.main') }}"
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
