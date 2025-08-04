@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Pos Pengeluaran')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Pos Pengeluaran')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Pos Pengeluaran">
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_pos_pengeluaran" id="id_pos_pengeluaran" value="{{ $data ? old('id_pos_pengeluaran',$data->id) : old('id_pos_pengeluaran') }}">
        <div class="row">
            <x-adminlte-select2 name="pos_pemasukan_id" label="Pos Pemasukan" fgroup-class="col-md-6">
                <x-adminlte-options :options="$pos_pemasukan"
                selected="{{ $data ? old('pos_pemasukan_id',$data->pos_pemasukan_id) : old('pos_pemasukan_id') }}" empty-option=".:: Pilih Pos Pemasukan ::."/>
            </x-adminlte-select2>

            <x-adminlte-input name="nama_pos_pengeluaran" label="Nama Pos Pengeluaran" placeholder="Tulis Nama Pos Pengeluaran" id="nama_pos_pengeluaran"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('nama_pos_pengeluaran',$data->nama_pos_pengeluaran) : old('nama_pos_pengeluaran') }}"/>

            <x-adminlte-select name="kategori" label="Kategori Pos Pengeluaran" fgroup-class="col-md-6">
                <x-adminlte-options :options="$kategori"
                selected="{{ $data ? old('kategori',$data->kategori) : old('kategori') }}" empty-option=".:: Pilih Kategori Pos Pengeluaran ::."/>
            </x-adminlte-select>

            <x-adminlte-input hidden type="number" min="0" step="500" name="nominal_valid" {{-- label="Nominal" --}} placeholder="Tulis Nominal" id="nominal_valid"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('nominal_valid',number_format($data->nominal_valid,0,',','')) : old('nominal_valid',0) }}"/>
        </div>
    </form>

    <a href="{{ route('pos_pengeluaran.main') }}" class="btn btn-default btn-flat">Kembali</a>

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
            $.post("{{ route('pos_pengeluaran.store') }}", data)
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
                        location.href = "{{ route('pos_pengeluaran.main') }}"
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
