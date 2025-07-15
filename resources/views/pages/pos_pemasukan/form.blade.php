@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Pos Pemasukan')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Pos Pemasukan')
@section('plugins.Select2', true)

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Pos Pemasukan">
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_pos_pemasukan" id="id_pos_pemasukan" value="{{ $data ? old('id_pos_pemasukan',$data->id) : old('id_pos_pemasukan') }}">
        <div class="row">
            <h5 class="col-12">Informasi Pos Pemasukan</h5>
            <x-adminlte-select name="jenis" label="Jenis Pos Pemasukan" fgroup-class="col-md-6">
                <x-adminlte-options :options="$jenis"
                :selected="$data ? old('jenis',$jenisSelected) : old('jenis')" empty-option=".:: Pilih Jenis Pos Pemasukan ::."/>
            </x-adminlte-select>

            <x-adminlte-select name="pembayaran" label="Pembayaran" fgroup-class="col-md-6">
                <x-adminlte-options :options="$pembayaran"
                :selected="$data ? old('pembayaran',$pembayaranSelected) : old('pembayaran')" empty-option=".:: Pilih Pembayaran ::."/>
            </x-adminlte-select>

            <x-adminlte-input name="nama_pos_pemasukan" label="Nama Pos Pemasukan" placeholder="Tulis Nama Pos Pemasukan" id="nama_pos_pemasukan"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('nama_pos_pemasukan',$data->nama_pos_pemasukan) : old('nama_pos_pemasukan') }}"/>

            @if ($data && !$data->hari_aktif)
            <x-adminlte-input type="number" min="0" step="500" name="nominal_valid" label="Nominal" placeholder="Tulis Nominal" id="nominal_valid"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('nominal_valid',number_format($data->nominal_valid,0,',','')) : old('nominal_valid') }}"/>
            @endif
        </div>
        <div class="row">
            <h5 class="col-12">Untuk Jenjang</h5>
            <x-adminlte-select2 name="jenjang_id[]" id="jenjang_id" label="Jenjang" fgroup-class="col-md-6" :config="$config" multiple="multiple">
                <x-adminlte-options :options="$jenjang"
                :selected="$data ? old('jenjang_id',$jenjangSelected) : old('jenjang_id')"
                />
            </x-adminlte-select2>

            <x-adminlte-select name="tahun_ajaran_id" label="Tahun Ajaran" fgroup-class="col-md-6">
                <x-adminlte-options :options="$tahun_ajaran"
                :selected="$data ? old('tahun_ajaran_id',$tahunAjaranSelected) : old('tahun_ajaran_id')" empty-option=".:: Pilih Tahun Ajaran ::."/>
            </x-adminlte-select>
        </div>
    </form>

    @if ($data && $data->hari_aktif)
        <a href="javascript:void(0);" class="mb-3" onclick="handleSettingNominalBulan()">setting nominal tiap bulan</a>
        <div class="setting-nominal-bulan-modal mb-3"></div>
    @endif

    <a href="{{ route('pos_pemasukan.main') }}" class="btn btn-default btn-flat">Kembali</a>

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
        function handleSettingNominalBulan(){
            const idPosPemasukan = $('#id_pos_pemasukan').val();
            if(idPosPemasukan){
                $.get("{{ route('pos_pemasukan.setting_nominal_bulan') }}", {id_pos_pemasukan: idPosPemasukan})
                .done(function (response) {
                    $('.setting-nominal-bulan-modal').html(response.result);
                })
                .fail(function(xhr,status,error){
                    Swal.fire('Terjadi Kesalahan', error, 'error')
                });
            }else{
                Swal.fire('Peringatan', 'Simpan terlebih dahulu pos pemasukan ini sebelum mengatur nominal tiap bulan', 'warning')
            }
        }
    </script>
    <script>
        $('#formData').submit(function (e) {
            e.preventDefault();
            const data = $('#formData').serialize()
            $.post("{{ route('pos_pemasukan.store') }}", data)
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
                        location.href = "{{ route('pos_pemasukan.main') }}"
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
